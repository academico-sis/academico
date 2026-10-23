<?php

namespace App\Filament\Resources\Enrollments\Pages;

use App\Filament\Actions\DownloadCertificateAction;
use App\Filament\Actions\DownloadResultAction;
use App\Filament\Pages\CheckoutPage;
use App\Filament\Pages\GradeEdit;
use App\Filament\Pages\SkillEvaluationPage;
use App\Filament\Resources\Enrollments\EnrollmentResource;
use App\Filament\Resources\Enrollments\RelationManagers\EnrollmentCommentsRelationManager;
use App\Filament\Resources\Enrollments\RelationManagers\ScholarshipsRelationManager;
use App\Filament\Resources\Invoices\InvoiceResource;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\HtmlString;

class ViewEnrollment extends ViewRecord
{
    protected static string $resource = EnrollmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('checkout')
                ->label(__('Checkout enrollment'))
                ->icon('heroicon-o-shopping-cart')
                ->color('success')
                ->url(CheckoutPage::getUrl(['enrollment_id' => $this->record->id]))
                ->visible(fn () => $this->record->status_id !== 2 && auth()->user()?->can('enrollments.edit')),

            Action::make('edit_price')
                ->label(__('Price'))
                ->icon('heroicon-o-currency-dollar')
                ->visible(fn (): bool => EnrollmentResource::canEdit($this->record))
                ->fillForm(fn () => ['total_price' => $this->record->total_price])
                ->form([
                    TextInput::make('total_price')
                        ->label(__('Price'))
                        ->numeric()
                        ->required()
                        ->minValue(0)
                        ->step(0.01)
                        ->prefix(config('academico.currency_position') === 'before' ? config('academico.currency_symbol') : null)
                        ->suffix(config('academico.currency_position') === 'after' ? config('academico.currency_symbol') : null),
                ])
                ->action(function (array $data) {
                    $this->record->update(['total_price' => $data['total_price']]);
                    $this->refreshFormData(['total_price']);
                }),

            Action::make('edit_status')
                ->label(__('Status'))
                ->icon('heroicon-o-arrow-path')
                ->visible(fn (): bool => EnrollmentResource::canEdit($this->record))
                ->fillForm(fn () => ['status_id' => $this->record->status_id])
                ->form([
                    Select::make('status_id')
                        ->label(__('Status'))
                        ->relationship('enrollmentStatus', 'name')
                        ->required()
                        ->preload(),
                ])
                ->action(function (array $data) {
                    $this->record->update(['status_id' => $data['status_id']]);
                    $this->refreshFormData(['status_id']);
                }),

            Action::make('change_course')
                ->label(__('Change course'))
                ->icon('heroicon-o-academic-cap')
                ->visible(fn (): bool => EnrollmentResource::canEdit($this->record))
                ->url(EnrollmentResource::getUrl('change-course', ['record' => $this->record])),

            Action::make('cancel')
                ->label(__('Cancel Enrollment'))
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->visible(fn (): bool => EnrollmentResource::canEdit($this->record))
                ->requiresConfirmation()
                ->modalDescription(__('This will cancel the enrollment and delete associated attendance records. This action cannot be undone.'))
                ->action(function () {
                    $this->record->cancel();
                    $this->redirect(EnrollmentResource::getUrl('index'));
                }),
        ];
    }

    protected function getInvoicesHtml(): string
    {
        $enrollment = $this->getRecord();
        $invoices = $enrollment->relatedInvoices()->unique('id');

        if ($invoices->isEmpty()) {
            return '<p class="text-sm text-gray-500">'.__('No invoices').'</p>';
        }

        $currencySymbol = config('academico.currency_symbol', '€');
        $currencyBefore = config('academico.currency_position') === 'before';
        $formatCurrency = fn ($value) => $currencyBefore
            ? $currencySymbol.' '.number_format((float) $value, 2)
            : number_format((float) $value, 2).' '.$currencySymbol;

        $rows = '';
        foreach ($invoices as $invoice) {
            $invoice->load(['invoiceDetails', 'payments', 'invoiceType']);
            $ref = e($invoice->invoice_reference);
            $date = $invoice->date?->format('d/m/Y') ?? '-';
            $total = $formatCurrency($invoice->totalPrice());
            $paid = $formatCurrency($invoice->paidTotal());
            $balance = $invoice->balance;
            $balanceFormatted = $formatCurrency($balance);
            $balanceColor = $balance > 0 ? 'text-danger-600 dark:text-danger-400' : 'text-success-600 dark:text-success-400';

            $refCell = '<a href="'.e(InvoiceResource::getUrl('view', ['record' => $invoice])).'" class="text-primary-600 hover:underline dark:text-primary-400">'.$ref.'</a>';

            $rows .= '<tr class="border-b border-gray-200 dark:border-gray-700">'
                .'<td class="px-3 py-2">'.$refCell.'</td>'
                .'<td class="px-3 py-2">'.$date.'</td>'
                .'<td class="px-3 py-2 text-right">'.$total.'</td>'
                .'<td class="px-3 py-2 text-right">'.$paid.'</td>'
                .'<td class="px-3 py-2 text-right '.$balanceColor.'">'.$balanceFormatted.'</td>'
                .'</tr>';
        }

        return '<table class="w-full text-sm">'
            .'<thead><tr class="border-b border-gray-300 dark:border-gray-600">'
            .'<th class="px-3 py-2 text-left font-medium">'.__('Invoice #').'</th>'
            .'<th class="px-3 py-2 text-left font-medium">'.__('Date').'</th>'
            .'<th class="px-3 py-2 text-right font-medium">'.__('Total').'</th>'
            .'<th class="px-3 py-2 text-right font-medium">'.__('Paid').'</th>'
            .'<th class="px-3 py-2 text-right font-medium">'.__('Balance').'</th>'
            .'</tr></thead>'
            .'<tbody>'.$rows.'</tbody>'
            .'</table>';
    }

    public function content(Schema $schema): Schema
    {
        $ownerRecord = $this->getRecord();
        $livewireData = ['ownerRecord' => $ownerRecord, 'pageClass' => static::class];

        return $schema
            ->components([
                Grid::make(['default' => 1, 'lg' => 2])
                    ->schema([
                        $this->getInfolistContentComponent()
                            ->columnSpan(1),
                        Grid::make(1)
                            ->schema([
                                Tabs::make()
                                    ->tabs([
                                        Tab::make(__('Result & Evaluation'))
                                            ->icon('heroicon-o-trophy')
                                            ->schema([
                                                Actions::make([
                                                    Action::make('edit_grades')
                                                        ->label(__('Manage Grades'))
                                                        ->icon('heroicon-o-pencil-square')
                                                        ->size('sm')
                                                        ->url(GradeEdit::getUrl(['courseId' => $ownerRecord->course_id]))
                                                        ->visible($ownerRecord->course?->evaluationType?->gradeTypes()?->count() > 0 && Gate::allows('edit-course-grades', $ownerRecord->course)),
                                                    Action::make('edit_skills')
                                                        ->label(__('Evaluate Skills'))
                                                        ->icon('heroicon-o-star')
                                                        ->size('sm')
                                                        ->url(SkillEvaluationPage::getUrl(['courseId' => $ownerRecord->course_id]))
                                                        ->visible($ownerRecord->course?->evaluationType?->skills()?->count() > 0 && Gate::allows('edit-course-grades', $ownerRecord->course)),
                                                    DownloadResultAction::make()
                                                        ->record($ownerRecord)
                                                        ->size('sm'),
                                                    DownloadCertificateAction::make()
                                                        ->record($ownerRecord)
                                                        ->size('sm'),
                                                ])->key('evaluationActions'),
                                                TextEntry::make('result.result_name.name')
                                                    ->label(__('Result'))
                                                    ->badge()
                                                    ->color($ownerRecord->result?->result_name?->color ? Color::hex($ownerRecord->result->result_name->color) : null)
                                                    ->state($ownerRecord->result?->result_name?->name)
                                                    ->placeholder('-'),
                                                TextEntry::make('total_grade')
                                                    ->label(__('Total'))
                                                    ->state($ownerRecord->grades->isEmpty() ? null : (string) $ownerRecord->grades->sum('grade'))
                                                    ->placeholder('-')
                                                    ->visible($ownerRecord->course?->evaluationType?->gradeTypes()?->count() > 0),
                                                TextEntry::make('result_comments')
                                                    ->label(__('Comments'))
                                                    ->state($ownerRecord->result?->comments?->pluck('body')->implode("\n"))
                                                    ->placeholder('-'),
                                            ]),
                                        Tab::make(__('Comments'))
                                            ->icon('heroicon-o-chat-bubble-left-right')
                                            ->schema([
                                                Livewire::make(EnrollmentCommentsRelationManager::class, [
                                                    ...$livewireData,
                                                    ...EnrollmentCommentsRelationManager::getDefaultProperties(),
                                                ])->key('comments'),
                                            ]),
                                    ]),
                                Tabs::make()
                                    ->tabs([
                                        Tab::make(__('Invoices'))
                                            ->icon('heroicon-o-document-text')
                                            ->schema([
                                                Placeholder::make('invoices_list')
                                                    ->label('')
                                                    ->content(fn () => new HtmlString($this->getInvoicesHtml())),
                                            ]),
                                        Tab::make(__('Books'))
                                            ->icon('heroicon-o-book-open')
                                            ->schema([
                                                TextEntry::make('books')
                                                    ->label(__('Books'))
                                                    ->badge()
                                                    ->state($ownerRecord->course?->books?->pluck('name')->toArray() ?? [])
                                                    ->placeholder(__('No books assigned')),
                                            ]),
                                        Tab::make(__('Scholarships'))
                                            ->icon('heroicon-o-academic-cap')
                                            ->schema([
                                                Livewire::make(ScholarshipsRelationManager::class, [
                                                    ...$livewireData,
                                                    ...ScholarshipsRelationManager::getDefaultProperties(),
                                                ])->key('scholarships'),
                                            ]),
                                    ]),
                            ])
                            ->columnSpan(1),
                    ]),
            ]);
    }
}
