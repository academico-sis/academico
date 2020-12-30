<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware(['permission:enrollments.edit']);
    }

    /**
     * Create a payment based on the cart contents for the specified user.
     */
    public function store(Request $request)
    {
        $enrollment = Enrollment::find($request->enrollment['id']);
        $enrollment->update(['total_price' => $request->enrollment['total_price']]);

        if (isset($request->comment)) {
            Comment::create([
                'commentable_id' => $enrollment->id,
                'commentable_type' => Enrollment::class,
                'body' => $request->comment,
                'author_id' => backpack_user()->id,
            ]);
        }

        // if the whole amount has been paid, mark the enrollment as such
        if ($enrollment->fresh()->payments->sum('value') >= $enrollment->price) {
            $enrollment->markAsPaid();
        }
    }
}
