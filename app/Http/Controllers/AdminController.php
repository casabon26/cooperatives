<?php

namespace App\Http\Controllers;

use App\Models\Cooperative;
use App\Models\News;
use App\Models\Document;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $counts = [
            'cooperatives' => Cooperative::count(),
            'news' => News::count(),
            'documents' => Document::count(),
        ];
        // Use the admin panel view as the dashboard for now
        return view('admin.panel', compact('counts'));
    }
}
