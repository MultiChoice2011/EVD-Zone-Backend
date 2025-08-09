<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class Categoryseeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        // Category::truncate();


        $ar = DB::table('languages')->where('code', 'ar')->first();
        $en = DB::table('languages')->where('code', 'en')->first();

        $first =  Category::create([
            'parent_id' => null , // Replace with your specific logic for parent_id.
            'image' => 'images/no-image.png', // Set a default image path or generate random paths.
            'level' =>  0,
            'status' => 'active',
            'sort_order' => 1, // Adjust as needed.
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('category_translations')->insert([
            'category_id' => $first->id,
            'language_id' => $ar->id,
            'name' => 'تمور',
            'slug' => 'تمور',
        ]);
        DB::table('category_translations')->insert([
            'category_id' => $first->id,
            'language_id' => $en->id,
            'name' => 'dates',
            'slug' => 'dates',
        ]);


        $first_sub1 =  Category::create([
            'parent_id' => $first->id , // Replace with your specific logic for parent_id.
            'image' => 'images/no-image.png', // Set a default image path or generate random paths.
            'level' =>  1,
            'status' => 'active',
            'sort_order' => 2, // Adjust as needed.
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('category_translations')->insert([
            'category_id' => $first_sub1->id,
            'language_id' => $ar->id,
            'name' => 'تمور خلاص',
            'slug' => 'تمور خلاص',
        ]);
        DB::table('category_translations')->insert([
            'category_id' => $first_sub1->id,
            'language_id' => $en->id,
            'name' => 'dates khalas',
            'slug' => 'dates khalas',
        ]);

        $first_sub1_1 =  Category::create([
            'parent_id' => $first_sub1->id , // Replace with your specific logic for parent_id.
            'image' => 'images/no-image.png', // Set a default image path or generate random paths.
            'level' =>  2,
            'status' => 'active',
            'sort_order' => 10, // Adjust as needed.
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('category_translations')->insert([
            'category_id' => $first_sub1_1->id,
            'language_id' => $ar->id,
            'name' => 'عجوة خلاص',
            'slug' => 'عجوة خلاص',
        ]);
        DB::table('category_translations')->insert([
            'category_id' => $first_sub1_1->id,
            'language_id' => $en->id,
            'name' => 'agwa khalas',
            'slug' => 'agwa khalas',
        ]);

        $first_sub1_2 =  Category::create([
            'parent_id' => $first_sub1->id , // Replace with your specific logic for parent_id.
            'image' => 'images/no-image.png', // Set a default image path or generate random paths.
            'level' =>  2,
            'status' => 'active',
            'sort_order' => 11, // Adjust as needed.
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('category_translations')->insert([
            'category_id' => $first_sub1_2->id,
            'language_id' => $ar->id,
            'name' => 'معمول خلاص',
            'slug' => 'معمول خلاص',
        ]);
        DB::table('category_translations')->insert([
            'category_id' => $first_sub1_2->id,
            'language_id' => $en->id,
            'name' => 'ma3mool khalas',
            'slug' => 'ma3mool khalas',
        ]);

        $first_sub2 =  Category::create([
            'parent_id' => $first->id , // Replace with your specific logic for parent_id.
            'image' => 'images/no-image.png', // Set a default image path or generate random paths.
            'level' =>  1,
            'status' => 'active',
            'sort_order' => 3, // Adjust as needed.
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('category_translations')->insert([
            'category_id' => $first_sub2->id,
            'language_id' => $ar->id,
            'name' => 'تمور سكرى',
            'slug' => 'تمور سكرى',
        ]);
        DB::table('category_translations')->insert([
            'category_id' => $first_sub2->id,
            'language_id' => $en->id,
            'name' => 'dates sokary',
            'slug' => 'dates sokary',
        ]);

        $first_sub2_1 =  Category::create([
            'parent_id' => $first_sub2->id , // Replace with your specific logic for parent_id.
            'image' => 'images/no-image.png', // Set a default image path or generate random paths.
            'level' =>  2,
            'status' => 'active',
            'sort_order' => 13, // Adjust as needed.
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('category_translations')->insert([
            'category_id' => $first_sub2_1->id,
            'language_id' => $ar->id,
            'name' => 'عجوة سكرى',
            'slug' => 'عجوة سكرى',
        ]);
        DB::table('category_translations')->insert([
            'category_id' => $first_sub2_1->id,
            'language_id' => $en->id,
            'name' => 'agwa sokary',
            'slug' => 'agwa sokary',
        ]);

        $first_sub2_2 =  Category::create([
            'parent_id' => $first_sub2->id , // Replace with your specific logic for parent_id.
            'image' => 'images/no-image.png', // Set a default image path or generate random paths.
            'level' =>  2,
            'status' => 'active',
            'sort_order' => 11, // Adjust as needed.
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('category_translations')->insert([
            'category_id' => $first_sub2_2->id,
            'language_id' => $ar->id,
            'name' => 'معمول سكرى',
            'slug' => 'معمول سكرى',
        ]);
        DB::table('category_translations')->insert([
            'category_id' => $first_sub2_2->id,
            'language_id' => $en->id,
            'name' => 'ma3mool sokary',
            'slug' => 'ma3mool sokary',
        ]);



        $second =  Category::create([
            'parent_id' => null , // Replace with your specific logic for parent_id.
            'image' => 'images/no-image.png', // Set a default image path or generate random paths.
            'level' =>  0,
            'status' => 'active',
            'sort_order' => 2, // Adjust as needed.
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('category_translations')->insert([
            'category_id' => $second->id,
            'language_id' => $ar->id,
            'name' => 'منتجات التمور',
            'slug' =>  'منتجات التمور' ,
        ]);
        DB::table('category_translations')->insert([
            'category_id' => $second->id,
            'language_id' => $en->id,
            'name' => 'dates products',
            'slug' => 'dates products',
        ]);

        // $first =  Category::create([
        //     'parent_id' => null , // Replace with your specific logic for parent_id.
        //     'image' => 'images/no-image.png', // Set a default image path or generate random paths.
        //     'level' =>  0,
        //     'status' => 'active',
        //     'sort_order' => 1, // Adjust as needed.
        //     'created_at' => now(),
        //     'updated_at' => now(),
        // ]);



        // foreach (range(1, 50) as $index) { // Adjust the range as needed for the number of records you want to seed.
        //     if($index > 10) $parent = Category::inRandomOrder()->first()->id;
        //     else $parent = null;
        //     DB::table('categories')->insert([
        //         'parent_id' => $parent , // Replace with your specific logic for parent_id.
        //         'image' => 'images/no-image.png', // Set a default image path or generate random paths.
        //         'level' => $parent == 0 ? 0 : $faker->numberBetween(1, 2), // Adjust as needed.
        //         'status' => 'active',
        //         'sort_order' => $faker->numberBetween(1, 100), // Adjust as needed.
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ]);
        // }

        // $productIds = DB::table('categories')->pluck('id')->toArray();
        // $languageIds = DB::table('languages')->pluck('id')->toArray();

        // foreach ($productIds as $productId) {
        //     foreach ($languageIds as $languageId) {
        //         DB::table('category_translations')->insert([
        //             'product_id' => $productId,
        //             'language_id' => $languageId,
        //             'name' => $faker->word,
        //             'desc' => $faker->sentence,
        //             'slug' => $faker->slug,
        //         ]);
        //     }
        // }
    }
}
