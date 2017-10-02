<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GenreSeeder extends Seeder
{
    private $genres = [
        ['Action', 'Akcija'],
        ['Adventure', 'Avantura'],
        ['Animation', 'Animacija'],
        ['Comedy', 'Komedija'],
        ['Crime', 'Kriminalna'],
        ['Documentary', 'Dokukumentarac'],
        ['Drama', 'Drama'],
        ['Family', 'Familija'],
        ['Fantasy', 'Fantazija'],
        ['History', 'Istorijski'],
        ['Horror', 'Horor'],
        ['Music', 'Muzički'],
        ['Mystery', 'Misterija'],
        ['Romance', 'Romantični'],
        ['Science Fiction', 'Naučna Fantastika'],
        ['TV Movie', 'Televizijska'],
        ['Thriller', 'Triler'],
        ['War', 'Ratni'],
        ['Western', 'Vestern']
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach($this->genres as $genre)
        {
            DB::table('genres')->insert([
                'name'       => $genre[0],
                'name_rs'    => $genre[1],
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString()
            ]);
        }
    }
}
