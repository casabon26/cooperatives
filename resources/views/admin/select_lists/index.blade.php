@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Manage Dropdown Lists @if(!empty($group)) — {{ $group }} @endif</h4>
            @if(!empty($group) && strtolower($group) === 'cabstop')
                <a href="{{ route('admin.select_lists.create', ['group' => $group]) }}" class="btn btn-primary">Add CabStop</a>
            @else
                <a href="{{ route('admin.select_lists.create', ['group' => $group]) }}" class="btn btn-primary">Add Item</a>
            @endif
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

        <div class="card">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Group</th>
                            <th>Label</th>
                            <th>Active</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $it)
                            <tr>
                                <td>{{ $it->group }}</td>
                                <td>{{ $it->label }}</td>
                                <td>{{ $it->active ? 'Yes' : 'No' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.select_lists.edit', $it) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form action="{{ route('admin.select_lists.destroy', $it) }}" method="post" style="display:inline" data-confirm="Delete list item?">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">
            {{ $items->links() }}
        </div>
    </div>
</div>
@endsection
