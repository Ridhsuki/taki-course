<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Web Development',
                'slug' => 'web-development',
                'icon' => null,
            ],
            [
                'name' => 'Mobile Development',
                'slug' => 'mobile-development',
                'icon' => null,
            ],
            [
                'name' => 'UI UX Design',
                'slug' => 'ui-ux-design',
                'icon' => null,
            ],
            [
                'name' => 'Data Science',
                'slug' => 'data-science',
                'icon' => null,
            ],
            [
                'name' => 'Cyber Security',
                'slug' => 'cyber-security',
                'icon' => null,
            ],
            [
                'name' => 'Digital Marketing',
                'slug' => 'digital-marketing',
                'icon' => null,
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
