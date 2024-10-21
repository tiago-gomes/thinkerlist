<?php

namespace App\Http\Controllers;

use App\Http\Requests\PartCreateRequest;
use App\Http\Requests\PartUpdateRequest;
use App\Http\Requests\PartDeleteRequest;
use App\Models\Episode;
use App\Services\PartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PartController extends Controller
{
    private $partService;

    public function __construct(PartService $partService)
    {
        $this->partService = $partService;
    }

    public function getAllParts(int $episodeId, Request $request): JsonResponse
    {
        $episode = $this->partService->checkIfEpisodeExists($episodeId);
        if (!$episode) {
            return response()->json(['message' => 'Episode not found'], 404);
        }

        return response()->json([
            "data" => $this->partService->getEpisodeParts($episodeId)
        ],
        200
        );
    }

    public function create(PartCreateRequest $request): JsonResponse
    {
        return response()->json([
            "data" => $this->partService->create($request->validated())
        ],
        200
        );
    }

    public function update(PartUpdateRequest $request): JsonResponse
    {
        $data       = $request->validated();
        $position   = $data['new_position'];

        return response()->json([
            "data" => $this->partService->update($data, $position)
        ],
        200
        );
    }

    public function delete(PartDeleteRequest $request)
    {
        $item = $request->validated();

        $episode = $this->partService->checkIfEpisodeExists($item['episode_id']);
        if (!$episode) {
            return response()->json(['message' => 'Episode not found'], 404);
        }

        $delete = $this->partService->delete([
            'part_id' => $item['part_id'],
            'episode_id' => $item['episode_id'],
            'position' => $item['position'],
        ]);

        if(!$delete) {
            return response()->json(['message' => 'Part not found'], 404);
        }

        return response()->json(['message' => 'Part deleted successfully'], 200);
    }
}
