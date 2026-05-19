<div class="card">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    @if(empty($group))<th>Group</th>@endif
                    <th>Label</th>
                    <th>Active</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $it)
                    <tr>
                        @if(empty($group))<td>{{ $it->group }}</td>@endif
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

<div class="mt-3">{{ $items->links() }}</div>
