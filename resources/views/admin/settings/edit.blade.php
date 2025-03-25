@extends('admin.layouts.master')
@section('title')
    Quản lý cài đặt
@endsection
@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">Quản lý cài đặt</h2>

        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tên Đường Dẫn</th>
                        <th>Đường Liên Kết</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $key => $value)
                        <tr>
                            <td>{{ $key }}</td>
                            <td>{{ $value->key }}</td>
                            <td>
                                <input type="text" name="settings[{{ $value->key }}]" class="form-control"
                                    value="{{ $value->value }}">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <button type="submit" class="btn btn-primary">Lưu Thay Đổi</button>
        </form>
    </div>
@endsection
