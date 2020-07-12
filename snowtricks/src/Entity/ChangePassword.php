<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;

class ChangePassword
{

    /**
     * @Assert\Length(
     *     min = 6,
     *      max = 50,
     *      minMessage = "Votre mot de passe doit comporter plus de  {{ limit }} characteres ",
     *      maxMessage = "Votre mot de passe doit comporter moins de  {{ limit }} characteres ",
     *      allowEmptyString = false
     *     )
     */
    private $password;

    /**
     * @Assert\EqualTo(propertyPath="password",message="Les deux mot de passes doivent etre identique")
     */
    private $passwordConfirm;

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPasswordConfirm(): ?string
    {
        return $this->passwordConfirm;
    }

    public function setPasswordConfirm(string $passwordConfirm): self
    {
        $this->passwordConfirm = $passwordConfirm;

        return $this;
    }
}
