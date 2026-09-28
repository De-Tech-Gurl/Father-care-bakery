@extends('layouts.admin')

@section('title', 'Add Category - Father Care Bakery')

@section('content')
    <h1 class="h3 fw-bold mb-4">Add category</h1>
    <div class="stat-card">
        @include('admin.categories._form', ['category' => null])
    </div>
@endsection
