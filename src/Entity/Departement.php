<?php
// Copyright (c) 2020. | David Annebicque | IUT de Troyes  - All Rights Reserved
// @file /Users/davidannebicque/htdocs/intranetV3/src/Entity/Departement.php
// @author davidannebicque
// @project intranetV3
// @lastUpdate 13/10/2020 06:34

namespace App\Entity;

use App\Entity\Traits\UuidTrait;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use function chr;
use function ord;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DepartementRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Vich\Uploadable
 */
class Departement extends BaseEntity
{
    use UuidTrait;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"actualite_administration"})
     */
    private $libelle;
    /**
     * @var Ufr
     * @ORM\ManyToOne(targetEntity="App\Entity\Ufr", inversedBy="departements")
     */
    private $ufr;
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50)
     */
    private $logoName;
    /**
     * @var UploadedFile
     *
     * @Vich\UploadableField(mapping="logo", fileNameProperty="logoName")
     */
    private $logoFile;
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    private $telContact;
    /**
     * @var string
     *
     * @ORM\Column(name="fax", type="string", length=16, nullable=true)
     */
    private $fax;
    /**
     * @var string
     *
     * @ORM\Column(name="couleur", type="string", length=16, nullable=true)
     */
    private $couleur;
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $siteWeb;
    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;
    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $optUpdateCelcat = false;
    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $optAgence = false;
    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $optMateriel = false;
    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $optEdt = true;
    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $optStage = true;
    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $optSynthese = true;
    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $optMessagerie = true;

    /**
     * @var Personnel
     * @ORM\ManyToOne(targetEntity="App\Entity\Personnel")
     */
    private $respri;
    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private $optAnneePrevisionnel;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TypeDocument", mappedBy="departement")
     * @MaxDepth(1)
     */
    private $typeDocuments;
    /**
     * @ORM\OneToMany(targetEntity="PersonnelDepartement", mappedBy="departement")
     */
    private $personnelDepartements;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Actualite", mappedBy="departement")
     */
    private $actualites;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Diplome", mappedBy="departement")
     * @ORM\OrderBy({"libelle"="ASC"})
     */
    private $diplomes;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SalleExamen", mappedBy="departement")
     */
    private $salleExamens;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Hrs", mappedBy="departement")
     */
    private $hrs;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TypeMateriel", mappedBy="departement")
     */
    private $typeMateriels;

    /**
     * @ORM\Column(type="boolean")
     */
    private $actif = true;
    /**
     * @ORM\Column(type="boolean")
     */
    private $preparationAnnee = false;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AnneeUniversitaire", inversedBy="departements")
     */

    private $anneeUniversitairePrepare;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CreneauCours", mappedBy="departement")
     */
    private $creneauCours;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ArticleCategorie", mappedBy="departement")
     */
    private $articleCategories;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Emprunt", mappedBy="departement")
     */
    private $emprunts;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Personnel", inversedBy="departements")
     */
    private $respMateriel;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Etudiant", mappedBy="departement")
     */
    private $etudiants;

    /**
     * Departement constructor.
     * @throws Exception
     */
    public function __construct()
    {
        $this->setUuid(Uuid::uuid4());
        $this->optAnneePrevisionnel = (int)date('Y');
        $this->typeDocuments = new ArrayCollection();
        $this->personnelDepartements = new ArrayCollection();
        $this->actualites = new ArrayCollection();
        $this->trelloTaches = new ArrayCollection();
        $this->diplomes = new ArrayCollection();
        $this->salleExamens = new ArrayCollection();
        $this->hrs = new ArrayCollection();
        $this->typeMateriels = new ArrayCollection();
        $this->materielPrets = new ArrayCollection();
        $this->creneauCours = new ArrayCollection();
        $this->articleCategories = new ArrayCollection();
        $this->emprunts = new ArrayCollection();
        $this->etudiants = new ArrayCollection();
    }

    public function __clone()
    {
        $this->setUuid(Uuid::uuid4());
    }


    /**
     * @return Ufr
     */
    public function getUfr(): ?Ufr
    {
        return $this->ufr;
    }

    /**
     * @param Ufr $ufr
     */
    public function setUfr(Ufr $ufr): void
    {
        $this->ufr = $ufr;
    }

    /**
     * @return string
     */
    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    /**
     * @param mixed $libelle
     */
    public function setLibelle($libelle): void
    {
        $this->libelle = $libelle;
    }

    /**
     * @return string
     */
    public function getTelContact(): ?string
    {
        return $this->telContact;
    }

    /**
     * @param string $telContact
     */
    public function setTelContact(string $telContact): void
    {
        $this->telContact = $telContact;
    }

    /**
     * @return string
     */
    public function getFax(): ?string
    {
        return $this->fax;
    }

    /**
     * @param string $fax
     */
    public function setFax(string $fax): void
    {
        $this->fax = $fax;
    }

    /**
     * @return string
     */
    public function getCouleur(): ?string
    {
        return $this->couleur;
    }

    /**
     * @param string $couleur
     */
    public function setCouleur(string $couleur): void
    {
        $this->couleur = $couleur;
    }

    /**
     * @return string
     */
    public function getSiteWeb(): ?string
    {
        return $this->siteWeb;
    }

    /**
     * @param string $siteWeb
     */
    public function setSiteWeb(string $siteWeb): void
    {
        $this->siteWeb = $siteWeb;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return bool
     */
    public function isOptUpdateCelcat(): bool
    {
        return $this->optUpdateCelcat;
    }

    /**
     * @param bool $optUpdateCelcat
     */
    public function setOptUpdateCelcat(bool $optUpdateCelcat): void
    {
        $this->optUpdateCelcat = $optUpdateCelcat;
    }

    /**
     * @return bool
     */
    public function isOptAgence(): bool
    {
        return $this->optAgence;
    }

    /**
     * @param bool $optAgence
     */
    public function setOptAgence(bool $optAgence): void
    {
        $this->optAgence = $optAgence;
    }

    /**
     * @return bool
     */
    public function isOptMateriel(): bool
    {
        return $this->optMateriel;
    }

    /**
     * @param bool $optMateriel
     */
    public function setOptMateriel(bool $optMateriel): void
    {
        $this->optMateriel = $optMateriel;
    }

    /**
     * @return bool
     */
    public function isOptEdt(): bool
    {
        return $this->optEdt;
    }

    /**
     * @param bool $optEdt
     */
    public function setOptEdt(bool $optEdt): void
    {
        $this->optEdt = $optEdt;
    }

    /**
     * @return bool
     */
    public function isOptStage(): bool
    {
        return $this->optStage;
    }

    /**
     * @param bool $optStage
     */
    public function setOptStage(bool $optStage): void
    {
        $this->optStage = $optStage;
    }

    /**
     * @return bool
     */
    public function isOptSynthese(): bool
    {
        return $this->optSynthese;
    }

    /**
     * @param bool $optSynthese
     */
    public function setOptSynthese(bool $optSynthese): void
    {
        $this->optSynthese = $optSynthese;
    }

    /**
     * @return bool
     */
    public function isOptMessagerie(): bool
    {
        return $this->optMessagerie;
    }

    /**
     * @param bool $optMessagerie
     */
    public function setOptMessagerie(bool $optMessagerie): void
    {
        $this->optMessagerie = $optMessagerie;
    }

    /**
     * @return Personnel
     */
    public function getRespri(): ?Personnel
    {
        return $this->respri;
    }

    /**
     * @param Personnel|null $respri
     */
    public function setRespri($respri): void
    {
        $this->respri = $respri;
    }

    /**
     * @return int
     */
    public function getOptAnneePrevisionnel(): int
    {
        return $this->optAnneePrevisionnel;
    }

    /**
     * @param int $optAnneePrevisionnel
     */
    public function setOptAnneePrevisionnel(int $optAnneePrevisionnel): void
    {
        $this->optAnneePrevisionnel = $optAnneePrevisionnel;
    }

    /**
     * @return Collection|TypeDocument[]
     */
    public function getTypeDocuments(): Collection
    {
        return $this->typeDocuments;
    }

    /**
     * @param TypeDocument $typeDocument
     *
     * @return Departement
     */
    public function addTypeDocument(TypeDocument $typeDocument): self
    {
        if (!$this->typeDocuments->contains($typeDocument)) {
            $this->typeDocuments[] = $typeDocument;
            $typeDocument->setDepartement($this);
        }

        return $this;
    }

    /**
     * @param TypeDocument $typeDocument
     *
     * @return Departement
     */
    public function removeTypeDocument(TypeDocument $typeDocument): self
    {
        if ($this->typeDocuments->contains($typeDocument)) {
            $this->typeDocuments->removeElement($typeDocument);
            // set the owning side to null (unless already changed)
            if ($typeDocument->getDepartement() === $this) {
                $typeDocument->setDepartement(null);
            }
        }

        return $this;
    }

    /**
     * @return null|File
     */
    public function getLogoFile(): ?File
    {
        return $this->logoFile;
    }

    /**
     * @param File|null $logo
     *
     * @throws Exception
     */
    public function setLogoFile(?File $logo = null): void
    {
        $this->logoFile = $logo;

        if (null !== $logo) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->setUpdated(new DateTime());
        }
    }

    /**
     * @return string
     */
    public function getLogoName(): ?string
    {
        return $this->logoName;
    }

    /**
     * @param string $logoName
     */
    public function setLogoName(string $logoName): void
    {
        $this->logoName = $logoName;
    }

    /**
     * @return Collection|PersonnelDepartement[]
     */
    public function getPersonnelDepartements(): Collection
    {
        return $this->personnelDepartements;
    }

    /**
     * @param PersonnelDepartement $personnelDepartement
     *
     * @return Departement
     */
    public function addPersonnelDepartement(PersonnelDepartement $personnelDepartement): self
    {
        if (!$this->personnelDepartements->contains($personnelDepartement)) {
            $this->personnelDepartements[] = $personnelDepartement;
            $personnelDepartement->setDepartement($this);
        }

        return $this;
    }

    /**
     * @param PersonnelDepartement $personnelDepartement
     *
     * @return Departement
     */
    public function removePersonnelDepartement(PersonnelDepartement $personnelDepartement): self
    {
        if ($this->personnelDepartements->contains($personnelDepartement)) {
            $this->personnelDepartements->removeElement($personnelDepartement);
            // set the owning side to null (unless already changed)
            if ($personnelDepartement->getDepartement() === $this) {
                $personnelDepartement->setDepartement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Actualite[]
     */
    public function getActualites(): Collection
    {
        return $this->actualites;
    }

    /**
     * @param Actualite $actualite
     *
     * @return Departement
     */
    public function addActualite(Actualite $actualite): self
    {
        if (!$this->actualites->contains($actualite)) {
            $this->actualites[] = $actualite;
            $actualite->setDepartement($this);
        }

        return $this;
    }

    /**
     * @param Actualite $actualite
     *
     * @return Departement
     */
    public function removeActualite(Actualite $actualite): self
    {
        if ($this->actualites->contains($actualite)) {
            $this->actualites->removeElement($actualite);
            // set the owning side to null (unless already changed)
            if ($actualite->getDepartement() === $this) {
                $actualite->setDepartement(null);
            }
        }

        return $this;
    }

    /**
     * @param $name
     * @param $value
     */
    public function update($name, $value): void
    {
        $name[0] = chr(ord($name[0]) - 32);
        $method = 'set' . $name;
        if (method_exists($this, $method)) {
            $this->$method($value);
        }
    }

    /**
     * @return Collection|Diplome[]
     */
    public function getDiplomes(): Collection
    {
        return $this->diplomes;
    }

    /**
     * @param Diplome $diplome
     *
     * @return Departement
     */
    public function addDiplome(Diplome $diplome): self
    {
        if (!$this->diplomes->contains($diplome)) {
            $this->diplomes[] = $diplome;
            $diplome->setDepartement($this);
        }

        return $this;
    }

    /**
     * @param Diplome $diplome
     *
     * @return Departement
     */
    public function removeDiplome(Diplome $diplome): self
    {
        if ($this->diplomes->contains($diplome)) {
            $this->diplomes->removeElement($diplome);
            // set the owning side to null (unless already changed)
            if ($diplome->getDepartement() === $this) {
                $diplome->setDepartement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|SalleExamen[]
     */
    public function getSalleExamens(): Collection
    {
        return $this->salleExamens;
    }

    public function addSalleExamen(SalleExamen $salleExamen): self
    {
        if (!$this->salleExamens->contains($salleExamen)) {
            $this->salleExamens[] = $salleExamen;
            $salleExamen->setDepartement($this);
        }

        return $this;
    }

    public function removeSalleExamen(SalleExamen $salleExamen): self
    {
        if ($this->salleExamens->contains($salleExamen)) {
            $this->salleExamens->removeElement($salleExamen);
            // set the owning side to null (unless already changed)
            if ($salleExamen->getDepartement() === $this) {
                $salleExamen->setDepartement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Hrs[]
     */
    public function getHrs(): Collection
    {
        return $this->hrs;
    }

    public function addHr(Hrs $hr): self
    {
        if (!$this->hrs->contains($hr)) {
            $this->hrs[] = $hr;
            $hr->setDepartement($this);
        }

        return $this;
    }

    public function removeHr(Hrs $hr): self
    {
        if ($this->hrs->contains($hr)) {
            $this->hrs->removeElement($hr);
            // set the owning side to null (unless already changed)
            if ($hr->getDepartement() === $this) {
                $hr->setDepartement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TypeMateriel[]
     */
    public function getTypeMateriels(): Collection
    {
        return $this->typeMateriels;
    }

    public function addTypeMateriel(TypeMateriel $typeMateriel): self
    {
        if (!$this->typeMateriels->contains($typeMateriel)) {
            $this->typeMateriels[] = $typeMateriel;
            $typeMateriel->setDepartement($this);
        }

        return $this;
    }

    public function removeTypeMateriel(TypeMateriel $typeMateriel): self
    {
        if ($this->typeMateriels->contains($typeMateriel)) {
            $this->typeMateriels->removeElement($typeMateriel);
            // set the owning side to null (unless already changed)
            if ($typeMateriel->getDepartement() === $this) {
                $typeMateriel->setDepartement(null);
            }
        }

        return $this;
    }

    public function getActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    public function getPreparationAnnee(): ?bool
    {
        return $this->preparationAnnee;
    }

    public function setPreparationAnnee(bool $preparationAnnee): self
    {
        $this->preparationAnnee = $preparationAnnee;

        return $this;
    }

    public function getAnneeUniversitairePrepare(): ?AnneeUniversitaire
    {
        return $this->anneeUniversitairePrepare;
    }

    public function setAnneeUniversitairePrepare(?AnneeUniversitaire $anneeUniversitairePrepare): self
    {
        $this->anneeUniversitairePrepare = $anneeUniversitairePrepare;

        return $this;
    }

    /**
     * @return Collection|CreneauCours[]
     */
    public function getCreneauCours(): Collection
    {
        return $this->creneauCours;
    }

    public function addCreneauCour(CreneauCours $creneauCour): self
    {
        if (!$this->creneauCours->contains($creneauCour)) {
            $this->creneauCours[] = $creneauCour;
            $creneauCour->setDepartement($this);
        }

        return $this;
    }

    public function removeCreneauCour(CreneauCours $creneauCour): self
    {
        if ($this->creneauCours->contains($creneauCour)) {
            $this->creneauCours->removeElement($creneauCour);
            // set the owning side to null (unless already changed)
            if ($creneauCour->getDepartement() === $this) {
                $creneauCour->setDepartement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ArticleCategorie[]
     */
    public function getArticleCategories(): Collection
    {
        return $this->articleCategories;
    }

    public function addArticleCategory(ArticleCategorie $articleCategory): self
    {
        if (!$this->articleCategories->contains($articleCategory)) {
            $this->articleCategories[] = $articleCategory;
            $articleCategory->setDepartement($this);
        }

        return $this;
    }

    public function removeArticleCategory(ArticleCategorie $articleCategory): self
    {
        if ($this->articleCategories->contains($articleCategory)) {
            $this->articleCategories->removeElement($articleCategory);
            // set the owning side to null (unless already changed)
            if ($articleCategory->getDepartement() === $this) {
                $articleCategory->setDepartement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Emprunt[]
     */
    public function getEmprunts(): Collection
    {
        return $this->emprunts;
    }

    public function addEmprunt(Emprunt $emprunt): self
    {
        if (!$this->emprunts->contains($emprunt)) {
            $this->emprunts[] = $emprunt;
            $emprunt->setDepartement($this);
        }

        return $this;
    }

    public function removeEmprunt(Emprunt $emprunt): self
    {
        if ($this->emprunts->contains($emprunt)) {
            $this->emprunts->removeElement($emprunt);
            // set the owning side to null (unless already changed)
            if ($emprunt->getDepartement() === $this) {
                $emprunt->setDepartement(null);
            }
        }

        return $this;
    }

    public function getRespMateriel(): ?Personnel
    {
        return $this->respMateriel;
    }

    public function setRespMateriel(?Personnel $respMateriel): self
    {
        $this->respMateriel = $respMateriel;

        return $this;
    }

    public function setUuid($uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getOptUpdateCelcat(): ?bool
    {
        return $this->optUpdateCelcat;
    }

    public function getOptAgence(): ?bool
    {
        return $this->optAgence;
    }

    public function getOptMateriel(): ?bool
    {
        return $this->optMateriel;
    }

    public function getOptEdt(): ?bool
    {
        return $this->optEdt;
    }

    public function getOptStage(): ?bool
    {
        return $this->optStage;
    }

    public function getOptSynthese(): ?bool
    {
        return $this->optSynthese;
    }

    public function getOptMessagerie(): ?bool
    {
        return $this->optMessagerie;
    }

    /**
     * @return Collection|Etudiant[]
     */
    public function getEtudiants(): Collection
    {
        return $this->etudiants;
    }

    public function addEtudiant(Etudiant $etudiant): self
    {
        if (!$this->etudiants->contains($etudiant)) {
            $this->etudiants[] = $etudiant;
            $etudiant->setDepartement($this);
        }

        return $this;
    }

    public function removeEtudiant(Etudiant $etudiant): self
    {
        if ($this->etudiants->contains($etudiant)) {
            $this->etudiants->removeElement($etudiant);
            // set the owning side to null (unless already changed)
            if ($etudiant->getDepartement() === $this) {
                $etudiant->setDepartement(null);
            }
        }

        return $this;
    }

    public function libelleInitiales()
    {
        return str_replace(' ', '<br>', $this->libelle);
    }
}
