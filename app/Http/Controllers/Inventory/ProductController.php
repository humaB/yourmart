<?php

namespace App\Http\Controllers\Inventory;

use App\Helpers\SlugHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseCollection;
use App\Http\Resources\ValidationCollection;
use App\Models\Inventory\Product\Color;
use App\Models\Inventory\Product\ProductAttachment;
use App\Models\Inventory\Product\Variation\Product;
use App\Models\Inventory\Product\Variation\ProductAttribute;
use App\Models\Inventory\Product\Variation\ProductDimension;
use App\Models\Inventory\Product\Variation\ProductDiscountPerQty;
use App\Models\Inventory\Product\Variation\ProductSaleSchedule;
use App\Models\Inventory\Product\Variation\ProductTag;
use App\Models\Inventory\Product\Variation\ProductUpsellCrossSell;
use App\Models\Inventory\Product\Variation\ProductVariation;
use App\Models\Inventory\Product\Variation\ProductVariationImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function index()
    {
        return view('inventory.product.products');
    }

    public function fetchProducts()
    {
        $products = Product::with('user:id,name')->orderBy('id', 'desc')->get();

        return (new ResponseCollection($products))
            ->response()
            ->setStatusCode(200);
    }

    public function dropDown(Request $request)
    {
        $products = Product::orderBy('id', 'desc')
            ->where('title', 'LIKE', "%$request->search%")
            ->select('id as code', 'title as label')
            ->limit(10)
            ->get();

        return (new ResponseCollection($products))
            ->response()
            ->setStatusCode(200);
    }

    public function details(Request $request)
    {
        $products = Product::with(
            'category:id,name',
            'brand:id,name',
            'shipping:id,name',
            'user:id,name',
            'attributes.attribute:id,name',
            'tags.tag:id,name',
            'dimensions',
            'discounts',
            'saleSchedule',
            'variations.color',
            'variations.size',
            'variations.images.attachment',
            'up_sells.product:id,title',
            'cross_sells.product:id,title',
            'bought_togethers.product:id,title'
        )
            ->where('id', $request->id)
            ->orderBy('id', 'desc')->get();

        return (new ResponseCollection($products))
            ->response()
            ->setStatusCode(200);
    }

    public function store(Request $request)
    {

        $lock = Cache::lock('add_new_product')->block(7, function () use ($request) {

            $validator = \Validator::make($request->all(), [
                'title'            => 'required|string|max:255',
                'shortDescription' => 'nullable|string',
                'brand'            => 'nullable|integer',
                'category'         => 'required|integer',
                'selectedShipping' => 'nullable|integer',
                'heroImage'        => 'string',
                'productDescription' => 'nullable|string',
                'regularPrice'       => 'required|numeric|min:0',
                'salePrice'          => 'nullable|numeric|min:0',
                'colors'             => 'nullable|array',
                'colors.*'           => 'integer|max:255',
                'tags'               => 'nullable|array',
                'tags.*'            => 'integer',
                'sizes'             => 'nullable|array',
                'sizes.*'           => 'integer',
                'attributes'        => 'nullable|array',
                'attributes.*'      => 'integer',
                'warranty'          => 'nullable|string|max:255',
                'quantityStep'      => 'nullable|integer|min:1',
                'maximumQuantity'   => 'nullable|integer|min:1',
                'images'            => 'nullable|array',
                'images.*'          => 'nullable|array',
                'saleSchedule'      => 'nullable|json',
                'discountPerQty'    => 'nullable|json',
            ]);

            $validation = $this->validation($validator);
            if ($validation) {
                return $validation;
            }

            $exist = Product::where('title', $request->input('title'))->first();
            if ($exist) {
                return (new ValidationCollection(['This product with this title is already added']))
                    ->response()
                    ->setStatusCode(421);
            }

            $userID = auth()->user()->id;
            // Create the product
            $product = Product::create([
                'title' => $request->input('title'),
                'slug' => SlugHelper::generateSlug($request->title),
                'short_description'   => $request->input('shortDescription'),
                'brand_id'            => $request->input('brand'),
                'category_id'         => $request->input('category'),
                'shipping_method_id'  => $request->input('selectedShipping'),
                'hero_image'          => $request->input('heroImage'),
                'video_link'          => $request->input('videoLink'),
                'product_description' => $request->input('productDescription'),
                'product_highlight' => $request->input('productHighlights'),
                'warranty'      => $request->input('warranty'),
                'max_quantity'  => $request->input('maximumQuantity') ?? '',
                'quantity_step' => $request->input('quantityStep') ?? '',
                'status'   => 0, // If saleSchedule exists, set to Schedule
                'added_by' => $userID,
            ]);

            // Handle Product Attributes
            if ($request->filled('attributes')) {
                foreach ($request->input('attributes') as $attributeId) {
                    ProductAttribute::create([
                        'product_id' => $product->id,
                        'attribute_id' => $attributeId,
                        'added_by' => $userID,
                    ]);
                }
            }

            // Handle Product Tags
            if ($request->filled('tags')) {
                foreach ($request->input('tags') as $tagId) {
                    ProductTag::create([
                        'product_id' => $product->id,
                        'tag_id' => $tagId,
                        'added_by' => $userID,
                    ]);
                }
            }

            // Handle Product Upsell and CrossSell
            if ($request->upsells && $request->filled('upsells')) {
                foreach ($request->input('upsells') as $upsellProductCode) {
                    if ($upsellProductCode != 'undefined') {
                        ProductUpsellCrossSell::create([
                            'product_id' => $product->id,
                            'type' => 'upsell',
                            'reference_product_id' => $upsellProductCode,
                            'added_by' => $userID,
                        ]);
                    }
                }
            }

            if ( $request->crossSells && $request->filled('crossSells')) {
                foreach ($request->input('crossSells') as $crossSellProductCode) {
                    if ($crossSellProductCode != 'undefined') {
                        ProductUpsellCrossSell::create([
                            'product_id' => $product->id,
                            'type' => 'cross sell',
                            'reference_product_id' => $crossSellProductCode,
                            'added_by' => $userID,
                        ]);
                    }
                }
            }

            if ( $request->boughtTogethers && $request->filled('boughtTogethers')) {
                foreach ($request->input('boughtTogethers') as $boughtTogetherProductCode) {
                    if ($boughtTogetherProductCode != 'undefined') {
                        ProductUpsellCrossSell::create([
                            'product_id' => $product->id,
                            'type' => 'bought togethers',
                            'reference_product_id' => $boughtTogetherProductCode,
                            'added_by' => $userID,
                        ]);
                    }
                }
            }

            $colors = $request->input('colors', []); // Get colors, default to empty array if not available
            $sizes = $request->input('sizes', []);   // Get sizes, default to empty array if not available

            if (!empty($colors) && !empty($sizes)) {
                // Case 1: Both colors and sizes are available
                foreach ($colors as $color) {
                    foreach ($sizes as $size) {
                        $this->createProductVariation($product, $color, $size, $request->input('regularPrice'), $request->input('salePrice'), $userID);
                    }
                }
            } elseif (!empty($colors)) {
                // Case 2: Only colors are available
                foreach ($colors as $color) {
                    $this->createProductVariation($product, $color, null, $request->input('regularPrice'), $request->input('salePrice'), $userID);
                }
            } elseif (!empty($sizes)) {
                // Case 3: Only sizes are available
                foreach ($sizes as $size) {
                    $this->createProductVariation($product, null, $size, $request->input('regularPrice'), $request->input('salePrice'), $userID);
                }
            }
            else {
                // Case 4: Neither colors nor sizes are available
                $this->createProductVariation($product, null, null, $request->input('regularPrice'), $request->input('salePrice'), $userID);
            }

            // Handle Product Variation Images
            if ($request->filled('images')) {
                foreach ($request->input('images') as $color => $imagePaths) {
                    $color_id = Color::where('name', $color)->value('id');
                    $variations = ProductVariation::where('product_id', $product->id)->where('color_id', $color_id ?? 0)->get();
                    if ($variations->count() > 0) {
                        foreach ($imagePaths as $imagePath) {
                            $attachment = ProductAttachment::where('attachment', $imagePath)->value('id');
                            foreach ($variations as $variation) {
                                ProductVariationImage::create([
                                    'product_variation_id' => $variation->id,
                                    'image_id' => $attachment,
                                    'added_by' => $userID,
                                ]);
                            }
                        }
                    }
                }
            }

            // Handle Sale Schedule
            if ($request->filled('saleSchedule')) {
                $schedule = json_decode($request->input('saleSchedule'), true);
                if( $schedule['status'] ){
                    ProductSaleSchedule::create([
                        'product_id' => $product->id,
                        'from' => $schedule['from'],
                        'to' => $schedule['to'],
                        'price' => $request->input('salePrice'),
                        'added_by' => $userID,
                    ]);
                }
            }

            // Handle Discounts per Quantity
            if ($request->filled('discountPerQty')) {
                $discounts = json_decode($request->input('discountPerQty'), true);
                foreach ($discounts as $discount) {
                    if( (float)$discount['quantity'] != 0 && (float)$discount['price'] != 0 ){
                        ProductDiscountPerQty::create([
                            'product_id' => $product->id,
                            'quantity' => $discount['quantity'],
                            'price' => $discount['price'],
                            'added_by' => $userID,
                        ]);
                    }
                }
            }
            if ($request->filled('dimensions')) {
                $dimensions = json_decode($request->input('dimensions'), true);
                if (
                    !empty($dimensions['weight']) && $dimensions['weight'] != 0 ||
                    !empty($dimensions['length']) && $dimensions['length'] != 0 ||
                    !empty($dimensions['height']) && $dimensions['height'] != 0 ||
                    !empty($dimensions['width']) && $dimensions['width'] != 0
                ) {
                    ProductDimension::create([
                        'product_id' => $product->id, // Make sure product_id is provided in the request
                        'weight' => $dimensions['weight'],
                        'length' => $dimensions['length'],
                        'height' => $dimensions['heigth'],
                        'width' => $dimensions['width'],
                        'added_by' => $userID, // Assuming 'added_by' is the ID of the authenticated user
                    ]);
                }
            }
        });

        return $lock;
    }

    // Helper function to create a product variation
    function createProductVariation($product, $color, $size, $regularPrice, $salePrice, $userID)
    {
        $variation = ProductVariation::create([
            'product_id' => $product->id,
            'sku' => 0,  // Temporary SKU, will update after creation
            'color_id' => $color ?? 0, // Null if no color available
            'size_id' => $size ?? 0,   // Null if no size available
            'regular_price' => $regularPrice,
            'sale_price'    => $salePrice,
            'stock' => 0, // Default stock, adjust as necessary
            'added_by' => $userID,
        ]);

        // Generate SKU based on available data
        $variation->update([
            'sku' => SlugHelper::generateSku($product, $color, $size, $variation->id),
        ]);
    }

    private function validation($validator)
    {

        if ($validator->fails()) {

            $validationErrors = [];
            $errors = $validator->errors()->all();

            foreach ($errors as $error) {
                array_push($validationErrors, $error);
            }

            return (new ValidationCollection($validationErrors))
                ->response()
                ->setStatusCode(400);
        }
    }
}
