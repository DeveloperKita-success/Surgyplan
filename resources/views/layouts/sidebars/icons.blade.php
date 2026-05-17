@switch($name)
    @case('user')
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M20 21a8 8 0 0 0-16 0M12 13a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        @break
    @case('clipboard')
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M9 5h6M9 3h6a1 1 0 0 1 1 1v2H8V4a1 1 0 0 1 1-1Zm-3 4h12v14H6V7Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>
        @break
    @case('calendar')
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M8 2v4M16 2v4M4 10h16M5 5h14a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        @break
    @case('check')
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="m5 12 4 4L19 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        @break
    @case('room')
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M4 21V5a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v16M9 21v-4h3v4M17 10h3a1 1 0 0 1 1 1v10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        @break
    @case('doctor')
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M12 13a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm7 8a7 7 0 0 0-14 0M19 8h3M20.5 6.5v3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        @break
    @case('report')
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M7 3h8l4 4v14H7V3Zm8 0v5h5M10 13h6M10 17h6M10 9h1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        @break
    @case('book')
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M5 4a2 2 0 0 1 2-2h12v18H7a2 2 0 0 0-2 2V4Zm0 0v16a2 2 0 0 1 2-2h12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        @break
@endswitch
