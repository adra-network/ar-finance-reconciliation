<?php

use Account\Models\CommentTemplate;
use Illuminate\Database\Seeder;

class CommentTemplatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        CommentTemplate::firstOrCreate([
            'id' => 1,
            'comment' => 'Balance due from Employee. Please submit payment through cash or check payable to ARA I to pay off balance. Balance due will automatically be deducted from your next expense report if payment is not recieved timely.'
        ]);
        CommentTemplate::firstOrCreate([
            'id' => 2,
            'comment' => 'Balance due to employee. A check/EFT will be issued to employee or the balance will be applied to the next expense report.'
        ]);
        CommentTemplate::firstOrCreate([
            'id' => 3,
            'comment' => 'Please submit outstanding expense reports to clear balance due.'
        ]);
    }
}
