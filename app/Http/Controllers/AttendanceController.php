<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $attendances = Attendance::with(['user', 'course'])->get();
        return response()->json(['attendances' => $attendances]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $attendance = Attendance::create($request->all());
        return response()->json(['attendance' => $attendance], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Attendance $attendance): JsonResponse
    {
        return response()->json(['attendance' => $attendance->load(['user', 'course'])]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attendance $attendance): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'signed' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        if ($data['signed']) {
            $data['signed_at'] = now();
        }

        $attendance->update($data);
        return response()->json(['attendance' => $attendance]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendance $attendance): JsonResponse
    {
        $attendance->delete();
        return response()->json(null, 204);
    }

    public function signAttendance(Request $request, Course $course): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'qr_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($course->qr_code !== $request->qr_code) {
            return response()->json(['error' => 'QR code invalide'], 422);
        }

        $attendance = Attendance::where('user_id', auth()->id())
            ->where('course_id', $course->id)
            ->first();

        if (!$attendance) {
            return response()->json(['error' => 'Présence non trouvée'], 404);
        }

        // Mettre à jour uniquement le champ signed
        $attendance->update([
            'signed' => true,
            'signed_at' => now()
        ]);

        return response()->json([
            'message' => 'Présence mise à jour',
            'attendance' => $attendance
        ]);
    }
}
