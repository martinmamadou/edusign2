<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Log::info('Tentative de récupération des cours');
        $courses = Course::all();
        Log::info('Cours trouvés:', ['courses' => $courses->toArray()]);
        return response()->json($courses);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'students' => 'required|array',
            'students.*' => 'exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $data = $request->except('students');
            $data['qr_code'] = Str::uuid()->toString();

            $course = Course::create($data);
            
            // Attacher les élèves au cours
            $course->users()->attach($request->students);

            return response()->json([
                'message' => 'Cours créé avec succès',
                'course' => $course
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la création du cours',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        $students = $course->users()->get()->map(function($user) {
            return [
                'id' => $user->id,
                'name' => $user->firstname . ' ' . $user->lastname,
                'signed' => $user->pivot->signed ?? false
            ];
        });

        return view('courses.show', compact('course', 'students'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'sometimes|required|date',
            'end_time' => 'sometimes|required|date|after:start_time',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $course->update($request->all());
        return response()->json(['course' => $course]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course): JsonResponse
    {
        $course->delete();
        return response()->json(null, 204);
    }

    public function generateNewQrCode(Course $course): JsonResponse
    {
        $course->update(['qr_code' => Str::uuid()->toString()]);
        return response()->json(['course' => $course]);
    }
}
