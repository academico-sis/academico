<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'book_id' => 'required',
            'student_id' => 'required',
        ]);

        $student = Student::find($request->student_id);

        $student->books()->attach(Book::find($request->book_id), [
            'code' => $request->code,
            'status_id' => $request->status_id,
            'expiry_date' => $request->expiry_date,
        ]);

        return redirect()->back();
    }

    public function update(Request $request)
    {
        $request->validate([
            'book_student_id' => 'required',
        ]);

        DB::table('book_student')->where('id', $request->book_student_id)->update([
            'status_id' => $request->status,
            'code' => $request->code,
            'expiry_date' => $request->expiry_date,
        ]);

        return Student::find(DB::table('book_student')->where('id', $request->book_student_id)->first()->student_id)->books;
    }

    public function destroy(Request $request)
    {
        $student_id = DB::table('book_student')->where('id', $request->book_student_id)->first()->student_id;

        DB::table('book_student')->where('id', $request->book_student_id)->delete();

        return Student::find($student_id)->books;
    }

    public function exportCode(Request $request)
    {

        $request->validate([
            'book_student_id' => 'required',
        ]);

        $book = DB::table('book_student')->find($request->book_student_id);
        $student = Student::find($book->student_id);

        $image = imagecreatefrompng(storage_path('afloja/vignette_livre.png'));
        $black = imagecolorallocate($image, 0, 0, 0);
        $font = storage_path('afloja/fonts/OpenSans-ExtraBold.ttf');

        // First line of text
        $font_size = 42;
        $text = "Alumno:";
        $angle = 0;
        $width = imagesx($image);
        $centerX = $width / 2;
        list($left, , $right, , , ) = imageftbbox($font_size, $angle, $font, $text);
        $left_offset = ($right - $left) / 2;
        $x = $centerX - $left_offset;
        imagettftext($image, $font_size, $angle, $x, 450, $black, $font, $text);

        // Name of student
        $text = $student->lastname . " " . $student->firstname;
        $angle = 0;
        $width = imagesx($image);
        $centerX = $width / 2;
        list($left, , $right, , , ) = imageftbbox($font_size, $angle, $font, $text);
        $left_offset = ($right - $left) / 2;
        $x = $centerX - $left_offset;
        imagettftext($image, $font_size, $angle, $x, 525, $black, $font, $text);

        // First line of text
        $text = "Código Premium:";
        $angle = 0;
        $width = imagesx($image);
        $centerX = $width / 2;
        list($left, , $right, , , ) = imageftbbox($font_size, $angle, $font, $text);
        $left_offset = ($right - $left) / 2;
        $x = $centerX - $left_offset;
        imagettftext($image, $font_size, $angle, $x, 620, $black, $font, $text);

        // Code
        $text = $book->code;
        $angle = 0;
        $width = imagesx($image);
        $centerX = $width / 2;
        list($left, , $right, , , ) = imageftbbox($font_size, $angle, $font, $text);
        $left_offset = ($right - $left) / 2;
        $x = $centerX - $left_offset;
        imagettftext($image, $font_size, $angle, $x, 690, $black, $font, $text);


        // last line of text
        $font = storage_path('afloja/fonts/OpenSans-Light.ttf');
        $font_size = 30;
        $expiry_date = Carbon::parse($book->expiry_date)->format("j/m/Y");
        $text = "(Valido hasta el $expiry_date)";
        $angle = 0;
        $width = imagesx($image);
        $centerX = $width / 2;
        list($left, , $right, , , ) = imageftbbox($font_size, $angle, $font, $text);
        $left_offset = ($right - $left) / 2;
        $x = $centerX - $left_offset;
        imagettftext($image, $font_size, $angle, $x, 755, $black, $font, $text);

        // Using imagepng() results in clearer text compared with imagejpeg()
        header('Content-Type: image/jpg');
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        ob_start();
        imagepng($image);
        header("Content-Disposition: attachment; filename=vignette.jpg");
        imagedestroy($image);
    }
}
