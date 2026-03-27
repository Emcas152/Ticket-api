<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Http\Resources\EventResource;
use App\Models\Event;
use App\Services\EventService;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function __construct(
        private readonly EventService $service,
    ) {
    }

    public function index(Request $request)
    {
        $perPage = (int) $request->integer('per_page', 15);
        $events = $this->service->list($perPage);

        return $this->success([
            'items' => EventResource::collection($events->getCollection()),
            'pagination' => [
                'current_page' => $events->currentPage(),
                'last_page' => $events->lastPage(),
                'per_page' => $events->perPage(),
                'total' => $events->total(),
            ],
        ]);
    }

    public function show(Event $event)
    {
        return $this->success(new EventResource($this->service->get($event->id)));
    }

    public function store(StoreEventRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;

        $event = $this->service->create($data)->load(['venue', 'organizer']);

        return $this->success(new EventResource($event), 'Event created successfully.', 201);
    }

    public function update(UpdateEventRequest $request, Event $event)
    {
        $updated = $this->service->update($event, $request->validated());

        return $this->success(new EventResource($updated), 'Event updated successfully.');
    }

    public function destroy(Event $event)
    {
        $this->service->delete($event);

        return $this->success(null, 'Event deleted successfully.');
    }
}
