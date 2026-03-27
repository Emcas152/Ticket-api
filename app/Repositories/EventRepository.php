<?php

namespace App\Repositories;

use App\Models\Event;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EventRepository
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Event::query()
            ->with(['venue', 'organizer'])
            ->latest('starts_at')
            ->paginate($perPage);
    }

    public function find(int $id): Event
    {
        return Event::query()
            ->with(['venue.sections.seats', 'organizer'])
            ->findOrFail($id);
    }

    public function create(array $data): Event
    {
        return Event::query()->create($data);
    }

    public function update(Event $event, array $data): Event
    {
        $event->update($data);

        return $event->fresh(['venue', 'organizer']);
    }

    public function delete(Event $event): bool
    {
        return (bool) $event->delete();
    }

    public function allByVenue(int $venueId): Collection
    {
        return Event::query()
            ->with(['venue', 'organizer'])
            ->where('venue_id', $venueId)
            ->get();
    }
}
