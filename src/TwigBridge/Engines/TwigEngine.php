<?php

/**
 * Brings Twig to Laravel 4.
 *
 * @author Rob Crowe <hello@vivalacrowe.com>
 * @license MIT
 */

namespace TwigBridge\Engines;

use Illuminate\View\Engines\EngineInterface;
use Twig_Environment;

/**
 * Laravel view engine for Twig.
 */
class TwigEngine implements EngineInterface
{
    /**
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * @var array Global data that is always passed to the template.
     */
    protected $global_data = array();

    /**
     * Create a new instance of the Twig engine.
     *
     * @param Twig_Environment $twig
     * @param array            $global_data
     */
    public function __construct(Twig_Environment $twig, array $global_data = array())
    {
        $this->twig        = $twig;
        $this->global_data = $global_data;
    }

    /**
     * Returns the instance of Twig used to render the template.
     *
     * @return Twig_Environment
     */
    public function getTwig()
    {
        return $this->twig;
    }

    /**
     * Set global data sent to the view.
     *
     * @param array $data Global data.
     * @return void
     */
    public function setGlobalData(array $data)
    {
        $this->global_data = $data;
    }

    /**
     * Get the global data.
     *
     * @return array
     */
    public function getGlobalData()
    {
        return $this->global_data;
    }

    /**
     * Get the data passed to the view. Merges with global.
     *
     * @param array $data View data.
     * @return array
     */
    public function getData(array $data)
    {
        return array_merge($this->getGlobalData(), $data);
    }

    /**
     * Loads the given template.
     *
     * @param mixed $name A template name or an instance of Twig_Template
     *
     * @return \Twig_TemplateInterface A \Twig_TemplateInterface instance
     *
     * @throws \InvalidArgumentException if the template does not exist
     */
    protected function load($name)
    {
        if ($name instanceof \Twig_Template) {
            return $name;
        }

        try {
            return $this->twig->loadTemplate($name);
        } catch (\Twig_Error_Loader $e) {
            throw new \InvalidArgumentException("Error in $name: ". $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get the evaluated contents of the view.
     *
     * @param  string  $path Full file path to Twig template
     * @param  array   $data
     * @param  string  $view Original view passed View::make.
     * @return string
     */
    public function get($path, array $data = array(), $view = null)
    {
        // Render template
        return $this->load($path)->render($this->getData($data));
    }
}
