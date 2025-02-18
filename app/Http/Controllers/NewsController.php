<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Storage;

class NewsController extends Controller
{
    const PATH_VIEW = "admin.news";

    public function index(){

        $data = News::query()->latest()->paginate(10);

        return view(self::PATH_VIEW . __FUNCTION__ , compact('data'));
    }

    public function create(){

        return view(self::PATH_VIEW . __FUNCTION__);

    }

    public function store(Request $request){
        $data = $request->validate([
            'title' => 'required',
            'image' => 'required|mimes:jpg,jpeg,png,webp|max:2048',
            'content' => 'required'
        ]);

        try {
            if ($request->hasFile('image')) {
                $data['image'] = Storage::put('news', $request->file('image'));
            }

            News::query()->create($data);

            return redirect()->route('news.index')->with('success', 'create successfully');

        } catch (\Throwable $th) {
            //throw $th;
            if (!empty($data['image']) && Storage::exists($data['image'])) {
                Storage::delete($data['image']);
            }

            return back()->with('error', $th->getMessage());
        }
    }

    public function show(News $news){
        return view(self::PATH_VIEW . __FUNCTION__, compact('news'));
    }

    public function edit(News $news){
        return view(self::PATH_VIEW . __FUNCTION__, compact('news'));
    }

    public function update(Request $request , News $news){
        $data = $request->validate([
            'title' => 'required',
            'image' => 'required|mimes:jpg,jpeg,png,webp|max:2048',
            'content' => 'required'
        ]);
        try {
            if ($request->hasFile('image')) {
                $data['image'] = Storage::put('news', $request->file('image'));
            }

            $currentimage = $news->image;

            News::query()->update($data);

            if (
                $request->hasFile('image')
                && !empty($currentimage)
                && Storage::exists($currentimage)
            ) {
                Storage::delete($currentimage);
            }

            return back()->with('success', true);

        } catch (\Throwable $th) {
            //throw $th;
            if (!empty($data['image']) && Storage::exists($data['image'])) {
                Storage::delete($data['image']);
            }

            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy(News $news)
    {
        try {

            $news->delete();

            return redirect()->route('news.index')->with('success', 'Đã xóa thành công bản ghi');

        } catch (\Throwable $th) {
            return back()
                ->with('success', true)
                ->with('error', $th->getMessage());

        }
    }

}
