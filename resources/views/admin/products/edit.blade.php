@extends('layouts.admin')

@section('title', 'Edit Product - Father Care Bakery')

@section('content')
    <h1 class="h3 fw-bold mb-4">Edit product</h1>
    <div class="stat-card">
        @include('admin.products._form', ['product' => $product])
    </div>
@endsection
