<?php

namespace AppBundle\Controller;

use AppBundle\Component\Installer;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class DefaultController extends Controller
{
    private $manager;

    private $installer;

    public function __construct(
        EntityManagerInterface $manager,
        Installer $installer
    ) {
        $this->manager   = $manager;
        $this->installer = $installer;
    }

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request, AuthenticationUtils $authUtils)
    {
        $this->installer->verify();

        if ($request->getMethod() == 'POST') {
            $username = $request->request->get('_username');

            $admin = $this->manager
                ->getRepository(\AppBundle\Entity\User::class)
                ->loadUserByUsername($username);

            if (null != $admin) {
                $token = new UsernamePasswordToken($admin, null, 'main', ['ROLE_ADMIN']);
                $this->get('session')->set('_security_main', serialize($token));
                return $this->redirectToRoute('kanban');
            }
        }

        $error = $authUtils->getLastAuthenticationError();
        $lastUsername = $authUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction(Request $request)
    {
        $this->get('security.token_storage')->setToken(null);
        //$request->getSession()->invalidate();

        return $this->redirectToRoute('login');
    }
}
