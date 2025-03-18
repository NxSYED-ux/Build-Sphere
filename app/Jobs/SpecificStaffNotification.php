<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\StaffMember;
use App\Notifications\DatabaseOnlyNotification;
use App\Notifications\UserNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class SpecificStaffNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $organizationId;
    protected $staffUserId;
    protected $image;
    protected $heading;
    protected $message;
    protected $link;
    protected $initiatorId;
    protected $initiatorHeading;
    protected $initiatorMessage;
    protected $initiatorLink;

    public function __construct($organizationId, $staffUserId, $image, $heading, $message, $link, $initiatorId = null, $initiatorHeading = null, $initiatorMessage = null, $initiatorLink = null)
    {
        $this->organizationId = $organizationId;
        $this->staffUserId = $staffUserId;
        $this->image = $image;
        $this->heading = $heading;
        $this->message = $message;
        $this->link = $link;
        $this->initiatorId = $initiatorId;

        $this->initiatorHeading = $initiatorHeading ?? 'Staff Member Notified';
        $this->initiatorMessage = $initiatorMessage ?? 'The specified staff member has been successfully notified.';
        $this->initiatorLink = $initiatorLink ?? '#';
    }

    public function handle()
    {
        $staffMember = StaffMember::where('organization_id', $this->organizationId)
            ->where('user_id', $this->staffUserId)
            ->first();

        if ($staffMember) {
            $user = User::find($this->staffUserId);
            if ($user) {
                Notification::send($user, new UserNotification(
                    $this->image,
                    $this->heading,
                    $this->message,
                    $this->link
                ));
            }
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
