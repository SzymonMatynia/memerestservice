<?php


namespace App\Service;;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\MemeApiController;
use Symfony\Component\HttpKernel\KernelInterface;
use App\Entity\MemeApi;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\MemeApiRepository;

class MemeApiService implements MemeApiServiceInterface
{
    private $em;
    private $appKernel;
    private $memeApiRepository;
    public function __construct(EntityManagerInterface $em, KernelInterface $appKernel, MemeApiRepository $memeApiRepository)
    {
        $this->em = $em;
        $this->appKernel = $appKernel;
        $this->memeApiRepository = $memeApiRepository;
    }

    public function addMeme(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $title = $data['title'];
        if(!$title) return ['success' => false, 'message' => 'You must provide title'];
        $image = $data['image'];
        $base64Image = @explode(',', $image)[1];
        $decodedImage = base64_decode($base64Image, true);
        $uniq = uniqid();
        if(!$decodedImage)
        {
            return ['success' => false, 'message' => 'Image is not valid.'];
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
        return ['success' => true, 'message' => 'Image has been correctly added to a collection as ' . $memeApi->getTitle()];
    }

    public function deleteMeme($id)
    {
        // TODO: Implement deleteMeme() method.
    }

    public function updateMeme($id, $title)
    {
        // TODO: Implement updateMeme() method.
    }

    public function getMeme($id)
    {
        // get the meme of given id
        $meme = $this->memeApiRepository->find($id);

        if(!$meme) return ['success' => false, 'message' => 'No such id in the database'];

        $basePath = $this->appKernel->getProjectDir();

        $path = $basePath . $meme->getImage();

        $base64 = base64_encode(file_get_contents($basePath . $meme->getImage()));

        $type = pathinfo($path, PATHINFO_EXTENSION);

        $data = ['title' => $meme->getTitle(), 'base64' => "$base64", 'type' => $type, 'success' => true];

        return $data;
    }

    public function getMemes()
    {
        $memes = $this->memeApiRepository->findAll();
        // if any meme doesnt exist then return 404
        if(!$memes)
        {
            return ['success' => false, 'message' => 'No memes!'];
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
        return $data;
    }

}