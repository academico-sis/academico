<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyInvoicingStructure extends Migration
{
    public function up()
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->integer('invoice_id')->unsigned()->nullable()->after('parent_id');
        });

        // change belongs-to-many relation to a one-to-many
        if(Schema::hasTable('enrollment_pre_invoice')) {
            $enrollment_invoice = DB::table('enrollment_pre_invoice')->select()->get();

            foreach ($enrollment_invoice as $e)
            {
                DB::table('enrollments')->where('id', $e->enrollment_id)->update([
                    'invoice_id' => $e->pre_invoice_id,
                ]);
            }
        }
    }

    public function down()
    {
        Schema::table('enrollments', function (Blueprint $table) {
            //
        });
    }
}
