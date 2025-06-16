<?php

namespace Tests\Unit\Stubs;

use Eloquence\Behaviours\ReadOnly\HasReadOnly;
use Illuminate\Database\Eloquent\Model;

final class ReadOnlyModelStub extends Model
{
    use HasReadOnly;

    protected $fillable = [
        'value'
    ];
}