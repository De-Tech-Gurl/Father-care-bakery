@extends('layouts.admin')

@section('title', 'Add Product - Father Care Bakery')

@section('content')
    <h1 class="h3 fw-bold mb-4">Add product</h1>
    <div class="stat-card">
        @include('admin.products._form', ['product' => null])
    </div>
@endsection
