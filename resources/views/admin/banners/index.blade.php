@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Banner List</h2>
    <a href="{{ route('banners.create') }}" class="btn btn-primary">Add Banner</a>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Image</th>
                <th>Link</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($banners as $banner)
                <tr>
                    <td>{{ $banner->title }}</td>
                    <td><img src="{{ asset('storage/'.$banner->image) }}" width="100"></td>
                    <td>{{ $banner->link }}</td>
                    <td>
                        <a href="{{ route('banners.edit', $banner->id) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('banners.destroy', $banner->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
