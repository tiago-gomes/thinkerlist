<?php

namespace App\Http\Controllers;

use App\Models\Episode;
use App\Services\PartService;
use Illuminate\Http\Request;

class PartController extends Controller
{
    private $partService;

    public function __construct(PartService $partService)
    {
        $this->partService = $partService;
    }

    public function getAllParts(int $episodeId, Request $request)
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
}
