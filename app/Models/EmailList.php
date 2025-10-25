<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailList extends Model
{
    protected $fillable = [
        'email',
        'contact_id',
    ];


    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
}
