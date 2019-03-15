<?php declare(strict_types=1);


namespace Swoft\Http\Server\Listener;


use Swoft\Bean\Container;
use Swoft\Context\Context;
use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Http\Message\Request;
use Swoft\Http\Message\Response;
use Swoft\Http\Server\HttpContext;
use Swoft\Http\Server\HttpServerEvent;
use Swoft\Log\Logger;

/**
 * Class BeforeRequestListener
 *
 * @since 2.0
 *
 * @Listener(HttpServerEvent::BEFORE_REQUEST)
 */
class BeforeRequestListener implements EventHandlerInterface
{
    /**
     * @param EventInterface $event
     */
    public function handle(EventInterface $event): void
    {
        /**
         * @var Request  $request
         * @var Response $response
         */
        [$request, $response] = $event->getParams();
        $httpContext = HttpContext::new($request, $response);

        /* @var Logger $logger */
        $logger = Container::$instance->getSingleton('logger');

        // Add log data
        if ($logger->isEnable()) {
            $data = [
                'traceid'     => $request->headerLine('traceid', ''),
                'spanid'      => $request->headerLine('spanid', ''),
                'uri'         => $request->getRequestTarget(),
                'requestTime' => $request->getRequestTime(),
            ];

            $httpContext->setMulti($data);
        }

        Context::set($httpContext);
    }
}