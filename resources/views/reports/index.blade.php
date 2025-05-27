@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-gray-800 p-6 rounded shadow text-white">
    <h2 class="text-xl font-semibold mb-6">📊 Finansinė ataskaita</h2>

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
        <div class="flex gap-2 mt-5">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                Filtruoti
            </button>
            <a href="{{ route('reports.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                Valyti filtrus
            </a>
        </div>
    </form>

    {{-- Ataskaita pagal kategorijas --}}
    <h3 class="text-lg font-semibold mb-2">Suminiai duomenys pagal kategorijas</h3>
    <div class="space-y-2 mb-6">
        @forelse ($byCategory as $category => $data)
            <div class="bg-gray-700 px-4 py-2 rounded flex justify-between">
                <span>{{ $category }} ({{ $data['type'] === 'income' ? 'Pajamos' : 'Išlaidos' }})</span>
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
    <ul class="list-disc list-inside text-gray-200 space-y-1">
        <li>Minimali transakcija: <span class="text-blue-400">{{ number_format($min ?? 0, 2) }} EUR</span></li>
        <li>Maksimali transakcija: <span class="text-blue-400">{{ number_format($max ?? 0, 2) }} EUR</span></li>
        <li>Vidutinė transakcija: <span class="text-blue-400">{{ number_format($avg ?? 0, 2) }} EUR</span></li>
    </ul>
</div>
@endsection
