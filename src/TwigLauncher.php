<?php

namespace Doppar\TwigBridge;

use Twig\TwigFunction;
use Twig\Loader\FilesystemLoader;
use Twig\Extension\DebugExtension;
use Twig\Extra\Html\HtmlExtension;
use Twig\Extra\Intl\IntlExtension;
use Twig\Environment as TwigEnvironment;
use Phaseolies\Http\Controllers\Controller;
use Phaseolies\Launchers\GhostableLauncher;
use Phaseolies\Launchers\PackageServiceLauncher;

class TwigLauncher extends PackageServiceLauncher implements GhostableLauncher
{
    /**
     * The package name.
     *
     * @var string
     */
    protected string $packageName = 'twig-bridge';

    /**
     * Configure the package.
     */
    protected function configurePackage()
    {
        $this->app->singleton(TwigEnvironment::class, function ($app) {
            $loader = new FilesystemLoader();

            $loader->addPath(base_path('templates/views'));

            $twig = new TwigEnvironment($loader, [
                'cache' => base_path('storage/framework/twig'),
                'debug' => env('APP_DEBUG') === 'true',
            ]);

            $twig->addExtension(new DebugExtension());
            $twig->addExtension(new IntlExtension());
            $twig->addExtension(new HtmlExtension());

            $twig->addFunction(new TwigFunction('dump', function (...$vars) {
                if (empty($vars)) {
                    return '';
                }

                ob_start();

                foreach ($vars as $var) {
                    dump($var);
                }

                return ob_get_clean();
            }, ['is_safe' => ['html']]));

            return $twig;
        });

        $this->app->bind(Controller::class, function ($app) {
            return new TwigController($app->make(TwigEnvironment::class));
        });
    }

    public function launch()
    {
    }

    /**
     * Get the services that should ghost-load this provider.
     *
     * @return array<int, string>
     */
    public function ghosts(): array
    {
        return [
            TwigEnvironment::class,
            Controller::class,
        ];
    }
}
