<?php

namespace Database\Seeders;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ownerRole = Role::create([
            'name' => 'owner'
        ]);

        $studentRole = Role::create([
            'name' => 'student'
        ]);

        $teacherRole = Role::create([
            'name' => 'teacher'
        ]);

        $userOwner = User::create([
            'name' => 'アドミン',
            'occupation' => 'Educator',
            'avatar' => null,
            'email' => 'owner@gmail.com',
            'password' => bcrypt('password')
        ]);
        $userOwner->assignRole($ownerRole);

        $userTeacher = User::create([
            'name' => 'Teacher Budi',
            'occupation' => 'Web Development Instructor',
            'avatar' => null,
            'email' => 'teacher@gmail.com',
            'password' => bcrypt('password')
        ]);
        $userTeacher->assignRole($teacherRole);

        Teacher::create([
            'user_id' => $userTeacher->id,
            'is_active' => true,
        ]);

        $userStudent = User::create([
            'name' => 'Student Andi',
            'occupation' => 'High School Student',
            'avatar' => null,
            'email' => 'student@gmail.com',
            'password' => bcrypt('password')
        ]);
        $userStudent->assignRole($studentRole);
    }
}
