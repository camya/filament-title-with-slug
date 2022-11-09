<?php

namespace Camya\Filament\Tests\Support;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function newFactory(): RecordFactory
    {
        return RecordFactory::new();
    }
}
