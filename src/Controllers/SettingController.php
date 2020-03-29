<?php
namespace Xpressengine\Plugins\Rss\Controllers;

use App\Http\Controllers\Controller;
use XePresenter;
use Xpressengine\Http\Request;
use Xpressengine\Plugins\Poll\Models\Poll;
use Carbon\Carbon;
use Xpressengine\Plugins\Rss\Handler;
use XeStorage;
use XeMedia;

class SettingController extends Controller
{
    public function index(Handler $handler)
    {
        $config = $handler->getConfig();
        $baseConfig = $config->all();

        $moduleConfig = $handler->getModuleConfig();
        $moduleConfigs = $handler->getModuleConfigList();
        $moduleConfigItems = [];
        foreach ($moduleConfigs as $moduleConfigItem) {
            $moduleConfigItems[$moduleConfigItem->get('id')] = $moduleConfigItem;
        }

        $instanceGroup = $handler->getModuleList();
        $instanceItems = [];
        foreach ($instanceGroup as $type => $instances) {
            foreach ($instances as $menuItem) {
                $instanceItems[$menuItem->id] = [
                    'menu_item' => $menuItem,
                    'config' => isset($moduleConfigItems[$menuItem->id]) ? $moduleConfigItems[$menuItem->id] : $moduleConfig,
                ];
            }
        }

        return XePresenter::make('xe_rss::views.setting.index', [
            'config' => $config,
            'baseConfig' => $baseConfig,
            'instanceItems' => $instanceItems,
        ]);
    }

    public function updateConfig(Request $request, Handler $handler)
    {
        $config = $handler->getConfig();

        $params = $request->except(['_token', 'feed_image']);
        if ($request->file('feed_image') !== null) {
            $file = XeStorage::find($config->get('feed_image_id'));
            if ($file != null) {
                XeStorage::delete($file);
            }

            $file = XeStorage::upload($request->file('feed_image'), 'public/seo');
            $params['feed_image'] = $file->getPathname();
            $params['feed_image_id'] = $file->id;
        } elseif ($request->get('feed_image') == '__delete_file__') {
            $file = XeStorage::find($config->get('feed_image_id'));
            if ($file != null) {
                XeStorage::delete($file);
            }
            $params['feed_image'] = '';
            $params['feed_image_id'] = '';
        }

        $handler->setConfig($params);

        return redirect()->route('xe_rss::setting.index');
    }

    public function updateModuleConfig(Request $request, Handler $handler)
    {

        $ids = $request->get('id');
        foreach ($ids as $id) {
            $params = [
                'id' => $id,
                'title' => $request->get('title_' . $id),
                'feed_publish' => $request->get('feed_publish_' . $id),
                'include_total_feed' => $request->get('include_total_feed_' . $id),
            ];
            $handler->updateModuleConfig($id, $params);
        }

        return redirect()->route('xe_rss::setting.index');
    }
}
