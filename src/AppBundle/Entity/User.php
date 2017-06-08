<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User
{
    /**
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    private $name;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $email;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $hash;

    /**
     * @ORM\Column(type="boolean", nullable=false, options = {"default": false})
     */
    private $notify_poll;

    /**
     * @ORM\Column(type="boolean", nullable=false, options = {"default": true})
     */
    private $notify_result;

    /**
     * @ORM\Column(type="boolean", nullable=false, options = {"default": false})
     */
    private $blocked;

    /**
     * @ORM\Column(type="boolean", nullable=false, options = {"default": false})
     */
    private $is_admin;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set notifyPoll
     *
     * @param boolean $notifyPoll
     *
     * @return User
     */
    public function setNotifyPoll($notifyPoll)
    {
        $this->notify_poll = $notifyPoll;

        return $this;
    }

    /**
     * Get notifyPoll
     *
     * @return boolean
     */
    public function getNotifyPoll()
    {
        return $this->notify_poll;
    }

    /**
     * Set notifyResult
     *
     * @param boolean $notifyResult
     *
     * @return User
     */
    public function setNotifyResult($notifyResult)
    {
        $this->notify_result = $notifyResult;

        return $this;
    }

    /**
     * Get notifyResult
     *
     * @return boolean
     */
    public function getNotifyResult()
    {
        return $this->notify_result;
    }

    /**
     * Set blocked
     *
     * @param boolean $blocked
     *
     * @return User
     */
    public function setBlocked($blocked)
    {
        $this->blocked = $blocked;

        return $this;
    }

    /**
     * Get blocked
     *
     * @return boolean
     */
    public function getBlocked()
    {
        return $this->blocked;
    }

    /**
     * Set isAdmin
     *
     * @param boolean $isAdmin
     *
     * @return User
     */
    public function setIsAdmin($isAdmin)
    {
        $this->is_admin = $isAdmin;

        return $this;
    }

    /**
     * Get isAdmin
     *
     * @return boolean
     */
    public function getIsAdmin()
    {
        return $this->is_admin;
    }

    /**
     * Set hash
     *
     * @param string $hash
     *
     * @return User
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get hash
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }
}
