<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Rate;
use App\Repository\RateRepository;
use App\Repository\SchoolRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class RateFixtures extends Fixture implements DependentFixtureInterface
{
    private RateRepository $rateRepository;

    private SchoolRepository $schoolRepository;

    public function __construct(RateRepository $rateRepository, SchoolRepository $schoolRepository)
    {
        $this->rateRepository = $rateRepository;
        $this->schoolRepository = $schoolRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $schools = $this->schoolRepository->findAll();
        $rates = [
            '1 clase' => 10,
            '2 clases' => 20,
            '1 mes' => 30,
            '3 meses' => 75,
            'temporada' => 185,
        ];
        foreach ($rates as $name => $amount) {
            $this->rateRepository->save((new Rate())
                ->setName($name)
                ->setAmount($amount)
                ->setSchool($schools[0])
            );
        }
        $this->rateRepository->save((new Rate())
            ->setName('Monográfico')
            ->setAmount(60)
            ->setSchool($schools[1])
        );
    }

    public function getDependencies()
    {
        return [SchoolFixtures::class];
    }
}
