<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	Schema::table('users', function (Blueprint $table) {
		    $table->string('email')->nullable()->change();
		    $table->string('last_name')->nullable();
		    $table->string('title')->nullable();
		    $table->string('login_number')->unique()->nullable();
		    $table->string('register_type')->nullable();
		    $table->string('company_name')->nullable();
		    $table->string('company_city')->nullable();
		    $table->string('company_state')->nullable();
		    $table->string('company_country')->nullable();
		    $table->string('company_url')->nullable();
		    $table->boolean('booth')->default(false);
		    $table->string('booth_type')->nullable();
		    $table->string('company_organization')->nullable();
		    $table->string('company_products')->nullable();
		    $table->string('company_areas')->nullable();
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
