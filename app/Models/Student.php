<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = ['user_id', 'nis', 'name', 'birth_date', 'birth_place', 
    'phone', 'guardian_name', 'guardian_phone', 'group_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function presences()
    {
        return $this->hasMany(Presence::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    protected static function booted()
    {
        static::updated(function ($student) {
            if ($student->wasChanged('name') && $student->user_id) {
                User::where('id', $student->user_id)->update([
                    'name' => $student->name
                ]);
            }
        });

        static::deleted(function ($student) {
            if ($student->user_id) {
                User::where('id', $student->user_id)->delete();
            }
        });
    }
}
