<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * spatie/laravel-activitylog v5 stores tracked model changes in a dedicated
 * `attribute_changes` column and no longer uses batches.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_log', function (Blueprint $table) {
            $table->json('attribute_changes')->nullable()->after('causer_type');
        });

        Schema::table('activity_log', function (Blueprint $table) {
            $table->dropColumn('batch_uuid');
        });

        DB::table('activity_log')
            ->where(function ($query) {
                $query->whereNotNull('properties->attributes')
                    ->orWhereNotNull('properties->old');
            })
            ->eachById(function ($row) {
                $properties = json_decode($row->properties, true);
                $changes = array_intersect_key($properties, array_flip(['attributes', 'old']));
                $remaining = array_diff_key($properties, array_flip(['attributes', 'old']));

                DB::table('activity_log')->where('id', $row->id)->update([
                    'attribute_changes' => empty($changes) ? null : json_encode($changes),
                    'properties' => empty($remaining) ? null : json_encode($remaining),
                ]);
            });
    }

    public function down(): void
    {
        Schema::table('activity_log', function (Blueprint $table) {
            $table->uuid('batch_uuid')->nullable()->after('properties');
        });

        DB::table('activity_log')
            ->whereNotNull('attribute_changes')
            ->eachById(function ($row) {
                $properties = json_decode($row->properties ?? '[]', true) ?: [];
                $changes = json_decode($row->attribute_changes, true) ?: [];

                DB::table('activity_log')->where('id', $row->id)->update([
                    'properties' => json_encode(array_merge($properties, $changes)),
                ]);
            });

        Schema::table('activity_log', function (Blueprint $table) {
            $table->dropColumn('attribute_changes');
        });
    }
};
