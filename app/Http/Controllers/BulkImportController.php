<?php

namespace App\Http\Controllers;

use App\Exports\BrandExport;
use App\Exports\CategoryExport;
use App\Exports\ProductExport;
use App\Http\Requests\BulkImportRequest;
use App\Imports\ProductsImport;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Mpdf;

class BulkImportController extends Controller
{
    //

    public function index()
    {
        return view('bulk-import.index');
    }

    public function import(BulkImportRequest $request)
    {
        $import = new ProductsImport();
        $import->import(request()->file('productListFile'));
        Toastr::success('Product Created Successfully');

        return back();
    }

    public function productExport()
    {
        return Excel::download(new ProductExport, 'products_bulk.xlsx', \Maatwebsite\Excel\Excel::XLSX, [
            'Content-Type' => 'text/xlsx',
        ]);
    }

    public function categoryExport()
    {
        return Excel::download(new CategoryExport, 'category.pdf', \Maatwebsite\Excel\Excel::MPDF, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function brandExport()
    {
        return Excel::download(new BrandExport, 'brand.pdf',  \Maatwebsite\Excel\Excel::MPDF, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
