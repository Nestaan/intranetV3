<?php
// Copyright (c) 2020. | David Annebicque | IUT de Troyes  - All Rights Reserved
// @file /Users/davidannebicque/htdocs/intranetV3/src/Entity/Configuration.php
// @author davidannebicque
// @project intranetV3
// @lastUpdate 05/07/2020 08:09

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ConfigurationRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Configuration extends BaseEntity
{
    /**
     * @ORM\Column(type="string", length=50)
     * @Groups({"configuration_administration"})
     */
    private $cle;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"configuration_administration"})
     */
    private $valeur;

    /**
     * @ORM\Column(type="string", length=1)
     */
    private $type = 'T';

    /**
     * @return null|string
     */
    public function getCle(): ?string
    {
        return $this->cle;
    }

    /**
     * @param string $cle
     *
     * @return Configuration
     */
    public function setCle(string $cle): self
    {
        $this->cle = $cle;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getValeur(): ?string
    {
        return $this->valeur;
    }

    /**
     * @param string $valeur
     *
     * @return Configuration
     */
    public function setValeur(string $valeur): self
    {
        $this->valeur = $valeur;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getTypeLong(): string
    {
        return $this->type === 'T' ? 'Texte' : 'Fichier';
    }
}
