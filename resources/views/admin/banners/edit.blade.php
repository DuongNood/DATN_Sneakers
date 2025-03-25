@extends('admin.layouts.master')
@section('title')
    Chỉnh sửa banner
@endsection
@section('content')
    <div class="container">
        <h2>Chỉnh sửa Banner</h2>

        <form action="{{ route('admin.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label>Title</label>
                <input type="text" name="title" class="form-control" value="{{ $banner->title }}">
            </div>

            <div class="mb-3">
                <label>Current Image</label>
                <br>
                <img src="{{ asset('storage/' . $banner->image) }}" width="100">
            </div>

            <div class="mb-3">
                <label>New Image</label>
                <input type="file" name="image" class="form-control">
            </div>

            <div class="mb-3">
                <label>Link</label>
                <input type="url" name="link" class="form-control" value="{{ $banner->link }}">
            </div>

            <button type="submit" class="btn btn-success">Update</button>
        </form>
    </div>
@endsection
