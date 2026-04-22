@extends('layouts.dashboard')

@section('styles')
    <style>
        /* KPI Cards */
        .kpi-card {
            background: var(--color-white);
            border-radius: var(--radius-md);
            padding: 1.25rem;
            box-shadow: var(--shadow-card);
            transition: all 0.3s ease;
            height: 100%;
        }

        .kpi-card:hover {
            box-shadow: var(--shadow-card-hover);
        }

        .kpi-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.75rem;
        }

        .kpi-card-header .title {
            font-size: 0.85rem;
            color: var(--color-text-muted);
            font-weight: 500;
        }

        .kpi-card-header .icon {
            width: 32px;
            height: 32px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
        }

        .kpi-card-header .icon.blue {
            background: rgba(0, 147, 208, 0.1);
            color: var(--color-accent-blue);
        }

        .kpi-card-header .icon.green {
            background: rgba(16, 185, 129, 0.1);
            color: var(--color-accent-green);
        }

        .kpi-card-header .icon.purple {
            background: rgba(233, 0, 123, 0.1);
            color: var(--color-accent-magenta);
        }

        .kpi-card-header .icon.orange {
            background: rgba(247, 148, 29, 0.1);
            color: var(--color-accent-yellow);
        }

        .kpi-value {
            font-family: var(--font-heading);
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--color-text-heading);
            margin-bottom: 0.25rem;
        }

        .kpi-change {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .kpi-change.positive {
            color: var(--color-accent-green);
        }

        .kpi-change.negative {
            color: var(--color-accent-red);
        }

        .kpi-period {
            font-size: 0.75rem;
            color: var(--color-text-muted);
            margin-left: 4px;
            font-weight: 400;
        }

        /* Dashboard Header */
        .dashboard-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .dashboard-header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
        }

        .dashboard-header-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .date-range-selector {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: var(--color-white);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-sm);
            font-size: 0.85rem;
            color: var(--color-text-body);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-header {
            padding: 0.5rem 1rem;
            border-radius: var(--radius-sm);
            font-size: 0.85rem;
            font-weight: 600;
            font-family: var(--font-heading);
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 6px;
            border: 1px solid var(--color-border);
            background: var(--color-white);
            color: var(--color-text-body);
        }

        .btn-header-primary {
            background: var(--color-primary);
            color: var(--color-white);
            border-color: var(--color-primary);
        }

        /* Chart Cards */
        .chart-card {
            background: var(--color-white);
            border-radius: var(--radius-md);
            padding: 1.5rem;
            box-shadow: var(--shadow-card);
            height: 100%;
        }

        .chart-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
        }

        .chart-card-header h3 {
            font-size: 1rem;
            font-weight: 600;
            margin: 0;
        }

        .chart-card-header .more-btn {
            background: none;
            border: none;
            color: var(--color-text-muted);
            cursor: pointer;
            padding: 4px;
            font-size: 1rem;
        }

        .profit-value {
            font-family: var(--font-heading);
            font-size: 2rem;
            font-weight: 700;
            color: var(--color-text-heading);
            margin-bottom: 0.5rem;
        }

        .profit-change {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: var(--radius-pill);
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .profit-change.positive {
            background: rgba(16, 185, 129, 0.1);
            color: var(--color-accent-green);
        }

        .chart-container {
            position: relative;
            height: 200px;
            width: 100%;
        }

        .customer-segments {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .segment-card {
            flex: 1;
            padding: 0.75rem;
            border-radius: var(--radius-sm);
            background: var(--color-bg-main);
        }

        .segment-card.blue { border-left: 3px solid var(--color-accent-blue); }
        .segment-card.green { border-left: 3px solid var(--color-accent-green); }
        .segment-card.orange { border-left: 3px solid var(--color-accent-yellow); }

        .segment-value { font-family: var(--font-heading); font-size: 1.1rem; font-weight: 700; color: var(--color-text-heading); }
        .segment-label { font-size: 0.75rem; color: var(--color-text-muted); }

        /* Line Chart SVG Styles */
        .line-chart-path { fill: none; stroke: var(--color-primary); stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round; }
        .line-chart-area { fill: url(#gradientBlue); }
        .line-chart-dot { fill: var(--color-white); stroke: var(--color-primary); stroke-width: 2; }
        .line-chart-dot.active { fill: var(--color-primary); r: 5; }
        .tooltip-box { fill: var(--color-text-heading); rx: 6; }
        .tooltip-text { fill: white; font-size: 11px; font-family: var(--font-body); }
        .chart-grid-line { stroke: var(--color-border); stroke-width: 0.5; stroke-dasharray: 4 4; }
        .chart-label { fill: var(--color-text-muted); font-size: 10px; font-family: var(--font-body); }

        /* Bar Chart */
        .bar-chart { display: flex; align-items: flex-end; justify-content: space-between; height: 150px; padding-top: 1rem; gap: 8px; }
        .bar-item { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 8px; }
        .bar { width: 100%; max-width: 40px; border-radius: 6px 6px 0 0; transition: all 0.3s ease; cursor: pointer; position: relative; }
        .bar.inactive { background: var(--color-bg-main); }
        .bar.active { background: var(--color-primary); }
        .bar-label { font-size: 0.7rem; color: var(--color-text-muted); font-weight: 500; }
        .bar-value { font-size: 0.75rem; font-weight: 700; color: var(--color-primary); }

        /* Gauge Chart */
        .gauge-container { display: flex; flex-direction: column; align-items: center; padding: 1rem 0; }
        .gauge-value { font-family: var(--font-heading); font-size: 2.5rem; font-weight: 700; color: var(--color-text-heading); margin-bottom: 0.25rem; }
        .gauge-subtitle { font-size: 0.85rem; color: var(--color-text-muted); margin-bottom: 0.75rem; }
        .btn-details { padding: 0.4rem 1rem; border: 1px solid var(--color-border); border-radius: var(--radius-pill); background: none; font-size: 0.8rem; font-weight: 600; color: var(--color-text-body); cursor: pointer; transition: all 0.2s ease; }

        /* Products Table */
        .products-table { width: 100%; }
        .products-table th { font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: var(--color-text-muted); padding: 0.75rem 1rem; border-bottom: 1px solid var(--color-border); text-align: left; }
        .products-table td { padding: 0.75rem 1rem; border-bottom: 1px solid var(--color-bg-main); font-size: 0.85rem; vertical-align: middle; }
        .product-info { display: flex; align-items: center; gap: 10px; }
        .product-thumb { width: 36px; height: 36px; border-radius: var(--radius-sm); background: var(--color-bg-main); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .product-name { font-weight: 600; color: var(--color-text-heading); font-size: 0.85rem; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .revenue-value { font-weight: 600; color: var(--color-accent-green); }
        .rating { display: flex; align-items: center; gap: 4px; color: var(--color-accent-yellow); font-size: 0.85rem; }

        /* AI Assistant */
        .ai-assistant-card { background: var(--color-white); border-radius: var(--radius-md); padding: 1.5rem; box-shadow: var(--shadow-card); }
        .ai-orb { width: 80px; height: 80px; margin: 1.5rem auto; background: radial-gradient(circle at 30% 30%, var(--color-accent-blue), var(--color-primary-dark)); border-radius: 50%; box-shadow: 0 10px 40px rgba(0, 51, 153, 0.3); animation: pulse 2s ease-in-out infinite; }
        @keyframes pulse { 0%, 100% { transform: scale(1); box-shadow: 0 10px 40px rgba(0, 51, 153, 0.3); } 50% { transform: scale(1.05); box-shadow: 0 15px 50px rgba(0, 51, 153, 0.4); } }
        .ai-input-wrapper { display: flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1rem; background: var(--color-bg-main); border-radius: var(--radius-pill); margin-top: 1rem; }
        .ai-input-wrapper input { flex: 1; border: none; background: none; font-size: 0.9rem; color: var(--color-text-body); font-family: var(--font-body); }
        .ai-input-wrapper button { width: 32px; height: 32px; border-radius: 50%; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; }
        .ai-input-wrapper .btn-send { background: var(--color-primary); color: var(--color-white); }

        /* Animations */
        .fade-in { opacity: 0; transform: translateY(10px); animation: fadeIn 0.5s ease forwards; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
@endsection

@section('content')
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <h1>Dashboard</h1>
        <div class="dashboard-header-actions">
            <div class="date-range-selector">
                <i class="fas fa-calendar-alt"></i>
                <span>Jan 1, 2025 - Feb, 1 2025</span>
                <i class="fas fa-chevron-down" style="font-size: 0.7rem;"></i>
            </div>
            <button class="btn-header">
                <i class="fas fa-plus"></i>
                Add widget
            </button>
            <button class="btn-header btn-header-primary">
                <i class="fas fa-download"></i>
                Export
            </button>
        </div>
    </div>

    <!-- KPI Cards Row -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card fade-in" style="animation-delay: 0.1s;">
                <div class="kpi-card-header">
                    <span class="title">Page Views</span>
                    <div class="icon blue"><i class="fas fa-eye"></i></div>
                </div>
                <div class="kpi-value">16,431</div>
                <div class="kpi-change positive">
                    <i class="fas fa-arrow-up"></i> 15.5%
                    <span class="kpi-period">vs. last period</span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card fade-in" style="animation-delay: 0.2s;">
                <div class="kpi-card-header">
                    <span class="title">Visitors</span>
                    <div class="icon green"><i class="fas fa-user-friends"></i></div>
                </div>
                <div class="kpi-value">6,225</div>
                <div class="kpi-change positive">
                    <i class="fas fa-arrow-up"></i> 8.4%
                    <span class="kpi-period">vs. last period</span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card fade-in" style="animation-delay: 0.3s;">
                <div class="kpi-card-header">
                    <span class="title">Click</span>
                    <div class="icon purple"><i class="fas fa-mouse-pointer"></i></div>
                </div>
                <div class="kpi-value">2,832</div>
                <div class="kpi-change negative">
                    <i class="fas fa-arrow-down"></i> 10.5%
                    <span class="kpi-period">vs. last period</span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card fade-in" style="animation-delay: 0.4s;">
                <div class="kpi-card-header">
                    <span class="title">Orders</span>
                    <div class="icon orange"><i class="fas fa-shopping-bag"></i></div>
                </div>
                <div class="kpi-value">1,224</div>
                <div class="kpi-change positive">
                    <i class="fas fa-arrow-up"></i> 4.4%
                    <span class="kpi-period">vs. last period</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-3 mb-4">
        <div class="col-xl-8 col-lg-7">
            <div class="chart-card">
                <div class="chart-card-header">
                    <h3>Total Profit</h3>
                    <button class="more-btn"><i class="fas fa-ellipsis-h"></i></button>
                </div>
                <div class="profit-value">$446.7K</div>
                <span class="profit-change positive"><i class="fas fa-arrow-up"></i> 24.4% vs. last period</span>
                <div class="chart-container">
                    <svg viewBox="0 0 600 200" preserveAspectRatio="none">
                        <defs>
                            <linearGradient id="gradientBlue" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="rgba(0, 51, 153, 0.15)"/>
                                <stop offset="100%" stop-color="rgba(0, 51, 153, 0)"/>
                            </linearGradient>
                        </defs>
                        <line x1="0" y1="40" x2="600" y2="40" class="chart-grid-line"/>
                        <line x1="0" y1="80" x2="600" y2="80" class="chart-grid-line"/>
                        <line x1="0" y1="120" x2="600" y2="120" class="chart-grid-line"/>
                        <line x1="0" y1="160" x2="600" y2="160" class="chart-grid-line"/>
                        <path d="M0,140 L30,135 L60,130 L90,125 L120,120 L150,110 L180,105 L210,95 L240,85 L270,75 L300,65 L330,60 L360,70 L390,80 L420,75 L450,65 L480,55 L510,50 L540,45 L570,40 L600,35 L600,180 L0,180 Z" class="line-chart-area"/>
                        <path d="M0,140 L30,135 L60,130 L90,125 L120,120 L150,110 L180,105 L210,95 L240,85 L270,75 L300,65 L330,60 L360,70 L390,80 L420,75 L450,65 L480,55 L510,50 L540,45 L570,40 L600,35" class="line-chart-path"/>
                        <circle cx="330" cy="60" r="4" class="line-chart-dot active"/>
                        <text x="0" y="195" class="chart-label">1 Jan</text>
                        <text x="300" y="195" class="chart-label">15 Jan</text>
                        <text x="600" y="195" class="chart-label" text-anchor="end">31 Jan</text>
                    </svg>
                </div>
                <div class="customer-segments">
                    <div class="segment-card blue"><div class="segment-value">2,884</div><div class="segment-label">Retainers</div></div>
                    <div class="segment-card green"><div class="segment-value">1,432</div><div class="segment-label">Distributors</div></div>
                    <div class="segment-card orange"><div class="segment-value">562</div><div class="segment-label">Wholesalers</div></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-5">
            <div class="chart-card mb-3">
                <div class="chart-card-header"><h3>Most Day Active</h3><button class="more-btn"><i class="fas fa-ellipsis-h"></i></button></div>
                <div class="bar-chart">
                    @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                        <div class="bar-item">
                            <div class="bar {{ $day == 'Tue' ? 'active' : 'inactive' }}" style="height: {{ rand(40, 100) }}%;"></div>
                            <span class="bar-label">{{ $day }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="chart-card">
                <div class="chart-card-header"><h3>Repeat Customer Rate</h3><button class="more-btn"><i class="fas fa-ellipsis-h"></i></button></div>
                <div class="gauge-container">
                    <svg width="200" height="120" viewBox="0 0 200 120">
                        <path d="M 20 100 A 80 80 0 0 1 180 100" fill="none" stroke="#E2E8F0" stroke-width="12" stroke-linecap="round"/>
                        <path d="M 20 100 A 80 80 0 0 1 146 32" fill="none" stroke="#10B981" stroke-width="12" stroke-linecap="round"/>
                    </svg>
                    <div class="gauge-value">68%</div>
                    <div class="gauge-subtitle">On track for 80% target</div>
                    <button class="btn-details">Show details</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Row -->
    <div class="row g-3">
        <div class="col-xl-8 col-lg-7">
            <div class="chart-card">
                <div class="chart-card-header"><h3>Best Selling Products</h3><button class="more-btn"><i class="fas fa-ellipsis-h"></i></button></div>
                <div class="table-responsive">
                    <table class="products-table">
                        <thead><tr><th>ID</th><th>Name</th><th>Sold</th><th>Revenue</th><th>Rating</th></tr></thead>
                        <tbody>
                            @foreach([
                                ['#83009', 'Hybrid Active Noise Cancel...', '2,310', '$124,839', '5.0'],
                                ['#83001', 'Casio G-Shock Shock Resi...', '1,230', '$92,662', '4.8'],
                                ['#83004', 'SAMSUNG Galaxy S25 Ultr...', '812', '$74,048', '4.7']
                            ] as $product)
                                <tr>
                                    <td class="product-id">{{ $product[0] }}</td>
                                    <td><div class="product-info"><div class="product-thumb"><i class="fas fa-box"></i></div><span class="product-name">{{ $product[1] }}</span></div></td>
                                    <td>{{ $product[2] }} sold</td>
                                    <td class="revenue-value">{{ $product[3] }}</td>
                                    <td><div class="rating"><i class="fas fa-star"></i><span>({{ $product[4] }})</span></div></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-5">
            <div class="ai-assistant-card" style="height: 100%;">
                <div class="chart-card-header"><h3>AI Assistant</h3><button class="more-btn"><i class="fas fa-expand"></i></button></div>
                <div class="ai-orb"></div>
                <div class="ai-input-wrapper">
                    <input type="text" placeholder="Ask me anything...">
                    <button class="btn-send"><i class="fas fa-arrow-up"></i></button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Animate numbers
        function animateValue(element, start, end, duration) {
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                const value = Math.floor(progress * (end - start) + start);
                element.textContent = value.toLocaleString();
                if (progress < 1) window.requestAnimationFrame(step);
            };
            window.requestAnimationFrame(step);
        }

        window.addEventListener('load', () => {
            document.querySelectorAll('.kpi-value').forEach(el => {
                const endValue = parseFloat(el.textContent.replace(/,/g, ''));
                if (!isNaN(endValue)) animateValue(el, 0, endValue, 1500);
            });
        });
    </script>
@endsection
