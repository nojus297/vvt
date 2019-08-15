<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditRouteStopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('route_stops', function (Blueprint $table) {
            $table->string('route_id', 28)->after('id');
            $table->dropColumn('type');
            $table->dropColumn('route_no');
        }); 

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('route_stops', function (Blueprint $table) {
            $table->dropColumn('route_id');
            $table->tinyInteger('type')->after('id');
            $table->string('route_no', 5)->after('type');
        }); 

    }
}
