<?php

namespace App\Filament\Resources\Groups;

use App\Filament\Resources\Groups\Pages\ManageGroups;
use App\Models\Group;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class GroupResource extends Resource
{
    protected static ?string $model = Group::class;

    protected static ?string $modelLabel = 'Kelompok Mengaji';      
    protected static ?string $pluralModelLabel = 'Kelompok Mengaji';
    protected static ?string $navigationLabel = 'Kelompok Mengaji';
    protected static ?int $navigationSort = 4;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::Swatch;

    protected static ?string $recordTitleAttribute = 'Kelompok Mengaji';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Select::make('preceptor_id')
                    ->label('Pengampu')
                    ->relationship('preceptor', 'name')
                    ->placeholder('None')
                    ->preload()
                    ->searchable()
                    ->native(false),
                TextInput::make('name')
                    ->label('Nama Kelompok'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Kelompok Mengaji')
            ->columns([
                TextColumn::make('preceptor.name')
                    ->label('Pengampu')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Nama Kelompok')
                    ->sortable()
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
            'index' => ManageGroups::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return in_array(Auth::user()->role_id, [1,2]);
    }
}
