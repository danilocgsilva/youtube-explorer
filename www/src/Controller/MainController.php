<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Services\Fetch;
use Symfony\Component\HttpFoundation\Request;


final class MainController extends AbstractController
{
    #[Route('/channel_id/', name: 'app_main_list')]
    public function list(Request $request, Fetch $fetch): Response
    {
        /**
         * @var array
         */
        $videosList = $fetch->fetch($request->get("youtube-channel-id"));

        return $this->render('main/list.html.twig', [
            'videos_list' => $videosList,
        ]);
    }
}
