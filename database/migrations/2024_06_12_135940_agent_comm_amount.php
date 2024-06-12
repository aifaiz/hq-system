<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('agent_users', function($t){
            $t->decimal('comm_amount', 10,2)->default(10)->nullable()->after('refcode');
            $t->date('lp_expire')->nullable()->after('comm_amount');
            $t->bigInteger('distributor_id')->after('lp_expire');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agent_users', function($t){
            $t->dropColumn(['comm_amount', 'lp_expire', 'distributor_id']);
        });
    }
};
