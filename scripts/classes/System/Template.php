<?php

require Cfg::PATH_VENDORS . '/Smarty-3.1.19/libs/Smarty.class.php';

/**
 * Класс работы с шаблонами
 *
 * @category   System
 * @package    System_Template
 * @version    1.0
 * @since      File available since Release 1.0
 */
class System_Template extends Smarty
{
    public function __construct($flagCaching = false)
    {
        parent::__construct();
        $this->template_dir = Cfg::PATH_TEMPLATES . '/';
        $this->config_dir = Cfg::PATH_TEMPLATES_CONFIG . '/';
        $this->compile_dir = Cfg::PATH_TEMPLATES_COMPILE . '/';
        $this->cache_dir = Cfg::PATH_TEMPLATES_CACHE . '/';
        $this->caching = $flagCaching;
        $this->debugging = false;
        //$this->assign('app_name', 'Guest Book');
    }
}
