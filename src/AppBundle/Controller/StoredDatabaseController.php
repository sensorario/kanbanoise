<?php

namespace AppBundle\Controller;

use AppBundle\Entity\StoredDatabase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("storeddatabase")
 */
class StoredDatabaseController extends Controller
{
    /**
     * @Route("/file/{file}", name="storeddatabase_load")
     * @Method("GET")
     */
    public function loadAction(string $file)
    {
        @unlink(__DIR__ . '/../../../var/data/data.sqlite');

        @copy(
            __DIR__ . '/../../../uploads/' . $file,
            __DIR__ . '/../../../var/data/data.sqlite'
        );

        return $this->redirectToRoute('kanban');
    }

    /**
     * @Route("/", name="storeddatabase_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $allFiles = scandir(__DIR__ . '/../../../uploads/');

        $filesToHide = [
            '.',
            '..',
            '.DS_Store',
            '.gitkeep'
        ];

        $storedDatabases = [];

        foreach ($allFiles as $itemKey => $itemValue) {
            if (!in_array($itemValue, $filesToHide)) {
                $storedDatabases[] = $itemValue;
            }
        }

        return $this->render('storeddatabase/index.html.twig', array(
            'storedDatabases' => $storedDatabases,
        ));
    }

    /**
     * @Route("/new", name="storeddatabase_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $form = $this->createForm('AppBundle\Form\StoredDatabaseType');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['relativePath']->getData();
            $extension = $file->getClientOriginalExtension();
            $file->move(
                __DIR__ . '/../../../uploads/',
                $relativePath = md5(time()) . '.' . $extension
            );

            return $this->redirectToRoute('storeddatabase_index');
        }

        return $this->render('storeddatabase/new.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{file}/edit", name="storeddatabase_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, string $file)
    {
        @unlink(__DIR__ . '/../../../uploads/' . $file);

        @copy(
            __DIR__ . '/../../../var/data/data.sqlite',
            __DIR__ . '/../../../uploads/' . $file
        );

        return $this->redirectToRoute('kanban');
    }

    /**
     * @Route("/{file}", name="storeddatabase_delete")
     * @Method("GET")
     */
    public function deleteAction(Request $request, string $file)
    {
        @unlink(__DIR__ . '/../../../uploads/' . $file);

        return $this->redirectToRoute('storeddatabase_index');
    }

    private function createDeleteForm(StoredDatabase $storedDatabase)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('storeddatabase_delete', array('id' => $storedDatabase->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
