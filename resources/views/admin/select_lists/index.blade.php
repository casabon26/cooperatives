@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-0">{{ !empty($group) ? ucfirst($group) : 'Manage Options' }}</h4>
            </div>
            @php $createLabel = (!empty($group) && strtolower($group) === 'cabstop') ? 'Add CabStop' : (!empty($group) ? ('Add '.ucfirst($group)) : 'Add Item'); @endphp
            <a href="{{ route('admin.select_lists.create', ['group' => $group]) }}" class="btn btn-primary">{{ $createLabel }}</a>
        </div>

        @if(!empty($missingTable))
            <div class="alert alert-warning">
                <strong>Database table missing:</strong> The dropdown manager requires the <code>select_list_items</code> table, which doesn't exist yet.
                Run the migrations to create it:
                <pre class="mt-2"><code>php artisan migrate</code></pre>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Tabs to switch groups without changing URL --}}
        <div class="mb-3">
            <ul class="nav nav-tabs" id="selectListTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ (empty($group) || $group==='programs') ? 'active' : '' }}" data-group="programs" type="button">Programs</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ ($group==='services') ? 'active' : '' }}" data-group="services" type="button">Services</button>
                </li>
                
            </ul>
        </div>

        <div id="selectListsTableContainer">
            @include('admin.select_lists._table', ['items' => $items, 'group' => $group])
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    const tabs = document.querySelectorAll('#selectListTabs [data-group]');
    const container = document.getElementById('selectListsTableContainer');
    tabs.forEach(function(btn){
        btn.addEventListener('click', function(){
            const group = btn.dataset.group;
            // update active state
            tabs.forEach(t=>t.classList.remove('active'));
            btn.classList.add('active');
            // fetch table fragment
            const url = new URL("{{ route('admin.select_lists.index') }}", window.location.origin);
            url.searchParams.set('group', group);
            fetch(url.toString(), {headers:{'X-Requested-With':'XMLHttpRequest'}})
                .then(r=>{ if(!r.ok) throw r; return r.text(); })
                .then(html=>{ container.innerHTML = html; })
                .catch(()=>{ alert('Failed to load items'); });
        });
    });
});
</script>
@endsection
