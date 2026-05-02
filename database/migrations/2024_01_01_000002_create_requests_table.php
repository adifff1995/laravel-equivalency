<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();

            // Unique tracking code for public student lookup
            $table->string('tracking_code', 20)->unique();

            $table->string('name');
            $table->string('student_id');

            // Optional contact info for tracking lookup
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();

            $table->enum('type', ['special', 'internal', 'external_bridge', 'external_other']);
            $table->string('major');
            $table->string('old_student_id')->nullable();
            $table->string('new_student_id')->nullable();
            $table->text('courses');
            $table->string('university')->nullable();
            $table->json('attachments')->nullable();

            $table->enum('status', [
                'new',
                'under_review',
                'ready_for_entry',
                'entered',
                'approved',
                'rejected',
            ])->default('new');

            $table->text('notes')->nullable();

            // Nullable: public form submissions are not tied to a user account
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
