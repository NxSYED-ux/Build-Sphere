<?php

namespace App\Jobs;

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

class OrganizationOwnerNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $organizationId;
    protected $buildingId;
    protected $image;
    protected $heading;
    protected $message;
    protected $link;
    protected $withManager;
    protected $initiatorId;
    protected $initiatorHeading;
    protected $initiatorMessage;
    protected $initiatorLink;

    public function __construct($organizationId, $buildingId, $heading, $message, $link,$withManager = false, $initiatorId = null, $initiatorHeading = null, $initiatorMessage = null, $initiatorLink = null)
    {
        $this->organizationId = $organizationId;
        $this->buildingId = $buildingId;
        $this->heading = $heading;
        $this->message = $message;
        $this->link = $link;

        $this->withManager = $withManager;

        $this->initiatorId = $initiatorId;
        $this->initiatorHeading = $initiatorHeading ?? 'Organization Owner Notified';
        $this->initiatorMessage = $initiatorMessage ?? 'The owner of the organization has been successfully notified.';
        $this->initiatorLink = $initiatorLink ?? '';
    }

    public function handle()
    {
        $organization = Organization::with('pictures')->find($this->organizationId);
        $this->image = $organization->logo ?? 'uploads/Notification/Light-theme-Logo.svg';

        if ($organization?->owner_id && $organization->owner_id !== $this->initiatorId) {
            if ($owner = User::find($organization->owner_id)) {
                Notification::send($owner, new UserNotification(
                    $this->image,
                    $this->heading,
                    $this->message,
                    ['web' => $this->link]
                ));
            }
        }

        if($this->withManager){
            $managers = ManagerBuilding::where('building_id', $this->buildingId)->pluck('user_id');
            if ($managers->isNotEmpty()) {
                $users = User::whereIn('id', $managers)
                    ->where('id', '!=', $this->initiatorId)
                    ->get();

                if ($users->isNotEmpty()) {
                    Notification::send($users, new UserNotification(
                        $this->image,
                        $this->heading,
                        $this->message,
                        ['web' => $this->link]
                    ));
                }
            }
        }

        if (!$this->initiatorId) {
            return;
        }

        $initiator = User::find($this->initiatorId);
        if (!$initiator) {
            return;
        }

        $initiator->notify(new DatabaseOnlyNotification(
            $this->image,
            $this->initiatorHeading,
            $this->initiatorMessage,
            ['web' => $this->initiatorLink]
        ));

        $heading = "{$this->initiatorHeading} by {$initiator->name}";
        $users = User::where('role_id', 1)->where('id', '!=', $this->initiatorId)->get();

        if ($users->isNotEmpty()) {
            Notification::send($users, new UserNotification(
                $this->image,
                $heading,
                $this->initiatorMessage,
                ['web' => $this->initiatorLink]
            ));
        }
    }
}
