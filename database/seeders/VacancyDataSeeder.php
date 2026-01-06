<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Vacancy;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VacancyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Define dummy vacancy data
        $vacancies = [
            // [
            //     'name' => 'Vacancy 1',
            //     'code' => 'VAC001',
            //     'start_date' => '2024-05-15',
            //     'end_date' => '2024-06-15',
            //     'posts' => [
            //         [
            //             'name' => 'Post 1A',
            //             'code' => 'POST1A',
            //         ],
            //         [
            //             'name' => 'Post 1B',
            //             'code' => 'POST1B',
            //         ],
            //     ],
            // ],
            // [
            //     'name' => 'Vacancy 2',
            //     'code' => 'VAC002',
            //     'start_date' => '2025-01-01',
            //     'end_date' => '2025-07-01',
            //     'posts' => [
            //         [
            //             'name' => 'Post 2A',
            //             'code' => 'POST2A',
            //         ],
            //         [
            //             'name' => 'Post 2B',
            //             'code' => 'POST2B',
            //         ],
            //     ],
            // ],
        ];

        // Store the data
        foreach ($vacancies as $vacancyData) {
            $posts = $vacancyData['posts'];
            unset($vacancyData['posts']);

            // Create the vacancy
            $vacancy = Vacancy::create($vacancyData);

            // Create the posts for this vacancy
            foreach ($posts as $postData) {
                $postData['vacancy_id'] = $vacancy->id;
                Post::create($postData);
            }
        }
    }
}