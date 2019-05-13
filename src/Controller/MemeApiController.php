<?php

namespace App\Controller;

use App\Entity\MemeApi;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\MemeApiRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;

class MemeApiController extends AbstractController
{
    private $appKernel;
    private $memeApiRepository;
    private $em;
    private $serializer;
    private $validator;
    private const IMAGES_PATH = '/public/uploads/';
    public function __construct(MemeApiRepository $memeApiRepository, EntityManagerInterface $em, KernelInterface $appKernel, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->memeApiRepository = $memeApiRepository;
        $this->em = $em;
        $this->appKernel = $appKernel;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    public function getMemes()
    {
        /* delete the data
        $query = $this->em->getConnection()->query('TRUNCATE meme_api');
        $query->execute();
        */


        $memes = $this->memeApiRepository->findAll();
        // if any meme doesnt exist then return 404
        if(!$memes)
        {
            return $this->json([
                'success' => false,
                'message' => 'No memes!',
            ], 404);
        }
        $basePath = $this->appKernel->getProjectDir();
        $data = [];

        // fill the data variable with appropriate values
        foreach($memes as $mms)
        {
            $path = $basePath . $mms->getImage();
            $base64 = @base64_encode(file_get_contents($basePath . $mms->getImage()));
            if(empty($base64)) continue; // what should i do?
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data[] = ['id' => $mms->getId(), 'title' => $mms->getTitle(), 'base64' => $base64, 'type' => $type, 'success' => true];
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
                'success' => false,
                'message' => 'No such id in the database',
            ], 404);
        }

        return $this->json([
            'success' => true,
            'message' => 'File deleted',
        ], 404);
        // delete the file
    }

    public function updateMeme()
    {
        // update the title of meme only if author or admin :)
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addMeme(Request $request)
    {

        $data = json_decode($request->getContent(), true);

        $title = $data['title'];
        if(!$title) return new JsonResponse(['message' => 'You must provide title']);
        $image = $data['image'];
        $base64Image = @explode(',', $image)[1];
        $decodedImage = base64_decode($base64Image, true);
        $uniq = uniqid();
        if(!$decodedImage)
        {
            return new JsonResponse(['success' => false, 'message' => 'Image not valid.']);
        }
        else
        {
            $imageType = explode('/', mime_content_type($image))[1];
            file_put_contents($this->appKernel->getProjectDir() . MemeApiController::IMAGES_PATH . $uniq . "_" . $title . "." . $imageType, $decodedImage);
        }

        $memeApi = new MemeApi();

        $memeApi->setTitle($uniq . "_" . $title)->setImage(MemeApiController::IMAGES_PATH . $uniq . "_" . $title . "." . $imageType);

        $this->em->persist($memeApi);
        $this->em->flush();

        $jsonResponse = new JsonResponse(['success' => true, 'message' => 'Image has been added.'], 200, [], false);
        return $jsonResponse;

    }
}

