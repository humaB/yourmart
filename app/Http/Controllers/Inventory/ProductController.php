<?php

namespace App\Http\Controllers\Inventory;

use App\Exports\ProductExport;
use App\Helpers\SlugHelper;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Helpers\NotificationHelper;
use App\Http\Resources\ResponseCollection;
use App\Http\Resources\ValidationCollection;
use App\Models\Inventory\Product\Color;
use App\Models\Inventory\Product\ProductAttachment;
use App\Models\Inventory\Product\ProductQrCode;
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
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public function index()
    {
        return view('inventory.product.products');
    }

    public function fetchProducts(Request $request)
    {
        $status = $request->query('status');
        $perPage = $request->query('per_page', 20); // Optional, default 10


        $products = Product::with('user:id,name', 'variation', 'category', 'tags.tag');

        if ($status === '0') {
            $products = $products->where('status', 0);
        } elseif ($status === '1') {
            $products = $products->where('status', 1);
        } elseif ($status === '3') {
            $products = $products->onlyTrashed();
        }

        $products = $products->orderBy('id', 'desc')->paginate($perPage);

      $data = [
        'allProductCount'       => Product::all()->count(),
        'publishedProductCount' => Product::where('status', 0)->count(),
        'draftProductCount'     => Product::where('status', 1)->count(),
        'trashProductCount'     => Product::onlyTrashed()->count(),
        'products'              => $products,
        'pagination'            => [
            'total'        => $products->total(),
            'per_page'     => $products->perPage(),
            'current_page' => $products->currentPage(),
            'last_page'    => $products->lastPage(),
            'from'         => $products->firstItem(),
            'to'           => $products->lastItem(),
        ],

        'productInsights' => [
            'total' => Product::where('status', 0)->count(),
            'airpod' => Product::where('status', 0)->where('category_id', '1')->count(),
            'kids' => Product::where('status', 0)->where('category_id', '5')->count(),
            'smartGadget' =>  Product::where('status', 0)->where('category_id', '7')->count(),
            'personalCare' => Product::where('status', 0)->where('category_id', '8')->count(),
            'smartWatch' =>  Product::where('status', 0)->where('category_id', '9')->count(),
            'decor' =>  Product::where('status', 0)->where('category_id', '10')->count(),
            'home' =>  Product::where('status', 0)->where('category_id', '6')->count(),
        ]
      ];

      return (new ResponseCollection($data))
      ->response()
      ->setStatusCode(200);
    }

    public function changeStatus( Request $request ){
        if( $request->action == "delete" )
        {
            Product::whereIn('id', $request->products)->update([
                  'status' => '3'
            ]);
            Product::whereIn('id', $request->products)->delete();
        }
        else
        {
            $products = Product::whereIn('id', $request->products)->get();

            if( $request->action == 'Published' ){
                foreach( $products as $product ){
                    $link = env('MIX_WEB_URL').'products/'.$product->slug;

                    NotificationHelper::addNotification(
                        $title = 'New Product Added',
                        $messge = "This product is now available: $product->title",
                        $link = $link,
                        $image = null,
                        $directImage = $product->hero_image,
                        $color    = 'blue',
                        $isPublic = 0,
                        $user = null
                    );

                    $product->update([
                        'status' => $request->action == 'Published' ? '0' : '1'
                    ]);
                }
            }
        }
    }

    public function filterData( Request $request ){

        $categoryCode = $request->input('category.code'); // Assuming payload is from a request
        $productName = $request->input('product');
        $tagCode = $request->input('tag.code');

        $products = Product::with('user:id,name', 'variation', 'category', 'tags.tag')
            ->when($categoryCode, function ($query, $categoryCode) {
                return $query->where('category_id', $categoryCode);
            })
            ->when($productName, function ($query, $productName) {
                return $query->where('title', 'like', "%{$productName}%");
            })
            ->when($tagCode, function ($query, $tagCode) {
                return $query->whereHas('tags', function ($tagQuery) use ($tagCode) {
                    $tagQuery->where('tag_id', $tagCode);
                });
            })
            ->orderBy('id', 'desc')
            ->get();

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

    public function completeDropDown(Request $request)
    {
        $products = Product::orderBy('id', 'desc')
            ->select('id as code', 'title as label')
            ->get();

        return (new ResponseCollection($products))
            ->response()
            ->setStatusCode(200);
    }

    public function details(Request $request)
    {
        $products = Product::withTrashed()
        ->with(
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
                'selectedPackaging' => 'nullable|integer',
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
            $slug = SlugHelper::generateSlug($request->title);
            // Create the product
            $product = Product::create([
                'title' => $request->input('title'),
                'slug' => $slug,
                'short_description'   => $request->input('shortDescription'),
                'brand_id'            => $request->input('brand'),
                'category_id'         => $request->input('category'),
                'shipping_method_id'  => $request->input('selectedPackaging') == 0 ? 1 : $request->input('selectedPackaging'),//this column using package class and there is no module like shipping class from now
                'hero_image'          => $request->input('heroImage'),
                'video_link'          => $request->input('videoLink'),
                'product_description' => $request->input('productDescription'),
                'product_highlight' => $request->input('productHighlights'),
                'warranty'      => $request->input('warranty'),
                'max_quantity'  => $request->input('maximumQuantity') ?? '',
                'quantity_step' => $request->input('quantityStep') ?? '',
                'status'   => 1, // If saleSchedule exists, set to Schedule
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

            if ($request->crossSells && $request->filled('crossSells')) {
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

            if ($request->boughtTogethers && $request->filled('boughtTogethers')) {
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
            } else {
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
                                    'product_id' => $product->id,
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
                if ($schedule['status']) {
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
                    ProductDiscountPerQty::create([
                        'product_id' => $product->id,
                        'quantity' => $discount['quantity'],
                        'price' => $discount['price'],
                        'added_by' => $userID,
                    ]);
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

    public function cloneProduct(Request $request)
    {
        $lock = Cache::lock('clone_product')->block(7, function () use ($request) {

            $validator = \Validator::make($request->all(), [
                'id' => 'required|integer|exists:inventory_products,id', // ID of the product to be cloned
            ]);

            $validation = $this->validation($validator);
            if ($validation) {
                return $validation;
            }

            $userID = auth()->user()->id;

            // Fetch the existing product
            $existingProduct = Product::with(['attributes', 'tags', 'up_sells', 'cross_sells', 'bought_togethers', 'variations', 'images', 'saleSchedule', 'discounts', 'dimensions'])
                ->findOrFail($request->id);

            // Create the new product
            $product = Product::create([
                'title' => $existingProduct->title . ' - Clone',
                'slug' => SlugHelper::generateSlug($existingProduct->title . '-clone'),
                'short_description'   => $existingProduct->short_description,
                'brand_id'            => $existingProduct->brand_id,
                'category_id'         => $existingProduct->category_id,
                'shipping_method_id'  => $existingProduct->shipping_method_id,
                'hero_image'          => $existingProduct->hero_image,
                'video_link'          => $existingProduct->video_link,
                'product_description' => $existingProduct->product_description,
                'product_highlight' => $existingProduct->product_highlight,
                'warranty'      => $existingProduct->warranty,
                'max_quantity'  => $existingProduct->max_quantity,
                'quantity_step' => $existingProduct->quantity_step,
                'status'   => $existingProduct->status,
                'added_by' => $userID,
            ]);

            $product->update([
                'title' => $existingProduct->title . ' - Clone' . $product->id,
                'slug' => SlugHelper::generateSlug($existingProduct->title . '-clone' . $product->id),
            ]);

            // Clone attributes
            foreach ($existingProduct->attributes as $attribute) {
                ProductAttribute::create([
                    'product_id' => $product->id,
                    'attribute_id' => $attribute->attribute_id,
                    'added_by' => $userID,
                ]);
            }

            // Clone tags
            foreach ($existingProduct->tags as $tag) {
                ProductTag::create([
                    'product_id' => $product->id,
                    'tag_id' => $tag->tag_id,
                    'added_by' => $userID,
                ]);
            }

            // Clone upsells, crossSells, and boughtTogethers
            foreach (['up_sells', 'cross_sells', 'bought_togethers'] as $relation) {
                foreach ($existingProduct->$relation as $relatedProduct) {
                    ProductUpsellCrossSell::create([
                        'product_id' => $product->id,
                        'type' => $relatedProduct->type,
                        'reference_product_id' => $relatedProduct->reference_product_id,
                        'added_by' => $userID,
                    ]);
                }
            }

            // Clone variations
            foreach ($existingProduct->variations as $variation) {
                $newVariation = $this->createProductVariation(
                    $product,
                    $variation->color_id,
                    $variation->size_id,
                    $variation->regular_price,
                    $variation->sale_price,
                    $userID
                );

                // Clone variation images
                foreach ($variation->images as $image) {
                    ProductVariationImage::create([
                        'product_id' => $product->id,
                        'product_variation_id' => $newVariation,
                        'image_id' => $image->image_id,
                        'added_by' => $userID,
                    ]);
                }
            }

            // Clone sale schedules
            if ($existingProduct->saleSchedule) {
                $schedule = $existingProduct->saleSchedule;
                ProductSaleSchedule::create([
                    'product_id' => $product->id,
                    'from' => $schedule->from,
                    'to' => $schedule->to,
                    'price' => $schedule->price,
                    'added_by' => $userID,
                ]);
            }

            // Clone discounts per quantity
            if($existingProduct->discountsPerQty){
                $discount = $existingProduct->discountsPerQty;
                ProductDiscountPerQty::create([
                    'product_id' => $product->id,
                    'quantity' => $discount->quantity,
                    'price' => $discount->price,
                    'added_by' => $userID,
                ]);
            }

            // Clone dimensions
            if ($existingProduct->dimensions) {
                ProductDimension::create([
                    'product_id' => $product->id,
                    'weight' => $existingProduct->dimensions->weight,
                    'length' => $existingProduct->dimensions->length,
                    'height' => $existingProduct->dimensions->height,
                    'width' => $existingProduct->dimensions->width,
                    'added_by' => $userID,
                ]);
            }

            return response()->json(['message' => 'Product cloned successfully', 'product_id' => $product->id], 201);
        });

        return $lock;
    }


    // Helper function to create a product variation
    private function createProductVariation($product, $color, $size, $regularPrice, $salePrice, $userID)
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

        return $variation->id;
    }

    public function update(Request $request)
    {
        $productId = $request->input('id'); // Assuming you're passing the product ID
        $lock = Cache::lock('update_product_' . $productId)->block(7, function () use ($request, $productId) {
            // Get the field and value from the request
            $field = $request->input('field');
            $value = $request->input('value');


            // Find the product to update
            $product = Product::findOrFail($productId);

            // If the field is 'title', perform a uniqueness check for another product
            if ($field === 'title') {
                $existingProduct = Product::where('title', $value)
                    ->where('id', '!=', $productId) // Exclude the current product
                    ->first();

                if ($existingProduct) {
                    return (new ValidationCollection(['This title is already used by another product']))
                        ->response()
                        ->setStatusCode(421);
                }

                // Update the title and also update the slug
                $product->title = $value;
                $product->slug = SlugHelper::generateSlug($value); // Generate a new slug based on the title
            } else {
                // Update other fields dynamically
                $product->{$field} = $value;
            }

            // Save the updated product
            $product->save();

            return response()->json([
                'message' => 'Product updated successfully.',
                'product' => $product
            ]);
        });
        return $lock;
    }

    public function variationUpdate(Request $request)
    {
        $variationId = $request->details['id']; // The current variation being updated

        // Fetch the existing variation
        $variation = ProductVariation::find($variationId);
        if (!$variation) {
            return response()->json(['status' => 'error', 'message' => 'Product variation not found.'], 404);
        }

        // Extract the current and new color and size
        $newColorId = $request->color ?? 0; // New color from the request
        $newSizeId = $request->size ?? 0; // New size from the request
        $currentColorId = $variation->color_id; // Existing color in the database
        $currentSizeId = $variation->size_id; // Existing size in the database

        // Check if color and size have changed
        if ($newColorId != $currentColorId || $newSizeId != $currentSizeId) {
            // If color or size is changed, validate the new combination
            $existingVariation = ProductVariation::where('product_id', $variation->product_id)
                ->where('color_id', $newColorId)
                ->where('size_id', $newSizeId)
                ->where('product_id', $variation->product_id)
                ->where('id', '!=', $variationId) // Exclude the current variation being updated
                ->first();

            if ($existingVariation) {
                return (new ValidationCollection(['This color and size combination already exists for this product.']))
                    ->response()
                    ->setStatusCode(421);
            }
        }

        // Lock the update process to avoid race conditions
        $lock = Cache::lock('update_product_variation_' . $variation->product_id)->block(7, function () use ($request, $variation, $newColorId, $newSizeId) {
            // Update the variation fields (color, size, regular price, sale price, etc.)
            $variation->update([
                'color_id' => $newColorId, // Update only if changed
                'size_id' => $newSizeId,   // Update only if changed
                'regular_price' => $request->details['regular_price'],
                'sale_price' => $request->details['sale_price'],
            ]);

            // Generate SKU based on available data
            $product = Product::find($variation->product_id);
            $variation->update([
                'sku' => SlugHelper::generateSku($product, $newColorId, $newSizeId, $variation->id),
            ]);
        });

        return $lock ? response()->json(['status' => 'success', 'message' => 'Product variation updated successfully.']) : response()->json(['status' => 'error', 'message' => 'Failed to update product variation.']);
    }

    public function variationDeleteImage( Request $request ){
        ProductVariationImage::where('product_variation_id', $request->variation)->where('image_id', $request->attachment)->delete();
        return response()->json(['message' => 'Variation Image Deleted Successfully'], 200);
    }

    public function variationChangeStatus(Request $request)
    {

        ProductVariation::where('id', $request->id)->update([
            'status' => $request->status,
        ]);

        return response()->json(['message' => 'Variation Status Updated Successfully'], 200);
    }

    public function discountChanged(Request $request)
{
    // Check if an ID is present in the request
    if (isset($request->discounts['id']) && !empty($request->discounts['id'])) {
        // Update the existing discount if ID is present
        ProductDiscountPerQty::where('id', $request->discounts['id'])->update([
            'quantity' => $request->discounts['quantity'],
            'price'    => $request->discounts['price'],
        ]);

        $message = 'Discount values updated successfully';
    } else {
        // Create a new discount if no ID is present
        ProductDiscountPerQty::create([
            'product_id' => $request->product, // Assuming product_id is provided
            'quantity'   => $request->discounts['quantity'],
            'price'      => $request->discounts['price'],
            'added_by'   => auth()->user()->id
        ]);

        $message = 'New discount created successfully';
    }

        return response()->json(['message' => $message], 200);
    }

    public function dimensionsChanged(Request $request)
    {

        $productDimension = ProductDimension::where('product_id', $request->product)->first();

        if ($productDimension) {
            $productDimension->update([
                'weight' => $request->dimensions['weight'],
            'length' => $request->dimensions['length'],
            'height' => $request->dimensions['height'],
            'width' => $request->dimensions['width'],
            ]);
        } else {
            ProductDimension::create([
                'product_id' => $request->product,
                'weight' => $request->dimensions['weight'],
                'length' => $request->dimensions['length'],
                'height' => $request->dimensions['height'],
                'width' => $request->dimensions['width'],
                'added_by' => auth()->user()->id,
            ]);
        }

        return response()->json(['message' => 'Dimensiopns values changed successfully'], 200);
    }

    public function updateStatus(Request $request)
    {
        $product = Product::withTrashed()->find($request->id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->status = $request->status == 'publish' ? '0' : '1';

        // If product is deleted, restore it
        if ($product->deleted_at !== null) {
            $product->restore();
        }

        $product->save();

        if( $request->status == 'publish' ){
            $link = env('MIX_WEB_URL').'products/'.$product->slug;

            NotificationHelper::addNotification(
                $title = 'New Product Added',
                $messge = "This product is now available: $product->title",
                $link = $link,
                $image = null,
                $directImage = $product->hero_image,
                $color    = 'blue',
                $isPublic = 0,
                $user = null
            );
        }


        return response()->json(['message' => 'Status changed successfully'], 200);
    }


    public function updateHeroImage(Request $request)
    {
        Product::where('id', $request->id)->update([
            'hero_image' => $request->attachment
        ]);

        return response()->json(['message' => 'Hero Image changed successfully'], 200);
    }

    public function updateVideo(Request $request)
    {
        Product::where('id', $request->id)->update([
            'video_link' => $request->attachment
        ]);

        return response()->json(['message' => 'Video changed successfully'], 200);
    }

    public function updateColorImages(Request $request)
    {
        $images = $request->images;
        $product = ProductVariation::find($request->id);
        // Check if $images is an array
        if (is_array($images)) {
            foreach ($images as $color => $imageArray) {
                foreach ($imageArray as $image) {
                    ProductVariationImage::updateOrCreate(
                        [
                            'product_variation_id' => $request->id,
                            'image_id' => $image['id'],
                        ],
                        [
                            'product_id' => $product->product_id,
                            'added_by' => $image['added_by'],
                        ]
                    );
                }
            }
        }

        return response()->json(['message' => 'Images updated or created successfully'], 200);
    }


    public function updateUpSells(Request $request)
    {

        $request->validate([
            'id'       => 'required|integer',
            'products' => 'required|array',
            'type'     => 'required|string'
        ]);

        $productId = $request->id;
        $type     = $request->type;
        $products = $request->products;

        if ($type == 'upsell') {
            $type = 'upsell';
        } else if ($type == 'crossSell') {
            $type = 'cross sell';
        } else {
            $type = 'bought togethers';
        }
        // Loop through each product
        foreach ($products as $product) {
            if ($product != 'undefined') {
                if( $product ){
                    // Use updateOrCreate to update if exists or create a new record
                    ProductUpsellCrossSell::updateOrCreate(
                        [
                            'product_id' => $productId,
                            'type'       => strtolower($type),
                            'reference_product_id' => $product['code'] // assuming 'code' is 'reference_product_id'
                        ],
                        [
                            'added_by' => auth()->user()->id
                        ]
                    );
                }
            }
        }

        return response()->json(['message' => 'Up Sells values changed successfully'], 200);
    }

    public function updateTags(Request $request)
    {
        $request->validate([
            'id'       => 'required|integer',
            'products' => 'required|array',
            'type'     => 'required|string'
        ]);

        $productId = $request->id;
        $type     = $request->type;
        $products = $request->products;

        if ($type == 'tags') {
            // Loop through each product
            foreach ($products as $product) {
                if ($product != 'undefined') {
                    if( $product ){
                        // Use updateOrCreate to update if exists or create a new record
                        ProductTag::updateOrCreate(
                            [
                                'product_id' => $productId,
                                'tag_id' => $product['code'] // assuming 'code' is 'reference_product_id'
                            ],
                            [
                                'added_by' => auth()->user()->id
                            ]
                        );
                    }
                }
            }
        } else {
            // Loop through each product
            foreach ($products as $product) {
                if ($product != 'undefined') {

                    // Use updateOrCreate to update if exists or create a new record
                    ProductAttribute::updateOrCreate(
                        [
                            'product_id' => $productId,
                            'attribute_id' => $product['code'] // assuming 'code' is 'reference_product_id'
                        ],
                        [
                            'added_by' => auth()->user()->id
                        ]
                    );
                }
            }
        }


        return response()->json(['message' => 'Tags values changed successfully'], 200);
    }

    public function removeTag( Request $request ){

        ProductTag::where('product_id', $request->id)->where('id', $request->tag)->delete();
        return response()->json(['message' => 'Tags Removed Successfully'], 200);
    }

    public function removeRelatedProduct( Request $request ){

        ProductUpsellCrossSell::where('id', $request->product)->delete();
        return response()->json(['message' => 'Related product Removed Successfully'], 200);
    }

    public function exportExcel(){
        return Excel::download(new ProductExport, 'products.xlsx');
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
