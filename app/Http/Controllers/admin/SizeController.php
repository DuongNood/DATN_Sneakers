<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ProductSize;
use Illuminate\Http\Request;

class SizeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $title = "Size giày";
        $size = ProductSize::get();    
        return view('admin.sizes.index', compact('size','title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $title ="Size giày";
        return view('admin.sizes.create',compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $params = $request->validate([
            'name'=> 'required|max:255|unique:product_sizes',
        ]);
        ProductSize::create($params);
        return redirect()->route('admin.sizes.index')->with('success', 'Add new Success List!');
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
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
