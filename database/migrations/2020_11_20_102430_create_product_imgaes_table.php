<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductImgaesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create( 'product_imgaes', function ( Blueprint $table ) {
            $table->id();
            $table->foreignId( 'product_id' );
            $table->string( 'name' );
            $table->tinyInteger( 'type' );
            $table->timestamps();
            $table->foreign( 'product_id' )->references( 'id' )->on( 'products' )->onDelete( 'cascade' );

        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists( 'product_imgaes' );
    }
}
