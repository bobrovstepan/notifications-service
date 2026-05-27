<?php

namespace Database\Factories;

use App\Enums\ReportStatus;
use App\Models\Report;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReportFactory extends Factory
{
    protected $model = Report::class;

    public function definition(): array
    {
        return [
            'user_id'     => 1,
            'period_from' => now()->subMonth()->toDateString(),
            'period_to'   => now()->toDateString(),
            'status'      => ReportStatus::Pending,
            'file_path'   => null,
        ];
    }

    public function ready(): static
    {
        return $this->state([
            'status'    => ReportStatus::Ready,
            'file_path' => 'test-report.csv',
        ]);
    }
}
