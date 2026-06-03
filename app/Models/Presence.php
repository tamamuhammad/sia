<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presence extends Model
{
    protected $fillable = ['student_id', 'presence_date', 'status', 'period_id', 'group_id'];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
