<?php
//wedusflare framework

namespace Embex;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;

class Framework{
	protected $matcher;
	protected $resolver;
	
	public function __construct(UrlMatcherInterface $matcher, ControllerResolverInterface $resolver){
		$this->matcher = $matcher;
		$this->resolver = $resolver;
	}
	
	public function handle(Request $request){
		try{
			$request->attributes->add($this->matcher->match($request->getPathInfo()));
			
			$controller = $this->resolver->getController($request);
			$arguments = $this->resolver->getArguments($request, $controller);
			
			return call_user_func_array($controller, $arguments);
		} catch (ResourceNotFoundException $e){
			return new Response('Mbeeex, Page Not Found', 404);
		} catch (\Exception $e){
			return new Response('Mbeeex, An error occurred', 500);
		}
	}
}
