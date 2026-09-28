@extends('layouts.admin')

@section('title', 'Edit Category - Father Care Bakery')

@section('content')
    <h1 class="h3 fw-bold mb-4">Edit category</h1>
    <div class="stat-card">
        @include('admin.categories._form', ['category' => $category])
    </div>
@endsection
