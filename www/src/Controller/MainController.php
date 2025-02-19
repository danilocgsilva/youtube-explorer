<?php

declare(strict_types=1);

namespace App\Controller;

use App\Services\Fetch;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\{
    HttpFoundation\Response,
    Routing\Attribute\Route,
    HttpFoundation\Request,
    Messenger\MessageBusInterface
};
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Message\FetchAllVideosFromYoutubeChannel;

final class MainController extends AbstractController
{
    #[Route('/channel_id/', name: 'app_main_list')]
    public function list(
        Request $request, 
        Fetch $fetch,
        LoggerInterface $logger
    ): Response
    {
        if (!$this->isCsrfTokenValid('search-term', $request->get("token"))) {
            throw $this->createNotFoundException();
        }

        /** @var \App\Data\FetcheResult */
        $videosList = $fetch->fetch($request->get("youtube-channel-id"), $logger);

        return $this->render('main/list.html.twig', [
            'videos_list' => $videosList,
        ]);
    }

    #[Route('/channel_id/async', name: 'app_process_async')]
    public function processAsync(
        Request $request, 
        MessageBusInterface $messageBus
    ): RedirectResponse
    {
        if (!$this->isCsrfTokenValid('search-term', $request->get("token"))) {
            throw $this->createNotFoundException();
        }

        $messageBus->dispatch(
            new FetchAllVideosFromYoutubeChannel(
                $request->get("youtube-channel-id"),
                $request->get("fetches_count"),
                $request->get("next_page_token")
            )
        );

        return $this->redirectToRoute('app_default');
    }
}
