<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ChannelName;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name'])]
/**
 * @property ChannelName $name
 */
class Channel extends Model
{
    use HasFactory;

    public const string FIELD_NAME = 'name';

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'name' => ChannelName::class,
        ];
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }
}
