<?php

namespace Doppar\TwigBridge\Tests;

use PHPUnit\Framework\TestCase;
use Doppar\TwigBridge\TwigServiceProvider;
use Twig\Environment as TwigEnvironment;
use Twig\Loader\FilesystemLoader;
use Twig\Extension\DebugExtension;
use Twig\Extra\Html\HtmlExtension;
use Twig\Extra\Intl\IntlExtension;

class TwigBridgeSimpleTest extends TestCase
{
    public function testTwigEnvironmentCreation()
    {
        $loader = new FilesystemLoader(__DIR__ . '/resources/views');
        
        $twig = new TwigEnvironment($loader, [
            'cache' => false,
            'debug' => true,
        ]);

        $twig->addExtension(new DebugExtension());
        $twig->addExtension(new IntlExtension());
        $twig->addExtension(new HtmlExtension());

        $this->assertInstanceOf(TwigEnvironment::class, $twig);
    }

    public function testRenderSimpleTemplate()
    {
        $loader = new FilesystemLoader(__DIR__ . '/resources/views');
        $twig = new TwigEnvironment($loader, ['cache' => false]);

        $result = $twig->render('hello.twig', ['name' => 'World']);
        
        $this->assertEquals('<h1>Hello World!</h1>', trim($result));
    }

    public function testRenderComplexTemplate()
    {
        $loader = new FilesystemLoader(__DIR__ . '/resources/views');
        $twig = new TwigEnvironment($loader, ['cache' => false]);

        $twig->addFunction(new \Twig\TwigFunction('dump', function (...$vars) {
            if (empty($vars)) {
                return '';
            }
            
            ob_start();
            foreach ($vars as $var) {
                var_dump($var);
            }
            return ob_get_clean();
        }, ['is_safe' => ['html']]));

        $data = [
            'title' => 'Test Page',
            'content' => 'This is a test content.',
            'items' => [
                ['name' => 'Item 1', 'price' => 10.50],
                ['name' => 'Item 2', 'price' => 25.99],
            ],
            'debug' => false,
        ];

        $result = $twig->render('complex.twig', $data);
        
        $this->assertStringContainsString('<title>Test Page</title>', $result);
        $this->assertStringContainsString('<h1>Test Page</h1>', $result);
        $this->assertStringContainsString('<p>This is a test content.</p>', $result);
        $this->assertStringContainsString('Item 1', $result);
        $this->assertStringContainsString('Item 2', $result);
    }

    public function testRenderWithEmptyItems()
    {
        $loader = new FilesystemLoader(__DIR__ . '/resources/views');
        $twig = new TwigEnvironment($loader, ['cache' => false]);

        $twig->addFunction(new \Twig\TwigFunction('dump', function (...$vars) {
            if (empty($vars)) {
                return '';
            }
            
            ob_start();
            foreach ($vars as $var) {
                var_dump($var);
            }
            return ob_get_clean();
        }, ['is_safe' => ['html']]));

        $data = [
            'title' => 'Empty Test',
            'content' => 'No items here.',
            'debug' => false,
        ];

        $result = $twig->render('complex.twig', $data);
        
        $this->assertStringContainsString('No items found.', $result);
        $this->assertStringNotContainsString('<ul>', $result);
    }

    public function testTwigExtensionsLoaded()
    {
        $loader = new FilesystemLoader(__DIR__ . '/resources/views');
        $twig = new TwigEnvironment($loader, ['cache' => false]);

        $twig->addExtension(new DebugExtension());
        $twig->addExtension(new IntlExtension());
        $twig->addExtension(new HtmlExtension());

        $extensions = $twig->getExtensions();
        
        $this->assertArrayHasKey(DebugExtension::class, $extensions);
        $this->assertArrayHasKey(IntlExtension::class, $extensions);
        $this->assertArrayHasKey(HtmlExtension::class, $extensions);
    }

    public function testServiceProviderPackageName()
    {
        $mockApp = $this->createMock(\Phaseolies\Application::class);
        $provider = new TwigServiceProvider($mockApp);
        
        $reflection = new \ReflectionClass($provider);
        $property = $reflection->getProperty('packageName');
        $property->setAccessible(true);
        
        $this->assertEquals('twig-bridge', $property->getValue($provider));
    }

    public function testServiceProviderBoot()
    {
        $mockApp = $this->createMock(\Phaseolies\Application::class);
        $provider = new TwigServiceProvider($mockApp);
        
        $this->assertNull($provider->boot());
    }
}
