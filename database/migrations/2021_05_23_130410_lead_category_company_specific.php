<?php

use App\Company;
use App\LeadCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LeadCategoryCompanySpecific extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lead_category', function (Blueprint $table) {
            $table->integer('company_id')->unsigned()->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
        });

        $leadCategory = LeadCategory::all();
        $companies = Company::all();
        foreach ($companies as $company) {
            if ($leadCategory) {
                foreach ($leadCategory as $key => $lead) {
                    $newLead =  new LeadCategory();
                    $newLead->category_name = $lead->category_name;
                    $newLead->company_id = $company->id;
                    $newLead->save();
                }
            }
        }
        
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
