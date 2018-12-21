<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MigrateComments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'datamigration:comments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retrieve comments from old DB and store them according to the new data structures';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // pedagogical comments
        $comments = DB::table('afc2.comments')
        ->select(DB::raw('telefono, celular, id'))
        ->where('role_id', 7) // filter students
        ->get();

        foreach ($comments as $comment)
        {
        }

        // administrative comments

        // enrollment comments - new structure

        // enrollment / prefactura comments - old structure

        // installments comments

        // course result comments



    }
}
