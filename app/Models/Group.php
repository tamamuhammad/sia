<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = ['name', 'preceptor_id'];

    public function preceptor()
    {
        return $this->belongsTo(Preceptor::class);
    }

    public function presences()
    {
        return $this->hasMany(Presence::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
