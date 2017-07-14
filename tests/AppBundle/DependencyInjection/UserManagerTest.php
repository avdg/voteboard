<?php

namespace Tests\AppBundle\DependencyInjection;

use AppBundle\Service\UserManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use PHPUnit\Framework\TestCase;

class UserManagerTest extends TestCase
{
    const PASSWORD_HASH = '$2y$10$Y995s9LJv8Dsrs/oDq5WSuhUQA9DaRPUR55RnXdKnYln5YhEP/29.';

    public function testValidateUserAsGuest()
    {
        $emMock = $this
            ->getMockBuilder('Doctrine\ORM\EntityManagerInterface')
            ->getMock();
        $requestStackMock = $this
            ->getMockBuilder('Symfony\Component\HttpFoundation\RequestStack')
            ->getMock();
        $requestMock = new Request();
        $userMock = $this
            ->getMockBuilder("AppBundle\Entity\User")
            ->getMock();
        $session = new Session(
            new MockArraySessionStorage()
        );
        $requestMock->setSession($session);
        $userRepositoryMock = $this
            ->getMockBuilder("Doctrine\ORM\EntityRepository")
            ->disableOriginalConstructor()
            ->getMock();

        $requestStackMock->expects($this->exactly(4))
            ->method("getCurrentRequest")
            ->willReturn($requestMock);

        $emMock->expects($this->exactly(2))
            ->method("getRepository")
            ->willReturn($userRepositoryMock);

        $userRepositoryMock->expects($this->exactly(2))
            ->method("findOneBy")
            ->willReturnOnConsecutiveCalls(null, $userMock);

        $userMock->expects($this->once())
            ->method("getHash")
            ->willReturn("barfoo");

        // No user session data
        $userManager = new UserManager($requestStackMock, $emMock);
        $this->assertEquals(false, $session->has("user"));
        $this->assertEquals(false, $userManager->validateUser());
        $this->assertEquals(true, $userManager->isGuest());
        $this->assertEquals(null, $userManager->getCurrentUser());

        // Incomplete user session data
        $session->set("user", null);
        $userManager = new UserManager($requestStackMock, $emMock);
        $this->assertEquals(true, $session->has("user"));
        $this->assertEquals(false, $userManager->validateUser());
        $this->assertEquals(true, $userManager->isGuest());
        $this->assertEquals(null, $userManager->getCurrentUser());
        $this->assertEquals(false, $userManager->getUserId());

        // Invalid user (in the rare case a user gets removed)
        $session->set("user", "foo");
        $userManager = new UserManager($requestStackMock, $emMock);
        $this->assertEquals(true, $session->has("user"));
        $this->assertEquals(false, $userManager->validateUser());
        $this->assertEquals(true, $userManager->isGuest());
        $this->assertEquals(null, $userManager->getCurrentUser());
        $this->assertEquals(false, $userManager->getUserId());

        // Valid user with invalid credentials
        $session->set("user", "bar");
        $session->set("user_token", "foobar");
        $userManager = new UserManager($requestStackMock, $emMock);
        $this->assertEquals(true, $session->has("user"));
        $this->assertEquals(false, $userManager->validateUser());
        $this->assertEquals(true, $userManager->isGuest());
        $this->assertEquals(null, $userManager->getCurrentUser());
        $this->assertEquals(false, $userManager->getUserId());
    }

    public function testValidateUserWithValidUser()
    {
        $emMock = $this
            ->getMockBuilder('Doctrine\ORM\EntityManagerInterface')
            ->getMock();
        $requestStackMock = $this
            ->getMockBuilder('Symfony\Component\HttpFoundation\RequestStack')
            ->getMock();
        $requestMock = new Request();
        $userMock = $this
            ->getMockBuilder("AppBundle\Entity\User")
            ->getMock();
        $session = new Session(
            new MockArraySessionStorage()
        );
        $requestMock->setSession($session);
        $userRepositoryMock = $this
            ->getMockBuilder("Doctrine\ORM\EntityRepository")
            ->disableOriginalConstructor()
            ->getMock();

        $requestStackMock->expects($this->once())
            ->method("getCurrentRequest")
            ->willReturn($requestMock);

        $emMock->expects($this->once())
            ->method("getRepository")
            ->willReturn($userRepositoryMock);

        $userRepositoryMock->expects($this->once())
            ->method("findOneBy")
            ->willReturn($userMock);

        $userMock->expects($this->exactly(4))
            ->method("getHash")
            ->willReturn("foobar");

        $userMock->expects($this->once())
            ->method("getId")
            ->willReturn(132);

        // Valid user with valid credentials
        $session->set("user", "bar");
        $session->set("user_token", "foobar");
        $userManager = new UserManager($requestStackMock, $emMock);
        $this->assertEquals(true, $session->has("user"));
        $this->assertEquals(true, $userManager->validateUser());
        $this->assertEquals(false, $userManager->isGuest());
        $this->assertEquals("bar", $userManager->getCurrentUser());
        $this->assertEquals(132, $userManager->getUserId());
    }

    public function testLoginUser()
    {
        $emMock = $this
            ->getMockBuilder('Doctrine\ORM\EntityManagerInterface')
            ->getMock();
        $requestStackMock = $this
            ->getMockBuilder('Symfony\Component\HttpFoundation\RequestStack')
            ->getMock();
        $requestMock = new Request();

        $userMock = $this
            ->getMockBuilder("AppBundle\Entity\User")
            ->getMock();
        $session = new Session(
            new MockArraySessionStorage()
        );
        $requestMock->setSession($session);
        $userRepositoryMock = $this
            ->getMockBuilder("Doctrine\ORM\EntityRepository")
            ->disableOriginalConstructor()
            ->getMock();

        $requestStackMock->expects($this->exactly(3))
            ->method("getCurrentRequest")
            ->willReturn($requestMock);

        $emMock->expects($this->exactly(3))
            ->method("getRepository")
            ->willReturn($userRepositoryMock);

        $userRepositoryMock->expects($this->exactly(3))
            ->method("findOneBy")
            ->willReturnOnConsecutiveCalls(null, $userMock, $userMock);

        $userMock->expects($this->exactly(3))
            ->method("getHash")
            ->willReturn(self::PASSWORD_HASH);

        $userMock->expects($this->once())
            ->method("getName")
            ->willReturn("foobar");

        // Invalid user
        $userManager = new UserManager($requestStackMock, $emMock);
        $this->assertEquals(
            false,
            $userManager->loginUser([
                "email" => "foobar@example.org",
                "password" => "barfoo"
            ])
        );
        $this->assertEquals(false, $session->has("user"));
        $this->assertEquals(false, $session->has("user_token"));

        // Valid user, invalid password
        $userManager = new UserManager($requestStackMock, $emMock);
        $this->assertEquals(
            false,
            $userManager->loginUser([
                "email" => "foo@example.org",
                "password" => "barfoo"
            ])
        );
        $this->assertEquals(false, $session->has("user"));
        $this->assertEquals(false, $session->has("user_token"));

        // Valid user, valid password
        $userManager = new UserManager($requestStackMock, $emMock);
        $this->assertEquals(
            true,
            $userManager->loginUser([
                "email" => "bar@example.org",
                "password" => "foobar"
            ])
        );
        $this->assertEquals("foobar", $session->get("user"));
        $this->assertEquals(self::PASSWORD_HASH, $session->get("user_token"));
    }

    public function testVerifyHash()
    {
        $emMock = $this
            ->getMockBuilder('Doctrine\ORM\EntityManagerInterface')
            ->getMock();
        $requestStackMock = $this
            ->getMockBuilder('Symfony\Component\HttpFoundation\RequestStack')
            ->getMock();
        $requestMock = new Request();

        $requestStackMock->expects($this->once())
            ->method("getCurrentRequest")
            ->willReturn($requestMock);

        $userManager = new UserManager($requestStackMock, $emMock);
        $this->assertEquals(true, $userManager->verifyHash("foobar", SELF::PASSWORD_HASH));
    }

    public function testCreateUser()
    {
        $emMock = $this
            ->getMockBuilder('Doctrine\ORM\EntityManagerInterface')
            ->getMock();
        $requestStackMock = $this
            ->getMockBuilder('Symfony\Component\HttpFoundation\RequestStack')
            ->getMock();
        $request = new Request();

        $requestStackMock->expects($this->once())
            ->method("getCurrentRequest")
            ->willReturn($request);

        $emMockSpy = $this->once();

        $emMock->expects($emMockSpy)
            ->method("persist");
        $emMock->expects($this->once())
            ->method("flush");

        $userManager = new UserManager($requestStackMock, $emMock);
        $userManager->createUser([
            "username" => "foo",
            "email"    => "foo@example.com",
            "password" => "foobar"
        ]);

        $persistCall = $emMockSpy->getInvocations()[0]->parameters;
        $this->assertEquals(1, sizeof($persistCall));
        $this->assertEquals(true, $persistCall[0] instanceof \AppBundle\Entity\User);
    }

    public function testGenerateHash()
    {
        $emMock = $this
            ->getMockBuilder('Doctrine\ORM\EntityManagerInterface')
            ->getMock();
        $requestStackMock = $this
            ->getMockBuilder('Symfony\Component\HttpFoundation\RequestStack')
            ->getMock();
        $requestMock = new Request();

        $requestStackMock->expects($this->once())
            ->method("getCurrentRequest")
            ->willReturn($requestMock);

        $userManager = new UserManager($requestStackMock, $emMock);

        $this->assertRegExp('/\$2y\$\d+\$[.\/0-9A-Za-z]{53}/', $userManager->generateHash("blah12345"));
    }
}
