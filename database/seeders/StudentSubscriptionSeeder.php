<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseStudent;
use App\Models\SubscribeTransaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class StudentSubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        $studentRole = Role::firstOrCreate([
            'name' => 'student',
            'guard_name' => 'web',
        ]);

        $activeStudent = $this->createStudent(
            name: 'Student Andi',
            email: 'student@gmail.com',
            occupation: 'Student',
            role: $studentRole
        );

        $pendingStudent = $this->createStudent(
            name: 'Student Siti',
            email: 'student.pending@gmail.com',
            occupation: 'Student',
            role: $studentRole
        );

        $expiredStudent = $this->createStudent(
            name: 'Student Rina',
            email: 'student.expired@gmail.com',
            occupation: 'Student',
            role: $studentRole
        );

        SubscribeTransaction::updateOrCreate(
            [
                'user_id' => $activeStudent->id,
                'proof' => 'proofs/student-active.jpg',
            ],
            [
                'total_amount' => 150000,
                'is_paid' => true,
                'subscription_start_date' => now()->toDateString(),
            ]
        );

        SubscribeTransaction::updateOrCreate(
            [
                'user_id' => $pendingStudent->id,
                'proof' => 'proofs/student-pending.jpg',
            ],
            [
                'total_amount' => 150000,
                'is_paid' => false,
                'subscription_start_date' => null,
            ]
        );

        SubscribeTransaction::updateOrCreate(
            [
                'user_id' => $expiredStudent->id,
                'proof' => 'proofs/student-expired.jpg',
            ],
            [
                'total_amount' => 150000,
                'is_paid' => true,
                'subscription_start_date' => now()
                    ->subMonths(2)
                    ->toDateString(),
            ]
        );

        Course::query()
            ->pluck('id')
            ->each(function (int $courseId) use ($activeStudent): void {
                CourseStudent::updateOrCreate([
                    'user_id' => $activeStudent->id,
                    'course_id' => $courseId,
                ]);
            });

        Course::query()
            ->limit(2)
            ->pluck('id')
            ->each(function (int $courseId) use ($expiredStudent): void {
                CourseStudent::updateOrCreate([
                    'user_id' => $expiredStudent->id,
                    'course_id' => $courseId,
                ]);
            });
    }

    private function createStudent(
        string $name,
        string $email,
        string $occupation,
        Role $role
    ): User {
        $student = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'occupation' => $occupation,
                'avatar' => null,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );

        $student->syncRoles([$role]);

        return $student;
    }
}
