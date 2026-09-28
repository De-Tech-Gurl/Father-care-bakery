@php
    $isEdit = $product !== null;
@endphp

<form method="POST" action="{{ $isEdit ? route('admin.products.update', $product) : route('admin.products.store') }}" enctype="multipart/form-data">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Name</label>
            <input type="text" name="name" value="{{ old('name', $product?->name) }}" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Category</label>
            <select name="category_id" class="form-select">
                <option value="">Uncategorized</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) old('category_id', $product?->category_id) === (string) $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12">
            <label class="form-label">Description</label>
            <textarea name="description" rows="4" class="form-control">{{ old('description', $product?->description) }}</textarea>
        </div>
        <div class="col-md-4">
            <label class="form-label">Price (₦)</label>
            <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $product?->price) }}" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Compare at price (₦)</label>
            <input type="number" step="0.01" min="0" name="compare_at_price" value="{{ old('compare_at_price', $product?->compare_at_price) }}" class="form-control">
            <div class="form-text">Optional. Shown as the crossed-out price on combo deals.</div>
        </div>
        <div class="col-md-4">
            <label class="form-label">Cost price (₦)</label>
            <input type="number" step="0.01" min="0" name="cost_price" value="{{ old('cost_price', $product?->cost_price) }}" class="form-control">
        </div>
        <div class="col-md-2">
            <label class="form-label">Stock</label>
            <input type="number" min="0" name="stock_quantity" value="{{ old('stock_quantity', $product?->stock_quantity ?? 0) }}" class="form-control" required>
        </div>
        <div class="col-md-2">
            <label class="form-label">Low stock at</label>
            <input type="number" min="0" name="low_stock_threshold" value="{{ old('low_stock_threshold', $product?->low_stock_threshold ?? 5) }}" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Image</label>
            @if($isEdit && $product?->imageUrl)
                <div class="mb-2">
                    <img src="{{ $product->imageUrl }}" alt="{{ $product->name }}"
                         style="width:96px;height:96px;object-fit:cover;border-radius:10px;border:1px solid var(--card-border);">
                </div>
            @endif
            <input type="file" name="image" class="form-control" accept="image/*">
            <div class="form-text">JPG, PNG, WEBP or GIF up to 10MB. Leave empty to keep the current photo.</div>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $product?->is_active ?? true))>
                <label class="form-check-label" for="is_active">Active</label>
            </div>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured" @checked(old('is_featured', $product?->is_featured ?? false))>
                <label class="form-check-label" for="is_featured">Featured</label>
            </div>
        </div>
    </div>

    <div class="mt-4 d-flex gap-2">
        <button class="btn btn-success" type="submit">{{ $isEdit ? 'Update product' : 'Save product' }}</button>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
