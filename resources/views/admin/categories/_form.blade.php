@php $isEdit = $category !== null; @endphp

<form method="POST" action="{{ $isEdit ? route('admin.categories.update', $category) : route('admin.categories.store') }}">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="mb-3">
        <label class="form-label">Name</label>
        <input type="text" name="name" value="{{ old('name', $category?->name) }}" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="3">{{ old('description', $category?->description) }}</textarea>
    </div>
    <div class="form-check mb-4">
        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="cat_active" @checked(old('is_active', $category?->is_active ?? true))>
        <label class="form-check-label" for="cat_active">Active</label>
    </div>
    <button class="btn btn-success" type="submit">{{ $isEdit ? 'Update category' : 'Save category' }}</button>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">Cancel</a>
</form>
