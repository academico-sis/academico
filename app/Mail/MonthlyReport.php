<?php

namespace App\Mail;

use App\Models\Teacher;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class MonthlyReport extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $monthName;

    public int $year;

    public Carbon $start;

    public Carbon $end;

    public $teachers;

    public function __construct()
    {
        Carbon::setLocale('fr');
        $this->monthName = Carbon::now()->monthName;
        $this->year = Carbon::now()->year;

        $this->start = Carbon::parse('First day of this month');
        $this->end = Carbon::parse('Last day of this month');

        $this->teachers = Teacher::with('remote_events')->with('events')->with('courses')->get();
    }

    public function build()
    {
        return $this->view('emails.monthly-report');
    }
}
