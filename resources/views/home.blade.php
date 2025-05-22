@php
use Illuminate\Support\Facades\Auth;
@endphp

@extends('layouts.app')

@section('title', 'Accueil')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="border-4 border-dashed border-gray-200 rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Bienvenue, {{ Auth::user()->firstname }} {{ Auth::user()->lastname }}!</h2>
            <button onclick="showCreateForm()" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                Créer un nouveau cours
            </button>
        </div>

        <!-- Liste des cours -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($courses as $course)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-2">{{ $course->title }}</h2>
                <p class="text-gray-600 mb-4">{{ $course->description }}</p>
                <div class="text-sm text-gray-500 mb-4">
                    <p>Début: {{ \Carbon\Carbon::parse($course->start_time)->format('d/m/Y H:i') }}</p>
                    <p>Fin: {{ \Carbon\Carbon::parse($course->end_time)->format('d/m/Y H:i') }}</p>
                </div>
                <a href="{{ route('courses.show', $course) }}" class="inline-block bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                    Voir les détails
                </a>
            </div>
            @endforeach
        </div>

        <!-- Modal de création de cours -->
        <div id="createCourseModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white rounded-lg p-8 max-w-md w-full max-h-[90vh] overflow-y-auto">
                    <h2 class="text-2xl font-bold mb-6">Créer un nouveau cours</h2>
                    <form action="{{ route('courses.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="title" class="block text-sm font-medium text-gray-700">Titre</label>
                            <input type="text" name="title" id="title" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" id="description" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        </div>
                        <div class="mb-4">
                            <label for="start_time" class="block text-sm font-medium text-gray-700">Date de début</label>
                            <input type="datetime-local" name="start_time" id="start_time" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div class="mb-6">
                            <label for="end_time" class="block text-sm font-medium text-gray-700">Date de fin</label>
                            <input type="datetime-local" name="end_time" id="end_time" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div class="mb-6">
                            <label for="students" class="block text-sm font-medium text-gray-700">Élèves</label>
                            <select name="students[]" id="students" multiple required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach(\App\Models\User::all() as $user)
                                    <option value="{{ $user->id }}">{{ $user->firstname }} {{ $user->lastname }}</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-sm text-gray-500">Maintenez Ctrl (ou Cmd sur Mac) pour sélectionner plusieurs élèves</p>
                        </div>
                        <div class="flex justify-end space-x-4">
                            <button type="button" onclick="hideCreateForm()"
                                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                Annuler
                            </button>
                            <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                Créer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function showCreateForm() {
        document.getElementById('createCourseModal').classList.remove('hidden');
    }

    function hideCreateForm() {
        document.getElementById('createCourseModal').classList.add('hidden');
    }

    // Gestion de la soumission du formulaire
    document.querySelector('form[action="{{ route('courses.store') }}"]').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = {};
        
        // Convertir FormData en objet
        for (let [key, value] of formData.entries()) {
            if (key === 'students[]') {
                if (!data.students) {
                    data.students = [];
                }
                data.students.push(value);
            } else {
                data[key] = value;
            }
        }
        
        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.errors) {
                // Afficher les erreurs
                alert('Erreur: ' + Object.values(data.errors).flat().join('\n'));
            } else {
                // Rediriger vers la page d'accueil
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Une erreur est survenue lors de la création du cours');
        });
    });
</script>
@endpush 