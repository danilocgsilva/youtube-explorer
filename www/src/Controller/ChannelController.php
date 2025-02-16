<?php

declare(strict_types=1);

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\ChannelRepository;

final class ChannelController extends AbstractController
{
    #[Route('/channels/', name: 'app_channels_list')]
    public function index(ChannelRepository $channelRepository): Response
    {
        return $this->render('channels/index.html.twig', [
            "channels" => $channelRepository->findAll()
        ]);
    }
}
