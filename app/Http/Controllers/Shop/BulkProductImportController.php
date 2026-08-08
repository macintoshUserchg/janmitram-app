<?php

namespace App\Http\Controllers\Shop;

use App\Exports\TemplateExport;
use App\Http\Controllers\Controller;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BulkProductImportController extends Controller
{
    public function index()
    {
        $this->abortUnlessRootShop();

        $shop = generaleSetting('shop');
        $galleries = $shop->galleries()->latest('id')->get();

        return view('shop.bulk-product.import', compact('galleries') + ['isRootShop' => true]);
    }

    public function store(Request $request)
    {
        $this->abortUnlessRootShop();

        $request->validate(['file' => 'required|file|mimes:xlsx']);

        $file = $request->file('file');

        $sheet = IOFactory::load($file->getRealPath())->getActiveSheet();
        $all = $sheet->toArray();
        $header = array_shift($all);

        $expectedHeaders = ProductRepository::importHeaders();
        $uploadedHeaders = array_map(fn ($h) => strtolower(trim((string) $h)), $header);

        if ($uploadedHeaders !== $expectedHeaders) {
            return back()->with('error', __('Invalid file format. The header row must be the standard product template ('.implode(', ', $expectedHeaders).').'));
        }

        if (count($all) <= 0) {
            return back()->with('error', __('Sorry! File is empty.'));
        }

        $result = ProductRepository::importRows($all);

        $flash = [
            'success' => 'Imported '.$result['imported'].', updated '.$result['updated'].', failed '.$result['failed'],
        ];

        if ($result['errors']) {
            $headersWithReason = [...$header, 'reason'];

            $failedRows = collect($result['errors'])
                ->sortKeys()
                ->map(fn ($error) => [...array_values($all[$error['row'] - 1]), 'reason' => $error['reason']])
                ->values();

            $filename = 'import-errors-'.uniqid().'.xlsx';

            Excel::store(new TemplateExport(collect([$headersWithReason])->concat($failedRows)), 'import-errors/'.$filename, 'local');

            $flash['errors_file'] = $filename;
        }

        return back()->with($flash);
    }

    public function downloadErrors(string $file)
    {
        $this->abortUnlessRootShop();

        if (basename($file) !== $file) {
            abort(404);
        }

        $path = 'import-errors/'.$file;

        if (! Storage::disk('local')->exists($path)) {
            abort(404);
        }

        return Storage::disk('local')->download($path);
    }

    private function abortUnlessRootShop(): void
    {
        if (generaleSetting('shop')?->id !== generaleSetting('rootShop')?->id) {
            abort(403);
        }
    }
}
