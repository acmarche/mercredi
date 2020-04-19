<?php


namespace AcMarche\Mercredi\Entity\Traits;


trait ConjointTrait
{
    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=200, nullable=true, options={"comment" = "belle-mere, pere, mere"})
     */
    private $conjoint;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $nom_conjoint;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $prenom_conjoint;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $telephone_conjoint;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $telephone_bureau_conjoint;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $gsm_conjoint;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $email_conjoint;

}
