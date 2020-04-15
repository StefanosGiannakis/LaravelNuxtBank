<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    public function accounts(){
        return $this->belongsToMany(Account::class);
    }
}
