<?php

//wedusflare framework

namespace Embex\Tests;

use Embex\Framework;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
 
class FrameworkTest extends \PHPUnit_Framework_TestCase
{
    public function testNotFoundHandling()
    {
        $framework = $this->getFrameworkForException(new ResourceNotFoundException());
 
        $response = $framework->handle(new Request());
 
        $this->assertEquals(404, $response->getStatusCode());
    }
 
    protected function getFrameworkForException($exception)
    {
        $matcher = $this->getMock('Symfony\Component\Routing\Matcher\UrlMatcherInterface');
        $matcher
            ->expects($this->once())
            ->method('match')
            ->will($this->throwException($exception))
        ;
        $resolver = $this->getMock('Symfony\Component\HttpKernel\Controller\ControllerResolverInterface');
 
        return new Framework($matcher, $resolver);
    }
    
    public function testErrorHandling(){
		$framework = $this->getFrameworkForException(new \RuntimeException());
		
		$response = $framework->handle(new Request());
		
		$this->assertEquals(500, $response->getStatusCode());
	}
	
	public function testControllerResponse(){
		$matcher = $this->getMock('Symfony\Component\Routing\Matcher\UrlMatcherInterface');
		$matcher
			->expects($this->once())
			->method('match')
			->will($this->returnValue(array(
				'_route' => 'mbeeex',
				'name' => 'Wedus',
				'_controller' => function ($name){
					return new Response('Hello'.$name);
				}
			)))
		;
		$resolver = new ControllerResolver();
		
		$framework = new Framework($matcher, $resolver);
		
		$response = $framework->handle(new Request());
		
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertContains('HelloWedus', $response->getContent());
	}
}