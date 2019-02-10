<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Enrollment;
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
        // private comments -> transform to adm
/*          $comments = DB::table('afc2.comments')
        ->select(DB::raw('id_user, id_user_responsable, fecha, comentario'))
        ->where('scope', 3)
        ->get();

        foreach ($comments as $comment)
        {
            $new_comment = new \App\Models\Comment;
            $new_comment->commentable_id = $comment->id_user;
            $new_comment->commentable_type = 'App\Models\Student';
            $new_comment->body = $comment->comentario;
            $new_comment->author_id = $comment->id_user_responsable;
            $new_comment->created_at = $comment->fecha;
            $new_comment->updated_at = null;
            $new_comment->save();
        }
 */
        // general comments (transform to result comments)
        $comments = DB::table('afc2.comments')
        ->select(DB::raw('id, id_user, id_user_responsable, fecha, comentario'))
        ->where('scope', '!=', 3)
        ->where('id_user_responsable', '!=', 3)
        ->get();

        foreach ($comments as $comment)
        {
            $enrollments_1 = Enrollment::where('student_id', $comment->id_user)->with('course')->get();
            
            // first look for the courses with this teacher
            $enrollments_2 = $enrollments_1->where('course.teacher_id', $comment->id_user_responsable);

            // if there is only one course with this teacher, associate the comment to this course
            if ($enrollments_2->count() == 1 && isset($enrollments_1->first()->result))
            {
                $new_comment = new \App\Models\Comment;
                $new_comment->commentable_id = $enrollments_1->first()->result->id;
                $new_comment->commentable_type = 'App\Models\Result';
                $new_comment->body = $comment->comentario;
                $new_comment->author_id = $comment->id_user_responsable;
                $new_comment->created_at = $comment->fecha;
                $new_comment->save();

                DB::table('afc2.comments')->where('id', $comment->id)->delete();
                continue;
            }

            // otherwise loop through all courses and filter according to dates
            foreach($enrollments_2 as $course) {
                // filter out all courses that started after the comment
                if((Carbon::parse($course->course->start_date) < Carbon::parse($comment->fecha) && Carbon::parse($course->course->end_date)->modify('+60 days') > Carbon::parse($comment->fecha)) && isset($course->result))
                {
                    // if dates match, register comment
                    $new_comment = new \App\Models\Comment;
                    $new_comment->commentable_id = $course->result->id;
                    $new_comment->commentable_type = 'App\Models\Result';
                    $new_comment->body = $comment->comentario;
                    $new_comment->author_id = $comment->id_user_responsable;
                    $new_comment->created_at = $comment->fecha;
                    $new_comment->save();
                    DB::table('afc2.comments')->where('id', $comment->id)->delete();
                    break;
                }
            
            }
        }
        
        

         $comments = DB::table('afc2.comments')
        ->select(DB::raw('id, id_user, id_user_responsable, fecha, comentario'))
        ->where('scope', '!=', 3)
        ->where('id_user_responsable', 3)
        ->get();

        foreach ($comments as $comment)
        {
           
            $enrollments_1 = Enrollment::where('student_id', $comment->id_user)->with('course')->get();
           

            // otherwise loop through all courses and filter according to dates
            foreach($enrollments_1 as $course) {
                
                if((Carbon::parse($course->course->start_date) < Carbon::parse($comment->fecha) && Carbon::parse($course->course->end_date)->modify('+20 days') > Carbon::parse($comment->fecha)) && isset($course->result))
                {
                    // if dates match, register comment
                    $new_comment = new \App\Models\Comment;
                    $new_comment->commentable_id = $course->result->id;
                    $new_comment->commentable_type = 'App\Models\Result';
                    $new_comment->body = $comment->comentario;
                    $new_comment->author_id = $comment->id_user_responsable;
                    $new_comment->created_at = $comment->fecha;
                    $new_comment->save();
                    DB::table('afc2.comments')->where('id', $comment->id)->delete();

                    break;

                }
            
            }
        }



/* 

        // administrative comments

        $comments = DB::table('afc2.adm_comments')
        ->select(DB::raw('id_user, id_user_responsable, fecha, comentario'))
        ->get();

        foreach ($comments as $comment)
        {
            $new_comment = new \App\Models\Comment;
            $new_comment->commentable_id = $comment->id_user;
            $new_comment->commentable_type = 'App\Models\Student';
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
        ->where('comment', '!=', "")
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
        // will be converted to enrollment comments

        $prefactura_comments = DB::table('afc2.bf_pre_factura_cabecera')
        ->select(DB::raw('id, id_matricula, observaciones, v_encfac_fechaemision'))
        ->where('observaciones', '!=', null)
        ->where('observaciones', '!=', "")
        ->get();

        foreach ($prefactura_comments as $comment)
        {
            $new_comment = new \App\Models\Comment;
            $new_comment->commentable_id = $comment->id_matricula;
            $new_comment->commentable_type = 'App\Models\Enrollment';
            $new_comment->body = $comment->observaciones;
            $new_comment->private = true;
            //$new_comment->author_id = $comment->id_user_create;
            $new_comment->created_at = $comment->v_encfac_fechaemision;
            $new_comment->updated_at = null;
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
        } */


    }
}
