@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-white dark:bg-gray-800 p-6 rounded shadow">
    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">Pridėti naują kategoriją</h2>

    <form action="{{ route('categories.store') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-200 mb-1">Pavadinimas:</label>
            <input type="text" name="name" class="w-full px-4 py-2 rounded border dark:bg-gray-700 dark:text-white" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-200 mb-1">Tipas:</label>
            <select name="type" class="w-full px-4 py-2 rounded border dark:bg-gray-700 dark:text-white" required>
                <option value="income">Pajamos</option>
                <option value="expense">Išlaidos</option>
            </select>
        </div>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Išsaugoti</button>
    </form>
</div>
@endsection
