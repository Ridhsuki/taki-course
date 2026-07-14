<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseKeypoint;
use App\Models\CourseVideo;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $teacherUserId = User::query()
            ->where('email', 'teacher@gmail.com')
            ->value('id');

        $teacher = Teacher::query()
            ->where('user_id', $teacherUserId)
            ->first();

        if (!$teacher) {
            throw new RuntimeException(
                'Teacher tidak ditemukan. Jalankan RolePermissionSeeder terlebih dahulu.'
            );
        }

        $categoryIds = Category::query()
            ->pluck('id', 'slug');

        $courses = $this->courses();

        foreach ($courses as $courseData) {
            $categorySlug = $courseData['category_slug'];

            if (!isset($categoryIds[$categorySlug])) {
                throw new RuntimeException(
                    "Category {$categorySlug} tidak ditemukan."
                );
            }

            $course = Course::updateOrCreate(
                ['slug' => $courseData['slug']],
                [
                    'name' => $courseData['name'],
                    'path_trailer' => $courseData['path_trailer'],
                    'about' => $courseData['about'],
                    'thumbnail' => $courseData['thumbnail'],
                    'teacher_id' => $teacher->id,
                    'category_id' => $categoryIds[$categorySlug],
                ]
            );

            foreach ($courseData['keypoints'] as $keypoint) {
                CourseKeypoint::updateOrCreate([
                    'course_id' => $course->id,
                    'name' => $keypoint,
                ]);
            }

            foreach ($courseData['videos'] as $video) {
                CourseVideo::updateOrCreate(
                    [
                        'course_id' => $course->id,
                        'name' => $video['name'],
                    ],
                    [
                        'path_video' => $video['path_video'],
                    ]
                );
            }
        }
    }

    private function courses(): array
    {
        return [
            [
                'name' => 'Laravel 11 untuk Pemula',
                'slug' => 'laravel-11-untuk-pemula',
                'path_trailer' => 'eUNWzJUvkCA',
                'about' => 'Mempelajari dasar Laravel 11 mulai dari routing, controller, Blade, database, autentikasi, hingga deployment.',
                'thumbnail' => null,
                'category_slug' => 'web-development',
                'keypoints' => [
                    'Memahami struktur project Laravel',
                    'Membuat routing dan controller',
                    'Menggunakan Blade template',
                    'Mengelola database dengan Eloquent',
                    'Membangun autentikasi dasar',
                ],
                'videos' => [
                    [
                        'name' => 'Pengenalan Laravel',
                        'path_video' => 'eUNWzJUvkCA',
                    ],
                    [
                        'name' => 'Routing dan Controller',
                        'path_video' => 'MFh0Fd7BsjE',
                    ],
                    [
                        'name' => 'Eloquent ORM',
                        'path_video' => 'ImtZ5yENzgE',
                    ],
                ],
            ],
            [
                'name' => 'React Native dan Expo',
                'slug' => 'react-native-dan-expo',
                'path_trailer' => 'obH0Po_RdWk',
                'about' => 'Membangun aplikasi mobile lintas platform menggunakan React Native dan Expo.',
                'thumbnail' => null,
                'category_slug' => 'mobile-development',
                'keypoints' => [
                    'Memahami komponen React Native',
                    'Mengatur navigation',
                    'Mengelola state aplikasi',
                    'Menghubungkan aplikasi dengan API',
                    'Membuat build aplikasi',
                ],
                'videos' => [
                    [
                        'name' => 'Dasar React Native',
                        'path_video' => 'obH0Po_RdWk',
                    ],
                    [
                        'name' => 'React Navigation',
                        'path_video' => 'nQVCkqvU1uE',
                    ],
                    [
                        'name' => 'Expo Fundamentals',
                        'path_video' => '0-S5a0eXPoc',
                    ],
                ],
            ],
            [
                'name' => 'UI UX Design dengan Figma',
                'slug' => 'ui-ux-design-dengan-figma',
                'path_trailer' => 'FTFaQWZBqQ8',
                'about' => 'Mempelajari proses desain antarmuka dan pengalaman pengguna dari wireframe hingga prototype.',
                'thumbnail' => null,
                'category_slug' => 'ui-ux-design',
                'keypoints' => [
                    'Memahami prinsip UI dan UX',
                    'Membuat wireframe',
                    'Menggunakan auto layout',
                    'Membuat component dan variant',
                    'Membangun prototype interaktif',
                ],
                'videos' => [
                    [
                        'name' => 'Figma untuk Pemula',
                        'path_video' => 'FTFaQWZBqQ8',
                    ],
                    [
                        'name' => 'Auto Layout',
                        'path_video' => 'TyaGpGDFczw',
                    ],
                    [
                        'name' => 'Prototype Interaktif',
                        'path_video' => '1pW_sk-2y40',
                    ],
                ],
            ],
            [
                'name' => 'Python Data Analysis',
                'slug' => 'python-data-analysis',
                'path_trailer' => 'vmEHCJofslg',
                'about' => 'Mengolah, membersihkan, menganalisis, dan memvisualisasikan data menggunakan Python dan Pandas.',
                'thumbnail' => null,
                'category_slug' => 'data-science',
                'keypoints' => [
                    'Menggunakan Python untuk analisis data',
                    'Mengolah data dengan Pandas',
                    'Membersihkan data',
                    'Membuat visualisasi',
                    'Menyusun insight dari data',
                ],
                'videos' => [
                    [
                        'name' => 'Pandas untuk Pemula',
                        'path_video' => 'vmEHCJofslg',
                    ],
                    [
                        'name' => 'Data Cleaning',
                        'path_video' => 'bDhvCp3_lYw',
                    ],
                    [
                        'name' => 'Data Visualization',
                        'path_video' => 'UO98lJQ3QGI',
                    ],
                ],
            ],
            [
                'name' => 'Cyber Security Fundamentals',
                'slug' => 'cyber-security-fundamentals',
                'path_trailer' => 'U_P23SqJaDc',
                'about' => 'Memahami konsep dasar keamanan siber, jaringan, ancaman, autentikasi, dan praktik perlindungan sistem.',
                'thumbnail' => null,
                'category_slug' => 'cyber-security',
                'keypoints' => [
                    'Memahami dasar keamanan siber',
                    'Mengenali jenis serangan umum',
                    'Memahami keamanan jaringan',
                    'Menerapkan autentikasi yang aman',
                    'Mengurangi risiko keamanan aplikasi',
                ],
                'videos' => [
                    [
                        'name' => 'Dasar Cyber Security',
                        'path_video' => 'U_P23SqJaDc',
                    ],
                    [
                        'name' => 'Network Security',
                        'path_video' => 'qiQR5rTSshw',
                    ],
                    [
                        'name' => 'Web Security Basics',
                        'path_video' => 'WlmKwIe9z1Q',
                    ],
                ],
            ],
            [
                'name' => 'Digital Marketing dan SEO',
                'slug' => 'digital-marketing-dan-seo',
                'path_trailer' => 'xsVTqzratPs',
                'about' => 'Mempelajari strategi digital marketing, content marketing, SEO, dan pengukuran performa kampanye.',
                'thumbnail' => null,
                'category_slug' => 'digital-marketing',
                'keypoints' => [
                    'Memahami funnel digital marketing',
                    'Membuat strategi konten',
                    'Menerapkan SEO dasar',
                    'Mengelola kampanye digital',
                    'Menganalisis performa marketing',
                ],
                'videos' => [
                    [
                        'name' => 'Digital Marketing Fundamentals',
                        'path_video' => 'xsVTqzratPs',
                    ],
                    [
                        'name' => 'SEO untuk Pemula',
                        'path_video' => 'MYE6T_gd7H0',
                    ],
                    [
                        'name' => 'Content Marketing',
                        'path_video' => '0R_3iarc8IA',
                    ],
                ],
            ],
        ];
    }
}
