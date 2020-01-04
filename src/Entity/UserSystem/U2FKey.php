<?php
/**
 * This file is part of Part-DB (https://github.com/Part-DB/Part-DB-symfony).
 *
 * Copyright (C) 2019 Jan Böhmer (https://github.com/jbtronics)
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA
 */

namespace App\Entity\UserSystem;

use App\Entity\Base\TimestampTrait;
use Doctrine\ORM\Mapping as ORM;
use R\U2FTwoFactorBundle\Model\U2F\TwoFactorInterface;
use R\U2FTwoFactorBundle\Model\U2F\TwoFactorKeyInterface;
use u2flib_server\Registration;

/**
 * @ORM\Entity
 * @ORM\Table(name="u2f_keys",
 * uniqueConstraints={@ORM\UniqueConstraint(name="user_unique",columns={"user_id",
 * "key_handle"})})
 * @ORM\HasLifecycleCallbacks()
 */
class U2FKey implements TwoFactorKeyInterface
{
    use TimestampTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=64)
     *
     * @var string
     **/
    public $keyHandle;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     **/
    public $publicKey;

    /**
     * @ORM\Column(type="text")
     *
     * @var string
     **/
    public $certificate;

    /**
     * @ORM\Column(type="string")
     *
     * @var int
     **/
    public $counter;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UserSystem\User", inversedBy="u2fKeys")
     *
     * @var User
     **/
    protected $user;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     **/
    protected $name;

    public function fromRegistrationData(Registration $data): void
    {
        $this->keyHandle = $data->keyHandle;
        $this->publicKey = $data->publicKey;
        $this->certificate = $data->certificate;
        $this->counter = $data->counter;
    }

    /** {@inheritdoc} */
    public function getKeyHandle()
    {
        return $this->keyHandle;
    }

    /** {@inheritdoc} */
    public function setKeyHandle($keyHandle)
    {
        $this->keyHandle = $keyHandle;
    }

    /** {@inheritdoc} */
    public function getPublicKey()
    {
        return $this->publicKey;
    }

    /** {@inheritdoc} */
    public function setPublicKey($publicKey)
    {
        $this->publicKey = $publicKey;
    }

    /** {@inheritdoc} */
    public function getCertificate()
    {
        return $this->certificate;
    }

    /** {@inheritdoc} */
    public function setCertificate($certificate)
    {
        $this->certificate = $certificate;
    }

    /** {@inheritdoc} */
    public function getCounter()
    {
        return $this->counter;
    }

    /** {@inheritdoc} */
    public function setCounter($counter)
    {
        $this->counter = $counter;
    }

    /** {@inheritdoc} */
    public function getName()
    {
        return $this->name;
    }

    /** {@inheritdoc} */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Gets the user, this U2F key belongs to.
     *
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * The primary key ID of this key.
     *
     * @return int
     */
    public function getID(): int
    {
        return $this->id;
    }

    /**
     * Sets the user this U2F key belongs to.
     *
     * @return $this
     */
    public function setUser(TwoFactorInterface $new_user): self
    {
        $this->user = $new_user;

        return $this;
    }
}