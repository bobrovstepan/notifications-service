<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Channel;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChannelFactory extends Factory
{
    protected $model = Channel::class;

    public function definition(): array
    {
        return [
            'code' => 'sms',
            'name' => 'SMS',
            'is_active' => true,
        ];
    }

    public function sms(): static
    {
        return $this->state(['code' => 'sms', 'name' => 'SMS']);
    }

    public function email(): static
    {
        return $this->state(['code' => 'email', 'name' => 'Email']);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
