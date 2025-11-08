<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveDeletedAtFromJenisUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jenis_units', function (Blueprint $table) {
            if (Schema::hasColumn('jenis_units', 'deleted_at')) {
                $table->dropColumn('deleted_at');
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
        Schema::table('jenis_units', function (Blueprint $table) {
            if (!Schema::hasColumn('jenis_units', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }
}
