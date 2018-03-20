<?php
namespace UserEx\MyDrugstore\Core;

use Pimple\Container;
use Symfony\Component\Yaml\Yaml;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Nette\Http\IRequest;
use \Doctrine\ORM\Configuration;
use Doctrine\ORM\Proxy\ProxyFactory;
use UserEx\MyDrugstore\Core\TwigExtensions\RouterTwigExtension;
use UserEx\MyDrugstore\Core\Exceptions\NotFoundException;

/**
 * @author ildar
 */
class Kernel
{
    /**
     * @var string
     */
    protected $container = null;
    
    /**
     * @var string
     */
    protected $configFile = __DIR__ . '/../../resources/config/config.yml';

    /**
     * @var string
     */
    protected $routeFile =  __DIR__ . '/../../resources/config/routes.yml';
    
    /**
     * @var string
     */
    protected $entitiesPath = __DIR__ . '/../Entities/';
    
    /**
     * @var string
     */
    protected $twigTemplatePath = __DIR__ . '/../../resources/views';
    
    /**
     * @var string
     */
    protected $twigCompilationCache = __DIR__ . '/../cache/twig_compilation_cache';
    
    
    /**
     * kernel constructor
     */
    public function __construct() {
        $this->container = new Container();
        
        $this->loadConfig();
        $this->registerRouter();
        $this->registerORMService();
        $this->registerTemplateEngine();
        $this->registerTemplateEngineExtension();
    }
    
    /**
     * Load configuration
     */
    protected function loadConfig()
    {
        $this->container['config'] = Yaml::parseFile($this->configFile);
        $this->container['routes'] = Yaml::parseFile($this->routeFile);
        $this->container['entities_path'] = array($this->entitiesPath);
        
        $this->container['user'] = null;
    }
    
    /**
     * register ORM
     */
    protected function registerORMService()
    {
        $this->container['em'] = function ($c) {
            
            $applicationMode = 'development';
            
            if ($applicationMode == "development") {
                $cache = new \Doctrine\Common\Cache\ArrayCache;
            } else {
                $cache = new \Doctrine\Common\Cache\ApcCache;
            }
            
            $config = new Configuration;
            $config->setMetadataCacheImpl($cache);
            $driverImpl = $config->newDefaultAnnotationDriver($c['entities_path'], false);
            $config->setMetadataDriverImpl($driverImpl);
            $config->setQueryCacheImpl($cache);
            $config->setProxyDir(__DIR__ . '/../Proxies');
            $config->setProxyNamespace('UserEx\MyDrugstore\Proxies');
            $config->setAutoGenerateProxyClasses($applicationMode === 'development');
            
            if ('development' === $applicationMode) {
                $config->setAutoGenerateProxyClasses(ProxyFactory::AUTOGENERATE_EVAL);
            }
            
            $entityManager = EntityManager::create($c['config']['database'], $config);
            
            return $entityManager;
        };
    }
    
    /**
     * register twig
     */
    protected function registerTemplateEngine()
    {
        $this->container['twig'] = function ($c) {
            $loader = new \Twig_Loader_Filesystem($this->twigTemplatePath);
            $twig = new \Twig_Environment($loader, array(
                'cache' => $this->twigCompilationCache,
                'auto_reload' => true,
                'debug' => true,
            ));
            
            $twig->addExtension(new \Twig_Extension_Debug());

            return $twig;
        };
    }
    
    /**
     * register twig extension
     */
    protected function registerTemplateEngineExtension() {
        $this->container['twig']->addExtension(
            new RouterTwigExtension($this->container)    
        );
    }
    
    /**
     * register router
     */
    protected function registerRouter()
    {
        $this->container['router'] = function ($c) {
            return new Router($c);
        };
    }
        
    /**
     * handler http request
     * 
     * @param IRequest $request
     */
    public function handle(IRequest $request)
    {        
        /* @var $router Router */
        $router = $this->container['router'];
        
        $twig = $this->container['twig'];
        $twig->addGlobal('user', $this->container['user']);
        $twig->addGlobal('exposedRoutes', $router->getExposedRoutes());
        
        list($controller, $action) = $router->getController($request);
        
        try {
            if (class_exists($controller) && method_exists($controller, $action)) {
                $controller = new $controller($this->container);    
                $response = $controller->$action($request);
            } else {
                throw new NotFoundException();
            }
        } catch (NotFoundException $e) {
            $response = new Response(
                $this->container['twig']->render('ExceptionTemplates/404_not_found.html.twig', array()),
                Response::S404_NOT_FOUND
            );            
        }
        
        $response->send();
    }
    
    /**
     * @return string
     */
    public function getContainer()
    {
        return $this->container;
    }
}