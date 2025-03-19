<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Organization;
use App\Notifications\DatabaseOnlyNotification;
use App\Notifications\UserNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class OrganizationOwnerNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $organizationId;
    protected $image;
    protected $heading;
    protected $message;
    protected $link;
    protected $initiatorId;
    protected $initiatorHeading;
    protected $initiatorMessage;
    protected $initiatorLink;

    public function __construct($organizationId, $image, $heading, $message, $link, $initiatorId = null, $initiatorHeading = null, $initiatorMessage = null, $initiatorLink = null)
    {
        $this->organizationId = $organizationId;
        $this->image = $image;
        $this->heading = $heading;
        $this->message = $message;
        $this->link = $link;
        $this->initiatorId = $initiatorId;

        $this->initiatorHeading = $initiatorHeading ?? 'Organization Owner Notified';
        $this->initiatorMessage = $initiatorMessage ?? 'The owner of the organization has been successfully notified.';
        $this->initiatorLink = $initiatorLink ?? '';
    }

    public function handle()
    {
        $organization = Organization::find($this->organizationId);

        if ($organization && $organization->owner_id) {
            $owner = User::find($organization->owner_id);

            if ($owner && $owner->id !== $this->initiatorId) {
                Notification::send($owner, new UserNotification(
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
