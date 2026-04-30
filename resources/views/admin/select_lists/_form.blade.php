<div class="mb-3">
    <label class="form-label">Group (identifier)</label>
    <input type="text" name="group" class="form-control @error('group') is-invalid @enderror" value="{{ old('group', $item->group ?? $group ?? '') }}" placeholder="e.g. programs, services, cabstop">
    @error('group') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
</div>

@php $isCabstop = strtolower($group ?? ($item->group ?? '')) === 'cabstop'; @endphp

<div class="mb-3">
    <label class="form-label">Label</label>
    <input type="text" name="label" class="form-control @error('label') is-invalid @enderror" value="{{ old('label', $item->label ?? '') }}">
    @error('label') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
</div>

<!-- Note: 'key' and 'sort_order' attributes removed; form now only captures group, label and active -->

<div class="mb-3 form-check form-switch">
    <input type="checkbox" name="active" value="1" class="form-check-input" id="activeSwitch" {{ old('active', $item->active ?? true) ? 'checked' : '' }}>
    <label class="form-check-label" for="activeSwitch">Active</label>
</div>
