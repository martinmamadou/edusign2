<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Cours</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Liste des Cours</h1>
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
                <div class="text-sm text-gray-500">
                    <p>Début: {{ \Carbon\Carbon::parse($course->start_time)->format('d/m/Y H:i') }}</p>
                    <p>Fin: {{ \Carbon\Carbon::parse($course->end_time)->format('d/m/Y H:i') }}</p>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Modal de création de cours -->
        <div id="createCourseModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
            <div class="flex items-center justify-center min-h-screen">
                <div class="bg-white rounded-lg p-8 max-w-md w-full">
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

    <script>
        function showCreateForm() {
            document.getElementById('createCourseModal').classList.remove('hidden');
        }

        function hideCreateForm() {
            document.getElementById('createCourseModal').classList.add('hidden');
        }
    </script>
</body>
</html> 