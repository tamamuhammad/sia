<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = ['student_id', 'achievement', 'notes', 'period_id', 'month'];

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
