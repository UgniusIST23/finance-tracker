@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto bg-white dark:bg-gray-800 p-6 rounded shadow text-gray-900 dark:text-white">
    <h2 class="text-xl font-semibold mb-4">Transakcijų sąrašas</h2>

    <form method="GET" action="{{ route('transactions.index') }}" class="mb-6 flex flex-wrap gap-4 items-end text-gray-900 dark:text-white">
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
            <label class="block text-sm mb-1" for="category_id">Kategorija</label>
            <select name="category_id" id="category_id"
                    class="px-3 py-1 rounded border border-gray-300 bg-white text-gray-900 dark:bg-gray-700 dark:text-white dark:border-gray-700">
                <option value="">– Visos –</option>
                <option value="-1" {{ request('category_id') == -1 ? 'selected' : '' }}>– Ištrintos kategorijos –</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }} ({{ $category->type === 'income' ? 'Pajamos' : 'Išlaidos' }})
                        {{ $category->trashed() ? '(LAIKINAI IŠTRINTA)' : '' }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm mb-1" for="per_page">Įrašų kiekis</label>
            <select name="per_page" id="per_page"
                    onchange="this.form.submit()"
                    class="px-3 py-1 w-24 rounded border border-gray-300 bg-white text-gray-900 dark:bg-gray-700 dark:text-white dark:border-gray-700">
                <option value="10" {{ $selectedPerPage == 10 ? 'selected' : '' }}>10</option>
                <option value="15" {{ $selectedPerPage == 15 ? 'selected' : '' }}>15</option>
                <option value="20" {{ $selectedPerPage == 20 ? 'selected' : '' }}>20</option>
                <option value="all" {{ $selectedPerPage == 'all' ? 'selected' : '' }}>Visi</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Filtruoti</button>
            <a href="{{ route('transactions.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">Valyti filtrus</a>
        </div>
    </form>

    <div class="mb-4">
        <p class="text-lg">
            <span class="font-semibold">Balansas:</span>
            <span class="{{ $balance < 0 ? 'text-red-600 dark:text-red-500' : 'text-green-600 dark:text-green-400' }}">
                {{ number_format($balance, 2) }} EUR
            </span>
        </p>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Pajamos: +{{ number_format($income, 2) }} EUR | Išlaidos: -{{ number_format($expense, 2) }} EUR
        </p>
    </div>

    @if (session('success'))
        <div class="mb-4 text-green-500">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('transactions.create') }}" class="text-purple-600 hover:text-purple-700 dark:text-purple-400 dark:hover:text-purple-500 mb-4 inline-block">
        Nauja transakcija
    </a>

    @forelse ($transactions as $transaction)
        <div class="flex justify-between items-center bg-white dark:bg-gray-700 px-4 py-2 rounded shadow mb-2 text-gray-900 dark:text-white">
            <div>
                <strong>
                    @if ($transaction->category)
                        {{ $transaction->category->name }}
                        @if ($transaction->category->trashed())
                            <span class="text-yellow-600 dark:text-yellow-400">(LAIKINAI IŠTRINTA)</span>
                        @endif
                        ({{ $transaction->category->type === 'income' ? 'Pajamos' : 'Išlaidos' }})
                    @else
                        <span class="text-red-600 dark:text-red-400">KATEGORIJA IŠTRINTA</span>
                    @endif
                </strong>
                <br>
                <span class="text-sm text-gray-600 dark:text-gray-300">
                    {{ number_format($transaction->amount, 2) }} {{ strtoupper($transaction->currency) }} – {{ $transaction->date }}
                    @if ($transaction->description)<br>{{ $transaction->description }}@endif
                </span>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('transactions.edit', $transaction) }}"
                   class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm">Redaguoti</a>
                <form action="{{ route('transactions.destroy', $transaction) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">Trinti</button>
                </form>
            </div>
        </div>
    @empty
        <p class="text-gray-600 dark:text-gray-400 mt-4">Transakcijų dar nėra.</p>
    @endforelse

    <div class="mt-6 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
        <form method="GET" class="flex items-center gap-2">
            <input type="hidden" name="date_from" value="{{ request('date_from') }}">
            <input type="hidden" name="date_to" value="{{ request('date_to') }}">
            <input type="hidden" name="category_id" value="{{ request('category_id') }}">

            <label for="per_page" class="text-sm text-gray-900 dark:text-white">Įrašų kiekis:</label>
            <select name="per_page" id="per_page" onchange="this.form.submit()"
                    class="px-3 py-2 rounded border border-gray-300 bg-white text-gray-900 dark:bg-gray-700 dark:text-white dark:border-gray-700 text-sm w-24">
                <option value="10" {{ $selectedPerPage == 10 ? 'selected' : '' }}>10</option>
                <option value="15" {{ $selectedPerPage == 15 ? 'selected' : '' }}>15</option>
                <option value="20" {{ $selectedPerPage == 20 ? 'selected' : '' }}>20</option>
                <option value="all" {{ $selectedPerPage == 'all' ? 'selected' : '' }}>Visi</option>
            </select>
        </form>

        @if ($transactions instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div>
                {{ $transactions->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
