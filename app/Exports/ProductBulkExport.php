<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductBulkExport implements FromCollection, WithHeadings, WithMapping
{
    protected $products;

    public function __construct($products)
    {
        $this->products = $products;
    }

    public function collection()
    {
        return $this->products;
    }

    public function headings(): array
    {
        return [
            'Name',
            'Description',
            'Unit Price',
            'Purchase Price',
            'Discount Type',
            'Discount',
            'Category',
            'Brand',
            'Vendor',
            'Number of Sales',
            'Approve Status',
            'Tags',
            'Publish Status',
        ];
    }

    public function map($product): array
    {
        return [
            $product->name,
            strip_tags($product->description),
            $product->unit_price,
            $product->purchase_price,
            $product->discount_type == Product::FIXED ? 'Fixed' : 'Percentage',
            $product->discount,
            optional($product->category)->name,
            optional($product->brand)->name,
            optional($product->user)->name,
            $product->num_of_sale,
            $product->is_approved ? 'Approved' : 'Disapproved',
            $product->tags,
            $product->is_published ? 'Published' : 'Unpublished',
        ];
    }
}
