<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ProjectGuideController extends Controller
{
    /**
     * Display the comprehensive Janmitram Project Guide.
     */
    public function index(): View
    {
        return view('admin.project-guide.index');
    }
}
