<?php

declare(strict_types=1);

namespace App\Filters;

use App\DTO\Notification\NotificationFilterDTO;
use App\Enums\ChannelName;
use App\Enums\NotificationStatus;
use App\Models\Channel;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Builder;

class NotificationQueryFilter
{
    public function apply(Builder $query, NotificationFilterDTO $filter): Builder
    {
        $this->filterByStatus($query, $filter->status);
        $this->filterByChannel($query, $filter->channel);

        return $query;
    }

    private function filterByStatus(Builder $query, ?NotificationStatus $status): Builder
    {
        return $query->when($status, fn ($q) => $q->where(Notification::FIELD_STATUS, $status->value));
    }

    private function filterByChannel(Builder $query, ?ChannelName $channel): Builder
    {
        return $query->when($channel, fn ($q) => $q->whereHas('channel', fn ($q) => $q->where(Channel::FIELD_NAME, $channel->value)));
    }
}
