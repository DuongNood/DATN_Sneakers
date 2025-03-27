@extends('admin.layouts.master')
@section('content')
    <div class="container-xxl">

        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                {{-- <h4 class="fs-18 fw-semibold m-0">{{ $title }}</h4> --}}
            </div>
            {{-- <a href="{{ route('news.create') }}" class="btn btn-success ">Create news</a> --}}
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
                                        <th scope="col">Content</th>
                                        <th scope="col">User</th>
                                        <th scope="col">Product</th>
                                        <th scope="col">Product Image </th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($comments as $comment)
                                        <tr>
                                            <th scope="row">{{ $comment->id }}</th>
                                            <td>{{ $comment->content }}</td>
                                            <td>{{ $comment->user->name }}</td>
                                            <td>{{ $comment->product->product_name }}</td>
                                            <td>
                                                @if ($comment->product->image)
                                                    <img src="{{ Storage::url($comment->product->image) }}" alt=""
                                                        width="100px">
                                                @endif
                                            </td>
                                            
                                            <td>
                                                
                                                <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST" class="d-inline me-2">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa bình luận?')">
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
