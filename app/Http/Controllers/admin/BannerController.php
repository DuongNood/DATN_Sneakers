<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    const PATH_VIEW = 'admin.banners.';

    public function index()
    {
        $data = Banner::latest('id')->paginate(5);

        return view(self::PATH_VIEW . __FUNCTION__, compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view(self::PATH_VIEW . __FUNCTION__);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|max:255',
            'image' => 'required|image|max:2048',
            'status' => ['nullable', Rule::in([0, 1])],
        ]);

        try {
            if ($request->hasFile('image')) {
                $data['image'] = Storage::put('banners', $request->file('image'));
            }

            Banner::query()->create($data);

            return redirect()
                ->route(self::PATH_VIEW . 'index')
                ->with('success', true);
        } catch (\Throwable $th) {

            if (!empty($data['image']) && Storage::exists($data['image'])) {
                Storage::delete($data['image']);
            }

            return back()
                ->with('success', false)
                ->with('error', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Banner $banner)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Banner $banner)
    {   
        return view(self::PATH_VIEW . __FUNCTION__, compact('banner'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Banner $banner)
    {
        $data = $request->validate([
            'title' => 'required|max:255',
            'image' => 'required|image|max:2048',
            'status' => ['nullable', Rule::in([0, 1])],
        ]);

        try {

            $data['status'] ??= 0;

            if ($request->hasFile('image')) {
                $data['image'] = Storage::put('banners', $request->file('image'));
            }

            $currentImage = $banner->image;

            $banner->update($data);

            if ($request->hasFile('image') && !empty($currentImage) && Storage::exists($currentImage)) {
                Storage::delete($currentImage);
            }

            return back()
                ->with('success', true);
        } catch (\Throwable $th) {

            if (!empty($data['image']) && Storage::exists($data['image'])) {
                Storage::delete($data['image']);
            }

            return back()
                ->with('success', false)
                ->with('error', $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Banner $banner)
    {
        try {
            $banner->delete();

            return back()
                ->with('success', true);
        } catch (\Throwable $th) {
            return back()
                ->with('success', false)
                ->with('error', $th->getMessage());
        }
    }
}
