<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProductTaxCheckArray extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $compaines = \App\Company::all();
        foreach($compaines as $company){
            $products = \App\Product::whereNotNull('taxes')->where('taxes', 'not like', '[%')->where('company_id', $company->id)->get();
            foreach($products as $product){
                $product->taxes = '['.$product->taxes.']';
                $product->save();
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
