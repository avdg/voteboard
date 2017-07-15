<?php

namespace AppBundle\DependencyInjection;

use Symfony\Component\HttpFoundation\RequestStack;

use Doctrine\ORM\EntityManagerInterface;

class UserManager
{
    protected $session;
    protected $em;
    protected $user;

    /**
     * Constructor
     */
    public function __construct(RequestStack $requestStack, EntityManagerInterface $em)
    {
        $this->session = $requestStack->getCurrentRequest()->getSession();
        $this->em = $em;
    }

    /**
     * Check if the user is logged in
     */
    public function validateUser()
    {
        if (!$this->session->has("user") || $this->session->get("user") === null) {
            return false;
        }

        if ($this->user === null) {
            $this->user = $this
                ->em
                ->getRepository("\AppBundle\Entity\User")
                ->findOneBy([
                    "name" => $this->session->get("user")
                ]);
        }

        if ($this->user === null
            || !hash_equals($this->user->getHash(), $this->session->get("user_token"))
        ) {
            $this->logoutUser();
            return false;
        }

        return true;
    }

    /**
     * Check if current user is a guest
     */
    public function isGuest()
    {
        return !$this->validateUser();
    }

    /**
     * Get the current user
     *
     * @return string|null Returns the user name if exists or null if the visitor is a guest
     */
    public function getCurrentUser()
    {
        $this->validateUser();

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
        if ($this->validateUser()) {
            return $this->user->getId();
        }

        return false;
    }

    /**
     * Check if the user is valid
     *
     * @param array $data Login data, expected keys are email and password
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

        if (self::verifyHash($data["password"], $user->getHash())) {
            $this->session->set("user", $user->getName());
            $this->session->set("user_token", $user->getHash());
            return true;
        }

        return false;
    }

    public function logoutUser()
    {
        $this->session->remove("user");
        $this->session->remove("user_token");
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
     * @param array $data create user data, expected keys are email, username
     *                    and password
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
