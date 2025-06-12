@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white dark:bg-gray-800 p-6 rounded shadow text-gray-900 dark:text-white">

    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold">Kategorijų sąrašas</h2>
        <a href="{{ route('categories.trashed') }}"
           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded text-sm">
            Ištrintos kategorijos
        </a>
    </div>

    <form method="GET" action="{{ route('categories.index') }}" class="mb-6 flex flex-wrap gap-4 items-end text-gray-900 dark:text-white">
        <div>
            <label for="search" class="block text-sm mb-1">Paieška</label>
            <input type="text" name="search" id="search" value="{{ request('search') }}"
                   placeholder="Ieškoti kategorijos..."
                   class="w-48 px-3 py-2 rounded border border-gray-300 bg-white text-gray-900 dark:bg-gray-700 dark:text-white dark:border-gray-700">
        </div>
        <div>
            <label for="type" class="block text-sm mb-1">Tipas</label>
            <select name="type" id="type"
                    class="w-36 px-3 py-2 rounded border border-gray-300 bg-white text-gray-900 dark:bg-gray-700 dark:text-white dark:border-gray-700">
                <option value="">– Visi –</option>
                <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Pajamos</option>
                <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Išlaidos</option>
            </select>
        </div>
        <div>
            <label for="per_page" class="block text-sm mb-1">Įrašų kiekis</label>
            <select name="per_page" id="per_page"
                    class="w-24 px-3 py-2 rounded border border-gray-300 bg-white text-gray-900 dark:bg-gray-700 dark:text-white dark:border-gray-700">
                <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15</option>
                <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>20</option>
                <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>Visi</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                Filtruoti
            </button>
            <a href="{{ route('categories.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                Valyti filtrus
            </a>
        </div>
    </form>

    @if (session('success'))
        <div class="mb-4 text-green-500">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('categories.create') }}" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-500 mb-4 inline-block">
        Nauja kategorija
    </a>

    @forelse ($categories as $category)
        <div class="flex justify-between items-center bg-white dark:bg-gray-700 px-4 py-2 rounded shadow mb-2 text-gray-900 dark:text-white">
            <div>
                <strong>{{ $category->name }}</strong>
                ({{ $category->type === 'income' ? 'Pajamos' : 'Išlaidos' }})
            </div>
            <div class="flex gap-2">
                <a href="{{ route('categories.edit', $category) }}"
                   class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm">Redaguoti</a>
                <button type="button"
                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm"
                        onclick="openDeleteModal({{ $category->id }}, '{{ $category->name }}')">
                    Trinti
                </button>
            </div>
        </div>
    @empty
        <p class="text-gray-600 dark:text-gray-400 mt-4">Neturi sukurtų kategorijų.</p>
    @endforelse

    <div class="mt-6">
        {{ $categories->withQueryString()->links() }}
    </div>
</div>

<!-- Modal langas -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-60 hidden flex items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg max-w-lg w-full p-6 shadow-lg">
        <h2 class="text-xl font-semibold mb-4">Ar tikrai norite ištrinti kategoriją?</h2>
        <p id="modalCategoryName" class="mb-2 font-medium"></p>
        <form method="POST" action="{{ route('categories.destroy', 0) }}" id="deleteForm">
            @csrf
            @method('DELETE')
            <input type="hidden" name="category_id" id="modalCategoryId">
            <label class="flex items-center gap-2 mb-4">
                <input type="checkbox" name="delete_transactions" class="rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                Taip, noriu ištrinti ir su šia kategorija susijusias transakcijas
            </label>
            <div id="modalTransactions" class="max-h-48 overflow-y-auto bg-gray-100 dark:bg-gray-700 text-sm p-3 rounded mb-4 text-gray-800 dark:text-gray-300">
                Transakcijų sąrašas kraunamas...
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeDeleteModal()"
                        class="px-4 py-2 bg-gray-400 hover:bg-gray-500 text-white rounded">Atšaukti</button>
                <button type="submit"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded">Trinti</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openDeleteModal(categoryId, categoryName) {
        document.getElementById('deleteModal').classList.remove('hidden');
        document.getElementById('modalCategoryName').innerText = 'Kategorija: ' + categoryName;
        document.getElementById('modalCategoryId').value = categoryId;

        const form = document.getElementById('deleteForm');
        form.action = `{{ url('categories') }}/${categoryId}`;

        fetch(`{{ url('api/categories') }}/${categoryId}/transactions`)
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('modalTransactions');
                if (data.length === 0) {
                    container.innerHTML = '<p>Susijusių transakcijų nėra.</p>';
                } else {
                    container.innerHTML = '<ul class="list-disc list-inside space-y-1">' +
                        data.map(t => `<li>${t.date}: ${t.amount} ${t.currency} - ${t.description || 'be aprašymo'}</li>`).join('') +
                        '</ul>';
                }
            });
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }
</script>

@endsection
