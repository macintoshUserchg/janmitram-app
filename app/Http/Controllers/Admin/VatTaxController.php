<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\VatTaxRequest;
use App\Models\VatTax;
use App\Repositories\VatTaxRepository;
use Illuminate\Http\Request;

class VatTaxController extends Controller
{
    public function index()
    {
        $vatTaxes = VatTaxRepository::query()->latest('id')->paginate(20);
        $activeVatTaxes = VatTaxRepository::getActiveVatTaxes();
        $defaultVatTax = VatTaxRepository::getDefaultVatTax();

        return view('admin.vattax.index', compact('vatTaxes', 'activeVatTaxes', 'defaultVatTax'));
    }

    public function store(VatTaxRequest $request)
    {
        VatTaxRepository::storeByRequest($request);

        return to_route('admin.vatTax.index')->withSuccess(__('Vat tax created successfully'));
    }

    public function update(VatTax $vatTax, VatTaxRequest $request)
    {
        VatTaxRepository::updateByRequest($vatTax, $request);

        return to_route('admin.vatTax.index')->withSuccess(__('Vat tax updated successfully'));
    }

    public function toggle(VatTax $vatTax)
    {
        VatTaxRepository::toggle($vatTax);

        return to_route('admin.vatTax.index')->withSuccess(__('Status Updated Successfully'));
    }

    public function setDefault(VatTax $vatTax)
    {
        VatTaxRepository::setDefault($vatTax);

        return to_route('admin.vatTax.index')->withSuccess(__('Default tax updated successfully'));
    }

    public function setDefaultFromForm(Request $request)
    {
        $request->validate(['vat_tax_id' => 'required|exists:vat_taxes,id']);

        VatTaxRepository::setDefault(VatTax::findOrFail($request->vat_tax_id));

        return back()->withSuccess(__('Default tax updated successfully'));
    }

    public function destroy(VatTax $vatTax)
    {
        $vatTax->delete();

        return to_route('admin.vatTax.index')->withSuccess(__('Deleted Successfully'));
    }
}
