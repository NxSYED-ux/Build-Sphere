<?php

namespace App\Jobs;

use App\Models\ManagerBuilding;
use App\Models\Membership;
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

class MembershipNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $organizationId;
    protected $membershipId;
    protected $heading;
    protected $message;
    protected $link;
    protected $initiatorId;
    protected $initiatorHeading;
    protected $initiatorMessage;
    protected $initiatorLink;
    protected $user_id;
    protected $userHeading;
    protected $userMessage;
    protected $userLink;


    public function __construct($organizationId, $membershipId, $heading, $message, $link, $initiatorId, $initiatorHeading, $initiatorMessage, $initiatorLink, $user_id = null, $userHeading = null, $userMessage = null, $userLink = null)
    {
        $this->organizationId = $organizationId;
        $this->membershipId = $membershipId;
        $this->heading = $heading;
        $this->message = $message;
        $this->link = $link;

        $this->initiatorId = $initiatorId;
        $this->initiatorHeading = $initiatorHeading;
        $this->initiatorMessage = $initiatorMessage;
        $this->initiatorLink = $initiatorLink;

        $this->user_id = $user_id;
        $this->userHeading = $userHeading;
        $this->userMessage = $userMessage;
        $this->userLink = $userLink;
    }

    public function handle()
    {
        $membership = Membership::find($this->membershipId);
        $membershipImagePath = $membership->image ?? 'uploads/logo/application-logo.png';


        $organization = Organization::find($membership?->organization_id);
        $managers = ManagerBuilding::where('building_id', $membership?->building_id)->pluck('user_id');
        $initiator = User::find($this->initiatorId);

        $customHeading = $this->heading . ($initiator ? " ({$initiator->name})" : '');

        if ($organization && $organization->owner_id) {
            $owner = User::find($organization->owner_id);

            if ($owner && $owner->id !== $this->initiatorId) {
                Notification::send($owner, new UserNotification(
                    $membershipImagePath,
                    $customHeading,
                    $this->message,
                    ['web' => $this->link]
                ));
            }
        }

        if ($managers->isNotEmpty()) {
            $users = User::whereIn('id', $managers)
                ->where('id', '!=', $this->initiatorId)
                ->get();

            if ($users->isNotEmpty()) {
                Notification::send($users, new UserNotification(
                    $membershipImagePath,
                    $customHeading,
                    $this->message,
                    ['web' => $this->link]
                ));
            }
        }

        if ($initiator) {
            $initiator->notify(new DatabaseOnlyNotification(
                $membershipImagePath,
                $this->initiatorHeading,
                $this->initiatorMessage,
                ['web' =>$this->initiatorLink, 'mobile' => $this->userLink]
            ));
        }

        if ($this->user_id && $this->user_id !== $this->initiatorId) {
            $userRecipient = User::find($this->user_id);
            if ($userRecipient) {
                $userRecipient->notify(new UserNotification(
                    $membershipImagePath,
                    $this->userHeading,
                    $this->userMessage,
                    ['mobile' => $this->userLink]
                ));
            }
        }
    }
}
