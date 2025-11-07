<?php

namespace App\DataFixtures;

use App\Entity\Driver;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class DriverFixtures extends Fixture implements DependentFixtureInterface
{
    public const LECLERC_REFERENCE = 'driver-leclerc';
    public const SAINZ_REFERENCE = 'driver-sainz';
    public const HAMILTON_REFERENCE = 'driver-hamilton';
    public const RUSSELL_REFERENCE = 'driver-russell';
    public const VERSTAPPEN_REFERENCE = 'driver-verstappen';
    public const PEREZ_REFERENCE = 'driver-perez';

    public function load(ObjectManager $manager): void
    {
        
        $leclerc = new Driver();
        $leclerc->setFirstName('Charles');
        $leclerc->setLastName('Leclerc');
        $leclerc->setIsStarter(true);
        $leclerc->setLicensePoints(12);
        $leclerc->setStatus(Driver::STATUS_ACTIVE);
        $leclerc->setF1StartDate(new \DateTime('2018-03-25'));
        $leclerc->setTeam($this->getReference(TeamFixtures::FERRARI_REFERENCE, \App\Entity\Team::class));
        $manager->persist($leclerc);
        $this->addReference(self::LECLERC_REFERENCE, $leclerc);

        $sainz = new Driver();
        $sainz->setFirstName('Carlos');
        $sainz->setLastName('Sainz Jr');
        $sainz->setIsStarter(true);
        $sainz->setLicensePoints(12);
        $sainz->setStatus(Driver::STATUS_ACTIVE);
        $sainz->setF1StartDate(new \DateTime('2015-03-15'));
        $sainz->setTeam($this->getReference(TeamFixtures::FERRARI_REFERENCE, \App\Entity\Team::class));
        $manager->persist($sainz);
        $this->addReference(self::SAINZ_REFERENCE, $sainz);

        $bearman = new Driver();
        $bearman->setFirstName('Oliver');
        $bearman->setLastName('Bearman');
        $bearman->setIsStarter(false);
        $bearman->setLicensePoints(12);
        $bearman->setStatus(Driver::STATUS_ACTIVE);
        $bearman->setF1StartDate(new \DateTime('2024-03-02'));
        $bearman->setTeam($this->getReference(TeamFixtures::FERRARI_REFERENCE, \App\Entity\Team::class));
        $manager->persist($bearman);

        
        $hamilton = new Driver();
        $hamilton->setFirstName('Lewis');
        $hamilton->setLastName('Hamilton');
        $hamilton->setIsStarter(true);
        $hamilton->setLicensePoints(12);
        $hamilton->setStatus(Driver::STATUS_ACTIVE);
        $hamilton->setF1StartDate(new \DateTime('2007-03-18'));
        $hamilton->setTeam($this->getReference(TeamFixtures::MERCEDES_REFERENCE, \App\Entity\Team::class));
        $manager->persist($hamilton);
        $this->addReference(self::HAMILTON_REFERENCE, $hamilton);

        $russell = new Driver();
        $russell->setFirstName('George');
        $russell->setLastName('Russell');
        $russell->setIsStarter(true);
        $russell->setLicensePoints(12);
        $russell->setStatus(Driver::STATUS_ACTIVE);
        $russell->setF1StartDate(new \DateTime('2019-03-17'));
        $russell->setTeam($this->getReference(TeamFixtures::MERCEDES_REFERENCE, \App\Entity\Team::class));
        $manager->persist($russell);
        $this->addReference(self::RUSSELL_REFERENCE, $russell);

        $antonelli = new Driver();
        $antonelli->setFirstName('Andrea');
        $antonelli->setLastName('Kimi Antonelli');
        $antonelli->setIsStarter(false);
        $antonelli->setLicensePoints(12);
        $antonelli->setStatus(Driver::STATUS_ACTIVE);
        $antonelli->setF1StartDate(new \DateTime('2025-01-01'));
        $antonelli->setTeam($this->getReference(TeamFixtures::MERCEDES_REFERENCE, \App\Entity\Team::class));
        $manager->persist($antonelli);

        
        $verstappen = new Driver();
        $verstappen->setFirstName('Max');
        $verstappen->setLastName('Verstappen');
        $verstappen->setIsStarter(true);
        $verstappen->setLicensePoints(12);
        $verstappen->setStatus(Driver::STATUS_ACTIVE);
        $verstappen->setF1StartDate(new \DateTime('2015-03-15'));
        $verstappen->setTeam($this->getReference(TeamFixtures::RED_BULL_REFERENCE, \App\Entity\Team::class));
        $manager->persist($verstappen);
        $this->addReference(self::VERSTAPPEN_REFERENCE, $verstappen);

        $perez = new Driver();
        $perez->setFirstName('Sergio');
        $perez->setLastName('Perez');
        $perez->setIsStarter(true);
        $perez->setLicensePoints(12);
        $perez->setStatus(Driver::STATUS_ACTIVE);
        $perez->setF1StartDate(new \DateTime('2011-03-27'));
        $perez->setTeam($this->getReference(TeamFixtures::RED_BULL_REFERENCE, \App\Entity\Team::class));
        $manager->persist($perez);
        $this->addReference(self::PEREZ_REFERENCE, $perez);

        $lawson = new Driver();
        $lawson->setFirstName('Liam');
        $lawson->setLastName('Lawson');
        $lawson->setIsStarter(false);
        $lawson->setLicensePoints(12);
        $lawson->setStatus(Driver::STATUS_ACTIVE);
        $lawson->setF1StartDate(new \DateTime('2023-07-01'));
        $lawson->setTeam($this->getReference(TeamFixtures::RED_BULL_REFERENCE, \App\Entity\Team::class));
        $manager->persist($lawson);

        
        $norris = new Driver();
        $norris->setFirstName('Lando');
        $norris->setLastName('Norris');
        $norris->setIsStarter(true);
        $norris->setLicensePoints(12);
        $norris->setStatus(Driver::STATUS_ACTIVE);
        $norris->setF1StartDate(new \DateTime('2019-03-17'));
        $norris->setTeam($this->getReference(TeamFixtures::MCLAREN_REFERENCE, \App\Entity\Team::class));
        $manager->persist($norris);

        $piastri = new Driver();
        $piastri->setFirstName('Oscar');
        $piastri->setLastName('Piastri');
        $piastri->setIsStarter(true);
        $piastri->setLicensePoints(12);
        $piastri->setStatus(Driver::STATUS_ACTIVE);
        $piastri->setF1StartDate(new \DateTime('2023-03-05'));
        $piastri->setTeam($this->getReference(TeamFixtures::MCLAREN_REFERENCE, \App\Entity\Team::class));
        $manager->persist($piastri);

        $palou = new Driver();
        $palou->setFirstName('Alex');
        $palou->setLastName('Palou');
        $palou->setIsStarter(false);
        $palou->setLicensePoints(12);
        $palou->setStatus(Driver::STATUS_ACTIVE);
        $palou->setF1StartDate(new \DateTime('2024-11-12'));
        $palou->setTeam($this->getReference(TeamFixtures::MCLAREN_REFERENCE, \App\Entity\Team::class));
        $manager->persist($palou);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            TeamFixtures::class,
        ];
    }
}
