<?php

namespace App\Exports;

use App\Models\Inventory\Product\Variation\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = collect(); // Initialize an empty collection

        $products = Product::with(
            'variation',
            'category',
            'tags.tag',
            'discounts',
            'shipping',
            'dimensions',
            'up_sells.product',
            'cross_sells.product',
            'bought_togethers.product'
        )
        ->where('status', '0')
        ->get();

        $products->each(function($product, $index) use ($data) {
            // Extract tag names, discount info, cross sells, and bought together data
            $tags = $product->tags->pluck('tag.name')->toArray();
            $discounts = $product->discounts->map(function($discount) {
                return 'Q@' . $discount->quantity . ', P@' . $discount->price;
            })->toArray();
            $upsells = $product->up_sells->pluck('product.title')->toArray();
            $crosssells = $product->cross_sells->pluck('product.title')->toArray();
            $broughtTogether = $product->bought_togethers->pluck('product.title')->toArray();

            // Use implode to format each array as a comma-separated string
            $data->push([
                'Index'                 => $index + 1, // Adding 1 to make the index 1-based instead of 0-based
                'Title'                 => $product->title,
                'SKU'                   => $product->variation->sku,
                'Category'              => $product->category->name,
                'Tag'                   => implode(', ', $tags),
                'Sale Price'            => $product->variation->sale_price,
                'Discount Per Quantity' => implode(', ', $discounts),
                'Stock'                 => $product->variation->stock,
                'Weight'                => $product->dimensions->weight ?? 0,
                'Packing Class'         => $product->shipping->name,
                'Date Added'            => date('d-m-Y', strtotime($product->created_at)),
                'Up Sells'              => implode(', ', $upsells),
                'Cross Sells'           => implode(', ', $crosssells),
                'Bought Togethers'      => implode(', ', $broughtTogether),
            ]);
        });

        return $data;
    }


    public function headings(): array
    {
        return [
            'Index',
            'Title',
            'SKU',
            'Category',
            'Tag',
            'Sale Price',
            'Discount Per Quantity',
            'Stock',
            'Weight',
            'Packing Class',
            'Date Added',
            'Up Sells',
            'Cross Sells',
            'Bought Togethers',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]], // Make the header row bold
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,  // Index
            'B' => 25,  // Title
            'C' => 15,  // SKU
            'D' => 20,  // Category
            'E' => 20,  // Tag
            'F' => 15,  // Sale Price
            'G' => 25,  // Discount Per Quantity
            'H' => 10,  // Stock
            'I' => 10,  // Weight
            'J' => 20,  // Packing Class
            'K' => 15,  // Date Added
            'L' => 20,  // Up Sells
            'M' => 20,  // Cross Sells
            'N' => 20,  // Bought Togethers
        ];
    }
}
