<?php

namespace Workbench\App\Filament\Resources\Users;

use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Workbench\App\Filament\Resources\Users\Pages\ManageUsers;
use Workbench\App\Models\User;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $slug = 'users';

    protected static string|\UnitEnum|null $navigationGroup = 'Cadastros';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required(),
            TextInput::make('email')->email()->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->state(fn (User $record): string => match ($record->id % 3) {
                        0 => 'Pago',
                        1 => 'Pendente',
                        default => 'Cancelado',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'Pago' => 'success',
                        'Pendente' => 'warning',
                        default => 'danger',
                    }),
                TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([
                TernaryFilter::make('email_verified_at')->nullable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ])
            ->paginated([10]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageUsers::route('/'),
        ];
    }
}
