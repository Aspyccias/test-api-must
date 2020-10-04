<?php


namespace App\Tests\Controller\Authentication;


use App\DataFixtures\UserFixture;
use App\Entity\User;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthenticationControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private ?object $doctrine;

    public function setUp()
    {
        parent::setUp();

        $this->client = static::createClient();
        $container = $this->client->getContainer();
        $this->doctrine = $container->get('doctrine');
        $entityManager = $this->doctrine->getManager();

        $loader = new Loader();
        $loader->addFixture(new UserFixture());

        $purger = new ORMPurger($entityManager);
        $executor = new ORMExecutor($entityManager, $purger);
        $executor->execute($loader->getFixtures());
    }

    public function testLogin()
    {
        $this->client->request('POST', '/login', [], [], [], json_encode(['login' => 'polo', 'password' => 'mdp']));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertRegExp('/\{"token":.*\}/', $this->client->getResponse()->getContent());
    }

    public function testLoginReturnsCurrentTokenIfNotExpired()
    {
        $this->testLogin();

        $userRepository = $this->doctrine->getRepository(User::class);
        $user = $userRepository->findOneBy(['login' => 'polo']);
        $currentToken = $user->getApiToken();

        $this->client->request('POST', '/login', [], [], [], json_encode(['login' => 'polo', 'password' => 'mdp']));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('{"token":"'.$currentToken.'"}', $this->client->getResponse()->getContent());

        $user = $userRepository->findOneBy(['login' => 'polo']);
        $this->assertEquals($currentToken, $user->getApiToken());
    }

    public function testLoginReturnsNewTokenOnCurrentTokenExpired()
    {
        $userRepository = $this->doctrine->getRepository(User::class);
        $user = $userRepository->findOneBy(['login' => 'polo']);
        $user->setApiTokenExpiryDate(new \DateTime('-1 hour'));
        $currentToken = $user->getApiToken();
        $oldTokenExpiry = $user->getApiTokenExpiryDate();

        $this->client->request('POST', '/login', [], [], [], json_encode(['login' => 'polo', 'password' => 'mdp']));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertRegExp('/\{"token":.*\}/', $this->client->getResponse()->getContent());

        $user = $userRepository->findOneBy(['login' => 'polo']);
        $this->assertNotEquals($currentToken, $user->getApiToken());
        $this->assertGreaterThan($oldTokenExpiry, $user->getApiTokenExpiryDate());
    }
}
