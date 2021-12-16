<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Student;
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
}
