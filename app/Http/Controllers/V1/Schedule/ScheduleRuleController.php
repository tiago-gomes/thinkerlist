<?php

namespace App\Http\Controllers\V1\Schedule;

use App\Http\Controllers\Controller;
use App\Jobs\CreateBookingJob;
use App\Jobs\UpdateBookingJob;
use App\Models\ScheduleRule;
use App\Http\Requests\ScheduleRuleRequest;
use App\Http\Requests\SearchScheduleRuleRequest;
use App\Enums\ErrorCode;
use InvalidArgumentException;
use Exception;

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
     * @param ScheduleRuleRequest $request
     * @return void
     */
    public function store(ScheduleRuleRequest $request)
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

    public function update(ScheduleRuleRequest $request, int $schedule_rule_id)
    {
        // find the specific schedule rule
        $scheduleRule = ScheduleRule::find($schedule_rule_id);
        if (!$scheduleRule) {
            throw new Exception("Schedule rule not found", ErrorCode::BAD_REQUEST->value);
        }

        // verify if user as access to the schedule rule
        $user = $request->user();
        if($user->id != $scheduleRule->user_id) {
            throw new Exception("Unauthorized.", ErrorCode::UNAUTHORIZED->value);
        }

        // validated data
        $params = $request->validated();

        try {
            // update new schedule rules
            $scheduleRule->update($params);

            // Execute in background
            dispatch(new UpdateBookingJob($scheduleRule, $params))
                ->onQueue('recursive-queue');
        } catch (\Throwable $e) {
            // Handle dispatching error
            \Log::error("Failed to dispatch UpdateBookingJob: " . $e->getMessage());
            return response()->json(['error' => 'Failed to update schedule rule: '. $e->getMessage()], ErrorCode::INTERNAL_SERVER_ERROR->value);
        }

        // Return response
        return response()->json($scheduleRule, ErrorCode::OK->value);
    }
}
