<?php

namespace App\Http\Controllers\V1\Schedule;

use App\Http\Controllers\Controller;
use App\Jobs\CreateBookingJob;
use App\Models\ScheduleRule;
use App\Http\Requests\CreateScheduleRuleRequest;
use App\Http\Requests\SearchScheduleRuleRequest;
use App\Enums\ErrorCode;
use InvalidArgumentException;

class ScheduleRuleController extends Controller
{

    public function __construct()
    {
        //
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

        return response()->json($scheduleRules, ErrorCode::OK->value);
    }

    /**
     * Create a new Schedule Rule
     *
     * @param CreateScheduleRuleRequest $request
     * @return void
     */
    public function store(CreateScheduleRuleRequest $request)
    {
        $params = $request->validated();
        $user = $request->user();

        // check if is_custom and is_recursive are both true
        if (
            isset($params['is_recurring']) && $params['is_recurring'] == true &&
            isset($params['is_custom']) && $params['is_custom'] == true
        ) {
            throw new InvalidArgumentException("is_custom and is_recursive can not both be true.", ErrorCode::UNPROCESSABLE_ENTITY->value);
        }

        // check if schedule rule title is a duplicate
        $duplicate = ScheduleRule::where('title', $params['title'])->first();
        if ($duplicate) {
            throw new InvalidArgumentException("A schedule rule with this title already exists.", ErrorCode::UNPROCESSABLE_ENTITY->value);
        }

        // create new schedule rule
        $scheduleRule = $user->scheduleRules()->create($params);

        // execute in background
        dispatch(new CreateBookingJob($scheduleRule, $params))
            ->onQueue('recursive-queue');

        return response()->json($scheduleRule, ErrorCode::CREATED->value);
    }
}
