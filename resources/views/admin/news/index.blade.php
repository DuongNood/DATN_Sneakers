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
                                            <th scope="row">{{$news->id}}</th>
                                            <td>{{$news->title}}</td>
                                            <td>{{$news->image}}</td>
                                            {{-- <td>{{$news->content}}</td> --}}
                                            <td>
                                                {{-- button --}}
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