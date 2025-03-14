@extends('admin.layouts.master')
@section('title')
    Cập nhật người dùng {{ $user->name }}
@endsection
@section('content')
    <div class="container-xxl">

        @if (session()->has('success') && !session()->get('success'))
            <div class="alert alert-danger">
                {{ session()->get('error') }}
            </div>
        @endif

        @if (session()->has('success') && session()->get('success'))
            <div class="alert alert-info">
                Thao tác thành công
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row mt-3">
            <div class="col-12">
                <div class="card">

                    <div class="card-header">
                        <h5 class="card-title mb-0">Cập nhật người dùng {{ $user->name }}</h5>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <form action="{{ route('users.update') }}" enctype="multipart/form-data" method="POST">
                                    @csrf

                                    @method('PUT')

                                    <div class="mb-3 row">
                                        <label for="name" class="col-4 col-form-label">Name</label>
                                        <div class="col-8">
                                            <input type="text" class="form-control" name="name" id="name"
                                                value="{{ $user->name }}" />
                                        </div>
                                    </div>

                                    <div class="mb-3 row">
                                        <label for="email" class="col-4 col-form-label">Email</label>
                                        <div class="col-8">
                                            <input type="email" class="form-control" name="email" id="email"
                                                value="{{ $user->email }}" />
                                        </div>
                                    </div>

                                    <div class="mb-3 row">
                                        <label for="phone" class="col-4 col-form-label">Phone</label>
                                        <div class="col-8">
                                            <input type="tel" class="form-control" name="phone" id="phone"
                                                value="{{ $user->phone }}" />
                                        </div>
                                    </div>

                                    <div class="mb-3 row">
                                        <label for="address" class="col-4 col-form-label">Address</label>
                                        <div class="col-8">
                                            <input type="text" class="form-control" name="address" id="address"
                                                value="{{ $user->address }}" />
                                        </div>
                                    </div>

                                    <div class="mb-3 row">
                                        <label for="role_id" class="col-4 col-form-label">Role</label>
                                        <div class="col-8">
                                            <select class="form-select" name="role_id" id="role_id">
                                                <option value="">Select Role</option>
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->id }}"
                                                        {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                                        {{ $role->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <a class="btn btn-secondary" href="{{ route('users.index') }}">Back</a>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
