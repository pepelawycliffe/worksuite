<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class AddLinkInFrontDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('front_details', function (Blueprint $table) {
            $link =  \App\FrontDetail::first();
            if ($link){
                $front_details =	json_decode($link->social_links);
                $youtube_link = array_search('https://www.youtube.com', array_column($front_details, 'link'));
                $name = array_search('youtube', array_column($front_details, 'name'));
                if($youtube_link === false || $name === false){
                    $modules = (array)$front_details;
                    array_push($modules,  ['name' => 'youtube', 'link' => 'https://www.youtube.com']);
                    $link->social_links = json_encode($modules);
                    $link->save();
                }else{
                    return false;
                }
            }

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('front_details', function (Blueprint $table) {
            //
        });
    }
}
