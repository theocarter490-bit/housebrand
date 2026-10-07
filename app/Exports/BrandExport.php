<?php

namespace App\Exports;

use App\Models\Brand;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class BrandExport implements FromView
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): View
    {
        return view('bulk-import.brand-export', [
            'brands' => Brand::where('active_status', 1)->get()
        ]);
    }
}
