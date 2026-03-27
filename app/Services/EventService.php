<?php

namespace App\Services;

use App\Models\Event;
use App\Repositories\EventRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EventService
{
    public function __construct(
        private readonly EventRepository $repo,
    ) {
    }

    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repo->paginate($perPage);
    }

    public function get(int $id): Event
    {
        return $this->repo->find($id);
    }

    public function create(array $data): Event
    {
        if (($data['status'] ?? 'draft') === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        return $this->repo->create($data);
    }

    public function update(Event $event, array $data): Event
    {
        if (($data['status'] ?? null) === 'published' && ! $event->published_at) {
            $data['published_at'] = now();
        }

        return $this->repo->update($event, $data);
    }

    public function delete(Event $event): bool
    {
        return $this->repo->delete($event);
    }
}
