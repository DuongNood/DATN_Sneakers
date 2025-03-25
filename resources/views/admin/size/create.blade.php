@extends('admin.layouts.master')
@section('content')
    <div class="container-xxl">

        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">{{ $title }}</h4>
            </div>
        </div>

        <!-- start row -->
        <div class="row"> <!-- Basic Example -->
            <div class="col-12">
                <div class="card">                 
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <form action="{{ route('size.store') }}"  method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="simpleinput" class="form-label">Size</label>
                                        <input type="text" id="simpleinput" class="form-control" name="name">
                                    </div>                                                                    
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </form>
                            </div>
                        </div>


                    </div>
                </div><!-- end row -->
            </div>
@endsection
@section('js')
           
@endsection