<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * A single-tenant install keeps one database, which also holds the landlord tables: this migration
 * registers that database as its tenant, named after the institution, with the host of APP_URL as domain.
 * Its files stay in their folders.
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
        if (filled(config('multitenancy.landlord_database')) || DB::connection($this->getConnection())->table('tenants')->exists()) {
            return;
        }

        $connection = DB::connection($this->getConnection());
        $name = Schema::connection($this->getConnection())->hasTable('settings')
            ? json_decode((string) $connection->table('settings')->where('key', 'academico.company_name')->value('value'))
            : null;

        $tenantId = $connection->table('tenants')->insertGetId([
            'name' => $name ?: config('app.name'),
            'database' => config('database.connections.'.config('database.default').'.database'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $host = parse_url((string) config('app.url'), PHP_URL_HOST);

        if ($host) {
            $connection->table('tenant_domains')->insert([
                'tenant_id' => $tenantId,
                'domain' => strtolower($host),
                'is_primary' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (blank(config('multitenancy.landlord_database'))) {
            DB::connection($this->getConnection())->table('tenants')->delete();
        }
    }
};
