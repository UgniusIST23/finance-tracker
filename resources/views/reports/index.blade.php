@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto bg-gray-800 p-6 rounded shadow text-white">
    <h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
        Finansinė ataskaita
    </h2>

{{-- Filtravimo forma --}}
<form method="GET" action="{{ route('reports.index') }}" class="mb-6 flex flex-wrap gap-4 items-end text-white">
    <div>
        <label class="block text-sm mb-1" for="date_from">Nuo</label>
        <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
               class="px-3 py-1 rounded border dark:bg-gray-700 dark:text-white">
    </div>
    <div>
        <label class="block text-sm mb-1" for="date_to">Iki</label>
        <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
               class="px-3 py-1 rounded border dark:bg-gray-700 dark:text-white">
    </div>
    <div>
        <label class="block text-sm mb-1" for="per_page">Įrašų kiekis</label>
        <select name="per_page" id="per_page"
                onchange="this.form.submit()"
                class="px-3 py-1 w-24 rounded border dark:bg-gray-700 dark:text-white">
            @foreach ([10, 15, 20, $byCategory->count()] as $option)
                <option value="{{ $option }}" {{ request('per_page', 10) == $option ? 'selected' : '' }}>
                    {{ $option === $byCategory->count() ? 'Visi' : $option }}
                </option>
            @endforeach
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

{{-- Apibendrinimas --}}
<div class="mb-6">
    <h3 class="text-lg font-semibold mb-2">Bendri duomenys</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-gray-700 p-4 rounded">
            <p class="text-sm text-gray-400">Pajamos</p>
            <p class="text-green-400 text-lg font-bold">+{{ number_format($incomeTotal, 2) }} EUR</p>
        </div>
        <div class="bg-gray-700 p-4 rounded">
            <p class="text-sm text-gray-400">Išlaidos</p>
            <p class="text-red-400 text-lg font-bold">-{{ number_format($expenseTotal, 2) }} EUR</p>
        </div>
        <div class="bg-gray-700 p-4 rounded">
            <p class="text-sm text-gray-400">Viso</p>
            <p class="text-lg font-bold {{ $balance >= 0 ? 'text-green-300' : 'text-red-300' }}">
                {{ number_format($balance, 2) }} EUR
            </p>
        </div>
    </div>
</div>

{{-- Ataskaita pagal kategorijas --}}
<h3 class="text-lg font-semibold mb-2">Išlaidos/pajamos pagal kategorijas</h3>
<div class="space-y-2 mb-6">
    @php $paginated = $byCategory->slice(0, request('per_page', 10)) @endphp
    @forelse ($paginated as $category => $data)
@php
    $trashedLabel = '';
    $categoryLabel = $category;
    $categoryClass = '';

    if ($category === 'KATEGORIJA IŠTRINTA') {
        $categoryClass = 'text-red-400'; // Raudona spalva
    } else {
        if ($data['trashed'] === 'soft') {
            $trashedLabel = ' <span class="text-yellow-400">(KATEGORIJA LAIKINAI IŠTRINTA)</span>';
        } elseif ($data['trashed'] === 'hard') {
            $trashedLabel = ' <span class="text-red-400">(KATEGORIJA IŠTRINTA)</span>';
        }
    }
@endphp

<div class="bg-gray-700 px-4 py-2 rounded flex justify-between">
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

    </span>
    <span class="{{ $data['type'] === 'income' ? 'text-green-400' : 'text-red-400' }}">
        {{ number_format($data['sum'], 2) }} EUR
    </span>
</div>

    @empty
        <p class="text-gray-400">Nėra duomenų šiam laikotarpiui.</p>
    @endforelse
</div>

{{-- Min, Max, Avg --}}
<h3 class="text-lg font-semibold mb-2">Analizė</h3>
<ul class="list-disc list-inside text-gray-200 space-y-1 mb-6">
    <li>Mažiausia transakcija: <span class="text-blue-400">{{ number_format($min ?? 0, 2) }} EUR</span></li>
    <li>Didžiausia transakcija: <span class="text-blue-400">{{ number_format($max ?? 0, 2) }} EUR</span></li>
    <li>Vidutinis transakcijos dydis: <span class="text-blue-400">{{ number_format($avg ?? 0, 2) }} EUR</span></li>
</ul>

{{-- Diagramos --}}
<h3 class="text-lg font-semibold mb-2">Grafikai pagal kategorijas</h3>
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-white p-4 rounded text-black">
    <div>
        <h4 class="text-sm font-semibold mb-2">Pajamų diagrama</h4>
        <canvas id="incomeChart" height="200"></canvas>
    </div>
    <div>
        <h4 class="text-sm font-semibold mb-2">Išlaidų diagrama</h4>
        <canvas id="expenseChart" height="200"></canvas>
    </div>
    <div>
        <h4 class="text-sm font-semibold mb-2">Bendra diagrama</h4>
        <canvas id="combinedChart" height="200"></canvas>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const incomeChart = new Chart(document.getElementById('incomeChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($incomeData->keys()) !!},
            datasets: [{
                label: 'Pajamos',
                data: {!! json_encode($incomeData->values()) !!},
                backgroundColor: 'rgba(34, 197, 94, 0.6)',
                borderColor: 'rgba(34, 197, 94, 1)',
                borderWidth: 1
            }]
        }
    });

    const expenseChart = new Chart(document.getElementById('expenseChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($expenseData->keys()) !!},
            datasets: [{
                label: 'Išlaidos',
                data: {!! json_encode($expenseData->values()) !!},
                backgroundColor: 'rgba(239, 68, 68, 0.6)',
                borderColor: 'rgba(239, 68, 68, 1)',
                borderWidth: 1
            }]
        }
    });

    const combinedChart = new Chart(document.getElementById('combinedChart'), {
        type: 'pie',
        data: {
            labels: {!! json_encode($combinedData->keys()) !!},
            datasets: [{
                label: 'Suma pagal kategoriją',
                data: {!! json_encode($combinedData->map(fn($item) => $item['sum'])->values()) !!},
                backgroundColor: [
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
                    'rgba(253, 186, 116, 0.6)'
                ],
                borderColor: 'rgba(255, 255, 255, 0.1)',
                borderWidth: 1
            }]
        }
    });
</script>
@endsection
