<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    #[\Override]
    protected function casts(): array
    {
        return [
            'name' => 'string',
        ];
    }
}
