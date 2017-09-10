<?php

namespace Tests;

use Headplan\Framework;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpFoundation\Response;
use PHPUnit\Framework\TestCase;

class FrameworkTest extends TestCase
{
    public function testNotFoundHandling()
    {
        $framework = $this->getFrameworkForException(new ResourceNotFoundException());

        $response = $framework->handle(new Request());

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testErrorHandling()
    {
        $framework = $this->getFrameworkForException(new \RuntimeException());

        $response = $framework->handle(new Request());

        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testControllerResponse()
    {
        $matcher = $this->createMock('Symfony\Component\Routing\Matcher\UrlMatcher');
        $matcher
            ->expects($this->once())
            ->method('match')
            ->will($this->returnValue(array(
                '_route' => 'foo',
                'name' => 'Fabien',
                '_controller' => function ($name) {
                    return new Response('Hello '.$name);
                }
            )))
        ;
        $matcher
            ->expects($this->once())
            ->method('getContext')
            ->will($this->returnValue($this->createMock('Symfony\Component\Routing\RequestContext')))
        ;

        $controllerResolver = new ControllerResolver();
        $argumentResolver = new ArgumentResolver();

        $framework = new Framework($matcher, $controllerResolver, $argumentResolver);

        $response = $framework->handle(new Request());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Hello Fabien', $response->getContent());
    }

    private function getFrameworkForException($exception)
    {
        $matcher = $this->createMock('Symfony\Component\Routing\Matcher\UrlMatcher');
        $matcher
            ->expects($this->once())
            ->method('match')
            ->will($this->throwException($exception));
        $matcher
            ->expects($this->once())
            ->method('getContext')
            ->will($this->returnValue($this->createMock('Symfony\Component\Routing\RequestContext')));

        $controllerResolver = new ControllerResolver();
        $argumentResolver = new ArgumentResolver();

        return new Framework($matcher, $controllerResolver, $argumentResolver);
    }
}