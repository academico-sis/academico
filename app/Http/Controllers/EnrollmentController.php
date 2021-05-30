<?php

namespace App\Http\Controllers;

use App\Events\EnrollmentUpdated;
use App\Http\Requests\StoreEnrollmentRequest;
use App\Models\Attendance;
use App\Models\Book;
use App\Models\Config;
use App\Models\Course;
use App\Models\Discount;
use App\Models\Enrollment;
use App\Models\Fee;
use App\Models\InvoiceType;
use App\Models\Paymentmethod;
use App\Models\Student;
use App\Models\Tax;
use App\Traits\PeriodSelection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use Prologue\Alerts\Facades\Alert;

class EnrollmentController extends Controller
{
    use PeriodSelection;

    public function __construct()
    {
        parent::__construct();

        // these methods are reserved to administrators or staff members.
        // Only the store method can also be called by teachers to enroll students in their courses
        $this->middleware('permission:enrollments.edit', ['except' => 'store']);
    }

    /**
     * Store the newly created enrollment.
     */
    public function store(StoreEnrollmentRequest $request)
    {
        $course = Course::findOrFail($request->input('course_id'));

        if (Gate::forUser(backpack_user())->denies('enroll-in-course', $course)) {
            abort(403);
        }

        $student = Student::findOrFail($request->input('student_id'));
        $enrollment_id = $student->enroll($course);
        Alert::success(__('Enrollment successfully created'))->flash();

        Log::info(backpack_user()->firstname.' generated a new enrollment for student '.$student->name);

        if (backpack_user()->can('enrollments.edit')) {
            return url("/enrollment/$enrollment_id/show");
        }
    }

    public function update(Enrollment $enrollment, Request $request)
    {
        $course = Course::findOrFail($request->input('course_id'));
        $previousCourse = $enrollment->course;

        // if enrollment has children, delete them
        Enrollment::where('parent_id', $enrollment->id)->delete();

        // update enrollment with new course
        $enrollment->update([
            'course_id' => $course->id,
        ]);

        // if the new course has children, create an enrollment as well
        foreach ($course->children as $children_course) {
            $child_enrollment = Enrollment::firstOrNew([
                'student_id' =>  $enrollment->student_id,
                'course_id' => $children_course->id,
                'parent_id' => $enrollment->id,
            ]);
            $child_enrollment->responsible_id = backpack_user()->id ?? null;
            $child_enrollment->save();
        }

        // delete attendance
        foreach ($enrollment->course->events as $event) {
            Attendance::where('event_id', $event->id)->where('student_id', $enrollment->student_id)->delete();
        }

        foreach ($enrollment->course->children as $child) {
            foreach ($child->events as $event) {
                Attendance::where('event_id', $event->id)->where('student_id', $enrollment->student_id)->delete();
            }
        }

        // TODO delete grades and/or skills

        // update LMS
        EnrollmentUpdated::dispatch($enrollment->student, $previousCourse, $course);

        // display a confirmation message and redirect to enrollment details
        Alert::success(__('The enrollment has been updated'))->flash();

        return "enrollment/$enrollment->id/show";
    }

    /**
     * Create a new cart with the specified enrollment
     * and display the cart.
     */
    public function bill(Enrollment $enrollment)
    {
        // if the enrollment has already been invoiced, continue with the same invoice
        if ($enrollment->invoice) {
            Alert::success(__('This enrollment has already been invoiced'))->flash();
            return redirect()->back();
        }

        // otherwise create a new one.
        Log::info('User # '.backpack_user()->id.' is generating a invoice');

        // build an array with products to include
        $products = [];

        foreach (Fee::where('default', 1)->get() as $fee)
        {
            array_push($products, $fee);
        }

        array_push($products, $enrollment);

        if ($enrollment->course->books->count() > 0)
        {
            array_push($products, $enrollment->course->books);
        }


        // build an array with all contact data
        $clients = [];

        array_push($clients, [
            'name' => $enrollment->student_name,
            'email' => $enrollment->student_email,
            'idnumber' => $enrollment->student->idnumber,
        ]);

        foreach ($enrollment->student->contacts as $client) {
            array_push($clients, $client);
        }

        return view('carts.show', [
            'enrollment' => $enrollment,
            'products' => $products,
            'invoicetypes' => InvoiceType::all(),
            'clients' => $clients,
            'availableBooks' => Book::all(),
            'availableFees' => Fee::all(),
            'availableDiscounts' => Discount::all(),
            'availablePaymentMethods' => Paymentmethod::all(),
            'availableTaxes' => Tax::all(),
        ]);
    }

    public function markaspaid(Enrollment $enrollment)
    {
        $enrollment->markAsPaid();

        return redirect()->back();
    }

    public function markasunpaid(Enrollment $enrollment)
    {
        $enrollment->update(['status_id' => 1]);

        return redirect()->back();
    }

    public function savePrice(Enrollment $enrollment, Request $request)
    {
        $request->validate(['price' => 'required|numeric']);

        $enrollment->update(['total_price' => $request->price]);

        return $enrollment->fresh();
    }

    private function utf8_for_xml($string)
    {
        return preg_replace ('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $string);
    }

    public function exportToWord(Enrollment $enrollment)
    {
        $phpWord = new PhpWord();

        // Course general info
        $section = $phpWord->addSection();

        $header = $section->addHeader();

        // Image from string
        //printSeparator($section);
        $source = storage_path('logo.jpg');
        $fileContent = file_get_contents($source);
        $header->addImage($fileContent,    array(
            'width'            => \PhpOffice\PhpWord\Shared\Converter::cmToPixel(5),
            //'height'           => \PhpOffice\PhpWord\Shared\Converter::cmToPixel(3),
            'wrappingStyle'      => 'topAndBottom',
            'positioning'      => \PhpOffice\PhpWord\Style\Image::POSITION_RELATIVE,
            'posHorizontal'    => \PhpOffice\PhpWord\Style\Image::POSITION_HORIZONTAL_CENTER,
            'posHorizontalRel' => \PhpOffice\PhpWord\Style\Image::POSITION_RELATIVE_TO_COLUMN,
            'posVertical'      => \PhpOffice\PhpWord\Style\Image::POSITION_VERTICAL_TOP,
            //'marginLeft'       => \PhpOffice\PhpWord\Shared\Converter::cmToPixel(15.5),
            'marginTop'        => \PhpOffice\PhpWord\Shared\Converter::cmToPixel(1.55),
        ));

        $section->addText(config('app.company_name'));
        $section->addText(config('app.company_id'));
        $section->addText(config('app.company_address'));
        $section->addText(config('app.company_phone'));
        $section->addText(config('app.company_email'));

        $section->addTextBreak();

        $titleStyle = new \PhpOffice\PhpWord\Style\Font();
        $titleStyle->setBold(true);
        $section->addText(Str::upper(__('Enrollment sheet')))->setFontStyle($titleStyle);

        $normalStyle = new \PhpOffice\PhpWord\Style\Font();
        $normalStyle->setBold(false);
        $section->addText(__('Enrollment date') . ": " . $enrollment->date)->setFontStyle($normalStyle);

        $section->addTextBreak();
        $section->addTextBreak();
        $section->addText(__('Student Info'))->setFontStyle($titleStyle);

        $section->addListItem(__('Name') . " : " . $enrollment->student_name);
        if ($enrollment->student->idnumber) { $section->addListItem(__('ID number') . " : " . $enrollment->student->idnumber); }
        if ($enrollment->student->phone->first()->phone_number) { $section->addListItem(__('Phone Number') . " : " . $enrollment->student->phone->first()->phone_number); }
        if ($enrollment->student->email) { $section->addListItem(__('Email') . " : " . $enrollment->student->email); }
        if ($enrollment->student->address) { $section->addListItem(__('Address') . " : " . $enrollment->student->address); }

        $section->addTextBreak();
        $section->addTextBreak();
        $section->addText(__('Course Details'))->setFontStyle($titleStyle);
        $section->addText($enrollment->course->name);
        $section->addText(__('Start Date') . " : " . $enrollment->course->formatted_start_date);
        $section->addText(__('End Date') . " : " . $enrollment->course->formatted_end_date);

        if (config('invoicing.invoicing_system') === 'sepa' && $enrollment->invoice && $enrollment->invoice->payments)
        {
            $section->addTextBreak();
            $table = $section->addTable();

            $section->addTextBreak(1);
            $fancyTableStyleName = 'Fancy Table';
            $fancyTableStyle = array('borderSize' => 6, 'cellMargin' => 80, 'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER, 'cellSpacing' => 50);
            $fancyTableFirstRowStyle = array('borderBottomSize' => 18, 'borderBottomColor' => '0000FF', 'bgColor' => '66BBFF');
            $fancyTableCellStyle = array('valign' => 'center');
            $fancyTableCellBtlrStyle = array('valign' => 'center', 'textDirection' => \PhpOffice\PhpWord\Style\Cell::TEXT_DIR_BTLR);
            $fancyTableFontStyle = array('bold' => true);
            $phpWord->addTableStyle($fancyTableStyleName, $fancyTableStyle, $fancyTableFirstRowStyle);
            $table = $section->addTable($fancyTableStyleName);

            $table->addRow(500);
            $table->addCell(4000, $fancyTableCellStyle)->addText(Str::upper(__('Due Date')));
            $table->addCell(5000, $fancyTableCellStyle)->addText(Str::upper(__('Total')));

            foreach ($enrollment->invoice->payments as $payment) {
                $table->addRow(500);
                $table->addCell(4000, $fancyTableCellStyle)->addText($payment->date_for_humans);
                $table->addCell(5000, $fancyTableCellStyle)->addText($payment->value_with_currency);
            }
        }

        $footer = $section->addFooter();
        $fontStyle = new \PhpOffice\PhpWord\Style\Font();
        $fontStyle->setItalic(true);
        $fontStyle->setName('Tahoma');
        $fontStyle->setSize(8);
        $footerText = $this->utf8_for_xml($footer->addText(Config::firstWhere('name', 'enrollment_sheet_footer')->value));
        $footerText->setFontStyle($fontStyle);


        $section = $phpWord->addSection();

        $html = Config::firstWhere('name', 'enrollment_sheet_terms')->value;

        $html = str_replace('{{ course_name }}', $enrollment->course->name, $html);
        $html = str_replace("{{ course_volume }}", $enrollment->course->total_volume, $html);

        \PhpOffice\PhpWord\Shared\Html::addHtml($section, $this->utf8_for_xml($html), false, false);

        $footer = $section->addFooter();
        $fontStyle = new \PhpOffice\PhpWord\Style\Font();
        $fontStyle->setItalic(true);
        $fontStyle->setName('Tahoma');
        $fontStyle->setSize(8);
        $footerText = $this->utf8_for_xml($footer->addText(Config::firstWhere('name', 'enrollment_sheet_footer')->value));
        $footerText->setFontStyle($fontStyle);


        // Saving the document as OOXML file...
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        header('Content-type: application/msword');
        header('Cache-Control: no-store, no-cache');
        header('Content-Disposition: attachment; filename="document.docx"');

        $objWriter->save('php://output');
        exit;
    }
}
