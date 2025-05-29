@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-gray-800 p-6 rounded shadow text-white">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold">Ištrintos kategorijos</h2>
        <a href="{{ route('categories.index') }}"
           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded text-sm">
            ← Grįžti į sąrašą
        </a>
    </div>

    {{-- Filtravimo forma --}}
    <form method="GET" action="{{ route('categories.trashed') }}" class="mb-6 flex flex-wrap gap-4 items-end text-white">
        <div>
            <label for="type" class="block text-sm mb-1">Tipas</label>
            <select name="type" id="type" onchange="this.form.submit()"
                    class="w-36 px-3 py-2 rounded border dark:bg-gray-700 dark:text-white">
                <option value="">– Visi –</option>
                <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Pajamos</option>
                <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Išlaidos</option>
            </select>
        </div>
        <div>
            <label for="per_page" class="block text-sm mb-1">Įrašų kiekis</label>
            <select name="per_page" id="per_page" onchange="this.form.submit()"
                    class="w-24 px-3 py-2 rounded border dark:bg-gray-700 dark:text-white">
                @foreach ([10, 15, 20, $categories->total()] as $option)
                    <option value="{{ $option }}" {{ request('per_page', 10) == $option ? 'selected' : '' }}>
                        {{ $option === $categories->total() ? 'Visi' : $option }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    {{-- Sėkmės pranešimas --}}
    @if (session('success'))
        <div class="mb-4 text-green-500">
            {{ session('success') }}
        </div>
    @endif

    {{-- Kategorijų sąrašas --}}
    @forelse ($categories as $category)
        <div class="flex justify-between items-center bg-gray-700 px-4 py-2 rounded mb-2">
            <div>
                <strong>{{ $category->name }}</strong>
                ({{ $category->type === 'income' ? 'Pajamos' : 'Išlaidos' }})
            </div>
            <div class="flex gap-2">
                <form action="{{ route('categories.restore', $category->id) }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">
                        Atkurti
                    </button>
                </form>
                <form action="{{ route('categories.forceDelete', $category->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">
                        Trinti visam
                    </button>
                </form>
            </div>
        </div>
    @empty
        <p class="text-gray-400 mt-4">Ištrintų kategorijų nėra.</p>
    @endforelse

    {{-- Puslapiavimas --}}
    <div class="mt-6">
        {{ $categories->withQueryString()->links() }}
    </div>
</div>
@endsection
