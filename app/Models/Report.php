<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ReportStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

#[Fillable(['user_id', 'period_from', 'period_to', 'status', 'file_path'])]
/**
 * @property ReportStatus $status
 * @property Carbon $period_from
 * @property Carbon $period_to
 */
class Report extends Model
{
    use HasFactory;

    public const string FIELD_USER_ID = 'user_id';

    public const string FIELD_PERIOD_FROM = 'period_from';

    public const string FIELD_PERIOD_TO = 'period_to';

    public const string FIELD_STATUS = 'status';

    public const string FIELD_FILE_PATH = 'file_path';

    protected function casts(): array
    {
        return [
            'status' => ReportStatus::class,
            'period_from' => 'date',
            'period_to' => 'date',
        ];
    }
}
