<?php
// wedusflare framework

require_once __DIR__.'/../vendor/autoload.php';
 
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection;
use Symfony\Component\DependencyInjection\Reference;

$sc = include __DIR__.'/../src/container.php';
$sc->setParameter('routes', include __DIR__.'/../src/app.php');

$sc->register('listener.string_response', 'Embex\StringResponseListener');
$sc->getDefinition('dispatcher')
	->addMethodCall('addSubscriber', array(new Reference('listener.string_response')))
;

$request = Request::createFromGlobals();

$response = $sc->get('framework')->handle($request);

$response->send();
