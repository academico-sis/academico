<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Drops the tables and columns of features that no longer exist: leads, remote events, schedule presets,
 * titles, the LMS synchronisation, API tokens, the course view preference and the former invoice links.
 */
return new class extends Migration
{
    /**
     * @var list<string>
     */
    private array $unusedTables = ['lead_types', 'remote_events', 'schedule_presets', 'titles'];

    /**
     * @var array<string, list<string>>
     */
    private array $unusedColumns = [
        'courses' => ['lms_id', 'sync_to_lms'],
        'levels' => ['lms_id'],
        'rhythms' => ['lms_id'],
        'users' => ['lms_id', 'api_token', 'preferred_course_view'],
        'students' => ['lead_type_id', 'how_did_you_know_us'],
        'enrollments' => ['invoice_id'],
        'invoices' => ['company_id'],
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->unusedTables as $table) {
            Schema::dropIfExists($table);
        }

        if (Schema::hasColumn('users', 'api_token')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique(['api_token']);
            });
        }

        foreach ($this->unusedColumns as $table => $columns) {
            $columns = array_values(array_filter($columns, fn (string $column): bool => Schema::hasColumn($table, $column)));

            if ($columns !== []) {
                Schema::table($table, function (Blueprint $table) use ($columns) {
                    $table->dropColumn($columns);
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('lead_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->timestamps();
        });

        Schema::create('remote_events', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('teacher_id')->unsigned()->nullable();
            $table->string('name');
            $table->bigInteger('worked_hours')->unsigned();
            $table->integer('period_id')->unsigned()->nullable();
            $table->unsignedBigInteger('course_id')->nullable();
            $table->timestamps();
        });

        Schema::create('schedule_presets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->text('presets');
            $table->timestamps();
        });

        Schema::create('titles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title')->unique();
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->bigInteger('lms_id')->nullable();
            $table->boolean('sync_to_lms')->nullable();
        });

        foreach (['levels', 'rhythms'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->bigInteger('lms_id')->nullable();
            });
        }

        Schema::table('users', function (Blueprint $table) {
            $table->bigInteger('lms_id')->nullable();
            $table->string('api_token', 100)->unique()->nullable();
            $table->string('preferred_course_view')->nullable();
        });

        Schema::table('students', function (Blueprint $table) {
            $table->unsignedInteger('lead_type_id')->nullable();
            $table->text('how_did_you_know_us')->nullable();
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->integer('invoice_id')->unsigned()->nullable();
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->integer('company_id')->default(1);
        });
    }
};
