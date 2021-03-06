<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBoxTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('box', function($table)
		{
			$table->increments('id');
			$table->string('box_code', 10);
			$table->string('store_code', 50);
			$table->tinyInteger('in_use')->default(0);
			$table->tinyInteger('sync_status')->default(0);
			$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->timestamp('updated_at')->default('0000-00-00 00:00:00');
			$table->timestamp('jda_sync_date')->default('0000-00-00 00:00:00');
			$table->unique(array('box_code'));
			$table->engine = 'InnoDB';
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('box');
	}
	//TODO::Remove this when box is stable
/*	Schema::create('box_manifest', function($table)
		{
			$table->increments('id');
			$table->string('store_code', 50);
			$table->tinyInteger('is_shipped')->default(0);
			$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->timestamp('updated_at')->default('0000-00-00 00:00:00');

		});*/

}
