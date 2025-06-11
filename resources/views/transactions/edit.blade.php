@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto bg-white dark:bg-gray-800 p-6 rounded shadow">
    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">Redaguoti transakciją</h2>

    <form action="{{ route('transactions.update', $transaction) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-200 mb-1">Kategorija</label>
            <select name="category_id" class="w-full px-4 py-2 rounded border dark:bg-gray-700 dark:text-white" required>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $transaction->category_id) == $category->id ? 'selected' : '' }}>
                        {{ $category->name }} 
                        ({{ $category->type === 'income' ? 'Pajamos' : 'Išlaidos' }})
                        @if ($category->trashed())
                            (KATEGORIJA LAIKINAI IŠTRINTA)
                        @endif
                    </option>
                @endforeach

                @php
                    $categoryExists = $categories->pluck('id')->contains($transaction->category_id);
                @endphp

                @if (!$categoryExists && $transaction->category)
                    <option value="{{ $transaction->category->id }}" selected>
                        {{ $transaction->category->name }} 
                        ({{ $transaction->category->type === 'income' ? 'Pajamos' : 'Išlaidos' }})
                        (KATEGORIJA LAIKINAI IŠTRINTA)
                    </option>
                @endif
            </select>
            @error('category_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-200 mb-1">Suma</label>
            <input type="number" step="0.01" name="amount" value="{{ old('amount', $transaction->amount) }}"
                   class="w-full px-4 py-2 rounded border dark:bg-gray-700 dark:text-white" required>
            @error('amount')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <input type="hidden" name="currency" value="EUR">

        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-200 mb-1">Aprašymas</label>
            <textarea name="description" rows="2"
                      class="w-full px-4 py-2 rounded border dark:bg-gray-700 dark:text-white">{{ old('description', $transaction->description) }}</textarea>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-200 mb-1">Data</label>
            <input type="date" name="date" value="{{ old('date', $transaction->date) }}"
                   class="w-full px-4 py-2 rounded border dark:bg-gray-700 dark:text-white" required>
            @error('date')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Išsaugoti</button>
    </form>
</div>
@endsection
