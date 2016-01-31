<?php

/**
 * Класс конфигурации проекта
 *
 * @category
 * @package    _Cfg
 * @version    1.0
 * @since      File available since Release 1.0
 */
class Config
{
    const FILE_SYSTEM_SUPERUSER = 'root';
    const FILE_SYSTEM_USERNAME = 'user';

    const PATH_CONFIG = '/config';
    const PATH_SCRIPTS_CLASSES = '/scripts/classes';
    const PATH_TEMPLATES = '/templates';
    const PATH_TEMPLATES_CONFIG = '/templates_config';
    const PATH_TEMPLATES_COMPILE = '/templates_compile';
    const PATH_TEMPLATES_CACHE = '/templates_cache';
    const PATH_LOG = '/log';
    const PATH_WWW = '/www';
    const PATH_STATIC_HTML = '/static/html';
    const PATH_STATIC_IMG = '/static/img';
    const PATH_STATIC_PICS = '/static/pics';
    const PATH_VENDORS = '/vendors';

    const URL_STATIC_HTML = 'http://example.com/static/html';
    const URL_STATIC_CSS = 'http://example.com/static/css';
    const URL_STATIC_JS = 'http://example.com/static/js';
    const URL_STATIC_IMG = 'http://example.com/static/img';
    const URL_STATIC_PICS = 'http://example.com/static/pics';
    const URL_STATIC_PHOTOS = 'http://example.com/static/photos';
    const URL_STATIC_FLASH = 'http://example.com/static/flash';
    const URL_STATIC_FILES = 'http://example.com/static/files';
    const URL_STATIC_THUMBS = 'http://example.com/static/thumbs';

    const DEBUG = false;

    public $regionId;

    public $userIsAdmin;

    public function __construct($regionId, $userId = false)
    {
    }

    public function getConstantUrlStaticPics()
    {
        return self::URL_STATIC_PICS;
    }

    public function getConstantPathStaticPics()
    {
        return self::PATH_STATIC_PICS;
    }

    public function getConstantPathStaticImg()
    {
        return self::PATH_STATIC_IMG;
    }
}
