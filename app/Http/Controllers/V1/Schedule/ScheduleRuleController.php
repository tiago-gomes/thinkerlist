<?php

namespace App\Http\Controllers\V1\Schedule;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ScheduleRule;
use App\Http\Requests\CreateScheduleRuleRequest;
use App\Http\Requests\SearchScheduleRuleRequest;
use App\Services\ScheduleRuleService;
use App\Models\User;

class ScheduleRuleController extends Controller
{
    private scheduleRuleService $scheduleRuleService;

    public function __construct(ScheduleRuleService $scheduleRuleService)
    {
        $this->scheduleRuleService = $scheduleRuleService;
    }

    /**
     * Pagination
     *
     * @param SearchScheduleRuleRequest $request
     * @return void
     */
    public function index(SearchScheduleRuleRequest $request)
    {
        // Retrieve the current authenticated user
        $user = auth()->user();

        // Define pagination parameters
        $perPage = $request->input('perPage', 10);
        $page = $request->input('page', 1);

        // Query schedule rules with a relationship to the authenticated user
        $query = ScheduleRule::where('user_id', $user->id);

        // Apply search filters if provided
        $searchTerm = $request->input('search');
        if ($searchTerm) {
            $query->where(function ($query) use ($searchTerm) {
                $query->where('title', 'like', "%$searchTerm%")
                    ->orWhere('description', 'like', "%$searchTerm%");
            });
        }

        // Apply filter for custom or recurring
        $isCustom = $request->input('is_custom');
        if ($isCustom !== null) {
            $query->where('is_custom', $isCustom);
        }

        // Apply filter for custom or recurring
        $isRecurring = $request->input('is_recurring');
        if ($isRecurring !== null) {
            $query->where('is_recurring', $isRecurring);
        }

        // Paginate the results
        $scheduleRules = $query->paginate($perPage, ['title','description', 'is_recurring', 'is_custom'], 'page', $page);

        return response()->json($scheduleRules);
    }
}
