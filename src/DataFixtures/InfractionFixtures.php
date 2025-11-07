<?php

namespace App\DataFixtures;

use App\Entity\Infraction;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class InfractionFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        
        $infraction1 = new Infraction();
        $infraction1->setOccurredAt(new \DateTime('2025-03-08 14:30:00'));
        $infraction1->setRaceName('GP Bahrain');
        $infraction1->setDescription('Dépassement des limites de piste (track limits) - 3 fois');
        $infraction1->setType(Infraction::TYPE_PENALTY_POINTS);
        $infraction1->setAmount('3');
        $infraction1->setDriver($this->getReference(DriverFixtures::LECLERC_REFERENCE, \App\Entity\Driver::class));
        $manager->persist($infraction1);

        
        $infraction2 = new Infraction();
        $infraction2->setOccurredAt(new \DateTime('2025-03-08 16:45:00'));
        $infraction2->setRaceName('GP Bahrain');
        $infraction2->setDescription('Sortie dangereuse des stands (unsafe release)');
        $infraction2->setType(Infraction::TYPE_FINE_EUR);
        $infraction2->setAmount('25000.00');
        $infraction2->setTeam($this->getReference(TeamFixtures::FERRARI_REFERENCE, \App\Entity\Team::class));
        $manager->persist($infraction2);

        
        $infraction3 = new Infraction();
        $infraction3->setOccurredAt(new \DateTime('2025-03-22 15:20:00'));
        $infraction3->setRaceName('GP Arabie Saoudite');
        $infraction3->setDescription('Collision avec un autre pilote');
        $infraction3->setType(Infraction::TYPE_PENALTY_POINTS);
        $infraction3->setAmount('2');
        $infraction3->setDriver($this->getReference(DriverFixtures::VERSTAPPEN_REFERENCE, \App\Entity\Driver::class));
        $manager->persist($infraction3);

        
        $infraction4 = new Infraction();
        $infraction4->setOccurredAt(new \DateTime('2025-04-02 13:10:00'));
        $infraction4->setRaceName('GP Australie');
        $infraction4->setDescription('Dépassement du budget cap lors des essais');
        $infraction4->setType(Infraction::TYPE_FINE_EUR);
        $infraction4->setAmount('50000.00');
        $infraction4->setTeam($this->getReference(TeamFixtures::RED_BULL_REFERENCE, \App\Entity\Team::class));
        $manager->persist($infraction4);

        
        $infraction5 = new Infraction();
        $infraction5->setOccurredAt(new \DateTime('2025-05-05 14:55:00'));
        $infraction5->setRaceName('GP Miami');
        $infraction5->setDescription('Non-respect des drapeaux jaunes');
        $infraction5->setType(Infraction::TYPE_PENALTY_POINTS);
        $infraction5->setAmount('3');
        $infraction5->setDriver($this->getReference(DriverFixtures::HAMILTON_REFERENCE, \App\Entity\Driver::class));
        $manager->persist($infraction5);

        
        $infraction6 = new Infraction();
        $infraction6->setOccurredAt(new \DateTime('2025-05-25 16:30:00'));
        $infraction6->setRaceName('GP Monaco');
        $infraction6->setDescription('Équipement de sécurité non conforme');
        $infraction6->setType(Infraction::TYPE_FINE_EUR);
        $infraction6->setAmount('15000.00');
        $infraction6->setTeam($this->getReference(TeamFixtures::MERCEDES_REFERENCE, \App\Entity\Team::class));
        $manager->persist($infraction6);

        
        $infraction7 = new Infraction();
        $infraction7->setOccurredAt(new \DateTime('2025-06-15 15:40:00'));
        $infraction7->setRaceName('GP Canada');
        $infraction7->setDescription('Manœuvre dangereuse en défense');
        $infraction7->setType(Infraction::TYPE_PENALTY_POINTS);
        $infraction7->setAmount('2');
        $infraction7->setDriver($this->getReference(DriverFixtures::PEREZ_REFERENCE, \App\Entity\Driver::class));
        $manager->persist($infraction7);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            TeamFixtures::class,
            DriverFixtures::class,
        ];
    }
}
