<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ImportSchema extends Migration
{
    public function up()
    {
        $path = database_path('schema/sistema_tickets_mysql.sql');
        if (file_exists($path)) {
            $sql = file_get_contents($path);
            DB::unprepared($sql);
        }
    }

    public function down()
    {
        // Manual rollback
    }
}
