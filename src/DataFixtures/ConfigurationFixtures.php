<?php
// Copyright (c) 2020. | David Annebicque | IUT de Troyes  - All Rights Reserved
// @file /Users/davidannebicque/htdocs/intranetV3/src/DataFixtures/ConfigurationFixtures.php
// @author davidannebicque
// @project intranetV3
// @lastUpdate 29/09/2020 17:53

namespace App\DataFixtures;

use App\Entity\Configuration;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ConfigurationFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $conf = new Configuration();
        $conf->setCle('MAIL_FROM');
        $conf->setValeur('intranet@iut-troyes.univ-reims.fr');
        $manager->persist($conf);

        $conf = new Configuration();
        $conf->setCle('NOM_IUT');
        $conf->setValeur('IUT de Troyes');
        $manager->persist($conf);

        $conf = new Configuration();
        $conf->setCle('GRATIFICATION_HEURE_STAGE');
        $conf->setValeur('3.90');
        $manager->persist($conf);

        $conf = new Configuration();
        $conf->setCle('NOM_UNIVERSITE');
        $conf->setValeur('Université de Reims Champagne-Ardenne');
        $manager->persist($conf);

        $conf = new Configuration();
        $conf->setCle('LOGO_IUT');
        $conf->setValeur('logo_iut.png');
        $manager->persist($conf);

        $conf = new Configuration();
        $conf->setCle('SITE_IUT');
        $conf->setValeur('https://iut-troyes.univ-reims.fr');
        $manager->persist($conf);

        $conf = new Configuration();
        $conf->setCle('SITE_INTRANET_IUT');
        $conf->setValeur('https://intranet.iut-troyes.univ-reims.fr');
        $manager->persist($conf);

        $conf = new Configuration();
        $conf->setCle('ADRESSE_IUT');
        $conf->setValeur('Iut de Troyes, 9, Rue de Québec, CS90396, 10026 Troyes Cedex');
        $manager->persist($conf);

        $conf = new Configuration();
        $conf->setCle('BASE_PATH');
        $conf->setValeur('upload/');
        $manager->persist($conf);

        $conf = new Configuration();
        $conf->setCle('LOGO_UNIVERSITE');
        $conf->setValeur('urca.jpeg');
        $manager->persist($conf);

        $conf = new Configuration();
        $conf->setCle('COLOR_IUT');
        $conf->setValeur('#FAB001');
        $manager->persist($conf);

        $conf = new Configuration();
        $conf->setCle('MODIFICATION_PPN');
        $conf->setValeur(true);
        $manager->persist($conf);

        $conf = new Configuration();
        $conf->setCle('MODIFICATION_GROUPE');
        $conf->setValeur(true);
        $manager->persist($conf);

        $conf = new Configuration();
        $conf->setCle('LOGO_INTRANET_IUT');
        $conf->setValeur('logo_intranet_iut_troyes.svg');
        $manager->persist($conf);

        $manager->flush();
    }
}
