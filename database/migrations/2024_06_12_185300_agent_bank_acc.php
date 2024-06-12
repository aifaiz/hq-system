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
            $t->string('bank_name')->nullable()->after('status');
            $t->string('bank_acc_no')->nullable()->after('bank_name');
            $t->string('bank_acc_name')->nullable()->after('bank_acc_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agent_users', function($t){
            $t->dropColumn(['bank_name','bank_acc_no','bank_acc_name']);
        });
    }
};
