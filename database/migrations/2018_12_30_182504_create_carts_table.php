<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCartsTable extends Migration
{
    /**
     * Run the migrations.
     * 
     * This table stores the pending carts for each user
     *
     * @return void
     */
    public function up()
    {
        // this migration has been removed but the file is kept to prevent errors in production sites where the migration has already been ran
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
