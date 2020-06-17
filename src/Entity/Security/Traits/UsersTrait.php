<?php


namespace AcMarche\Mercredi\Entity\Security\Traits;

use AcMarche\Mercredi\Entity\Security\User;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

trait UsersTrait
{
    /**
     * @ORM\ManyToMany(targetEntity="AcMarche\Mercredi\Entity\Security\User", mappedBy="tuteurs" )
     */
    private $users;

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addTuteur($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            $user->removeTuteur($this);
        }

        return $this;
    }

}
