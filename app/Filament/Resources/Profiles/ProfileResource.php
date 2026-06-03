<?php

namespace App\Filament\Resources\Profiles;

use App\Filament\Resources\Profiles\Pages\EditProfile;
use App\Filament\Resources\Profiles\Schemas\ProfileForm;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ProfileResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $modelLabel = 'Profil';       
    protected static ?string $pluralModelLabel = 'Profil'; 
    protected static ?string $navigationLabel = 'Profil';
    protected static ?int $navigationSort = 1;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::UserCircle;

    protected static ?string $recordTitleAttribute = 'Profile';

    public static function form(Schema $schema): Schema
    {
        return ProfileForm::configure($schema);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => EditProfile::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canViewAny(): bool
    {
        return in_array(Auth::user()->role_id, [2,3,4]);
    }
}
