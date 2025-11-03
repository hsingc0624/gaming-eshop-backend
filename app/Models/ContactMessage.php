<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/** @property string $name @property string $email @property string $message */
class ContactMessage extends Model
{
    use HasFactory;
    protected $fillable = ['name','email','message'];
}
