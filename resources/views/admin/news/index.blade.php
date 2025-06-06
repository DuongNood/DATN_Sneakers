@extends('admin.layouts.master')

@section('content')
    <div class="container-xxl">
        <div class="py-3 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold">Danh sách Tin Tức</h4>
            <a href="{{ route('admin.news.create') }}" class="btn btn-success">
                <i class="mdi mdi-plus-circle-outline"></i> Thêm Tin Tức
            </a>
        </div>

        {{-- Tìm kiếm và lọc --}}
        <form method="GET" action="{{ request()->url() }}" class="row g-2 align-items-center mb-3">
            {{-- Ô tìm kiếm --}}
            <div class="col-lg-4 col-md-6">
                <input type="text" name="search" class="form-control shadow-sm" placeholder="Nhập từ khóa..."
                    value="{{ request('search') }}">
            </div>

            {{-- Nút lọc và reset --}}
            <div class="col-lg-8 col-md-6 text-end">
                <button type="submit" class="btn btn-success shadow-sm"><i class="bi bi-funnel"></i> Lọc</button>
                <a href="{{ request()->url() }}" class="btn btn-secondary shadow-sm"><i class="bi bi-arrow-clockwise"></i>
                    Reset</a>
            </div>
        </form>

        <!-- Danh sách tin tức -->
        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover table-sm">
                        <thead class="table-dark">
                            <tr class="text-center">
                                <th>ID</th>
                                <th>Tiêu đề</th>
                                <th>Hình ảnh</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $news)
                                <tr class="align-middle text-center">
                                    <td>{{ $news->id }}</td>
                                    <td class="text-start">{{ $news->title }}</td>
                                    <td>
                                        @if ($news->image)
                                            <img src="{{ $news->image }}" alt="Ảnh tin tức" width="100"
                                                class="rounded shadow-sm border">
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.news.edit', $news) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="mdi mdi-pencil"></i> Sửa
                                        </a>

                                        <form action="{{ route('admin.news.destroy', $news) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Bạn có chắc chắn muốn xóa?')">
                                                <i class="mdi mdi-delete"></i> Xóa
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $data->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
