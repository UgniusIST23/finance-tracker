@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- Sveikinimas --}}
        <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 p-6 rounded-lg shadow text-white">
            <h2 class="text-2xl font-bold mb-2">Sveiki sugrįžę į savo finansų valdymo sistemą!</h2>
            <p class="text-sm text-white/80">Jūs sėkmingai prisijungėte prie sistemos, čia galite valdyti savo pajamas, išlaidas ir peržiūrėti ataskaitas.</p>
        </div>

        {{-- Greitos instrukcijos --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">

            {{-- Kategorijos --}}
            <div class="bg-gray-800 rounded-lg p-5 shadow text-white">
                <h3 class="font-semibold text-lg mb-2">📁 Kategorijų valdymas</h3>
                <ul class="list-disc list-inside space-y-1 text-gray-300">
                    <li>Eikite į <strong>Kategorijos</strong> meniu.</li>
                    <li>Kurti kategoriją: mygtukas <em>Nauja kategorija</em></li>
                    <li>Redaguoti ar šalinti – šalia kiekvieno kategorijos</li>
                    <li>Laikinai ištrintos kategorijos žymimos geltonai</li>
                    <li>Pilnai ištrintos kategorijos žymimos raudonai</li>
                </ul>
            </div>

            {{-- Transakcijos --}}
            <div class="bg-gray-800 rounded-lg p-5 shadow text-white">
                <h3 class="font-semibold text-lg mb-2">💸 Transakcijų valdymas</h3>
                <ul class="list-disc list-inside space-y-1 text-gray-300">
                    <li>Pridėti naują: <strong>Transakcijos > Nauja transakcija</strong></li>
                    <li>Pasirinkite kategoriją, sumą, datą bei aprašymą</li>
                    <li>Redaguoti ir šalinti transakcijas galima sąraše</li>
                </ul>
            </div>

            {{-- Ataskaitos --}}
            <div class="bg-gray-800 rounded-lg p-5 shadow text-white">
                <h3 class="font-semibold text-lg mb-2">📊 Finansinės ataskaitos</h3>
                <ul class="list-disc list-inside space-y-1 text-gray-300">
                    <li>Filtruokite pagal datą ar įrašų kiekį</li>
                    <li>Matykite pajamas, išlaidas, balansą</li>
                    <li>Matykite pajamas bei išlaidas pagal kategorijas</li>
                    <li>Analizuokite mažiausias, didžiausias bei vidutines transakcijų sumas</li>
                    <li>Analizuokite grafikus</li>
                </ul>
            </div>
        </div>

        {{-- Papildomas patarimas --}}
        <div class="bg-yellow-100 dark:bg-yellow-800 text-yellow-900 dark:text-yellow-100 rounded p-4 text-sm shadow">
            <strong>Patarimas:</strong> jei kuriate naują kategoriją ar transakciją ir jos nematote – patikrinkite ar filtrai ataskaitose nėra aktyvūs!
        </div>

    </div>
</div>
@endsection
