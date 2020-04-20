<?php


namespace AcMarche\Mercredi\Entity\Traits;


trait BirthdayTrait
{
    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="birthday", type="date", nullable=true)
     */
    private $birthday;

    /**
     * @return \DateTime|null
     */
    public function getBirthday(): ?\DateTime
    {
        return $this->birthday;
    }

    /**
     * @param \DateTime|null $birthday
     */
    public function setBirthday(?\DateTime $birthday): void
    {
        $this->birthday = $birthday;
    }


}
