<div class="mb-3">
    <label class="form-label">Group (identifier)</label>
    <input type="text" name="group" class="form-control @error('group') is-invalid @enderror" value="{{ old('group', $item->group ?? $group ?? '') }}" placeholder="e.g. programs, services, cabstop">
    @error('group') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">Key (optional)</label>
    <input type="text" name="key" class="form-control @error('key') is-invalid @enderror" value="{{ old('key', $item->key ?? '') }}" placeholder="machine-friendly value (optional)">
    @error('key') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">Label</label>
    <input type="text" name="label" class="form-control @error('label') is-invalid @enderror" value="{{ old('label', $item->label ?? '') }}">
    @error('label') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">Sort Order</label>
    <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', $item->sort_order ?? 0) }}">
    @error('sort_order') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
</div>

<div class="mb-3 form-check form-switch">
    <input type="checkbox" name="active" value="1" class="form-check-input" id="activeSwitch" {{ old('active', $item->active ?? true) ? 'checked' : '' }}>
    <label class="form-check-label" for="activeSwitch">Active</label>
</div>
