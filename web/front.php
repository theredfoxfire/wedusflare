<?php
// wedusflare framework

require_once __DIR__.'/../vendor/autoload.php';
 
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing;
use Symfony\Component\HttpKernel;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\HttpCache\HttpCache;
use Symfony\Component\HttpKernel\HttpCache\Store;
use Symfony\Component\HttpKernel\HttpCache\Esi;
 
function render_template($request)
{
    extract($request->attributes->all());
    ob_start();
    include sprintf(__DIR__.'/../src/pages/%s.php', $_route);
 
    return new Response(ob_get_clean());
}
 
$request = Request::createFromGlobals();
$routes = include __DIR__.'/../src/app.php';
 
$context = new Routing\RequestContext();
$context->fromRequest($request);
$matcher = new Routing\Matcher\UrlMatcher($routes, $context);
$resolver = new HttpKernel\Controller\ControllerResolver();

$errorHandler = function (HttpKernel\Exception\FlattenException $exception) {
    $msg = 'Mbeeex, Something went wrong! ('.$exception->getMessage().')';
 
    return new Response($msg, $exception->getStatusCode());
};

$listener = new HttpKernel\EventListener\ExceptionListener('Calender\\Controller\\ErrorController::exceptionAction');

$dispatcher = new EventDispatcher();
$dispatcher->addSubscriber(new Embex\ContentLengthListener());
$dispatcher->addSubscriber(new Embex\GoogleListener());
$dispatcher->addSubscriber(new HttpKernel\EventListener\ExceptionListener($errorHandler));
$dispatcher->addSubscriber($listener);
$dispatcher->addSubscriber(new HttpKernel\EventListener\ResponseListener('UTF-8'));
$dispatcher->addSubscriber(new HttpKernel\EventListener\StreamedResponseListener());
$dispatcher->addSubscriber(new Embex\StringResponseListener());

$framework = new Embex\Framework($dispatcher, $matcher, $resolver);
$framework = new HttpCache($framework, new Store(__DIR__.'/../cache'), new Esi(), array('debug' => true));

$framework->handle($request)->send();
