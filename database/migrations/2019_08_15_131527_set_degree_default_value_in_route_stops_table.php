<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetDegreeDefaultValueInRouteStopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('route_stops', function (Blueprint $table) {
            $table->smallInteger('degree')->nullable()->default(NULL)->change();
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
            $table->smallInteger('degree')->nullable()->change();
        });
    }
}
