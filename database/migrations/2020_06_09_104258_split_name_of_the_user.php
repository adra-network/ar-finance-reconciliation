<?php

use App\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SplitNameOfTheUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function(Blueprint $table) {
            $table->string('lastname')->nullable()->after('name');
        });

        User::all()->each(function(User $user) {
           $split = explode(' ', $user->name);
           
           $name = array_shift($split);
           $last = join(' ', $split);

           $user->name = $name;
           $user->lastname = $last;
           $user->save();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
