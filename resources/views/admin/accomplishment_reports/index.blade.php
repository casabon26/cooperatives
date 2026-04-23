@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Accomplishment Reports</h4>
            <a href="{{ route('admin.accomplishment-reports.create') }}" class="btn btn-primary" target="_self">Create Report</a>
        </div>

        {{-- success flash is shown via the global dynamic popup in the layout; remove inline duplicate --}}

        @if($reports->count())
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
                            @foreach($reports as $r)
                                <tr>
                                    <td>{{ $r->code }}</td>
                                    <td>{{ $r->title ?? $r->code }}</td>
                                    <td>{{ optional($r->published_at)->toDayDateTimeString() }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.accomplishment-reports.edit', $r) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <form action="{{ route('admin.accomplishment-reports.destroy', $r) }}" method="post" style="display:inline-block" data-confirm="Delete this report?">
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

            <div class="mt-3">{{ $reports->links() }}</div>
        @else
            <div class="alert alert-info">No accomplishment reports yet.</div>
        @endif
    </div>
</div>
@endsection
