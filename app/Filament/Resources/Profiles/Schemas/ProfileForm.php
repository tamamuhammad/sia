<?php

namespace App\Filament\Resources\Profiles\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class ProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()
                ->schema([
                    Grid::make()
                        ->columns(1)
                        ->columnSpan(1)
                        ->schema([
                            Section::make('Profil User')
                                ->extraAttributes([
                                    'class' => implode(' ', [
                                        '[&_.fi-section-header]:bg-gradient-to-br',
                                        '[&_.fi-section-header]:from-emerald-500',
                                        '[&_.fi-section-header]:to-teal-600',
                                        '[&_.fi-section-header]:dark:from-emerald-900',
                                        '[&_.fi-section-header]:dark:to-teal-950',
                                        '[&_.fi-section-header]:rounded-t-2xl',
                                        '[&_.fi-section-header-heading]:!text-white',
                                        '[&_.fi-section-header-description]:!text-white/80',
                                        '[&_.fi-section-header_.fi-icon-btn]:!text-white',
                                    ])
                                ])
                                ->schema([
                                    TextEntry::make('pas')
                                        ->hiddenLabel()
                                        ->html()
                                        ->extraAttributes(['class' => 'flex justify-center'])
                                        ->state(function ($record) {
                                            $initials = collect(explode(' ', $record->name))
                                                ->map(fn ($segment) => $segment[0] ?? '')
                                                ->take(2)
                                                ->join('');

                                            return '
                                                <div class="shrink-0 relative">
                                                    <div class="w-38 h-38 rounded-full bg-gray-500 flex items-center justify-center text-3xl font-bold text-white border-4 border-white/10 shadow-md">
                                                        ' . strtoupper($initials) . '
                                                    </div>
                                                </div>
                                            ';
                                        }),
                                ]),
                        ]),

                    Grid::make()
                        ->columns(1)
                        ->columnSpan(2)
                        ->schema([
                            Section::make('Data Pribadi')
                                ->columns(2)
                                ->schema([
                                    TextInput::make('name')
                                        ->label('Nama Lengkap')
                                        ->columnSpan(fn () => in_array(Auth::user()->role_id, [2, 4]) ? 1 : 2)
                                        ->required(),
                                        
                                    TextInput::make('username')
                                        ->label('NIS')
                                        ->disabled()
                                        ->dehydrated(),
                                        
                                    TextInput::make('groupName')
                                        ->label('Kelompok Mengaji')
                                        ->disabled()
                                        ->visible(fn () => in_array(Auth::user()->role_id, [2, 4])),

                                    TextInput::make('preceptor.phone')
                                        ->label('No. Telepon / WA')
                                        ->tel()
                                        ->mask('9999-9999-9999')
                                        ->required()
                                        ->visible(fn () => Auth::user()->role_id == 3),

                                    TextInput::make('student.phone')
                                        ->label('No. Telepon Santri')
                                        ->tel()
                                        ->mask('9999-9999-9999')
                                        ->required()
                                        ->visible(fn () => in_array(Auth::user()->role_id, [2, 4])),
                                        
                                    TextInput::make('student.birth_place')
                                        ->label('Tempat Lahir')
                                        ->required()
                                        ->visible(fn () => in_array(Auth::user()->role_id, [2, 4])),
                                        
                                    DatePicker::make('student.birth_date')
                                        ->label('Tanggal Lahir')
                                        ->required()
                                        ->reactive()
                                        ->native(false)
                                        ->visible(fn () => in_array(Auth::user()->role_id, [2, 4])),
                                        
                                    TextInput::make('student.guardian_name')
                                        ->label('Nama Wali')
                                        ->required()
                                        ->visible(fn () => in_array(Auth::user()->role_id, [2, 4])),
                                        
                                    TextInput::make('student.guardian_phone')
                                        ->label('No. Telepon Wali')
                                        ->tel()
                                        ->mask('9999-9999-9999')
                                        ->required()
                                        ->visible(fn () => in_array(Auth::user()->role_id, [2, 4])),
                                ]),
                        ]),
                ])
                ->columns(3)
                ->columnSpanFull(),
        ]);
    }
}
