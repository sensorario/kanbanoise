<?php

namespace Tests\AppBundle\Controller;

use Kanban\Factories;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class DefaultControllerTest extends WebTestCase
{
    public function testRedirectUserToKanbanAfterLogin()
    {
        $this->adminClient = self::createClient();
        $container = $this->adminClient->getContainer();
        $manager = $container->get('doctrine.orm.entity_manager');

        $manager->createQuery('delete from AppBundle\Entity\User')->execute();

        $user = Factories\UserFactory::buildAdmin();
        $manager->persist($user);
        $manager->flush();

        $board = Factories\BoardFactory::buildWithOwner('admin');
        $manager->persist($board);
        $manager->flush();

        $admin = $manager
            ->getRepository(\AppBundle\Entity\User::class)
            ->loadUserByUsername('admin');

        if (!$admin) {
            throw new \RuntimeException(
                'Oops! There is no user'
            );
        }

        $token = new UsernamePasswordToken($admin, null, 'main', ['ROLE_ADMIN']);
        $container->get('session')->set('_security_main', serialize($token));
        $this->adminClient->followRedirects(true);
        $this->adminClient->request('GET', '/card/kanban');
        $this->assertEquals(200, $this->adminClient->getResponse()->getStatusCode());
    }
}
