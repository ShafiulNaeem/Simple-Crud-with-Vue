<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Models\Variant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index()
    {
        return view('products.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $variants = Variant::all();
        return view('products.create', compact('variants'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'sku' => 'required|unique:products',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'url' => '/',
                'message' => $validator->errors()
            ]);
        }

        // product data
        $product_data = [
          'title' => $request->title,
          'sku' => $request->sku,
          'description' => $request->description
        ];

        $product = Product::create($product_data);


        // product images
        $product_images = [];
        if ($request->product_image != []){
            foreach ($request->product_image as $image_index => $image){
                $product_images[$image_index]['product_id'] = $product->id;
                $product_images[$image_index]['file_path'] = $image['file_path'];
                $product_images[$image_index]['thumbnail'] = 1;
                $product_images[$image_index]['created_at'] = Carbon::now()->toDateTimeString();
                $product_images[$image_index]['updated_at'] = Carbon::now()->toDateTimeString();

            }

            ProductImage::insert($product_images);
        }


        // products variant
        $product_variants = [];
        $index = 0;
        foreach ($request->product_variant as $key=>$value){
            if ($value['tags'] != []){
                foreach ($value['tags'] as $variant){
                    $product_variants[$index]['variant'] = $variant;
                    $product_variants[$index]['variant_id'] = $value['option'];
                    $product_variants[$index]['product_id'] = $product->id;
                    $product_variants[$index]['created_at'] = Carbon::now()->toDateTimeString();
                    $product_variants[$index]['updated_at'] = Carbon::now()->toDateTimeString();

                    $index++;
                }
            }
        }

        if ($product_variants != [] ){

            ProductVariant::insert($product_variants);

            // products variant prices
            $product_variant_prices = [];
            if ($request->product_variant_prices != []){
                foreach ($request->product_variant_prices as $price_index => $price){
                    $title = rtrim($price['title'],'/');
                    $title = explode('/',$title);

                    if (array_key_exists(0,$title))
                    {
                        $product_variant_prices[$price_index]['product_variant_one'] = $this->get_product_variant($title[0],$product->id);
                    }
                    if (array_key_exists(1,$title))
                    {
                        $product_variant_prices[$price_index]['product_variant_two'] = $this->get_product_variant($title[1],$product->id);
                    }
                    if (array_key_exists(2,$title))
                    {
                        $product_variant_prices[$price_index]['product_variant_three'] = $this->get_product_variant($title[2],$product->id);
                    }

                    $product_variant_prices[$price_index]['product_id'] = $product->id;
                    $product_variant_prices[$price_index]['price'] = $price['price'];
                    $product_variant_prices[$price_index]['stock'] = $price['stock'];
                    $product_variant_prices[$price_index]['created_at'] = Carbon::now()->toDateTimeString();
                    $product_variant_prices[$price_index]['updated_at'] = Carbon::now()->toDateTimeString();

                }
            }

            // insert prices data
            ProductVariantPrice::insert($product_variant_prices);

        }

        return response()->json([
            'status' => 'success',
            'url' => route('product.index'),
            'message' => 'Data inserted successfully'
        ]);


    }

    public function get_product_variant($variant,$product_id){
        $data = ProductVariant::where([
            'variant' => $variant,
            'product_id' => $product_id,
        ])->select('id')->first();
        return $data->id;
    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show($product)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $variants = Variant::all();
        return view('products.edit', compact('variants'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }

    public function multiple_image(Request $request)
    {
        $imageName = time() . '.' . $request->file->getClientOriginalExtension();
        $request->file->move(public_path('images'), $imageName);
        $path = '/images/'.$imageName;

        return response()->json(['file_path' => $path]);

    }

}
