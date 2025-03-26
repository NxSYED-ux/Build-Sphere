<?php

namespace App\Jobs;

use App\Models\BuildingUnit;
use App\Models\ManagerBuilding;
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

class UnitNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $organizationId;
    protected $unitId;
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


    public function __construct($organizationId, $unitId, $heading, $message, $link, $initiatorId, $initiatorHeading, $initiatorMessage, $initiatorLink, $user_id = null, $userHeading = null, $userMessage = null, $userLink = null)
    {
        $this->organizationId = $organizationId;
        $this->unitId = $unitId;
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
        $unit = BuildingUnit::with('pictures')->find($this->unitId);
        $unitImage = $unit?->pictures[0]?->image;

        $organization = Organization::find($unit?->organization_id);
        $managers = ManagerBuilding::where('building_id', $unit?->building_id)->pluck('user_id');
        $initiator = User::find($this->initiatorId);

        $customHeading = $this->heading . "({$initiator->name})";

        if ($organization && $organization->owner_id) {
            $owner = User::find($organization->owner_id);

            if ($owner && $owner->id !== $this->initiatorId) {
                Notification::send($owner, new UserNotification(
                    $unitImage->file_path,
                    $customHeading,
                    $this->message,
                    $this->link
                ));
            }
        }

        if ($managers->isNotEmpty()) {
            $users = User::whereIn('id', $managers)
                ->where('id', '!=', $this->initiatorId)
                ->get();

            if ($users->isNotEmpty()) {
                Notification::send($users, new UserNotification(
                    $unitImage->file_path,
                    $customHeading,
                    $this->message,
                    $this->link
                ));
            }
        }

        if ($initiator) {
            $initiator->notify(new DatabaseOnlyNotification(
                $unitImage->file_path,
                $this->initiatorHeading,
                $this->initiatorMessage,
                $this->initiatorLink
            ));
        }

        if ($this->user_id) {
            $initiator = User::find($this->user_id);
            if ($initiator) {
                $initiator->notify(new UserNotification(
                    $unitImage->file_path,
                    $this->userHeading,
                    $this->userMessage,
                    $this->userLink
                ));
            }
        }
    }
}
