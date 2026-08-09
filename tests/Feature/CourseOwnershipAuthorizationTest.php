<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseVideo;
use App\Models\Teacher;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseOwnershipAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected User $teacherUser1;

    protected Teacher $teacher1;

    protected User $teacherUser2;

    protected Teacher $teacher2;

    protected User $ownerUser;

    protected User $studentUser;

    protected Category $category;

    protected Course $course1;

    protected Course $course2;

    protected CourseVideo $video1;

    protected CourseVideo $video2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);

        // Teacher 1 setup
        $this->teacherUser1 = User::factory()->create([
            'email' => 'teacher1@example.com',
            'occupation' => 'Instructor 1',
        ]);
        $this->teacherUser1->assignRole('teacher');
        $this->teacher1 = Teacher::create([
            'user_id' => $this->teacherUser1->id,
            'is_active' => true,
        ]);

        // Teacher 2 setup
        $this->teacherUser2 = User::factory()->create([
            'email' => 'teacher2@example.com',
            'occupation' => 'Instructor 2',
        ]);
        $this->teacherUser2->assignRole('teacher');
        $this->teacher2 = Teacher::create([
            'user_id' => $this->teacherUser2->id,
            'is_active' => true,
        ]);

        // Owner setup
        $this->ownerUser = User::factory()->create([
            'email' => 'adminowner@example.com',
            'occupation' => 'Platform Owner',
        ]);
        $this->ownerUser->assignRole('owner');

        // Student setup
        $this->studentUser = User::factory()->create([
            'email' => 'studentuser@example.com',
            'occupation' => 'Student',
        ]);
        $this->studentUser->assignRole('student');

        // Category setup
        $this->category = Category::create([
            'name' => 'Web Development',
            'slug' => 'web-development',
            'icon' => 'icons/web.png',
        ]);

        // Course 1 (owned by Teacher 1)
        $this->course1 = Course::create([
            'name' => 'Course One',
            'slug' => 'course-one',
            'path_trailer' => 'trailer1.mp4',
            'about' => 'About course one',
            'thumbnail' => 'thumbnails/1.jpg',
            'teacher_id' => $this->teacher1->id,
            'category_id' => $this->category->id,
        ]);

        // Course 2 (owned by Teacher 2)
        $this->course2 = Course::create([
            'name' => 'Course Two',
            'slug' => 'course-two',
            'path_trailer' => 'trailer2.mp4',
            'about' => 'About course two',
            'thumbnail' => 'thumbnails/2.jpg',
            'teacher_id' => $this->teacher2->id,
            'category_id' => $this->category->id,
        ]);

        // Video 1 in Course 1
        $this->video1 = CourseVideo::create([
            'name' => 'Video One',
            'path_video' => 'video1.mp4',
            'course_id' => $this->course1->id,
        ]);

        // Video 2 in Course 2
        $this->video2 = CourseVideo::create([
            'name' => 'Video Two',
            'path_video' => 'video2.mp4',
            'course_id' => $this->course2->id,
        ]);
    }

    // --- Course Authorization Tests ---

    public function test_teacher_can_view_own_course(): void
    {
        $response = $this->actingAs($this->teacherUser1)
            ->get(route('admin.courses.show', $this->course1));

        $response->assertOk();
    }

    public function test_teacher_cannot_view_another_teachers_course(): void
    {
        $response = $this->actingAs($this->teacherUser1)
            ->get(route('admin.courses.show', $this->course2));

        $response->assertForbidden();
    }

    public function test_teacher_can_edit_own_course(): void
    {
        $response = $this->actingAs($this->teacherUser1)
            ->get(route('admin.courses.edit', $this->course1));

        $response->assertOk();
    }

    public function test_teacher_cannot_edit_another_teachers_course(): void
    {
        $response = $this->actingAs($this->teacherUser1)
            ->get(route('admin.courses.edit', $this->course2));

        $response->assertForbidden();
    }

    public function test_teacher_can_update_own_course(): void
    {
        $response = $this->actingAs($this->teacherUser1)
            ->put(route('admin.courses.update', $this->course1), [
                'name' => 'Course One Updated',
                'path_trailer' => 'trailer1_updated.mp4',
                'about' => 'Updated about section',
                'category_id' => $this->category->id,
                'course_keypoints' => ['Keypoint 1'],
            ]);

        $response->assertRedirect(route('admin.courses.show', $this->course1));
        $this->assertDatabaseHas('courses', [
            'id' => $this->course1->id,
            'name' => 'Course One Updated',
        ]);
    }

    public function test_teacher_cannot_update_another_teachers_course(): void
    {
        $response = $this->actingAs($this->teacherUser1)
            ->put(route('admin.courses.update', $this->course2), [
                'name' => 'Course Two Hacked',
                'path_trailer' => 'trailer2_hacked.mp4',
                'about' => 'Hacked about section',
                'category_id' => $this->category->id,
                'course_keypoints' => ['Hacked Keypoint'],
            ]);

        $response->assertForbidden();
        $this->assertDatabaseHas('courses', [
            'id' => $this->course2->id,
            'name' => 'Course Two',
        ]);
    }

    public function test_teacher_can_delete_own_course(): void
    {
        $response = $this->actingAs($this->teacherUser1)
            ->delete(route('admin.courses.destroy', $this->course1));

        $response->assertRedirect(route('admin.courses.index'));
        $this->assertSoftDeleted('courses', [
            'id' => $this->course1->id,
        ]);
    }

    public function test_teacher_cannot_delete_another_teachers_course(): void
    {
        $response = $this->actingAs($this->teacherUser1)
            ->delete(route('admin.courses.destroy', $this->course2));

        $response->assertForbidden();
        $this->assertDatabaseHas('courses', [
            'id' => $this->course2->id,
            'deleted_at' => null,
        ]);
    }

    // --- CourseVideo Authorization Tests ---

    public function test_teacher_can_access_add_video_form_for_own_course(): void
    {
        $response = $this->actingAs($this->teacherUser1)
            ->get(route('admin.course.add_video', $this->course1->id));

        $response->assertOk();
    }

    public function test_teacher_cannot_access_add_video_form_for_another_teachers_course(): void
    {
        $response = $this->actingAs($this->teacherUser1)
            ->get(route('admin.course.add_video', $this->course2->id));

        $response->assertForbidden();
    }

    public function test_teacher_can_store_video_for_own_course(): void
    {
        $response = $this->actingAs($this->teacherUser1)
            ->post(route('admin.course.add_video.save', $this->course1->id), [
                'name' => 'New Lesson 1',
                'path_video' => 'lesson1.mp4',
            ]);

        $response->assertRedirect(route('admin.courses.show', $this->course1->id));
        $this->assertDatabaseHas('course_videos', [
            'name' => 'New Lesson 1',
            'course_id' => $this->course1->id,
        ]);
    }

    public function test_teacher_cannot_store_video_for_another_teachers_course(): void
    {
        $response = $this->actingAs($this->teacherUser1)
            ->post(route('admin.course.add_video.save', $this->course2->id), [
                'name' => 'Malicious Lesson',
                'path_video' => 'malicious.mp4',
            ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('course_videos', [
            'name' => 'Malicious Lesson',
            'course_id' => $this->course2->id,
        ]);
    }

    public function test_teacher_can_edit_video_of_own_course(): void
    {
        $response = $this->actingAs($this->teacherUser1)
            ->get(route('admin.course_videos.edit', $this->video1));

        $response->assertOk();
    }

    public function test_teacher_cannot_edit_video_of_another_teachers_course(): void
    {
        $response = $this->actingAs($this->teacherUser1)
            ->get(route('admin.course_videos.edit', $this->video2));

        $response->assertForbidden();
    }

    public function test_teacher_can_update_video_of_own_course(): void
    {
        $response = $this->actingAs($this->teacherUser1)
            ->put(route('admin.course_videos.update', $this->video1), [
                'name' => 'Video One Updated',
                'path_video' => 'video1_updated.mp4',
            ]);

        $response->assertRedirect(route('admin.courses.show', $this->course1->id));
        $this->assertDatabaseHas('course_videos', [
            'id' => $this->video1->id,
            'name' => 'Video One Updated',
        ]);
    }

    public function test_teacher_cannot_update_video_of_another_teachers_course(): void
    {
        $response = $this->actingAs($this->teacherUser1)
            ->put(route('admin.course_videos.update', $this->video2), [
                'name' => 'Video Two Hacked',
                'path_video' => 'video2_hacked.mp4',
            ]);

        $response->assertForbidden();
        $this->assertDatabaseHas('course_videos', [
            'id' => $this->video2->id,
            'name' => 'Video Two',
        ]);
    }

    public function test_teacher_can_delete_video_of_own_course(): void
    {
        $response = $this->actingAs($this->teacherUser1)
            ->delete(route('admin.course_videos.destroy', $this->video1));

        $response->assertRedirect(route('admin.courses.show', $this->course1->id));
        $this->assertSoftDeleted('course_videos', [
            'id' => $this->video1->id,
        ]);
    }

    public function test_teacher_cannot_delete_video_of_another_teachers_course(): void
    {
        $response = $this->actingAs($this->teacherUser1)
            ->delete(route('admin.course_videos.destroy', $this->video2));

        $response->assertForbidden();
        $this->assertDatabaseHas('course_videos', [
            'id' => $this->video2->id,
            'deleted_at' => null,
        ]);
    }

    // --- Owner Authorization Tests ---

    public function test_owner_can_view_edit_update_delete_any_teachers_course(): void
    {
        // View Course 1
        $this->actingAs($this->ownerUser)
            ->get(route('admin.courses.show', $this->course1))
            ->assertOk();

        // Edit Course 1
        $this->actingAs($this->ownerUser)
            ->get(route('admin.courses.edit', $this->course1))
            ->assertOk();

        // Update Course 2 (owned by Teacher 2)
        $this->actingAs($this->ownerUser)
            ->put(route('admin.courses.update', $this->course2), [
                'name' => 'Course Two Owner Updated',
                'path_trailer' => 'trailer2_owner.mp4',
                'about' => 'Updated by owner',
                'category_id' => $this->category->id,
                'course_keypoints' => ['Owner Keypoint'],
            ])
            ->assertRedirect(route('admin.courses.show', $this->course2));

        $this->assertDatabaseHas('courses', [
            'id' => $this->course2->id,
            'name' => 'Course Two Owner Updated',
        ]);

        // Delete Course 1 (owned by Teacher 1)
        $this->actingAs($this->ownerUser)
            ->delete(route('admin.courses.destroy', $this->course1))
            ->assertRedirect(route('admin.courses.index'));

        $this->assertSoftDeleted('courses', [
            'id' => $this->course1->id,
        ]);
    }

    public function test_owner_can_manage_course_videos_of_any_teachers_course(): void
    {
        // Add video form for Course 2
        $this->actingAs($this->ownerUser)
            ->get(route('admin.course.add_video', $this->course2->id))
            ->assertOk();

        // Store video for Course 2
        $this->actingAs($this->ownerUser)
            ->post(route('admin.course.add_video.save', $this->course2->id), [
                'name' => 'Owner Added Video',
                'path_video' => 'owner_video.mp4',
            ])
            ->assertRedirect(route('admin.courses.show', $this->course2->id));

        $this->assertDatabaseHas('course_videos', [
            'name' => 'Owner Added Video',
            'course_id' => $this->course2->id,
        ]);

        // Edit video 2
        $this->actingAs($this->ownerUser)
            ->get(route('admin.course_videos.edit', $this->video2))
            ->assertOk();

        // Update video 2
        $this->actingAs($this->ownerUser)
            ->put(route('admin.course_videos.update', $this->video2), [
                'name' => 'Video Two Updated By Owner',
                'path_video' => 'v2_owner.mp4',
            ])
            ->assertRedirect(route('admin.courses.show', $this->course2->id));

        // Delete video 2
        $this->actingAs($this->ownerUser)
            ->delete(route('admin.course_videos.destroy', $this->video2))
            ->assertRedirect(route('admin.courses.show', $this->course2->id));

        $this->assertSoftDeleted('course_videos', [
            'id' => $this->video2->id,
        ]);
    }

    // --- Student Authorization Test ---

    public function test_student_cannot_access_course_or_video_management_routes(): void
    {
        $this->actingAs($this->studentUser)
            ->get(route('admin.courses.index'))
            ->assertForbidden();

        $this->actingAs($this->studentUser)
            ->get(route('admin.courses.show', $this->course1))
            ->assertForbidden();

        $this->actingAs($this->studentUser)
            ->get(route('admin.course.add_video', $this->course1->id))
            ->assertForbidden();
    }
}
