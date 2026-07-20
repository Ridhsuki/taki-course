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
                'icon' => 'category_icons/web-development.svg',
            ],
            [
                'name' => 'Mobile Development',
                'slug' => 'mobile-development',
                'icon' => 'category_icons/mobile-development.svg',
            ],
            [
                'name' => 'UI UX Design',
                'slug' => 'ui-ux-design',
                'icon' => 'category_icons/ui-ux-design.svg',
            ],
            [
                'name' => 'Data Science',
                'slug' => 'data-science',
                'icon' => 'category_icons/data-science.svg',
            ],
            [
                'name' => 'Cyber Security',
                'slug' => 'cyber-security',
                'icon' => 'category_icons/cyber-security.svg',
            ],
            [
                'name' => 'Digital Marketing',
                'slug' => 'digital-marketing',
                'icon' => 'category_icons/digital-marketing.svg',
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
