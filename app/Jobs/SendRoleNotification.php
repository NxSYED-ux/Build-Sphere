<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\DatabaseOnlyNotification;
use App\Notifications\UserNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class SendRoleNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $roleId;
    protected $image;
    protected $heading;
    protected $message;
    protected $link;
    protected $initiatorId;
    protected $initiatorHeading;
    protected $initiatorMessage;
    protected $initiatorLink;

    public function __construct($roleId, $image, $heading, $message, $link, $initiatorId = null, $initiatorHeading = null, $initiatorMessage = null, $initiatorLink = null)
    {
        $this->roleId = $roleId;
        $this->image = $image;
        $this->heading = $heading;
        $this->message = $message;
        $this->link = $link;
        $this->initiatorId = $initiatorId;

        $this->initiatorHeading = $initiatorHeading ?? 'Role-based Notification Sent';
        $this->initiatorMessage = $initiatorMessage ?? 'All users with the specified role have been successfully notified.';
        $this->initiatorLink = $initiatorLink ?? '';
    }

    public function handle()
    {
        $users = User::where('role_id', $this->roleId)->where('id', '!=', $this->initiatorId)->get();

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
                    $this->initiatorHeading,
                    $this->initiatorMessage,
                    $this->initiatorLink
                ));
            }
        }
    }
}
