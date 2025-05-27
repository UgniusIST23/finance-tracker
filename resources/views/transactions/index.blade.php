@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-gray-800 p-6 rounded shadow text-white">
    <h2 class="text-xl font-semibold mb-4">Transakcijų sąrašas</h2>

    {{-- Filtravimo forma --}}
    <form method="GET" action="{{ route('transactions.index') }}" class="mb-6 flex flex-wrap gap-4 items-end text-white">
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
            <label class="block text-sm mb-1" for="category_id">Kategorija</label>
            <select name="category_id" id="category_id" class="px-3 py-1 rounded border dark:bg-gray-700 dark:text-white">
                <option value="">– Visos –</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }} ({{ $category->type === 'income' ? 'Pajamos' : 'Išlaidos' }})
                    </option>
                @endforeach
            </select>
        </div>
            <div class="flex gap-2">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                 Filtruoti
                 </button>
                    <a href="{{ route('transactions.index') }}"
       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded inline-block">
        Valyti filtrus
    </a>
</div>
    </form>

    <div class="mb-4">
        <p class="text-lg">
            <span class="font-semibold">Balansas:</span>
            <span class="{{ $balance < 0 ? 'text-red-500' : 'text-green-400' }}">
                {{ number_format($balance, 2) }} EUR
            </span>
        </p>
        <p class="text-sm text-gray-400">
            Pajamos: +{{ number_format($income, 2) }} EUR | Išlaidos: -{{ number_format($expense, 2) }} EUR
        </p>
    </div>

    @if (session('success'))
        <div class="mb-4 text-green-500">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('transactions.create') }}" class="text-purple-400 hover:text-purple-600 mb-4 inline-block">
        Nauja transakcija
    </a>

    @forelse ($transactions as $transaction)
        <div class="flex justify-between items-center bg-gray-700 px-4 py-2 rounded mb-2">
            <div>
                <strong>{{ $transaction->category->name }}</strong>
                ({{ $transaction->category->type === 'income' ? 'Pajamos' : 'Išlaidos' }})<br>
                <span class="text-sm text-gray-300">
                    {{ number_format($transaction->amount, 2) }} {{ strtoupper($transaction->currency) }} – {{ $transaction->date }}
                    @if ($transaction->description)<br>{{ $transaction->description }}@endif
                </span>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('transactions.edit', $transaction) }}"
                   class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm">
                    Redaguoti
                </a>
                <form action="{{ route('transactions.destroy', $transaction) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">
                        Trinti
                    </button>
                </form>
            </div>
        </div>
    @empty
        <p class="text-gray-400 mt-4">Transakcijų dar nėra.</p>
    @endforelse
</div>
@endsection
