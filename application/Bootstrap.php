<?php 
class Bootstrap extends Yaf_Bootstrap_Abstract {

    private $_config;

    public function _initBootstrap(){
        $this->_config = Yaf_Application::app()->getConfig();
        //Yaf_Register 对象注册表(或称对象仓库)是一个用于在整个应用空间(application space)内存储对象和值的容器
        $db_conf = new Yaf_Config_Ini(APP_PATH . '/conf/db.ini');
        Yaf_Registry::set('db', $db_conf->toArray());
    }

    public function __initAutoload(Yaf_Dispacther $dispacther)
    {
        //开启，yaf自动加载失败时会继续运行其他自动加载器
        ini_set('yaf.use_spl_autoload', true);

        spl_autoload_register(function($class){
            if ($class) {
                $file = str_replace('\\', '/', __DIR__ . '/' . $class);
                $file = $file . 'php';
                if (file_exists($file)) {
                    Yaf_Loader::import($file);
                }
            }
        });
    }

    public function _initRoutes(){
        $route_conf = new Yaf_Config_Ini(APP_PATH . '/conf/route.ini');
        $router = Yaf_Dispatcher::getInstance()->getRouter();
        $router->addConfig($route_conf->routes);
    }
}
