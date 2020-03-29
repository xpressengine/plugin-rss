<?php
namespace Xpressengine\Plugins\Rss;

use Xpressengine\Config\ConfigManager;
use Xpressengine\Menu\Models\MenuItem;
use Xpressengine\Menu\ModuleHandler;

class Handler
{
    protected $configManager ;

    protected $configName = 'xe_rss';

    protected $configModuleName = 'xe_rss_modules';

    public function __construct(ConfigManager $configManager)
    {
        $this->configManager = $configManager;
    }

    public function getConfigName()
    {
        return $this->configName;
    }

    public function getModuleConfigName($id = null)
    {
        $configName = $this->configModuleName;
        if ($id != null) {
            $configName = sprintf('%s.%s', $this->getModuleConfigName(), $id);
        }

        return $configName;
    }

    public function getConfig()
    {
        $config = $this->configManager->get($this->getConfigName());

        if ($config->get('total_function_use') != 'use') {
            $config->set('total_function_use', 'disuse');
        }
        if ($config->get('feed_title') == null) {
            $config->set('feed_title', '');
        }
        if ($config->get('feed_description') == null) {
            $config->set('feed_description', '');
        }
        if ($config->get('feed_image') == null) {
            $config->set('feed_image', '');
            $config->set('feed_image_path', '');
        } else {
            $config->set('feed_image_path', '/storage/app/'.$config->get('feed_image'));
        }
        if ($config->get('feed_copyright') == null) {
            $config->set('feed_copyright', '');
        }
        if ($config->get('per_page') == null) {
            $config->set('per_page', '15');
        }

        return $config;
    }

    public function setConfig($args)
    {
        $config = $this->configManager->get($this->getConfigName());
        foreach ($args as $key => $value) {
            $config->set($key, $value);
        }
        $this->configManager->modify($config);
    }

    public function getModuleConfig($id = null)
    {
        $config = $this->configManager->get($this->getModuleConfigName($id));
        return $config;
    }



    public function getModuleTypeName()
    {
        // name => config instance name
        $names = [
            'board' => 'module/board@board',
        ];
        return $names;
    }

    public function isSupportModule($moduleType)
    {
        $moduleName =  full_module_id($moduleType);
        $names = $this->getModuleTypeName();
        return in_array($moduleName, $names);
    }

    public function getModuleList()
    {
        $names = $this->getModuleTypeName();

        $moduleGroup = [];
        foreach ($names as $type => $moduleName) {
            $menuItems = MenuItem::where('type', short_module_id($moduleName))->orderBy('ordering')->get();
            $modules = [];
            foreach ($menuItems as $menuItem) {
                $modules[] = $menuItem;
            }
            $moduleGroup[$type] = $modules;
        }

        return $moduleGroup;
    }

    public function getModuleConfigList()
    {
        $config = $this->configManager->get($this->getModuleConfigName());
        $configs = $this->configManager->children($config);
        return $configs;
    }

    public function updateModuleConfig($id, $args)
    {
        $config = $this->getModuleConfig($id);
        if ($config == null) {
            $config = $this->configManager->set($this->getModuleConfigName($id), []);
        }
        foreach ($args as $key => $value) {
            $config->set($key, $value);
        }
        $this->configManager->modify($config);
    }

    public function getMenuItemByUrl($url)
    {
        $item = MenuItem::where('url', $url)->first();
        return $item;
    }

    public function getMenuItems($ids)
    {
        $items = MenuItem::whereIn('id', $ids)->get();
        return $items;
    }
}
