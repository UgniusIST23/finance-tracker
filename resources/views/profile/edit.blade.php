@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto p-6 lg:p-8 bg-white dark:bg-gray-800 rounded shadow space-y-6">
    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Redaguoti paskyrą</h2>

    @include('profile.partials.update-profile-information-form')

    @include('profile.partials.update-password-form')

    @include('profile.partials.delete-user-form')
</div>
@endsection
