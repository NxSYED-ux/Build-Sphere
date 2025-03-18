<?php

namespace App\Jobs;

use App\Models\StaffMember;
use App\Models\User;
use App\Notifications\DatabaseOnlyNotification;
use App\Notifications\UserNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class DepartmentStaffNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $departmentId;
    protected $image;
    protected $heading;
    protected $message;
    protected $link;
    protected $initiatorId;

    public function __construct($departmentId, $image, $heading, $message, $link, $initiatorId = null)
    {
        $this->departmentId = $departmentId;
        $this->image = $image;
        $this->heading = $heading;
        $this->message = $message;
        $this->link = $link;
        $this->initiatorId = $initiatorId;
    }

    public function handle()
    {
        $staffUserIds = StaffMember::where('department_id', $this->departmentId)->pluck('user_id');

        $users = User::whereIn('id', $staffUserIds)->get();

        if ($users->isNotEmpty()) {
            Notification::send($users, new UserNotification(
                $this->image,
                $this->heading,
                $this->message,
                $this->link
            ));
        }

        if ($this->initiatorId) {
            $initiator = User::find($this->initiatorId);
            if ($initiator) {
                $initiator->notify(new DatabaseOnlyNotification(
                    $this->image,
                    'Department Notification Sent',
                    'Your request to notify department staff has been successfully processed. All relevant members have been informed.',
                    '#'
                ));
            }
        }
    }
}

