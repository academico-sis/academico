<?php

namespace App\Filament\Resources\Enrollments\RelationManagers;

use App\Filament\RelationManagers\CommentsRelationManager;
use App\Models\Enrollment;
use App\Models\Invoice;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EnrollmentCommentsRelationManager extends CommentsRelationManager
{
    public function table(Table $table): Table
    {
        return parent::table($table)
            ->modifyQueryUsing(function (Builder $query) {
                /** @var Enrollment $enrollment */
                $enrollment = $this->getOwnerRecord();

                $invoiceIds = $enrollment->invoices()->pluck('id')->toArray();

                if (empty($invoiceIds)) {
                    return $query;
                }

                return $query->orWhere(function (Builder $q) use ($invoiceIds) {
                    $q->where('commentable_type', (new Invoice)->getMorphClass())
                        ->whereIn('commentable_id', $invoiceIds);
                });
            })
            ->columns([
                TextColumn::make('author.name')
                    ->label(__('Author')),
                TextColumn::make('body')
                    ->label(__('Comment'))
                    ->wrap()
                    ->limit(100),
                TextColumn::make('commentable_type')
                    ->label(__('Source'))
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        (new Invoice)->getMorphClass() => __('Invoice'),
                        default => __('Enrollment'),
                    }),
                TextColumn::make('created_at')
                    ->label(__('Date'))
                    ->dateTime()
                    ->sortable(),
            ]);
    }
}
