<?php

namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\RequestStack;

use Doctrine\ORM\EntityManagerInterface;

class UserManager
{
    protected $session;
    protected $em;

    /**
     * Constructor
     */
    public function __construct(RequestStack $requestStack, EntityManagerInterface $em)
    {
        $this->session = $requestStack->getCurrentRequest()->getSession();
        $this->em = $em;
    }

    /**
     * Check if current user is a guest
     */
    public function isGuest()
    {
        return !$this->session->has("user") || $this->session->get("user") === null;
    }

    /**
     * Get the current user
     *
     * @return string|null Returns the user name if exists or null if the visitor is a guest
     */
    public function getCurrentUser()
    {
        if ($this->session->has("user")) {
            return $this->session->get("user");
        }
    }

    /**
     * Get the current user id
     *
     * @return integer|false Returns the user id if exists or false if the visitor is a guest
     */
    public function getUserId()
    {
        if ($this->session->has("user")) {
            $user = $this->em->getRepository("\AppBundle\Entity\User")
                ->findOneBy(["name" => $this->session->get("user")]);

            if ($user !== null) {
                return $user->getId();
            }
        }

        return false;
    }

    /**
     * Check if the user is valid
     *
     * @return boolean True if the user successfully logged in
     */
    public function loginUser($data)
    {
        $user = $this
            ->em
            ->getRepository("\AppBundle\Entity\User")
            ->findOneBy(["email" => $data["email"]]);

        if ($user === null) {
            return false;
        }

        $hash = null; // Get the salt from the database

        if (self::verifyHash($data["password"], $user->getHash())) {
            $this->session->set("user", $user->getName());
            return true;
        }

        return false;
    }

    public function logoutUser()
    {
        $this->session->set("user", null);
    }

    /**
     * Check if the password matches the hash
     *
     * @return boolean
     */
    public static function verifyHash($password, $hash)
    {
        return password_verify($password, $hash);
    }

    /**
     * Creates a new user
     *
     * @return \AppBundle\Entity\User New user object
     */
    public function createUser($data)
    {
        $entity = new \AppBundle\Entity\User();
        $entity->setName($data["username"]);
        $entity->setEmail($data["email"]);
        $entity->setHash($this->generateHash($data["password"]));


        $this->em->persist($entity);
        $this->em->flush();
    }

    /**
     * Generate a new hash for a password
     *
     * @return string Password hash
     */
    public function generateHash($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }
}
