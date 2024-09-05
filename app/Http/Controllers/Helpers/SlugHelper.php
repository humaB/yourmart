<?php

namespace App\Helpers;

use App\Models\Inventory\Product\Category;
use App\Models\Inventory\Product\Color;
use App\Models\Inventory\Product\Size;
use App\Models\Inventory\Product\Variation\Product;
use Illuminate\Support\Str;

class SlugHelper
{
    public static function generateSlug($string)
    {
        return Str::slug($string, '-');
    }

    public static function generateSku($product, $color = null, $size = null, $id)
    {
        // Get the category name from the product
        $productCategory = Category::find($product->category_id);
        $productId = $product->id; // Assuming product ID is used as a code

        // Default SKU base with product category and product ID
        $sku = substr($productCategory->name, 0, 2) . '-' . substr($productId, 0, 2);

        // Append color code if available
        if ($color) {
            $colorCode = Color::find($color)->code; // Color code or name
            $sku .= '-' . substr($colorCode, 0, 2); // Append color to SKU
        } else {
            $sku .= '-00'; // Fallback for missing color
        }

        // Append size code if available
        if ($size) {
            $sizeCode = Size::find($size)->code; // Size code or name
            $sku .= '-' . substr($sizeCode, 0, 2); // Append size to SKU
        } else {
            $sku .= '-00'; // Fallback for missing size
        }

        // Append the variation ID to make SKU unique
        $sku .= '-' . $id;

        // Return the generated SKU
        return $sku;
    }
}
