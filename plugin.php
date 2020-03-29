<?php
namespace Xpressengine\Plugins\Rss;

use Route;
use Xpressengine\Plugin\AbstractPlugin;
use Illuminate\Database\Schema\Blueprint;
use Schema;

class Plugin extends AbstractPlugin
{
    public function register()
    {
        app()->singleton(Handler::class, function ($app) {
            $proxyClass = app('xe.interception')->proxy(Handler::class, 'XeRss');
            return new $proxyClass(app('xe.config'));
        });
        app()->alias(Handler::class, 'xe.xe_rss.handler');
    }

    /**
     * 이 메소드는 활성화(activate) 된 플러그인이 부트될 때 항상 실행됩니다.
     *
     * @return void
     */
    public function boot()
    {
        $this->route();
    }

    protected function route()
    {
        app('xe.register')->push(
            'settings/menu',
            'setting.xe_rss',
            [
                'title' => 'RSS',
                'description' => 'RSS management ',
                'display' => true,
                'ordering' => 7000
            ]
        );

        Route::settings(
            $this->getId(),
            function () {
                Route::group(
                    ['namespace' => 'Xpressengine\Plugins\Rss\Controllers'],
                    function () {
                        Route::get(
                            '/',
                            [
                                'as' => 'xe_rss::setting.index',
                                'uses' => 'SettingController@index',
                                'settings_menu' => 'setting.xe_rss',
                            ]
                        );
                        Route::post('/update/config', ['as' => 'xe_rss::setting.update.config', 'uses' => 'SettingController@updateConfig']);
                        Route::post('/update/module/config', ['as' => 'xe_rss::setting.update.module.config', 'uses' => 'SettingController@updateModuleConfig']);
                    }
                );
            }
        );

        Route::fixed(self::getId(), function () {
            Route::get('/{format?}', ['as' => 'xe_rss::index', 'uses' => 'UserController@index']);
        }, ['namespace' => 'Xpressengine\Plugins\Rss\Controllers']);

        Route::get('/{module_url}/xe_rss/{format?}', ['as' => 'xe_rss::module_rss', 'uses' => '\Xpressengine\Plugins\Rss\Controllers\UserController@moduleRss']);
    }

    public function getSettingsURI()
    {
        return route('xe_rss::setting.index');
    }

    /**
     * 플러그인이 활성화될 때 실행할 코드를 여기에 작성한다.
     *
     * @param string|null $installedVersion 현재 XpressEngine에 설치된 플러그인의 버전정보
     *
     * @return void
     */
    public function activate($installedVersion = null)
    {
        // implement code
    }

    /**
     * 플러그인을 설치한다. 플러그인이 설치될 때 실행할 코드를 여기에 작성한다
     *
     * @return void
     */
    public function install()
    {
        $configName = app('xe.xe_rss.handler')->getConfigName();
        $config = app('xe.config')->get($configName);
        if ($config == null) {
            app('xe.config')->set($configName, []);
        }
        $configName = app('xe.xe_rss.handler')->getModuleConfigName();
        $config = app('xe.config')->get($configName);
        if ($config == null) {
            app('xe.config')->set($configName, [
                'id' => '',
                'title' => '',
                'feed_description' => '',
                'feed_publish' => 'private', // all, simple, private
                'include_total_feed' => 'disuse',
            ]);
        }
    }

    /**
     * 해당 플러그인이 설치된 상태라면 true, 설치되어있지 않다면 false를 반환한다.
     * 이 메소드를 구현하지 않았다면 기본적으로 설치된 상태(true)를 반환한다.
     *
     * @return boolean 플러그인의 설치 유무
     */
    public function checkInstalled()
    {
        // implement code

        return parent::checkInstalled();
    }

    /**
     * 플러그인을 업데이트한다.
     *
     * @return void
     */
    public function update()
    {
        // implement code
    }

    /**
     * 해당 플러그인이 최신 상태로 업데이트가 된 상태라면 true, 업데이트가 필요한 상태라면 false를 반환함.
     * 이 메소드를 구현하지 않았다면 기본적으로 최신업데이트 상태임(true)을 반환함.
     *
     * @return boolean 플러그인의 설치 유무,
     */
    public function checkUpdated()
    {
        // implement code

        return parent::checkUpdated();
    }
}
