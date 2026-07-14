<?php

namespace Database\Seeders;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $ownerRole = Role::firstOrCreate([
            'name' => 'owner',
            'guard_name' => 'web',
        ]);

        $teacherRole = Role::firstOrCreate([
            'name' => 'teacher',
            'guard_name' => 'web',
        ]);

        $studentRole = Role::firstOrCreate([
            'name' => 'student',
            'guard_name' => 'web',
        ]);

        $owner = User::updateOrCreate(
            ['email' => 'owner@gmail.com'],
            [
                'name' => 'Admin Taki Course',
                'occupation' => 'Owner',
                'avatar' => null,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );
        $owner->syncRoles([$ownerRole]);

        $teacherUser = User::updateOrCreate(
            ['email' => 'teacher@gmail.com'],
            [
                'name' => 'Teacher Budi',
                'occupation' => 'Web Development Instructor',
                'avatar' => null,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );
        $teacherUser->syncRoles([$teacherRole]);

        Teacher::updateOrCreate(
            ['user_id' => $teacherUser->id],
            ['is_active' => true]
        );

        $student = User::updateOrCreate(
            ['email' => 'student@gmail.com'],
            [
                'name' => 'Student Andi',
                'occupation' => 'Student',
                'avatar' => null,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );
        $student->syncRoles([$studentRole]);
    }
}
