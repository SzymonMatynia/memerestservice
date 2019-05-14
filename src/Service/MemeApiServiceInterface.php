<?php


namespace App\Service;

use Symfony\Component\HttpFoundation\Request;

interface MemeApiServiceInterface
{
    public function addMeme(Request $request);
    public function deleteMeme($id);
    public function updateMeme($id);
    public function getMeme($id);
    public function getMemes();

}