<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Yosymfony\Spress;
 
use Silex\Provider\TwigServiceProvider;
use Yosymfony\Silex\ConfigServiceProvider\ConfigServiceProvider;
use Yosymfony\Spress\ContentLocator\ContentLocator;
use Yosymfony\Spress\ContentManager\ContentManager;
use Yosymfony\Spress\ContentManager\Renderizer;
use Yosymfony\Spress\Operation\NewOperation;

/**
 * Spress Application
 * 
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class Application extends \Silex\Application
{
    const VERSION = "0.1.0";
    
    public function __construct()
    {
        parent::__construct();
        
        $spressPath = realpath(dirname(__FILE__) . '/../../../');
        
        // Paths and filenames standard
        $this['spress.paths'] = array(
            'root'          => $spressPath,
            'config'        => $spressPath  . '/app/config',
            'config.file'   => 'config.yml',
            'templates'     => $spressPath  . '/app/templates',
            'web'           => $spressPath  . '/web',
            'web.index'     => $spressPath  . '/web/index.php',
        );
        $this['spress.version'] = self::VERSION;
        
        $this->register(new ConfigServiceProvider(array($this['spress.paths']['config'])));
        
        $this['spress.config'] = $this->share(function($app){
            return new Configuration($app['configuration'], $app['spress.paths'], $app['spress.version']);
        });
        
        $this['spress.content_locator'] = $this->share(function($app){
            return new ContentLocator($app['spress.config']);
        });
        
        $this['spress.markdown'] = $this->share(function(){
            return new MarkdownWrapper();
        });
        
        $this['spress.twig_factory'] = $this->share(function(){
            return new TwigFactory();
        });
        
        $this['spress.cms.renderizer'] = $this->share(function($app){
            return new Renderizer(
                $app['spress.twig_factory'],
                $app['spress.content_locator'],
                $app['spress.config']);
        });
        
        $this['spress.cms'] = $this->share(function($app){
            return new ContentManager(
                $app['spress.cms.renderizer'], 
                $app['spress.markdown'], 
                $app['spress.config'], 
                $app['spress.content_locator']);
        });
        
        $this['spress.operation.new'] = $this->share(function($app){
            return new NewOperation($app['spress.paths']['templates']);
        });
    }
    
    /**
     * Parse a site
     * 
     * @param string $localConfigPath Path of the local configuration
     * @param string $timezone Set the timezone
     * @param bool $drafts Include draft
     * @param bool $safe
     * 
     * @return array Key-value result
     */
    public function parse($localConfigPath = null, $timezone = null, $drafts = null, $safe = null)
    {
        $this['spress.config']->loadLocal($localConfigPath);
        
        if(null !== $drafts && is_bool($drafts))
        {
            $this['spress.config']->getRepository()->set('drafts', $drafts);
        }
        
        if(null !== $timezone && is_string($timezone))
        {
            $this['spress.config']->getRepository()->set('timezone', $timezone);
        }
        
        if(null !== $safe && is_bool($safe))
        {
            $this['spress.config']->getRepository()->set('safe', $safe);
        }
        
        return $this['spress.cms']->processSite();
    }
}