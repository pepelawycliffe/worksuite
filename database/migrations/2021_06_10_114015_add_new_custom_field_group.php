<?php

use Illuminate\Database\Migrations\Migration;

class AddNewCustomFieldGroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::table('custom_field_groups')->insert([
            'name' => 'Company',
            'model' => 'App\Company',
            'company_id' => null
        ]);
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
