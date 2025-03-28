<?php

namespace App\Jobs;

use App\Models\Building;
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

class BuildingNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $organizationId;
    protected $buildingId;
    protected $heading;
    protected $message;
    protected $link;
    protected $initiatorId;
    protected $initiatorHeading;
    protected $initiatorMessage;
    protected $initiatorLink;

    protected $toAdmin;
    protected $adminHeading;
    protected $adminMessage;
    protected $adminLink;

    public function __construct($organizationId, $buildingId, $heading, $message, $link, $initiatorId, $initiatorHeading, $initiatorMessage, $initiatorLink, $toAdmin = false, $adminHeading = null, $adminMessage = null, $adminLink = null)
    {
        $this->organizationId = $organizationId;
        $this->buildingId = $buildingId;
        $this->heading = $heading;
        $this->message = $message;
        $this->link = $link;

        $this->initiatorId = $initiatorId;
        $this->initiatorHeading = $initiatorHeading;
        $this->initiatorMessage = $initiatorMessage;
        $this->initiatorLink = $initiatorLink;

        $this->toAdmin = $toAdmin;
        $this->adminHeading = $adminHeading;
        $this->adminMessage = $adminMessage;
        $this->adminLink = $adminLink;
    }

    public function handle()
    {
        $building = Building::with('pictures')->find($this->buildingId);
        $ImagePath = optional($building->pictures->first())->file_path ?? 'uploads/logo/application-logo.png';

        $organization = Organization::find($this->organizationId);
        $managers = ManagerBuilding::where('building_id', $this->buildingId)->pluck('user_id');
        $initiator = User::find($this->initiatorId);

        if ($organization && $organization->owner_id) {
            $owner = User::find($organization->owner_id);

            if ($owner && $owner->id !== $this->initiatorId) {
                Notification::send($owner, new UserNotification(
                    $ImagePath,
                    $this->heading,
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
                    $ImagePath,
                    $this->heading,
                    $this->message,
                    $this->link
                ));
            }
        }

        if ($initiator) {
            $initiator->notify(new DatabaseOnlyNotification(
                $ImagePath,
                $this->initiatorHeading,
                $this->initiatorMessage,
                $this->initiatorLink
            ));
        }

        if($this->toAdmin){
            $admins = User::where('role_id', 1)
                ->where('id', '!=', $this->initiatorId)
                ->get();

            if ($admins->isNotEmpty()) {
                Notification::send($admins, new UserNotification(
                    $ImagePath,
                    $this->adminHeading ?? "{$this->initiatorHeading} by {$initiator?->name}",
                    $this->adminMessage ?? "{$this->initiatorMessage}",
                    $this->adminLink ?? "{$this->initiatorLink}"
                ));
            }
        }
    }
}
