<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Video;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class VideoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first() ?? User::factory()->create([
            'name' => 'Seeder User',
            'email' => 'seeder@example.com',
            'password' => 'password',
        ]);

        Video::truncate();

        Video::create([
            'user_id' => $user->id,
            'title'  => 'Big Buck Bunny (MP4)',
            'src'    => 'http://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4',
            'poster' => 'https://i.imgur.com/4NJl6dO.jpeg',
            'tracks' => [
                [
                    'kind'    => 'subtitles',
                    'src'     => url('subtitles/bbb-en.vtt'), // put file in public/subtitles
                    'srclang' => 'en',
                    'label'   => 'English'
                ]
            ]
        ]);

        Video::create([
            'user_id' => $user->id,
            'title'  => 'Elephant Dream (MP4)',
            'src'    => 'http://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ElephantsDream.mp4',
            'poster' => 'https://i.imgur.com/4c2H7bT.jpeg',
            'tracks' => [],
        ]);

        Video::create([
            'user_id' => $user->id,
            'title'  => 'Sintel (MP4)',
            'src'    => 'http://commondatastorage.googleapis.com/gtv-videos-bucket/sample/Sintel.mp4',
            'poster' => 'https://i.imgur.com/3uGQm2n.jpeg',
            'tracks' => [],
        ]);
    }
}
