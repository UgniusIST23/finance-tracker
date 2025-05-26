@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto bg-white dark:bg-gray-800 p-6 rounded shadow">
    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">Nauja transakcija</h2>

    <form action="{{ route('transactions.store') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-200 mb-1">Kategorija</label>
            <select name="category_id" class="w-full px-4 py-2 rounded border dark:bg-gray-700 dark:text-white" required>
                <option value="">-- Pasirinkti --</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">
                        {{ $category->name }} ({{ $category->type === 'income' ? 'Pajamos' : 'Išlaidos' }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-200 mb-1">Suma</label>
            <input type="number" step="0.01" name="amount" class="w-full px-4 py-2 rounded border dark:bg-gray-700 dark:text-white" required>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-200 mb-1">Valiuta</label>
            <input type="text" name="currency" value="EUR" maxlength="3" class="w-full px-4 py-2 rounded border dark:bg-gray-700 dark:text-white" required>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-200 mb-1">Aprašymas (nebūtinas)</label>
            <textarea name="description" rows="2" class="w-full px-4 py-2 rounded border dark:bg-gray-700 dark:text-white"></textarea>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-200 mb-1">Data</label>
            <input type="date" name="date" class="w-full px-4 py-2 rounded border dark:bg-gray-700 dark:text-white" required>
        </div>

        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Išsaugoti</button>
    </form>
</div>
@endsection
