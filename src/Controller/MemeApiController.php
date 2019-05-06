<?php

namespace App\Controller;

use App\Entity\MemeApi;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\MemeApiRepository;
class MemeApiController extends AbstractController
{
    private $appKernel;
    private $memeApiRepository;
    private $em;
    private const IMAGES_PATH = '/public/uploads/';
    public function __construct(MemeApiRepository $memeApiRepository, EntityManagerInterface $em, KernelInterface $appKernel)
    {
        $this->memeApiRepository = $memeApiRepository;
        $this->em = $em;
        $this->appKernel = $appKernel;
    }

    public function getMemes()
    {
        $memes = $this->memeApiRepository->findAll();
        // if any meme doesnt exist then return 404
        if(!$memes)
        {
            return $this->json([
                'message' => 'No memes!',
            ], 404);
        }
        $basePath = $this->appKernel->getProjectDir();
        $data = [];

        // fill the data variable with appropriate values
        foreach($memes as $mms)
        {
            $path = $basePath . $mms->getImage();
            $base64 = base64_encode(file_get_contents($basePath . $mms->getImage()));
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data[] = ['title' => $mms->getTitle(), 'base64' => $base64, 'type' => $type];
        }

        // use only if dont find solution to unescaped slashes
        $response = new Response(json_encode($data, JSON_UNESCAPED_SLASHES), 200, ['application/json']);
        $response->headers->set('Content-Type', 'application/json');
        return $response;

        /*return $this->json([
            'data' =>  $data,
        ], 200);*/
    }

    public function getMeme($id)
    {
        // get the meme of given id
        $meme = $this->memeApiRepository->find($id);
        if(!$meme)
        {
            return $this->json([
                'message' => 'No such id in the database',
            ], 404);
        }
        $basePath = $this->appKernel->getProjectDir();
        $data = [];

        $path = $basePath . $meme->getImage();

        $base64 = base64_encode(file_get_contents($basePath . $meme->getImage()));

        $type = pathinfo($path, PATHINFO_EXTENSION);

        $data[] = ['title' => $meme->getTitle(), 'base64' => "$base64", 'type' => $type];


        // use only if dont find solution to unescaped slashes
        $response = new Response(json_encode($data, JSON_UNESCAPED_SLASHES), 200, ['application/json']);
        $response->headers->set('Content-Type', 'application/json');
        return $response;


        /*return $this->json([
            $data,
        ], 200);*/

    }

    public function deleteMeme($id)
    {
        $meme = $this->memeApiRepository->find($id);
        if(!$meme)
        {
            return $this->json([
                'message' => 'No such id in the database',
            ], 404);
        }
        // delete the file
    }

    public function updateMeme()
    {
        // update the title of meme only if author or admin :)
    }

    public function addMeme(Request $request)
    {

        $uploadedFile = $request->files->get('file');
        //return new JsonResponse(var_dump($uploadedFile->getPathname()));
        if(!isset($uploadedFile) || empty($uploadedFile)) return new JsonResponse(['message' => 'Please upload file.'], 400);

        $uploadedFileType = $uploadedFile->getMimeType();


        if(!($uploadedFileType == 'image/png' || $uploadedFileType == 'image/jpeg' || $uploadedFileType == 'image/gif'))
        {
            return new JsonResponse(['message' => 'Not allowed type sent.'], 400);
        }

        $memeApi = new MemeApi();
        $memeApi->setTitle(uniqid());
        $memeApi->setImage(MemeApiController::IMAGES_PATH . $memeApi->getTitle() . "." . $uploadedFile->getClientOriginalExtension());
        $this->em->persist($memeApi);
        $this->em->flush();

        move_uploaded_file($uploadedFile->getPathname(), $this->appKernel->getProjectDir() . $memeApi->getImage());

        return new JsonResponse(['message' => 'Image ' . $uploadedFile->getClientOriginalName() . ' has been added to db with name: ' . $memeApi->getTitle()]);
    }
}

