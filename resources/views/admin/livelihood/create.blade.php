@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h5>Create SLPA Entry</h5>
    <form method="post" action="{{ route('admin.livelihood.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input name="name" class="form-control" value="{{ old('name') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control">{{ old('description') }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Number of Members</label>
            <input name="members_count" type="number" min="0" class="form-control" value="{{ old('members_count') }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Address</label>
            <textarea name="address" class="form-control">{{ old('address') }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Products</label>
            @php
                $initialProducts = old('products', []);
                if (is_string($initialProducts) && trim($initialProducts) !== '') {
                    $tmp = preg_split('/\r?\n|,/', $initialProducts);
                    $initialProducts = array_map('trim', $tmp);
                }
                if (!is_array($initialProducts)) $initialProducts = [];
                // normalize array items to objects with name + description
                $initialProducts = array_map(function($it){
                    if (is_array($it) || is_object($it)) {
                        return [
                            'name' => trim((string)(data_get($it,'name') ?? (isset($it[0]) ? $it[0] : ''))),
                            'description' => trim((string)(data_get($it,'description') ?? '')),
                        ];
                    }
                    return ['name' => trim((string)$it), 'description' => ''];
                }, $initialProducts);
            @endphp
            <div id="products-area">
                @if(count($initialProducts))
                    @foreach($initialProducts as $i => $p)
                        <div class="product-row row g-2 align-items-center mb-2">
                            <div class="col-md-5">
                                <input type="text" name="products[{{ $i }}][name]" class="form-control" placeholder="Product name" value="{{ $p['name'] }}">
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="products[{{ $i }}][description]" class="form-control" placeholder="Product details (optional)" value="{{ $p['description'] }}">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-danger btn-sm remove-product">&times;</button>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="product-row row g-2 align-items-center mb-2">
                        <div class="col-md-5">
                            <input type="text" name="products[0][name]" class="form-control" placeholder="Product name">
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="products[0][description]" class="form-control" placeholder="Product details (optional)">
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-danger btn-sm remove-product">&times;</button>
                        </div>
                    </div>
                @endif
            </div>
            <div>
                <button type="button" id="add-product" class="btn btn-outline-secondary btn-sm">Add product</button>
                <div class="form-text">You can add product name and optional details. Public view shows 10 per page.</div>
            </div>
            <script>
                (function(){
                    const container = document.getElementById('products-area');
                    document.getElementById('add-product').addEventListener('click', function(){
                        const idx = container.querySelectorAll('.product-row').length;
                        const row = document.createElement('div');
                        row.className = 'product-row row g-2 align-items-center mb-2';
                        row.innerHTML = `
                            <div class="col-md-5">
                                <input type="text" name="products[${idx}][name]" class="form-control" placeholder="Product name">
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="products[${idx}][description]" class="form-control" placeholder="Product details (optional)">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-danger btn-sm remove-product">&times;</button>
                            </div>
                        `;
                        container.appendChild(row);
                    });
                    document.addEventListener('click', function(e){
                        if (e.target && e.target.classList.contains('remove-product')) {
                            const row = e.target.closest('.product-row');
                            if (row) row.remove();
                        }
                    });
                })();
            </script>
        </div>
        <!-- Products description removed -->
        <div class="mb-3">
            <label class="form-label">Business (optional)</label>
            @php
                $initialBusinesses = old('business', []);
                if (is_string($initialBusinesses) && trim($initialBusinesses) !== '') {
                    $tmp = preg_split('/\r?\n|,/', $initialBusinesses);
                    $initialBusinesses = array_map('trim', $tmp);
                }
                if (!is_array($initialBusinesses)) $initialBusinesses = [];
            @endphp
            <div id="business-area">
                @if(count($initialBusinesses))
                    @foreach($initialBusinesses as $i => $b)
                        <div class="business-row row g-2 align-items-center mb-2">
                            <div class="col-md-11">
                                <input type="text" name="business[]" class="form-control" placeholder="Business type (e.g. Retail, Handicraft)" value="{{ $b }}">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-danger btn-sm remove-business">&times;</button>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="business-row row g-2 align-items-center mb-2">
                        <div class="col-md-11">
                            <input type="text" name="business[]" class="form-control" placeholder="Business type (e.g. Retail, Handicraft)">
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-danger btn-sm remove-business">&times;</button>
                        </div>
                    </div>
                @endif
            </div>
            <div>
                <button type="button" id="add-business" class="btn btn-outline-secondary btn-sm">Add business</button>
                <div class="form-text">Add one or more business types for this SLPA.</div>
            </div>
            <script>
                (function(){
                    const container = document.getElementById('business-area');
                    document.getElementById('add-business').addEventListener('click', function(){
                        const row = document.createElement('div');
                        row.className = 'business-row row g-2 align-items-center mb-2';
                        row.innerHTML = `
                            <div class="col-md-11">
                                <input type="text" name="business[]" class="form-control" placeholder="Business type (e.g. Retail, Handicraft)">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-danger btn-sm remove-business">&times;</button>
                            </div>
                        `;
                        container.appendChild(row);
                    });
                    document.addEventListener('click', function(e){
                        if (e.target && e.target.classList.contains('remove-business')) {
                            const row = e.target.closest('.business-row');
                            if (row) row.remove();
                        }
                    });
                })();
            </script>
        </div>
        <div class="mb-3">
            <label class="form-label">Image (optional)</label>
            <input type="file" name="image" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Gallery images (optional)</label>
            <input type="file" name="gallery[]" class="form-control" multiple>
            <div class="form-text">Upload one or more gallery images (optional).</div>
        </div>
        <button class="btn btn-primary">Save</button>
        @include('partials.back-button', ['url' => route('admin.livelihood.index'), 'label' => 'Cancel', 'class' => 'btn-secondary ms-2'])
    </form>
</div>

@endsection
