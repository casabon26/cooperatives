@php
    // Accepts: $url, $label, $class
    $label = $label ?? 'Back';
    $extraClass = isset($class) ? $class : '';
    $routeName = optional(request()->route())->getName();

    // If an explicit url is provided, use it.
    if (isset($url)) {
        $backUrl = $url;
    }
    // For admin pages, always point to the resource index (and do not render on the index itself)
    elseif ($routeName && \Illuminate\Support\Str::startsWith($routeName, 'admin.')) {
        if (\Illuminate\Support\Str::endsWith($routeName, '.index')) {
            // Keep back button visible on admin index pages; point it to admin panel to avoid toggle loops
            $backUrl = url('/admin/panel');
        } else {
            $parts = explode('.', $routeName);
            $resource = $parts[1] ?? null;
            $indexRoute = 'admin.' . $resource . '.index';
            if ($resource && \Illuminate\Support\Facades\Route::has($indexRoute)) {
                $backUrl = route($indexRoute);
            } else {
                $backUrl = url('/admin/panel');
            }
        }
    }
    // Otherwise, prefer previous URL but fall back to sensible parent routes
    else {
        $backUrl = $backUrl ?? url()->previous();
        $currentUrl = url()->current();
        // If previous is empty or equals current (can cause toggle loops), pick a sensible parent
        if (empty($backUrl) || $backUrl === $currentUrl) {
            // Public resource show pages -> map to their listing pages
            if ($routeName && \Illuminate\Support\Str::endsWith($routeName, '.show')) {
                if (\Illuminate\Support\Facades\Route::has('livelihood')) {
                    $backUrl = route('livelihood');
                } elseif (\Illuminate\Support\Facades\Route::has('cooperatives.directory')) {
                    $backUrl = route('cooperatives.directory');
                } else {
                    $backUrl = url('/');
                }
            } else {
                $backUrl = url('/');
            }
        }
    }
@endphp

@if(!empty($backUrl))
<a href="{{ $backUrl }}" class="btn btn-back {{ $extraClass }}" role="button" aria-label="{{ $label }}" title="{{ $label }}">
    <span class="btn-icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" style="display:block;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
    </span>
    <span class="btn-label">{{ $label }}</span>
</a>
@endif
