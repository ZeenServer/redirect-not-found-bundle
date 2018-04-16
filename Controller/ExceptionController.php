<?php
/**
 * @author Vitiko <vitiko@mail.ru>
 */

namespace Zeen\ZeenRedirectNotFoundBundle\Controller;

use Symfony\Bundle\TwigBundle\Controller\ExceptionController as BaseExceptionController;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;

class ExceptionController extends Controller
{
    public function showExceptionAction(
        Request $request, FlattenException $exception, DebugLoggerInterface $logger = null
    )
    {
        if ($exception->getStatusCode() == 404) {
            $uri = str_replace('/app_dev.php', '', $request->getRequestUri());
            $redirectUri = $this->get('zeen.redirectnotfound.observer_pool')->findRedirect($uri);

            if (!is_null($redirectUri)) {
                return new RedirectResponse($redirectUri,301);
            }
        }

        return $this->get('twig.controller.exception')->showAction($request, $exception, $logger);
    }
} 