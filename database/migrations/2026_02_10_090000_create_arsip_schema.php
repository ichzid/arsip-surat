<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('divisions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('incoming_letters', function (Blueprint $table) {
            $table->id();
            $table->string('agenda_number')->unique();
            $table->string('mail_number');
            $table->date('mail_date');
            $table->date('received_date');
            $table->string('origin');
            $table->string('subject');
            $table->string('file_path')->nullable();
            $table->enum('status', ['new', 'disposition', 'done'])->default('new');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });

        Schema::create('outgoing_letters', function (Blueprint $table) {
            $table->id();
            $table->string('mail_number')->unique();
            $table->string('recipient');
            $table->date('mail_date');
            $table->string('subject');
            $table->foreignId('division_id')->constrained('divisions');
            $table->string('file_path')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });

        Schema::create('dispositions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incoming_letter_id')->constrained('incoming_letters')->cascadeOnDelete();
            $table->foreignId('from_division_id')->constrained('divisions');
            $table->foreignId('to_division_id')->constrained('divisions');
            $table->text('notes')->nullable();
            $table->date('due_date')->nullable();
            $table->enum('status', ['pending', 'process', 'done'])->default('pending');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
        
        // Add division_id to users
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('division_id')->nullable()->after('email')->constrained('divisions');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['division_id']);
            $table->dropColumn('division_id');
        });
        Schema::dropIfExists('dispositions');
        Schema::dropIfExists('outgoing_letters');
        Schema::dropIfExists('incoming_letters');
        Schema::dropIfExists('divisions');
    }
};
