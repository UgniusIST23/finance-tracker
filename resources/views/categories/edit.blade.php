@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-white dark:bg-gray-800 p-6 rounded shadow text-gray-900 dark:text-white">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold">Redaguoti kategoriją</h2>
        <a href="{{ route('categories.index') }}"
           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded text-sm">
            ← Grįžti atgal
        </a>
    </div>

    <form action="{{ route('categories.update', $category) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-200 mb-1">Pavadinimas:</label>
            <input type="text" name="name" value="{{ old('name', $category->name) }}"
                   class="w-full px-4 py-2 rounded border {{ $errors->has('name') ? 'border-red-500' : 'border-gray-300 dark:border-gray-700' }} bg-white text-gray-900 dark:bg-gray-700 dark:text-white" required>
            @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-200 mb-1">Tipas:</label>
            <select name="type"
                    class="w-full px-4 py-2 rounded border border-gray-300 bg-white text-gray-900 dark:bg-gray-700 dark:text-white dark:border-gray-700" required>
                <option value="income" @selected(old('type', $category->type) === 'income')>Pajamos</option>
                <option value="expense" @selected(old('type', $category->type) === 'expense')>Išlaidos</option>
            </select>
        </div>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Išsaugoti</button>
    </form>
</div>
@endsection
