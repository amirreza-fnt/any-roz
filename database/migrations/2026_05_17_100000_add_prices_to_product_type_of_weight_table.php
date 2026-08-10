<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_type_of_weight', function (Blueprint $table) {
            if (! Schema::hasColumn('product_type_of_weight', 'price')) {
                $table->unsignedInteger('price')->default(0)->after('stock');
            }
            if (! Schema::hasColumn('product_type_of_weight', 'price_buy')) {
                $table->unsignedInteger('price_buy')->default(0)->after('price');
            }
            if (! Schema::hasColumn('product_type_of_weight', 'price_discounted')) {
                $table->unsignedInteger('price_discounted')->default(0)->after('price_buy');
            }
        });
    }

    public function down(): void
    {
        Schema::table('product_type_of_weight', function (Blueprint $table) {
            $cols = [];
            foreach (['price_discounted', 'price_buy', 'price'] as $col) {
                if (Schema::hasColumn('product_type_of_weight', $col)) {
                    $cols[] = $col;
                }
            }
            if ($cols !== []) {
                $table->dropColumn($cols);
            }
        });
    }
};
