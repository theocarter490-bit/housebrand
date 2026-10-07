<?php

namespace App\Exports;

use App\Models\Category;


use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CategoryExport implements FromView
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): View
    {
        return view('bulk-import.category-export', [
            'categories' => Category::where('active_status',1)->get()
        ]);
    }
}
