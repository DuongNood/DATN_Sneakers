@extends('admin.layouts.master')
@section('content')
    <div class="container-xxl">

        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">

                <h4 class="fs-18 fw-semibold m-0">{{$title}}</h4>
            </div>
        </div>

        <!-- start row -->
        <div class="row">                     <!-- Basic Example -->
            <div class="col-xl-12">
                <div class="card">                   
                    <div class="card-body">
                        <div class="table-responsive">

                <h4 class="fs-18 fw-semibold m-0">{{$title}}</h4>              
            </div>
            <a href="{{route('categories.create')}}" class="btn btn-success col-1">Create</a>
        </div>

        <!-- start row -->
        <div class="row">   

            <div class="col-xl-12 ">
                <div class="card">                   
                    <div class="card-body">
                        <div class="table-responsive">
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-babel="Close">
                                    </button>
                                </div>
                            @endif

                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Category_name</th>
                                        <th scope="col">image</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Act</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($category as $item)
                                        <tr>
                                            <th scope="row">{{$item->id}}</th>
                                            <td>{{$item->category_name}}</td>
                                            <td><img src="{{Storage::url($item->image)}}" alt="" width="150px"></td>                                           
                                            <td class="{{ $item->status == 0 ? 'text-danger' : 'text-success' }}">
                                                {{ $item->status == 0 ? 'Inactive' : 'Activate' }}
                                            </td>
                                            <td>
                                                <a href="{{ route('categories.edit', $item->id) }}"><i
                                                    class="mdi mdi-pencil text-muted fs-18 rounded-2 border p-1 me-1"></i></a>
                                                    <form action="{{ route('categories.destroy', $item->id) }}" method="POST" class="d-inline me-2">
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