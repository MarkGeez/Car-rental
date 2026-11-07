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
        Schema::table('vendors', function (Blueprint $table) {
            $table->string('payment_qr_code')->nullable()->after('business_permit');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->string('payment_status')->default('unpaid')->change();
            $table->string('proof_path')->nullable()->after('transaction_reference');
            $table->timestamp('reviewed_at')->nullable()->after('paid_at');
            $table->timestamp('refunded_at')->nullable()->after('reviewed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['proof_path', 'reviewed_at', 'refunded_at']);
        });

        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn('payment_qr_code');
        });
    }
};
