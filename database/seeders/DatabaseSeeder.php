<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Helpers\MongoModel;
use MongoDB\BSON\ObjectID;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $tasks = new MongoModel('tasks');

        for ($i = 0; $i < 10; $i++) {
            $tasks->collection->insertOne([
                '_id' => (string) new ObjectID(),
                'title' => "Task ke $i",
                'description' => "deskirpsi task ke $i",
                'assigned' => null,
                'subtasks' => [],
                'created_at' => time()
            ]);
        }
    }
}
