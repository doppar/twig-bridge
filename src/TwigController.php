<?php

namespace Doppar\TwigBridge;

use Phaseolies\Http\Controllers\Controller;
use Twig\Environment as TwigEnvironment;

class TwigController extends Controller
{
    /**
     * The Twig templating engine instance.
     *
     * @var TwigEnvironment
     */
    protected TwigEnvironment $twig;

    /**
     * Inject the Twig environment and call the parent controller constructor.
     *
     * @param TwigEnvironment $twig
     */
    public function __construct(TwigEnvironment $twig)
    {
        parent::__construct();
        $this->twig = $twig;
    }

    /**
     * Render a view using Twig or the default controller method.
     *
     * @param string $name
     * @param array $data
     * @param bool $returnOnly
     * @return string
     */
    public function render($name, array $data = [], $returnOnly = false)
    {
        if (is_string($name) && str_ends_with($name, '.twig')) {
            $html = $this->renderWithTwig($name, $data);
        } else {
            $html = parent::render($name, $data, true);
        }

        return $returnOnly ? $html : print($html);
    }

    /**
     * Render a view using Twig.
     *
     * @param string $name
     * @param array $data
     * @return string
     */
    protected function renderWithTwig(string $name, array $data): string
    {
        return $this->twig->render($name, $data);
    }
}
