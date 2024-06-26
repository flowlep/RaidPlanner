<?php

namespace App\Service;

use App\Entity\User;
use App\Enum\RolesEnum;
use App\Repository\UserRepository;
use App\Util\File\FileManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\File;

final readonly class UserService
{
    public function __construct(
        private FileManager           $fileManager,
        private ParameterBagInterface $parameterBag,
        private UserRepository        $userRepository
    ) {}

    public function setDefaultProfilePicture(User $user): void
    {
        $profilePictureDirectory = $this->parameterBag->get('profile_picture_directory');

        if ($user->getProfilePicture()) {
            $this->fileManager->removeFile(
                $user->getProfilePicture(), $profilePictureDirectory
            );
        }

        $tempPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'default_profile_picture.jpg';
        copy($this->parameterBag->get('default_profile_picture'), $tempPath);

        $defaultProfilePicture = $this->fileManager->uploadFile(
            new File($tempPath, true),
            $profilePictureDirectory
        );

        $user->setProfilePicture($defaultProfilePicture);
    }

    /**
     * @return User[]
     */
    public function getActiveMembers(): array
    {
        $users = $this->userRepository->findAllOrderedByName();

        return array_filter($users, static function (User $user) {
            if (in_array($user->getRole(), RolesEnum::getActiveGuildRoles())) {
                return true;
            }

            return false;
        });
    }
}
