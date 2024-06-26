<?php

namespace App\DataFixtures;

use App\DataFixtures\util\FileMockUploader;
use App\Entity\Job;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use FilesystemIterator;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class JobFixtures extends Fixture
{
    public function __construct(
        private readonly KernelInterface       $kernel,
        private readonly ParameterBagInterface $parameterBag,
        private readonly FileMockUploader      $fileMockUploader,
    ) {}

    final public function load(ObjectManager $manager): void
    {
        if ($this->kernel->getEnvironment() === 'dev') {
            $this->purgeIconDirectory();
        }

        foreach ($this->getJobList() as $jobName => $jobColor) {
            $manager->persist($this->createJob($jobName, $jobColor));
        }

        $manager->persist($this->createJob('Default', '#ffffff')->setDefaultJob(true));

        $manager->flush();
    }

    private function purgeIconDirectory(): void
    {
        $files = new FilesystemIterator($this->parameterBag->get('icon_directory'));

        foreach ($files as $file) {
            if ($file->isFile() && $file->getFilename() !== '.gitignore') {
                unlink($file->getPathname());
            }
        }
    }

    private function createJob(string $name, string $color): Job
    {
        $filename = $this->fileMockUploader->mockFileUpload(strtolower($name));

        return (new Job())->setName($name)
            ->setIcon($filename)
            ->setColor($color);
    }

    /**
     * @return array<string, string>
     */
    private function getJobList(): array
    {
        return [
            'Elementalist' => '#f54251',
            'Engineer' => '#ce7f4b',
            'Guardian' => '#5fbcd3',
            'Mesmer' => '#d42aff',
            'Necromancer' => '#22bc72',
            'Ranger' => '#95c34a',
            'Revenant' => '#860000',
            'Thief' => '#89676d',
            'Warrior' => '#c2a056'
        ];
    }
}
