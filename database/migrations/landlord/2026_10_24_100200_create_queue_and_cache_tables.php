<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The queue and the cache are shared by all tenants, in the landlord database: one worker serves every
 * tenant, and cache keys carry the tenant prefix. A single-tenant install already has these tables.
 *
 * Failed jobs are logged with their UUID (the "database-uuids" driver), which the table did not have.
 */
return new class extends Migration
{
    public function getConnection(): ?string
    {
        return config('multitenancy.landlord_database_connection_name');
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $schema = Schema::connection($this->getConnection());

        if (! $schema->hasTable('cache')) {
            $schema->create('cache', function (Blueprint $table) {
                $table->string('key')->primary();
                $table->mediumText('value');
                $table->integer('expiration');
            });
        }

        if (! $schema->hasTable('cache_locks')) {
            $schema->create('cache_locks', function (Blueprint $table) {
                $table->string('key')->primary();
                $table->string('owner');
                $table->integer('expiration');
            });
        }

        if (! $schema->hasTable('jobs')) {
            $schema->create('jobs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('queue')->index();
                $table->longText('payload');
                $table->unsignedTinyInteger('attempts');
                $table->unsignedInteger('reserved_at')->nullable();
                $table->unsignedInteger('available_at');
                $table->unsignedInteger('created_at');
            });
        }

        if (! $schema->hasTable('job_batches')) {
            $schema->create('job_batches', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->string('name');
                $table->integer('total_jobs');
                $table->integer('pending_jobs');
                $table->integer('failed_jobs');
                $table->longText('failed_job_ids');
                $table->mediumText('options')->nullable();
                $table->integer('cancelled_at')->nullable();
                $table->integer('created_at');
                $table->integer('finished_at')->nullable();
            });
        }

        if (! $schema->hasTable('failed_jobs')) {
            $schema->create('failed_jobs', function (Blueprint $table) {
                $table->id();
                $table->string('uuid')->unique();
                $table->text('connection');
                $table->text('queue');
                $table->longText('payload');
                $table->longText('exception');
                $table->timestamp('failed_at')->useCurrent();
            });
        } elseif (! $schema->hasColumn('failed_jobs', 'uuid')) {
            $schema->table('failed_jobs', function (Blueprint $table) {
                $table->string('uuid')->nullable()->unique()->after('id');
            });
        }
    }

    /**
     * Reverse the migrations. The tables of a single-tenant install belong to its own migrations.
     */
    public function down(): void
    {
        $schema = Schema::connection($this->getConnection());

        if ($schema->hasColumn('failed_jobs', 'uuid')) {
            $schema->table('failed_jobs', function (Blueprint $table) {
                $table->dropUnique(['uuid']);
                $table->dropColumn('uuid');
            });
        }

        if (filled(config('multitenancy.landlord_database'))) {
            foreach (['failed_jobs', 'job_batches', 'jobs', 'cache_locks', 'cache'] as $table) {
                $schema->dropIfExists($table);
            }
        }
    }
};
