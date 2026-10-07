<?php

namespace Database\Seeders;

use App\Models\NoticeBoard;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NoticeBoardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        NoticeBoard::create([
            'title' => 'First Notice',
            'type_id' => 1,
            'receivers' => json_encode(["2","3","4"]),
            'description' => 'This is the first notice.',
            'attachment' => null,
            'published_at' => null,
            'active_status' => 0,
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        NoticeBoard::create([
            'title' => 'Second Notice',
            'type_id' => 1,
            'receivers' => json_encode(["1","2","4","4"]),
            'description' => 'This is the second notice.',
            'attachment' => null,
            'published_at' => Carbon::now(),
            'active_status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
        ]);
    }
}
