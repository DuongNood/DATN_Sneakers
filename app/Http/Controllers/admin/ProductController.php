<?php

namespace App\Http\Controllers\admin;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\ImageProduct;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $title = "Product";
        $listProduct = Product::where('status', true)->get();
        return view('admin.product.index',compact('title','listProduct'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $title = "Product";
        $listCategories = Category::where('status',true)->get();
        return view('admin.product.create',compact('title','listCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        
            $params = $request->except('_token');
           
            if ($request->has('status')) {
            $params['status'] = 1;
            } 

            if($request->hasFile('image')){
                $params['image'] =$request->file('image')->store('uploads/product', 'public');
            }else{
                $params['image'] =null;
            }
            
            $product = Product::create($params);
            //lay id sp vua create
            $productID = $product->id;
            if($request->has('list_image')){
                foreach($request->file('list_image') as $image){
                    if($image){
                        $path = $image->store('uploads/ablum_product/id_'.$productID, 'public');
                        $product->imageProduct()->create(
                            ['product_id'=>$productID,
                            'image_product'=>$path,]);
                    }
                }
            }
            return redirect()->route('products.index')->with('success', 'Successfully added new product');
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $title ="Cap nhat San pham";
        $product = Product::find($id);
        $category = Category::where('status', true)->get();
        return view('admin.product.edit', compact('title', 'product','category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
         $params = $request->except('_token','_method');

           

            $product =Product::find($id);

            if($request->hasFile('image')){
                if($product->image && Storage::disk('public')->exists($product->image)){
                    Storage::disk('public')->delete($product->image);
                }
                $params['image'] =$request->file('image')->store('uploads/product', 'public');
            }else{
                $params['image'] =$product->image;
            }
            
           
                $currentImages= $product->imageProduct()->pluck('id')->toArray();
                $arrayCombine = array_combine($currentImages, $currentImages);
                foreach($arrayCombine as $key => $values){
                    if(!array_key_exists($key, $request->list_image)){
                        $hinhAnhSp = ImageProduct::query()->find($key);
                        if($hinhAnhSp->image_product && Storage::disk('public')->exists($hinhAnhSp->image_product)){
                            Storage::disk('public')->delete($hinhAnhSp->image_product);
                            $hinhAnhSp->delete();
                        }
                    }
                }

                foreach($request->list_image as $key=>$image){
                    if(!array_key_exists($key, $arrayCombine)){
                        if($request->hasFile("list_image.$key")){
                            $path = $image->store('uploads/ablum_product/id_'.$id, 'public');
                            $product->imageProduct()->create([
                                'san_pham_id'=>$id,
                                'image_product'=>$path
                            ]);
                        }
                    }else if(is_file($image) && $request->hasFile("list_image.$key")){
                        $hinhAnhSp = ImageProduct::query()->find($key);
                        if($hinhAnhSp && Storage::disk('public')->exists($hinhAnhSp->image_product)){
                            Storage::disk('public')->delete($hinhAnhSp->image_product);
                        }
                        $path= $image->store('uploads/ablum_product/id_'.$id, 'public');
                        $hinhAnhSp->update([
                                'image_product'=>$path
                            ]);
                    }            
                }

            
            $product->update($params);
            return redirect()->route('products.index')->with('success', 'Successfully updated product');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function productDiscontinued()
    {
        //
        $title = "Product";
        $listProduct = Product::where('status', false)->get();
        return view('admin.product.productDiscontinued',compact('title','listProduct'));
    }
}
