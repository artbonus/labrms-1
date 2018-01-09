<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoryReceiptTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_receipt', function (Blueprint $table) {
            $table->integer('inventory_id')->unsigned();
            $table->foreign('inventory_id')
                    ->references('id')
                    ->on('inventories')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->integer('receipt_id')->unsigned();
            $table->foreign('receipt_id')
                    ->references('id')
                    ->on('receipts')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory_receipt');
    }
}
