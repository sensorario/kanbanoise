<?php

/** @todo use kernel.root_dir instead of __DIR__ */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("download")
 */
class DownloadController extends Controller
{
    /**
     * @Route("/downloads", name="downloads")
     * @Method("GET")
     */
    public function downloadsAction()
    {
        return $this->render('downloads/index.html.twig');
    }

    /**
     * @Route("/data", name="download_data")
     * @Method("GET")
     */
    public function regressAction()
    {
        $filename = __DIR__ . '/../../../var/data/data.sqlite';
        $response = new Response();
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', mime_content_type($filename));
        $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($filename) . '";');
        $response->headers->set('Content-length', filesize($filename));
        $response->sendHeaders();
        $content = file_get_contents($filename);
        $response->setContent($content);
        return $response;
    }
}
