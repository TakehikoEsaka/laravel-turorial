<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    public function tasks()
    {
        // 1対多の1の方にこれを記載する。たどることが出来るのは1の方から
        return $this->hasMany('App\Models\Task');
    }
}
