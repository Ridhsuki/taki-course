<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\CourseVideo;
use App\Models\User;

class CourseVideoPolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('owner')) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can create course videos for the course.
     */
    public function create(User $user, Course $course): bool
    {
        return $user->hasRole('teacher') && $course->teacher?->user_id === $user->id;
    }

    /**
     * Determine whether the user can update the course video.
     */
    public function update(User $user, CourseVideo $courseVideo): bool
    {
        return $user->hasRole('teacher') && $courseVideo->course?->teacher?->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the course video.
     */
    public function delete(User $user, CourseVideo $courseVideo): bool
    {
        return $user->hasRole('teacher') && $courseVideo->course?->teacher?->user_id === $user->id;
    }
}
