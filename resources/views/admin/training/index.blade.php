@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Manage Training Videos</h3>
        <a href="/admin/manage-training/create" class="btn btn-primary">Add Training Video</a>
    </div>

    <table class="table">
        <thead><tr><th>Title</th><th>Created</th><th></th></tr></thead>
        <tbody>
            @foreach($videos as $v)
                <tr>
                    <td>{{ $v->title }}</td>
                    <td>{{ $v->created_at->format('Y-m-d') }}</td>
                    <td>
                        <a href="/admin/manage-training/{{ $v->id }}/edit" class="btn btn-sm btn-secondary">Edit</a>
                        <form method="POST" action="/admin/manage-training/{{ $v->id }}/delete" style="display:inline">@csrf
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $videos->links() }}
</div>
@endsection
