<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function index()
    {
        $categories = Category::query()
            ->select('id', 'name', 'slug', 'icon')
            ->orderBy('name')->take(4)
            ->get();

        $courses = Course::with(['category', 'teacher', 'students'])->orderByDesc('id')->get();

        return view('front.index', compact('categories', 'courses'));
    }

    public function details(Course $course)
    {
        return view('front.details', compact('course'));
    }
}
