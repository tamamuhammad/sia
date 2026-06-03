<?php

namespace App\Filament\Resources\Periods;

use App\Filament\Resources\Periods\Pages\ManagePeriods;
use App\Models\Period;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PeriodResource extends Resource
{
    protected static ?string $model = Period::class;

    protected static ?string $modelLabel = 'Tahun Ajaran';      
    protected static ?string $pluralModelLabel = 'Tahun Ajaran';
    protected static ?string $navigationLabel = 'Tahun Ajaran';
    protected static ?int $navigationSort = 6;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::CalendarDateRange;

    protected static ?string $recordTitleAttribute = 'Tahun Ajaran';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('name')
                    ->label('Nama Tahun Ajaran')
                    ->placeholder('YYYY/YYYY'),
                DatePicker::make('start_date')
                    ->label('Tanggal Mulai')
                    ->required(),
                DatePicker::make('end_date')
                    ->label('Tanggal Selesai')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Tahun Ajaran')
            ->columns([
                TextColumn::make('name')
                    ->label('Tahun Ajaran')
                    ->searchable(),
                TextColumn::make('start_date')
                    ->label('Tanggal Mulai')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label('Tanggal Selesai')
                    ->date()
                    ->sortable(),
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
            'index' => ManagePeriods::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return in_array(Auth::user()->role_id, [1,2]);
    }
}
