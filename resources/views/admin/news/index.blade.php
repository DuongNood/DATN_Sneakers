@extends('admin.layouts.master')
@section('content')
    <div class="container-xxl">

        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">{{ $title }}</h4>
            </div>
            <a href="{{ route('news.create') }}" class="btn btn-success ">Create news</a>
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
                                            {{-- <td>{{$news->content}}</td> --}}
                                            <td>
                                                <a class="btn btn-primary me-3 mb-3"
                                                    href="{{ route('news.show', $news) }}" role="button">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a class="btn btn-warning  mb-3"
                                                    href="{{ route('news.edit', $news) }}" role="button">
                                                    <i class="fa fa-pencil-square-o"></i>
                                                </a>

                                                <form action="{{ route('news.destroy', $news) }}" method="post">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit" onclick="return confirm('co chac chan muon xoa')"
                                                        class="btn btn-danger mb-3">
                                                        <i class="fa fa-trash-o"></i>
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
