<?php
namespace TwigBridge\Twig\Loader;

use Illuminate\Filesystem\Filesystem;
use Illuminate\View\ViewFinderInterface;


class ViewfinderLoader extends PathLoader
{

    protected $finder;
    protected $extension;
    protected $cache = array();

    public function __construct(ViewFinderInterface $finder, $extension = 'twig'){
        $this->finder = $finder;
        $this->extension = $extension;
    }

    protected function findTemplate($name){

        if(isset($this->cache[$name])){
            return $this->cache[$name];
        }else{
            $view = $name;
            $ext = ".".$this->extension;
            $len = strlen($ext);
            if(substr($view, -$len) == $ext){
                $view = substr($view, 0, -$len);
            }
            return $this->cache[$name] = $this->finder->find($view);
        }
    }

}