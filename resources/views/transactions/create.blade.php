@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto bg-white dark:bg-gray-800 p-6 rounded shadow text-gray-900 dark:text-white">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold">Nauja transakcija</h2>
        <a href="{{ route('transactions.index') }}"
           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded text-sm">
            ← Grįžti atgal
        </a>
    </div>

    <form action="{{ route('transactions.store') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-200 mb-1">Kategorija</label>
            <select name="category_id" class="w-full px-4 py-2 rounded border border-gray-300 bg-white text-gray-900 dark:bg-gray-700 dark:text-white dark:border-gray-700" required>
                <option value="">-- Pasirinkti --</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                        ({{ $category->type === 'income' ? 'Pajamos' : 'Išlaidos' }})
                        @if ($category->trashed())
                            (LAIKINAI IŠTRINTA)
                        @endif
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-200 mb-1">Suma</label>
            <input type="number" step="0.01" name="amount" value="{{ old('amount') }}"
                   class="w-full px-4 py-2 rounded border border-gray-300 bg-white text-gray-900 dark:bg-gray-700 dark:text-white dark:border-gray-700" required>
            @error('amount')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <input type="hidden" name="currency" value="EUR">

        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-200 mb-1">Aprašymas (nebūtinas)</label>
            <textarea name="description" rows="2"
                      class="w-full px-4 py-2 rounded border border-gray-300 bg-white text-gray-900 dark:bg-gray-700 dark:text-white dark:border-gray-700">{{ old('description') }}</textarea>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-200 mb-1">Data</label>
            <input type="date" name="date" value="{{ old('date') }}"
                   class="w-full px-4 py-2 rounded border border-gray-300 bg-white text-gray-900 dark:bg-gray-700 dark:text-white dark:border-gray-700" required>
            @error('date')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Išsaugoti</button>
    </form>
</div>
@endsection
