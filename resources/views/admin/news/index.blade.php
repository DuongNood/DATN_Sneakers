@extends('admin.layouts.master')
@section('content')
    <div class="container-xxl">

        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                {{-- <h4 class="fs-18 fw-semibold m-0">{{ $title }}</h4> --}}
            </div>
            <a href="{{ route('admin.news.create') }}" class="btn btn-success ">Create news</a>
        </div>

        <!-- start row -->
        <div class="row"> <!-- Basic Example -->
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Title</th>
                                        <th scope="col">Image</th>
                                        {{-- <th scope="col">Content</th> --}}
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $news)
                                        <tr>
                                            <th scope="row">{{ $news->id }}</th>
                                            <td>{{ $news->title }}</td>
                                            <td>
                                                @if ($news->image)
                                                    <img src="{{ Storage::url($news->image) }}" alt=""
                                                        width="100px">
                                                @endif
                                            </td>
                                            
                                            <td>
                                                <a href="{{ route('admin.news.edit', $news) }}">
                                                    <i class="mdi mdi-pencil text-muted fs-18 rounded-2 border p-1 me-1"></i>
                                                </a>
                                                <form action="{{ route('admin.news.destroy', $news) }}" method="POST" class="d-inline me-2">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa?')">
                                                        <i class="mdi mdi-delete text-muted fs-14"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- end row -->



    </div>
@endsection
