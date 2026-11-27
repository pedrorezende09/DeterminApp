<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreActivityRequest;
use App\Http\Resources\ActivityResource;
use App\Http\Requests\UpdateActivityRequest;
use App\Models\Activity;
use App\Enums\ActivityType;
use App\Enums\ActivityStatus;
use App\Enums\ActivityPriority;

use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
         $activities = $request->user()
        ->activities()
        ->latest()
        ->get();

    return ActivityResource::collection($activities);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreActivityRequest $request)
{
    $data = $request->validated();

    $activity = new Activity();

    $activity->title = $data['title'];
    $activity->description = $data['description'] ?? null;
    $activity->category_id = $data['category_id'];
    $activity->type = ActivityType::from($data['type']);

    if (ActivityType::from($data['type']) === ActivityType::OneTime) {
        $activity->priority = ActivityPriority::from($data['priority']);
        $activity->due_date = $data['due_date'];
        $activity->status = ActivityStatus::Pending;
        $activity->ends_at = null;
    } else {
        $activity->priority = null;
        $activity->due_date = null;
        $activity->status = ActivityStatus::Active;
        $activity->ends_at = now()->endOfMonth()->toDateString();
    }

    $request->user()->activities()->save($activity);

    return response()->json([
        'message' => 'Atividade criada com sucesso.',
        'data' => new ActivityResource($activity),
    ], 201);
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $activity = request()->user()
            ->activities()
            ->findOrFail($id);


        return new ActivityResource($activity);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateActivityRequest $request, string $id)
    {
        $activity = $request->user()
            ->activities()
            ->findOrFail($id);

        $activity->fill($request->validated());

        $activity->save();

        return response()->json([
            'message' => 'Atividade atualizada com sucesso.',
            'data' => new ActivityResource($activity),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
