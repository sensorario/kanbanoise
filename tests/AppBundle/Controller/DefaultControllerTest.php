<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class DefaultControllerTest extends WebTestCase
{
    public function testRedirectUserToKanbanAfterLogin()
    {
        $this->adminClient = self::createClient();
        $container = $this->adminClient->getContainer();
        $manager = $container->get('doctrine.orm.entity_manager');
        $admin = $manager
            ->getRepository(\AppBundle\Entity\User::class)
            ->loadUserByUsername('admin');
        $token = new UsernamePasswordToken($admin, null, 'main', ['ROLE_ADMIN']);
        $container->get('session')->set('_security_main', serialize($token));
        $this->adminClient->followRedirects(true);
        $this->adminClient->request('GET', '/card/kanban');
        $this->assertEquals(200, $this->adminClient->getResponse()->getStatusCode());
    }
}
