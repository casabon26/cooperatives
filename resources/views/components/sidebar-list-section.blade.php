@props([
    'title' => 'List Section',
    'items' => [],
    'years' => [],
    'yearCounts' => [],
    'selectedCount' => 0,
    'totalCount' => 0,
    'iconColor' => '#B82132',
    'badgeGradient' => 'linear-gradient(135deg,#fee2e2,#fdd2d2)',
    'badgeColor' => '#991b1b',
    'actionType' => 'link', // 'link' or 'modal'
    'actionRoute' => null,
    'viewAllRoute' => null,
    'viewAllText' => 'View All Items',
    'noItemsText' => 'No items available.',
])

<style>
    .memo-item { transition: background .12s ease; }
    .memo-item:hover { background: rgba(var(--primary-r), 0.08); }
    .memo-link { display:inline-block; border-radius:6px; padding:0 .25rem; }
    .memo-item .memo-icon { background:#fff; border-radius:8px; padding:.45rem; }
    .memo-badge { border-radius:8px !important; }
</style>

<div class="card mb-3" style="background: linear-gradient(180deg,#fff5f6,#fff0f2); border:1px solid rgba(var(--primary-r), 0.07); border-radius:12px; box-shadow:0 12px 36px rgba(var(--primary-r), 0.06); overflow:hidden;">
    <div class="card-body" style="padding:1rem;">
        <h5 class="card-title mb-3">{{ $title }}</h5>

        @if(count($years) > 0)
            <div class="mb-2">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label class="small mb-0">Filter by year</label>
                    @php
                        $shownCount = null;
                        if (isset($items)) {
                            if ($items instanceof \Illuminate\Contracts\Pagination\Paginator || $items instanceof \Illuminate\Pagination\LengthAwarePaginator) {
                                $shownCount = $items->count();
                            } elseif (is_array($items) || $items instanceof \Illuminate\Support\Collection) {
                                $shownCount = count($items);
                            }
                        }
                    @endphp
                    <div class="small text-muted">Showing: <strong>{{ $shownCount ?? $selectedCount ?? $totalCount }}</strong></div>
                </div>

                <div class="memo-filter">
                    <div class="dropdown">
                        @php
                            $currentYear = request('item_year');
                        @endphp
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="itemYearDropdown_{{ $loop->index ?? 'default' }}" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ $currentYear ? $currentYear : 'All years' }}
                            <span class="badge bg-secondary ms-2">{{ $currentYear ? ($yearCounts[$currentYear] ?? 0) : ($totalCount ?? 0) }}</span>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="itemYearDropdown_{{ $loop->index ?? 'default' }}">
                            <li><a class="dropdown-item d-flex justify-content-between align-items-center {{ $currentYear ? '' : 'active' }}" href="{{ url('/') }}">All years <span class="badge bg-secondary ms-2">{{ $totalCount ?? 0 }}</span></a></li>
                            @foreach($years as $y)
                                <li>
                                    <a class="dropdown-item d-flex justify-content-between align-items-center {{ $currentYear == $y ? 'active' : '' }}" href="{{ url('/?item_year='.$y) }}">
                                        {{ $y }}
                                        <span class="badge bg-secondary ms-2">{{ $yearCounts[$y] ?? 0 }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        @if(count($items) > 0)
            <ul class="list-unstyled small mb-0 memo-list">
                @foreach($items as $item)
                    <li class="memo-item" style="border-radius:10px; overflow:hidden;">
                        <div class="memo-icon flex-shrink-0" aria-hidden="true" style="color: {{ $iconColor }};">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7 7h6v6H7z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/><path d="M21 15V7a2 2 0 0 0-2-2H9" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>

                        <div class="flex-grow-1">
                                @if($actionType === 'modal')
                                <a href="#" class="memo-link item-modal-link" data-id="{{ $item->id }}" data-title="{{ $item->title ?? '' }}" data-content="{{ $item->content ?? '' }}" data-file="{{ $item->file_path ?? '' }}" data-published="{{ optional($item->published_at ?? $item->created_at)->format('Y') }}" title="click to open" aria-label="{{ $item->title ?? 'Item' }}">
                                    {{ $item->title ?? 'Item' }}
                                </a>
                            @else
                                <a href="{{ url(str_replace('{id}', $item->id, $actionRoute)) }}" class="memo-link" target="_self" title="click to open" aria-label="{{ $item->title ?? 'Item' }}">
                                    {{ $item->title ?? 'Item' }}
                                </a>
                            @endif
                            
                            @if(isset($item->published_at) || isset($item->created_at))
                                <div class="memo-meta">Published: {{ optional($item->published_at ?? $item->created_at)->format('Y') }}</div>
                            @endif
                        </div>
                        
                        <div class="flex-shrink-0 text-end" style="min-width:56px;">
                            <div class="memo-badge" style="background: {{ $badgeGradient }}; color: {{ $badgeColor }}; padding:.18rem .45rem; border-radius:10px; font-weight:700; font-size:.75rem;">
                                {{ optional($item->published_at ?? $item->created_at)->format('Y') ?? '' }}
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="small text-muted">{{ $noItemsText }}</div>
        @endif

        @if($viewAllRoute)
            <div class="mt-3 text-center">
                <a href="{{ $viewAllRoute }}" class="btn btn-primary d-inline-flex align-items-center" style="background:{{ $iconColor }}; border-color:{{ $iconColor }}; font-weight:700;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="margin-right:8px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ $viewAllText }}
                </a>
            </div>
        @endif
    </div>
</div>
