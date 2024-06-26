<?php

namespace App\Checker\SlotAssignmentPermission\Steps;

use App\Entity\GuildEventRelation\EventBattle;
use App\Entity\User;
use App\Enum\AttendanceTypeEnum;
use App\Interface\SlotAssignmentPermissionStepInterface;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class CheckUserIsNotAbsentStep implements SlotAssignmentPermissionStepInterface
{
    public function __construct(
        private Security $security
    ) {}

    public function check(EventBattle $eventBattle): bool
    {
        /** @var User $currentUser */
        $currentUser = $this->security->getUser();

        foreach ($eventBattle->getGuildEvent()?->getEventAttendances() as $eventAttendance) {
            if ($eventAttendance->getUser() === $currentUser) {
                return $eventAttendance->getType() !== AttendanceTypeEnum::ABSENT;
            }
        }

        return true;
    }
}
