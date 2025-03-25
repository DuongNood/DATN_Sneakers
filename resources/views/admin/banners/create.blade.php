@extends('admin.layouts.master')
@section('title')
    Thêm mới banner
@endsection
@section('content')
    <div class="container">
        <h2>Thêm mới banner</h2>

        <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label>Title</label>
                <input type="text" name="title" class="form-control">
            </div>

            <div class="mb-3">
                <label>Image</label>
                <input type="file" name="image" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Link</label>
                <input type="url" name="link" class="form-control">
            </div>

            <button type="submit" class="btn btn-success">Save</button>
        </form>
    </div>
@endsection
