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
                                        <th scope="col">Category_name</th>
                                        <th scope="col">image</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">act</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($category as $item)
                                        <tr>
                                            <th scope="row">{{$item->id}}</th>
                                            <td>{{$item->category_name}}</td>
                                            <td>{{$item->image}}</td>
                                            <td>{{$item->status}}</td>
                                            <td>{{$item->id}}</td>
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