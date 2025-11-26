<?php

namespace Doppar\TwigBridge;

use Phaseolies\Http\Controllers\Controller;
use Twig\Environment as TwigEnvironment;

class TwigController extends Controller
{
    protected TwigEnvironment $twig;

    public function __construct(TwigEnvironment $twig)
    {
        parent::__construct();
        $this->twig = $twig;
    }

    public function render($name, array $data = [], $returnOnly = false)
    {
        if (is_string($name) && str_ends_with($name, '.twig')) {
            $html = $this->renderWithTwig($name, $data);
        } else {
            $html = parent::render($name, $data, true);
        }

        return $returnOnly ? $html : print($html);
    }

    protected function renderWithTwig(string $name, array $data): string
    {
        return $this->twig->render($name, $data);
    }
}
