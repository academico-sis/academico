<?php

namespace App\Filament\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';

    public function isReadOnly(): bool
    {
        return false;
    }

    public static function getTitle(mixed $ownerRecord, string $pageClass): string
    {
        return __('Comments');
    }

    /**
     * Users who can edit the commented record, or who hold `comments.edit`, can add comments.
     */
    protected function getCreateAuthorizationResponse(): Response
    {
        return $this->canEditAnyComment() || $this->getPageClass()::getResource()::canEdit($this->getOwnerRecord())
            ? Response::allow()
            : Response::deny();
    }

    /**
     * A comment can be changed by its author, or by users who hold `comments.edit`.
     */
    protected function getEditAuthorizationResponse(Model $record): Response
    {
        return $this->canEditAnyComment() || $record->author()->is(auth()->user())
            ? Response::allow()
            : Response::deny();
    }

    protected function getDeleteAuthorizationResponse(Model $record): Response
    {
        return $this->getEditAuthorizationResponse($record);
    }

    protected function canEditAnyComment(): bool
    {
        return auth()->user()?->can('comments.edit') ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('author.name')
                    ->label(__('Author')),
                TextColumn::make('body')
                    ->label(__('Comment'))
                    ->wrap()
                    ->limit(100),
                TextColumn::make('created_at')
                    ->label(__('Date'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('Add a comment'))
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['author_id'] = auth()->id();

                        return $data;
                    }),
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Textarea::make('body')
                ->label(__('Comment'))
                ->required()
                ->rows(3)
                ->columnSpanFull(),
            Hidden::make('author_id')
                ->default(auth()->id()),
        ]);
    }
}
