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
        if (Schema::hasTable('purchase_order')) { return; }

        Schema::create('purchase_order', function (Blueprint $table) {
            $table->id()->comment('主键');
$table->string('number', 25)->unique()->comment('批量采购单号');
$table->string('name', 255)->comment('采购单名称');
$table->creatorId();
$table->createdAt();
$table->updatedAt();
$table->deletedAt();

$table->engine='InnoDB';
$table->comment('采购单');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_order');
    }
};
