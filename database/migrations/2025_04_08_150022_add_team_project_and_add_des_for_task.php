<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('team_project', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('m_teams');
            $table->foreignId('project_id')->constrained('projects');
            $table->integer('ins_id');
            $table->integer('upd_id')->nullable();
            $table->dateTime('ins_datetime')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('upd_datetime')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->char('del_flag', 1)->default('0');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->string('description')->nullable()->after('task_status'); // hoặc sau cột nào đó
        });

        Schema::table('projects', function (Blueprint $table) {
            // Xóa foreign key constraint trước
            $table->dropForeign(['team_id']);
            // Sau đó mới xóa cột
            $table->dropColumn('team_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
