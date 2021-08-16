<?php


namespace App\Controller;


use App\Modules\FileSaver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ApiController extends AbstractController
{
    /**
     * @Route("/api/load-file", name="load-file")
     */
    public function loadFile(Request $request, FileSaver $fileSaver): JsonResponse
    {
        if (!empty($_FILES)) {
            foreach ($_FILES as $key => $file) {
                /** @var $file UploadedFile */
                $file = $request->files->get($key);

                if ($file) {
                    try {

                        $fileSaver->save($file);
                    } catch (FileException $exception) {
                        return $this->json(['success' => false]);
                    }
                }

                return $this->json(['success' => true]);

            }
        }

        return $this->json(['success' => false]);
    }
}