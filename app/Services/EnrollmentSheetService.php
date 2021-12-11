<?php

namespace App\Services;

use App\Models\Enrollment;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class EnrollmentSheetService
{
    private function utf8_for_xml($string)
    {
        return preg_replace('/^[\p{L}\p{N}_-]+$/u', ' ', $string);
    }

    public function exportToWord(Enrollment $enrollment)
    {
        App::setLocale(config('app.locale'));
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor(storage_path('enrollment.docx'));


        $templateProcessor->setValue('enrollment_date', $enrollment->date);
        $templateProcessor->setValue('name', $enrollment->student_name);

        $nif = $enrollment->student->idnumber ? $enrollment->student->idnumber : '';
        $phone = $enrollment->student->phone->count() > 0 && $enrollment->student->phone->first()->phone_number ? $enrollment->student->phone->first()->phone_number : '';
        $email = $enrollment->student->email ? $enrollment->student->email : '';
        $address = $enrollment->student->address ? $enrollment->student->address : '';
        $city = $enrollment->student->city ? $enrollment->student->city : '';

        $templateProcessor->setValue('address', $address);
        $templateProcessor->setValue('city', $city);
        $templateProcessor->setValue('phone', $phone);
        $templateProcessor->setValue('nif', $nif);
        $templateProcessor->setValue('email', $email);

        $templateProcessor->setValue('description', $this->utf8_for_xml($enrollment->course->name ?? ""));
        $templateProcessor->setValue('start_date', $enrollment->course->formatted_start_date);
        $templateProcessor->setValue('end_date', $enrollment->course->formatted_end_date);
        $templateProcessor->setValue('volume', $enrollment->course->volume);

        $table = new \PhpOffice\PhpWord\Element\Table(['borderSize' => 8, 'borderColor' => 'black', 'cellMargin' => 80, 'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER, 'cellSpacing' => 50, 'width' => 100 * 50,]);

        $firstRowStyle = array('bgColor' => 'd9d9d9');

        $table->addRow(500, $firstRowStyle);
        $table->addCell(4000, $firstRowStyle)->addText(Str::upper(__('Due Date')));
        $table->addCell(5000, $firstRowStyle)->addText(Str::upper(__('Total')));

        if ($enrollment->scheduledPayments->count() > 0) {
            foreach ($enrollment->scheduledPayments as $payment) {
                $table->addRow(500);
                $table->addCell(4000)->addText($payment->date_for_humans, [], ['spaceAfter' => 0]);
                $table->addCell(5000)->addText($payment->value_with_currency, [], ['spaceAfter' => 0]);
            }
            $templateProcessor->setComplexBlock('payments', $table);
        } else {
            $templateProcessor->setValue('payments', '');
        }

        $path = $templateProcessor->save();
        return response()->download($path)->deleteFileAfterSend(true);

    }
}
