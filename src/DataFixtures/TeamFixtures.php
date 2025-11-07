<?php

namespace App\DataFixtures;

use App\Entity\Engine;
use App\Entity\Team;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TeamFixtures extends Fixture
{
    public const FERRARI_REFERENCE = 'team-ferrari';
    public const MERCEDES_REFERENCE = 'team-mercedes';
    public const RED_BULL_REFERENCE = 'team-redbull';
    public const MCLAREN_REFERENCE = 'team-mclaren';

    public function load(ObjectManager $manager): void
    {
        
        $ferrari = new Team();
        $ferrari->setName('Scuderia Ferrari');
        $manager->persist($ferrari);

        $ferrariEngine = new Engine();
        $ferrariEngine->setBrand('Ferrari');
        $ferrariEngine->setTeam($ferrari);
        $manager->persist($ferrariEngine);

        $this->addReference(self::FERRARI_REFERENCE, $ferrari);

        
        $mercedes = new Team();
        $mercedes->setName('Mercedes-AMG Petronas F1 Team');
        $manager->persist($mercedes);

        $mercedesEngine = new Engine();
        $mercedesEngine->setBrand('Mercedes');
        $mercedesEngine->setTeam($mercedes);
        $manager->persist($mercedesEngine);

        $this->addReference(self::MERCEDES_REFERENCE, $mercedes);

        
        $redBull = new Team();
        $redBull->setName('Oracle Red Bull Racing');
        $manager->persist($redBull);

        $redBullEngine = new Engine();
        $redBullEngine->setBrand('Honda RBPT');
        $redBullEngine->setTeam($redBull);
        $manager->persist($redBullEngine);

        $this->addReference(self::RED_BULL_REFERENCE, $redBull);

        
        $mclaren = new Team();
        $mclaren->setName('McLaren F1 Team');
        $manager->persist($mclaren);

        $mclarenEngine = new Engine();
        $mclarenEngine->setBrand('Mercedes');
        $mclarenEngine->setTeam($mclaren);
        $manager->persist($mclarenEngine);

        $this->addReference(self::MCLAREN_REFERENCE, $mclaren);

        $manager->flush();
    }
}
