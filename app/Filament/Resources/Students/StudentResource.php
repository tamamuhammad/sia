<?php

namespace App\Filament\Resources\Students;

use App\Filament\Resources\Students\Pages\ManageStudents;
use App\Models\Student;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $modelLabel = 'Santri';      
    protected static ?string $pluralModelLabel = 'Santri';
    protected static ?string $navigationLabel = 'Santri';
    protected static ?int $navigationSort = 3;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::UserGroup;

    protected static ?string $recordTitleAttribute = 'Santri';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Hidden::make('user_id'),
                Grid::make(12)
                    ->schema([
                        Section::make('Identitas Santri')
                            ->icon('heroicon-o-identification')
                            ->columnSpan(8)
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('user.username')
                                            ->label('Nomor Induk Santri')
                                            ->required()
                                            ->formatStateUsing(fn ($record) => $record?->user?->username)
                                            ->maxLength(20),

                                        Select::make('group_id')
                                            ->label('Kelompok Mengaji')
                                            ->relationship('group', 'name')
                                            ->preload()
                                            ->searchable()
                                            ->native(false)
                                            ->required(),

                                        TextInput::make('name')
                                            ->label('Nama Lengkap')
                                            ->required()
                                            ->columnSpanFull()
                                            ->placeholder('Masukkan Nama Lengkap'),

                                        TextInput::make('birth_place')
                                            ->label('Tempat Lahir')
                                            ->required(),

                                        DatePicker::make('birth_date')
                                            ->label('Tanggal Lahir')
                                            ->required()
                                            ->displayFormat('d F Y')
                                            ->native(false),
                                    ]),
                            ]),

                        Section::make('Kontak & Orang Tua')
                            ->icon('heroicon-o-phone')
                            ->columnSpan(4)
                            ->schema([
                                TextInput::make('phone')
                                    ->label('No. Telepon Santri')
                                    ->tel(),

                                TextInput::make('guardian_name')
                                    ->label('Nama Wali / Orang Tua')
                                    ->required()
                                    ->placeholder('Nama Ayah/Ibu'),

                                TextInput::make('guardian_phone')
                                    ->label('No. Telepon Wali')
                                    ->tel(),
                            ]),
                    ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Grid::make(12)
                    ->schema([
                        Section::make(fn ($record) => $record->name)
                            ->icon('heroicon-m-user')
                            ->columnSpan(8)
                            ->schema([
                                Grid::make(10)
                                    ->schema([
                                        TextEntry::make('user.username')
                                            ->label('NIS')
                                            ->weight(FontWeight::Bold)
                                            ->copyable()
                                            ->icon('heroicon-m-identification')
                                            ->columnSpan(5),

                                        TextEntry::make('group.name')
                                            ->label('Kelompok Mengaji')
                                            ->badge()
                                            ->color('info')
                                            ->placeholder('Belum ada kelompok')
                                            ->columnSpan(5),

                                        TextEntry::make('birth')
                                            ->label('Tempat, Tanggal Lahir')
                                            ->getStateUsing(fn ($record) => Carbon::parse($record->birth_date)->format('d F Y') ? $record->birth_place . ', ' . Carbon::parse($record->birth_date)->format('d F Y') : $record->birth_place)
                                            ->columnSpan(5),

                                        TextEntry::make('created_at')
                                            ->label('Terdaftar')
                                            ->dateTime('d F Y, H:i')
                                            ->size(TextSize::ExtraSmall)
                                            ->color('gray')
                                            ->columnSpan(5),

                                        TextEntry::make('updated_at')
                                            ->label('Update Terakhir')
                                            ->dateTime('d F Y, H:i')
                                            ->size(TextSize::ExtraSmall)
                                            ->color('gray')
                                            ->columnSpan(5),
                                    ])
                                    ->extraAttributes(['class' => 'gap-y-4']),
                            ])
                            ->compact(),

                        Section::make('Kontak & Sistem')
                            ->icon('heroicon-m-phone')
                            ->columnSpan(4)
                            ->schema([
                                TextEntry::make('phone')
                                    ->label('Telepon Santri')
                                    ->icon('heroicon-m-device-phone-mobile')
                                    ->placeholder('-'),

                                TextEntry::make('guardian_name')
                                    ->label('Nama Wali'),

                                TextEntry::make('guardian_phone')
                                    ->label('Telepon Wali')
                                    ->icon('heroicon-m-phone')
                                    ->placeholder('-'),
                            ])
                            ->compact(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Santri')
            ->columns([
                TextColumn::make('user.username')
                    ->label('NIS')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Telepon'),
                TextColumn::make('guardian_name')
                    ->label('Nama Wali')
                    ->searchable(),
                TextColumn::make('guardian_phone')
                    ->label('Telepon Wali'),
                TextColumn::make('group.name')
                    ->label('Kelompok Mengaji')
                    ->searchable()
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
                Action::make('print_qr')
                    ->label('QR Absen')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn ($record) => route('profile.print-qr', ['studentId' => $record->id]), shouldOpenInNewTab: true),
                ViewAction::make(),
                EditAction::make(),
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
            'index' => ManageStudents::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return in_array(Auth::user()->role_id, [1,2]);
    }
}
