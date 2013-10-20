<?php

/**
 * Brings Twig to Laravel.
 *
 * @author Rob Crowe <hello@vivalacrowe.com>
 * @license MIT
 */

namespace TwigBridge\Twig;

use Twig_Template;
use Twig_TemplateInterface;
use App;
use Illuminate\View\View;

/**
 * This class is extended by the generated templates.
 */
abstract class Template extends Twig_Template
{
    /**
     * {@inheritdoc}
     */
    public function display(array $context, array $blocks = array())
    {
        $name = $this->getTemplateName();

        // Deal with view composers
        if ($this->shouldFireEvents($name)) {
            /** @var \Illuminate\View\Environment $env */
            $env  = $context['__env'];
            \View::callCreator($view = new View($env, $env->getEngineResolver()->resolve('twig'), $name, null, $context));
            \View::callComposer($view);
            $context = $view->getData();
        }

        parent::display($context, $blocks);
    }

    /**
     * Determine of the template should fire creator/composer events.
     *
     * @param $name
     * @return bool
     */
    public function shouldFireEvents($name){
        // Only when the first view is loaded, the template name is the full path.
        return file_exists($name);
    }

    /**
     * Returns the attribute value for a given array/object.
     *
     * Allows Eloquent results to work properly.
     */
    protected function getAttribute(
        $object,
        $item,
        array $arguments = array(),
        $type = Twig_TemplateInterface::ANY_CALL,
        $isDefinedTest = false,
        $ignoreStrictCheck = false
    ) {
        // We need to handle accessing attributes/methods on an Eloquent instance differently
        if (is_a($object, 'Illuminate\Database\Eloquent\Model')) {

            if (method_exists($object, $item)) {
                $ret = call_user_func_array(array($object, $item), $arguments);
            } else {

                // Calling getAttributes lets us deal with accessors, mutators & relations
                $ret = $object->getAttribute($item);

                // getAttributes doesn't deal with attributes that aren't part of the models data
                if ($ret === null AND isset($object->$item)) {
                    $ret = $object->$item;
                }
            }

        } else {
            $ret = parent::getAttribute($object, $item, $arguments, $type, $isDefinedTest, $ignoreStrictCheck);
        }

        // We need to handle relations differently when dealing with Eloquent objects
        if (is_a($ret, 'Illuminate\Database\Eloquent\Relations\Relation')) {

            // Grab the value from the relation
            $ret = $object->getAttribute($item);
        }

        if ($ret AND $isDefinedTest) {
            return true;
        }

        return $ret;
    }
}
