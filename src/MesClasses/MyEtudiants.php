<?php
// Copyright (C) 11 / 2019 | David annebicque | IUT de Troyes - All Rights Reserved
// @file /Users/davidannebicque/htdocs/intranetv3/src/MesClasses/MyEtudiants.php
// @author     David Annebicque
// @project intranetv3
// @date 25/11/2019 10:20
// @lastUpdate 23/11/2019 09:14

/**
 * Created by PhpStorm.
 * User: davidannebicque
 * Date: 24/12/2018
 * Time: 14:38
 */

namespace App\MesClasses;


use App\Entity\Adresse;
use App\Entity\Bac;
use App\Entity\Etudiant;
use App\Entity\Semestre;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class MyEtudiants
{

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var MyUpload */
    private $myUpload;

    /**
     * MyEtudiants constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param MyUpload               $myUpload
     */
    public function __construct(EntityManagerInterface $entityManager, MyUpload $myUpload)
    {
        $this->entityManager = $entityManager;
        $this->myUpload = $myUpload;
    }


    /**
     * @param               $fichier
     * @param Semestre|null $semestre
     *
     * @return bool
     * @throws Exception
     */
    public function importListeCsv($fichier, ?Semestre $semestre)
    {
        $bacs = $this->entityManager->getRepository(Bac::class)->getCodeObjet();
        if ($semestre !== null) {
            $file = $this->myUpload->upload($fichier, 'temp');
            $handle = fopen($file, 'rb');

            /*Si on a réussi à ouvrir le fichier*/
            if ($handle) {
                /* supprime la première ligne */
                fgetcsv($handle, 1024, ';');
                /*Tant que l'on est pas à la fin du fichier*/
                while (!feof($handle)) {
                    /*On lit la ligne courante*/
                    $ligne = fgetcsv($handle, 1024, ';');
                    if (is_array($ligne) && count($ligne) > 10) {
                        $etudiant = new Etudiant();
//login	numetudiant	numine	nom	prenom	photo	mailuniv	siteuniv	mailperso	siteperso	visible	sexe	promo	anneesortie	datenaissance	bac	tel1	tel2	remarques	signature	anneebac	commentaire	typeuser	intitulesecu	adressesecu	loginMMI	slug	fifc	boursier	adresse1	adresse2	adresse3	cp	ville	pays	nomadresse	codeetape
                        $etudiant->setSemestre($semestre);
                        $etudiant->setDepartement($semestre->getDiplome()->getDepartement());
                        $etudiant->setUsername($ligne[0]);
                        $etudiant->setNumEtudiant($ligne[1]);
                        $etudiant->setNumIne($ligne[2]);
                        $etudiant->setNom($ligne[3]);
                        $etudiant->setPrenom($ligne[4]);
                        $etudiant->setPhotoName($ligne[5]);
                        $etudiant->setMailUniv($ligne[6]);
                        $etudiant->setSiteUniv($ligne[7]);
                        $etudiant->setMailPerso($ligne[8]);
                        $etudiant->setSitePerso($ligne[9]);
                        $etudiant->setVisible($ligne[10]);
                        $etudiant->setCivilite($ligne[11]);
                        $etudiant->setPromotion($ligne[12]);
                        $etudiant->setAnneeSortie($ligne[13]);
                        $etudiant->setDateNaissance(new DateTime($ligne[14]));
                        if (array_key_exists($ligne[15], $bacs)) {
                            $etudiant->setBac($bacs[$ligne[15]]);
                        } else {
                            $etudiant->setBac(null);
                        }
                        $etudiant->setTel1($ligne[16]);
                        $etudiant->setTel2($ligne[17]);
                        $etudiant->setRemarque($ligne[18]);
                        $etudiant->setSignature($ligne[19]);
                        $etudiant->setAnneeBac($ligne[20]);
                        $etudiant->setTypeUser('etudiant');
                        $etudiant->setIntituleSecuriteSociale($ligne[23]);
                        $etudiant->setAdresseSecuriteSociale($ligne[24]);
                        $etudiant->setLoginSpecifique($ligne[25]);
                        $etudiant->setSlug($ligne[26]);
                        $etudiant->setFormationContinue($ligne[27]);
                        $etudiant->setBoursier($ligne[28]);
                        $adresse = new Adresse();
                        $adresse->setAdresse1($ligne[29]);
                        $adresse->setAdresse2($ligne[30]);
                        $adresse->setAdresse3($ligne[31]);
                        $adresse->setCodePostal($ligne[32]);
                        $adresse->setVille($ligne[33]);
                        $this->entityManager->persist($adresse);
                        $etudiant->setAdresse($adresse);

                        $this->entityManager->persist($etudiant);
                    }
                }
            }
            $this->entityManager->flush();

            /*On ferme le fichier*/
            fclose($handle);
            unlink($file); //suppression du fichier

            return true;
        }

        return false;
    }
}
