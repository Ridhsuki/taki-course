<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FrontController extends Controller
{
    public function index()
    {
        $categories = Category::query()
            ->select('id', 'name', 'slug', 'icon')
            ->orderBy('name')
            ->take(4)
            ->get();

        $courses = Course::with(['category', 'teacher', 'students'])->orderByDesc('id')->get();

        return view('front.index', compact('categories', 'courses'));
    }

    public function pricing()
    {
        return view('front.pricing');
    }

    public function checkout()
    {
        return view('front.checkout');
    }

    public function details(Course $course)
    {
        return view('front.details', compact('course'));
    }

    public function learning(Course $course, $courseVideoId)
    {
        $user = Auth::user();
        if (!$user->HasActiveSubscribtion()) {
            return redirect()->route('front.pricing');
        }
        $video = $course->course_videos->firstWhere('id', $courseVideoId);

        $user->courses()->syncWithoutDetaching($course->id);

        return view('front.learning', compact('course', 'video'));
    }
}
