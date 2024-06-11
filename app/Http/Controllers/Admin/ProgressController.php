<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Progress;
use Illuminate\Support\Facades\Auth;

class ProgressController extends Controller
{
    //
    public function markComplete(Request $request, Course $course, $contentId, $type)
    {
        $user = Auth::user();

        // Find or create the progress record
        $progress = Progress::firstOrCreate(
            [
                'user_id' => $user->id,
                'course_id' => $course->id,
                'content_id' => $contentId,
                'type' => $type,
            ],
            [
                'completed' => false,
                'points' => 0,
            ]
        );

        // Mark the content as completed and allocate points
        $progress->completed = true;
        $progress->points = ($type == 'lesson') ? 10 : 20; // Example points allocation for lesson and quiz
        $progress->save();

        // Check if all lessons/quizzes in the course are completed
        $this->checkCourseCompletion($course, $user);

        return response()->json(['status' => 'success']);
    }

    private function checkCourseCompletion(Course $course, $user)
    {
        $totalLessons = $course->lessons()->count();
        $completedLessons = Progress::where('course_id', $course->id)
            ->where('user_id', $user->id)
            ->where('completed', true)
            ->count();

        if ($totalLessons === $completedLessons) {
            $course->completed = true;
            $course->save();
        }
    }
}
