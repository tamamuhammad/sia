<?php

namespace App\Filament\Resources\Preceptors;

use App\Filament\Resources\Preceptors\Pages\ManagePreceptors;
use App\Models\Preceptor;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PreceptorResource extends Resource
{
    protected static ?string $model = Preceptor::class;

    protected static ?string $modelLabel = 'Asatidz';      
    protected static ?string $pluralModelLabel = 'Asatidz';
    protected static ?string $navigationLabel = 'Asatidz';
    protected static ?int $navigationSort = 5;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::Users;

    protected static ?string $recordTitleAttribute = 'Asatidz';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('user_id')
                    ->hidden()
                    ->label('ID User'),
                TextInput::make('user.username')
                    ->required()
                    ->label('Nomor Induk Asatidz')
                    ->formatStateUsing(fn ($record) => $record?->user?->username)
                    ->maxLength(20),
                TextInput::make('name')
                    ->required()
                    ->label('Nama'),
                TextInput::make('phone')
                    ->tel()
                    ->label('No. Telepon'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Asatidz')
            ->columns([
                TextColumn::make('user.username')
                    ->label('NIS'),
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('No. Telepon')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->modalWidth('lg'),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePreceptors::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return in_array(Auth::user()->role_id, [1,2]);
    }
}
