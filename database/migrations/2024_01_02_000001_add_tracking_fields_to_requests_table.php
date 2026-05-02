<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            // Tracking code — unique, auto-generated on public submission
            $table->string('tracking_code', 20)->unique()->after('id');

            // Optional contact fields for tracking lookup
            $table->string('email')->nullable()->after('student_id');
            $table->string('phone', 30)->nullable()->after('email');

            // Make created_by nullable so unauthenticated public submissions are allowed
            $table->unsignedBigInteger('created_by')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn(['tracking_code', 'email', 'phone']);
            $table->unsignedBigInteger('created_by')->nullable(false)->change();
        });
    }
};
