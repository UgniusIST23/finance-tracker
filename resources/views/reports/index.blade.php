@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto bg-white dark:bg-gray-800 p-6 rounded shadow text-gray-900 dark:text-white">
    <h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
        Finansinė ataskaita
    </h2>

    <form method="GET" action="{{ route('reports.index') }}" class="mb-6 flex flex-wrap gap-4 items-end text-gray-900 dark:text-white">
        <div>
            <label class="block text-sm mb-1" for="date_from">Nuo</label>
            <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                   class="px-3 py-1 rounded border border-gray-300 bg-white text-gray-900 dark:bg-gray-700 dark:text-white dark:border-gray-700">
        </div>
        <div>
            <label class="block text-sm mb-1" for="date_to">Iki</label>
            <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                   class="px-3 py-1 rounded border border-gray-300 bg-white text-gray-900 dark:bg-gray-700 dark:text-white dark:border-gray-700">
        </div>
        <div>
            <label class="block text-sm mb-1" for="per_page">Įrašų kiekis</label>
            <select name="per_page" id="per_page"
                    onchange="this.form.submit()"
                    class="px-3 py-1 w-24 rounded border border-gray-300 bg-white text-gray-900 dark:bg-gray-700 dark:text-white dark:border-gray-700">
                <option value="10" {{ $perPageReport == 10 ? 'selected' : '' }}>10</option>
                <option value="15" {{ $perPageReport == 15 ? 'selected' : '' }}>15</option>
                <option value="20" {{ $perPageReport == 20 ? 'selected' : '' }}>20</option>
                <option value="all" {{ $perPageReport == 'all' ? 'selected' : '' }}>Visi</option>
            </select>
        </div>
        <div class="flex gap-2 mt-5">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                Filtruoti
            </button>
            <a href="{{ route('reports.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                Valyti filtrus
            </a>
        </div>
    </form>

    <div class="mb-6">
        <h3 class="text-lg font-semibold mb-2">Bendri duomenys</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-gray-700 p-4 rounded shadow">
                <p class="text-sm text-gray-600 dark:text-gray-400">Pajamos</p>
                <p class="text-green-600 dark:text-green-400 text-lg font-bold">+{{ number_format($incomeTotal, 2) }} EUR</p>
            </div>
            <div class="bg-white dark:bg-gray-700 p-4 rounded shadow">
                <p class="text-sm text-gray-600 dark:text-gray-400">Išlaidos</p>
                <p class="text-red-600 dark:text-red-400 text-lg font-bold">-{{ number_format($expenseTotal, 2) }} EUR</p>
            </div>
            <div class="bg-white dark:bg-gray-700 p-4 rounded shadow">
                <p class="text-sm text-gray-600 dark:text-gray-400">Viso</p>
                <p class="text-lg font-bold {{ $balance >= 0 ? 'text-green-600 dark:text-green-300' : 'text-red-600 dark:text-red-300' }}">
                    {{ number_format($balance, 2) }} EUR
                </p>
            </div>
        </div>
    </div>

    <h3 class="text-lg font-semibold mb-2">Išlaidos/pajamos pagal kategorijas</h3>
    <div class="space-y-2 mb-6">
        @forelse ($byCategory as $category => $data)
            @php
                $trashedLabel = '';
                $categoryClass = '';

                if ($category === 'KATEGORIJA IŠTRINTA') {
                    $categoryClass = 'text-red-600 dark:text-red-400';
                } else {
                    if (isset($data['trashed']) && $data['trashed'] === 'soft') {
                        $trashedLabel = ' <span class="text-yellow-600 dark:text-yellow-400">(LAIKINAI IŠTRINTA)</span>';
                    } elseif (isset($data['trashed']) && $data['trashed'] === 'hard') {
                        $trashedLabel = ' <span class="text-red-600 dark:text-red-400">(IŠTRINTA)</span>';
                    }
                }
            @endphp

            <div class="bg-white dark:bg-gray-700 px-4 py-2 rounded shadow flex justify-between text-gray-900 dark:text-white">
                <span class="{{ $categoryClass }}">
                    <span>{!! $category !!}{!! $trashedLabel !!} (
                    @if ($data['type'] === 'income')
                        Pajamos
                    @elseif ($data['type'] === 'expense')
                        Išlaidos
                    @else
                        Nežinoma
                    @endif
                    )</span>
                    <span class="text-gray-500 dark:text-gray-400 text-xs">
                        ({{ $data['count'] }} transakcijų)
                    </span>
                </span>
                <span class="{{ $data['type'] === 'income' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                    {{ number_format($data['sum'], 2) }} EUR
                </span>
            </div>
        @empty
            <p class="text-gray-600 dark:text-gray-400">Nėra duomenų šiam laikotarpiui.</p>
        @endforelse
    </div>

    <h3 class="text-lg font-semibold mb-2">Analizė</h3>
    <ul class="list-disc list-inside text-gray-700 dark:text-gray-200 space-y-2 mb-6">
        <li>
            Mažiausia transakcija:
            @if ($minTransaction)
                <span class="text-blue-600 dark:text-blue-400">
                    {{ number_format($minTransaction->amount, 2) }} EUR
                    ({{ $minTransaction->date }})
                    @if ($minTransaction->description)
                        - "{{ Str::limit($minTransaction->description, 30) }}"
                    @endif
                    @if ($minTransaction->category)
                        (Kategorija: {{ $minTransaction->category->name }}
                        @if ($minTransaction->category->trashed())
                            <span class="text-yellow-600 dark:text-yellow-400">(LAIKINAI IŠTRINTA)</span>
                        @endif)
                    @else
                        (<span class="text-red-600 dark:text-red-400">Kategorija ištrinta</span>)
                    @endif
                </span>
            @else
                <span class="text-gray-600 dark:text-gray-400">Nėra</span>
            @endif
        </li>
        <li>
            Didžiausia transakcija:
            @if ($maxTransaction)
                <span class="text-blue-600 dark:text-blue-400">
                    {{ number_format($maxTransaction->amount, 2) }} EUR
                    ({{ $maxTransaction->date }})
                    @if ($maxTransaction->description)
                        - "{{ Str::limit($maxTransaction->description, 30) }}"
                    @endif
                    @if ($maxTransaction->category)
                        (Kategorija: {{ $maxTransaction->category->name }}
                        @if ($maxTransaction->category->trashed())
                            <span class="text-yellow-600 dark:text-yellow-400">(LAIKINAI IŠTRINTA)</span>
                        @endif)
                    @else
                        (<span class="text-red-600 dark:text-red-400">Kategorija ištrinta</span>)
                    @endif
                </span>
            @else
                <span class="text-gray-600 dark:text-gray-400">Nėra</span>
            @endif
        </li>
        <li>
            Naujausia transakcija:
            @if ($latestTransaction)
                <span class="text-blue-600 dark:text-blue-400">
                    {{ number_format($latestTransaction->amount, 2) }} EUR
                    ({{ $latestTransaction->date }})
                    @if ($latestTransaction->description)
                        - "{{ Str::limit($latestTransaction->description, 30) }}"
                    @endif
                    @if ($latestTransaction->category)
                        (Kategorija: {{ $latestTransaction->category->name }}
                        @if ($latestTransaction->category->trashed())
                            <span class="text-yellow-600 dark:text-yellow-400">(LAIKINAI IŠTRINTA)</span>
                        @endif)
                    @else
                        (<span class="text-red-600 dark:text-red-400">Kategorija ištrinta</span>)
                    @endif
                </span>
            @else
                <span class="text-gray-600 dark:text-gray-400">Nėra</span>
            @endif
        </li>
        <li>Vidutinis transakcijos dydis: <span class="text-blue-600 dark:text-blue-400">{{ number_format($avgTotal ?? 0, 2) }} EUR</span></li>
    </ul>

    <h3 class="text-lg font-semibold mb-2">Grafikai pagal kategorijas</h3>
    <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded text-gray-900 dark:text-white">
        <div class="flex flex-nowrap overflow-x-auto space-x-12 pb-4">
            <div class="flex-shrink-0 h-[450px] w-[550px]">
                <h4 class="text-sm font-semibold mb-2 text-center">Pajamų diagrama</h4>
                <canvas id="incomeChart" style="width: 100%; height: 100%;"></canvas>
            </div>
            <div class="flex-shrink-0 h-[450px] w-[550px]">
                <h4 class="text-sm font-semibold mb-2 text-center">Išlaidų diagrama</h4>
                <canvas id="expenseChart" style="width: 100%; height: 100%;"></canvas>
            </div>
            <div class="flex-shrink-0 h-[450px] w-[550px]">
                <h4 class="text-sm font-semibold mb-2 text-center">Bendra diagrama</h4>
                <canvas id="combinedChart" style="width: 100%; height: 100%;"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const dynamicColors = function() {
        const r = Math.floor(Math.random() * 255);
        const g = Math.floor(Math.random() * 255);
        const b = Math.floor(Math.random() * 255);
        return `rgba(${r},${g},${b},0.6)`;
    };

    const predefinedColors = [
        'rgba(34, 197, 94, 0.6)',
        'rgba(239, 68, 68, 0.6)',
        'rgba(234, 179, 8, 0.6)',
        'rgba(59, 130, 246, 0.6)',
        'rgba(168, 85, 247, 0.6)',
        'rgba(20, 184, 166, 0.6)',
        'rgba(244, 114, 182, 0.6)',
        'rgba(251, 191, 36, 0.6)',
        'rgba(107, 114, 128, 0.6)',
        'rgba(132, 204, 22, 0.6)',
        'rgba(96, 165, 250, 0.6)',
        'rgba(236, 72, 153, 0.6)',
        'rgba(125, 211, 252, 0.6)',
        'rgba(253, 186, 116, 0.6)',
        'rgba(147, 197, 253, 0.6)',
        'rgba(192, 132, 252, 0.6)',
        'rgba(252, 165, 165, 0.6)',
        'rgba(253, 224, 71, 0.6)',
        'rgba(74, 222, 128, 0.6)',
        'rgba(255, 127, 80, 0.6)',
        'rgba(100, 149, 237, 0.6)',
        'rgba(218, 112, 214, 0.6)',
        'rgba(0, 128, 128, 0.6)',
        'rgba(255, 215, 0, 0.6)',
        'rgba(128, 0, 0, 0.6)',
        'rgba(0, 0, 128, 0.6)'
    ];

    const getChartColors = (count) => {
        let colors = [...predefinedColors];
        while (colors.length < count) {
            colors.push(dynamicColors());
        }
        return colors;
    };

    const baseChartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.dataset.label || '';
                        if (label) {
                            label += ': ';
                        }
                        if (context.chart.config.type === 'bar') {
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('lt-LT', { style: 'currency', currency: 'EUR' }).format(context.parsed.y);
                            }
                        } else {
                            if (context.parsed !== null && typeof context.parsed === 'number') {
                                label += new Intl.NumberFormat('lt-LT', { style: 'currency', currency: 'EUR' }).format(context.parsed);
                            }
                        }
                        return label;
                    }
                }
            }
        },
        animation: {
            duration: 1200,
            easing: 'easeOutExpo'
        },
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Suma (EUR)',
                    color: '#cbd5e1'
                },
                ticks: {
                    color: '#cbd5e1',
                    callback: function(value, index, ticks) {
                        return new Intl.NumberFormat('lt-LT').format(value) + ' €';
                    }
                },
                grid: {
                    color: 'rgba(255, 255, 255, 0.1)',
                    drawBorder: false
                }
            },
            x: {
                title: {
                    display: true,
                    text: 'Kategorija',
                    color: '#cbd5e1'
                },
                ticks: {
                    color: '#cbd5e1',
                    maxRotation: 45,
                    minRotation: 45,
                    autoSkip: true,
                    maxTicksLimit: 20
                },
                grid: {
                    color: 'rgba(255, 255, 255, 0.1)',
                    drawBorder: false
                }
            }
        }
    };

    const incomeCtx = document.getElementById('incomeChart').getContext('2d');
    const incomeChart = new Chart(incomeCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($incomeData->keys()) !!},
            datasets: [{
                label: 'Pajamos',
                data: {!! json_encode($incomeData->values()) !!},
                backgroundColor: getChartColors({!! count($incomeData->keys()) !!}),
                borderColor: 'rgba(255, 255, 255, 0.3)',
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            ...baseChartOptions,
            scales: {
                y: { ...baseChartOptions.scales.y },
                x: { ...baseChartOptions.scales.x }
            }
        }
    });

    const expenseCtx = document.getElementById('expenseChart').getContext('2d');
    const expenseChart = new Chart(expenseCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($expenseData->keys()) !!},
            datasets: [{
                label: 'Išlaidos',
                data: {!! json_encode($expenseData->values()) !!},
                backgroundColor: getChartColors({!! count($expenseData->keys()) !!}),
                borderColor: 'rgba(255, 255, 255, 0.3)',
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            ...baseChartOptions,
            scales: {
                y: { ...baseChartOptions.scales.y },
                x: { ...baseChartOptions.scales.x }
            }
        }
    });

    const combinedCtx = document.getElementById('combinedChart').getContext('2d');
    const combinedChart = new Chart(combinedCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($combinedData->keys()) !!},
            datasets: [{
                label: 'Suma pagal kategoriją',
                data: {!! json_encode($combinedData->map(fn($item) => $item['sum'])->values()) !!},
                backgroundColor: getChartColors({!! count($combinedData->keys()) !!}),
                borderColor: 'rgba(255, 255, 255, 0.8)',
                borderWidth: 2,
                hoverOffset: 10
            }]
        },
        options: {
            ...baseChartOptions,
            scales: {
                y: { display: false },
                x: { display: false }
            },
            plugins: {
                ...baseChartOptions.plugins,
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        color: '#cbd5e1',
                        font: {
                            size: 12
                        },
                        boxWidth: 20,
                        padding: 15
                    }
                }
            }
        }
    });
</script>
@endsection
