@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 p-6 rounded shadow">
    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">Kategorijų sąrašas</h2>

    @if (session('success'))
        <div class="mb-4 text-green-600 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('categories.create') }}" class="inline-block mb-4 text-blue-600 dark:text-blue-400 hover:underline">
        Nauja kategorija
    </a>

    @forelse ($categories as $category)
        <div class="flex justify-between items-center bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2 rounded mb-2">
            <div>
                <strong>{{ $category->name }}</strong> ({{ $category->type === 'income' ? 'Pajamos' : 'Išlaidos' }})
            </div>
            <div class="flex gap-2">
                <a href="{{ route('categories.edit', $category) }}" class="text-yellow-500 hover:text-yellow-600">Redaguoti</a>
                <form action="{{ route('categories.destroy', $category) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-700">Trinti</button>
                </form>
            </div>
        </div>
    @empty
        <p class="text-gray-600 dark:text-gray-300">Neturi sukurtų kategorijų.</p>
    @endforelse
</div>
@endsection
