<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('purchase')) { return; }

        Schema::create('purchase', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->string('name', 255)->comment('采购单名称');
            $table->text('description')->nullable()->comment('任务描述');
            $table->string('file', 500)->nullable()->comment('文件路径');
            $table->creatorId();
            $table->createdAt();
            $table->updatedAt();
            $table->deletedAt();

            $table->engine='InnoDB';
            $table->comment('批量采购单');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase');
    }
};

