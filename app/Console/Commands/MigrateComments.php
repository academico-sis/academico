<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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
        ->select(DB::raw('id_user, id_user_responsable, fecha, comentario, if(scope = 1, false, true) as private'))
        ->get();

        foreach ($comments as $comment)
        {
            $new_comment = new \App\Models\Comment;
            $new_comment->commentable_id = $comment->id_user;
            $new_comment->commentable_type = 'App\Models\Student'; // todo confirm this. User ?
            $new_comment->body = $comment->comentario;
            $new_comment->private = $comment->private;
            $new_comment->author_id = $comment->id_user_responsable;
            $new_comment->created_at = $comment->fecha;
            $new_comment->updated_at = null;
            $new_comment->save();
        }

        // administrative comments

        $comments = DB::table('afc2.adm_comments')
        ->select(DB::raw('id_user, id_user_responsable, fecha, comentario'))
        ->get();

        foreach ($comments as $comment)
        {
            $new_comment = new \App\Models\Comment;
            $new_comment->commentable_id = $comment->id_user;
            $new_comment->commentable_type = 'App\Models\Student'; // todo confirm this. User ? Client ?
            $new_comment->body = $comment->comentario;
            $new_comment->private = true;
            $new_comment->author_id = $comment->id_user_responsable;
            $new_comment->created_at = $comment->fecha;
            $new_comment->updated_at = null;
            $new_comment->save();
        }

        // enrollment comments - new structure
        $enrollment_comments = DB::table('afc2.enrollments')
        ->select(DB::raw('id, comment, id_user_create, fecha'))
        ->where('comment', '!=', null)
        ->get();

        foreach ($enrollment_comments as $comment)
        {
            $new_comment = new \App\Models\Comment;
            $new_comment->commentable_id = $comment->id;
            $new_comment->commentable_type = 'App\Models\Enrollment';
            $new_comment->body = $comment->comment;
            $new_comment->private = true;
            $new_comment->author_id = $comment->id_user_create;
            $new_comment->created_at = $comment->fecha;
            $new_comment->updated_at = null;
            $new_comment->save();
        }


        // enrollment / prefactura comments - old structure

        $prefactura_comments = DB::table('afc2.bf_pre_factura_cabecera')
        ->select(DB::raw('id, observaciones, v_encfac_fechaemision'))
        ->get();

        foreach ($prefactura_comments as $comment)
        {
            $new_comment = new \App\Models\Comment;
            $new_comment->commentable_id = $comment->id;
            $new_comment->commentable_type = 'App\Models\PreInvoice';
            $new_comment->body = $comment->observaciones;
            $new_comment->private = true;
            //$new_comment->author_id = $comment->id_user_create;
            $new_comment->created_at = $comment->v_encfac_fechaemision;
            $new_comment->updated_at = null;
            $new_comment->save();
        }


        foreach ($results as $result)
        {
            $new_comment = new \App\Models\Comment;
            $new_comment->commentable_id = $result->id;
            $new_comment->commentable_type = 'App\Models\Result';
            $new_comment->body = $result->comment;
            $new_comment->private = false;
            $new_comment->author_id = $result->responsable_id;
            //$new_comment->created_at = $comment->fecha;
            //$new_comment->updated_at = null;
            $new_comment->save();
        }


        // course result comments
        $results = DB::table('afc2.matricula_result')
        ->select(DB::raw('id, matricula_id, responsable_id, result_id, comment'))
        ->get();

        foreach ($results as $result)
        {
            $new_comment = new \App\Models\Comment;
            $new_comment->commentable_id = $result->id;
            $new_comment->commentable_type = 'App\Models\Result';
            $new_comment->body = $result->comment;
            $new_comment->private = false;
            $new_comment->author_id = $result->responsable_id;
            //$new_comment->created_at = $comment->fecha;
            //$new_comment->updated_at = null;
            $new_comment->save();
        }


    }
}
