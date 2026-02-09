@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Memorandums</h4>
            <a href="{{ route('admin.memorandums.create') }}" class="btn btn-primary">Create Memorandum</a>
        </div>

        {{-- success flash is shown via the global dynamic popup in the layout; remove inline duplicate --}}

        @if($memorandums->count())
            <div class="card">
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Title</th>
                                <th>Published</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($memorandums as $m)
                                <tr>
                                    <td>{{ $m->code }}</td>
                                        <td>
                                        <a href="{{ url('/memorandums/'.$m->id) }}" onclick="window.location.href=this.href; return false;">{{ $m->title ?? $m->code }}</a>
                                    </td>
                                    <td>{{ optional($m->published_at)->toDayDateTimeString() }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.memorandums.edit', $m) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <form action="{{ route('admin.memorandums.destroy', $m) }}" method="post" style="display:inline-block" onsubmit="return confirm('Delete this memorandum?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-3">{{ $memorandums->links() }}</div>
        @else
            <div class="alert alert-info">No memorandums yet.</div>
        @endif
    </div>
</div>
@endsection
