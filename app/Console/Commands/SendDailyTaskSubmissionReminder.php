<?php

namespace App\Console\Commands;

use App\Models\TaskModel;
use App\Models\User;
use App\Services\FcmService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendDailyTaskSubmissionReminder extends Command
{
    protected $fcmService;

    public function __construct(FcmService $fcmService)
    {
        parent::__construct();
        $this->fcmService = $fcmService;
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-daily-task-submission-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Log::Info('Daily task submission reminder sent');
        $this->info('Task submission reminders sent successfully!');
        $tasks = TaskModel::whereIn('t_status', ['active', 'inProgress']) // specially not canceled tasks
            ->whereDate('t_planed_end_time', now()->toDateString()) // same day
            ->get();

        foreach ($tasks as $task) {
            $taskAssignees = json_decode($task->t_assigned_to, true);
            $users_id = [];
            foreach ($taskAssignees as $taskAssignee) {
                $user = User::find($taskAssignee);
                if (!$user) {
                    Log::warning('User not found for task assignee ID: ' . $taskAssignee);
                    continue;
                }

                if ($user->hasSubmittedTask($task->t_id, $taskAssignee)) {
                    continue;
                }
                $users_id[] = $taskAssignee;

                // Log::Info('Task submission reminder sent to user: ' . $user->name . ' for task: ' . $task->t_id);
            }

            try {
                $this->fcmService->sendNotification(
                    'تذكير: لا تنسَ تسليم التكليف قبل انتهاء الموعد! 🕒',
                    $task->t_content,
                    $users_id,
                    config('constants.notification_type.task'),
                    $task->t_id
                );
            } catch (\Throwable $th) {
                Log::error($th->getMessage());
            }
        }
    }
}
