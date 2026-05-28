<?php

declare(strict_types=1);

namespace App\Http\Requests\Report;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'min:1'],
            'period_from' => ['nullable', 'date', 'before_or_equal:period_to'],
            'period_to' => ['nullable', 'date', 'after_or_equal:period_from'],
        ];
    }
}
