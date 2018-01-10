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
            $table->integer('quantity');
            $table->integer('profiled_items');
            $table->integer('unit_id')->unsigned();
            $table->foreign('unit_id')
                    ->references('id')
                    ->on('units')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
            $table->primary([ 'inventory_id', 'receipt_id']);
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
