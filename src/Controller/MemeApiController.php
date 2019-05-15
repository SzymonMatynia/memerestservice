<?php

namespace App\Controller;

use App\Service\MemeApiServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class MemeApiController extends AbstractController
{
    public const IMAGES_PATH = '/public/uploads/';
    private $memeApiService;

    /**
     * MemeApiController constructor.
     * @param MemeApiServiceInterface $memeApiService
     */
    public function __construct(MemeApiServiceInterface $memeApiService)
    {
        $this->memeApiService = $memeApiService;
    }

    /**
     * @return JsonResponse|Response
     */
    public function getMemes(): Response
    {
        /* delete the data
        $query = $this->em->getConnection()->query('TRUNCATE meme_api');
        $query->execute();
        */
        $data = $this->memeApiService->getMemes();
        $response = new Response(json_encode($data, JSON_UNESCAPED_SLASHES), 200, ['application/json']);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @param $id
     * @return Response
     */
    public function getMeme($id): Response
    {

        $response = $this->memeApiService->getMeme($id);

        if($response['success'] === false) return new JsonResponse($response, 400);

        $response = new Response(
            json_encode($response, JSON_UNESCAPED_SLASHES),
            200,
            ['application/json']);

        $response->headers->set('Content-Type', 'application/json');
        return $response;

    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function deleteMeme($id): Response
    {
        return new JsonResponse(['success'=> true]);
    }

    public function updateMeme()
    {
        // update the title of meme only if author or admin :)
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addMeme(Request $request): Response
    {

        $response = $this->memeApiService->addMeme($request);

        if($response['success'] === false) return new JsonResponse($response, 400);

        return new JsonResponse($response, 200);

    }
}

