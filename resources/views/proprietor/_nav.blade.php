<ul class="nav nav-pills mb-4 flex-wrap gap-1">
    @foreach ([
        'proprietor.dashboard' => ['Home', 'fa-home'],
        'proprietor.overview' => ['School Overview', 'fa-school'],
        'proprietor.finance' => ['Financial Reports', 'fa-chart-line'],
        'proprietor.academics' => ['Academic Reports', 'fa-graduation-cap'],
        'proprietor.enrollment' => ['Enrollment Stats', 'fa-users'],
    ] as $route => [$label, $icon])
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs($route) ? 'active' : '' }}" href="{{ route($route) }}">
                <i class="fas {{ $icon }} me-1"></i>{{ $label }}
            </a>
        </li>
    @endforeach
</ul>
