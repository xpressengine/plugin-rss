<?php
namespace Xpressengine\Plugins\Rss\Controllers;

use App\Http\Controllers\Controller;
use XePresenter;
use Xpressengine\Document\Models\Document;
use Xpressengine\Http\Request;
use Xpressengine\Plugins\Poll\Models\Poll;
use Carbon\Carbon;
use Xpressengine\Plugins\Rss\Handler;

class UserController extends Controller
{
    public function index(Request $request, Handler $handler, $format = 'rss20')
    {
        // config
        $config = $handler->getConfig();
        $title = $config->get('feed_title');
        $link = url('/');
        $description = $config->get('feed_description');
        $now = Carbon::now();
        $date = $now->format("D, d M Y H:i:s") . ' ' .  $now->getTimezone()->getName();
        $copyright = $config->get('feed_copyright', '');
        $perPage = $config->get('per_page', 15);

        // make module config
        $moduleConfigs = $handler->getModuleConfigList();
        $instanceIds = [];
        $publishByIds = [];
        foreach ($moduleConfigs as $moduleConfig) {
            if ($moduleConfig->get('include_total_feed') != 'use') {
                continue;
            }
            if ($moduleConfig->get('feed_publish') == 'private') {
                continue;
            }
            $id = $moduleConfig->get('id');
            $instanceIds[] = $id;
            $publishByIds[$id] = $moduleConfig->get('feed_publish');
        }

        // setup url
        $urlByIds = [];
        $menuItems = $handler->getMenuItems($instanceIds);
        foreach ($menuItems as $menuItem) {
            $urlByIds[$menuItem->id] = $menuItem->url;
        }

        // is use
        if ($config->get('total_function_use') != 'use') {
            $description = 'The feed function is locked.';
            $instanceIds = [];
        }

        // document 에 tag 매크로 추가
        Document::macro('rss_tag', function() {
            return $this->belongsToMany(\Xpressengine\Tag\Tag::class, 'taggables', 'taggable_id', 'tag_id');
        });

        $documentList = Document::whereIn('instance_id', $instanceIds)
            ->where('status', Document::STATUS_PUBLIC)
            ->where('display', Document::DISPLAY_VISIBLE)
            ->where('published', Document::PUBLISHED_PUBLISHED)
            ->where('approved', Document::APPROVED_APPROVED)
            ->with(['rss_tag'])
            ->orderBy('head', 'desc')->paginate($perPage);

        foreach ($documentList as $document) {
            $document->url = url(
                sprintf('/%s/show/%s', $urlByIds[$document->instance_id], $document->id)
            );
            $document->feed_publish = $publishByIds[$document->instance_id];
            $document->tag_list = $document->rss_tag->pluck(['word'])->toArray();
        }

        $info = [
            'title' => $title,
            'link' => $link,
            'description' => $description,
            'date' => $date,
            'language' => app('xe.translator')->getLocale(),
            'copyright' => $copyright,
            'image' => '',
        ];

        $request->setRequestFormat('xml');
        XePresenter::htmlRenderPartial();

        return XePresenter::make('xe_rss::views.user.' . $format, [
            'info' => $info,
            'config' => $config,
            'documentList' => $documentList,
        ]);
    }

    public function moduleRss(Request $request, Handler $handler, $moduleUrl, $format = 'rss20')
    {
        $menuItem = $handler->getMenuItemByUrl($moduleUrl);

        if ($handler->isSupportModule($menuItem->type) == false) {
            $this->notSupportException();
        }
        $moduleConfig = $handler->getModuleConfig($menuItem->id);

        // config
        $config = $handler->getConfig();
        $title = $moduleConfig->get('title');
        $link = url($moduleUrl);
        $description = $moduleConfig->get('feed_description');
        $now = Carbon::now();
        $date = $now->format("D, d M Y H:i:s") . ' ' .  $now->getTimezone()->getName();

        $copyright = $config->get('feed_copyright');


        $perPage = $config->get('per_page');

        // make module config
        $instanceIds = [];
        $publishByIds = [];
        if ($moduleConfig->getPure('id', '') != '') {
            $id = $moduleConfig->get('id');
            $instanceIds[] = $id;
            $publishByIds[$id] = $moduleConfig->get('feed_publish');
        }

        // setup url
        $urlByIds = [];
        $menuItems = $handler->getMenuItems($instanceIds);
        foreach ($menuItems as $menuItem) {
            $urlByIds[$menuItem->id] = $menuItem->url;
        }

        // is use
        if ($moduleConfig->get('feed_publish') == 'private') {
            $description = 'The feed function is locked.';
            $instanceIds = [];
        }

        // document 에 tag 매크로 추가
        Document::macro('rss_tag', function() {
            return $this->belongsToMany(\Xpressengine\Tag\Tag::class, 'taggables', 'taggable_id', 'tag_id');
        });

        $documentList = Document::whereIn('instance_id', $instanceIds)
            ->where('status', Document::STATUS_PUBLIC)
            ->where('display', Document::DISPLAY_VISIBLE)
            ->where('published', Document::PUBLISHED_PUBLISHED)
            ->where('approved', Document::APPROVED_APPROVED)
            ->with(['rss_tag'])
            ->orderBy('head', 'desc')->paginate($perPage);

        foreach ($documentList as $document) {
            $document->url = url(
                sprintf('/%s/show/%s', $urlByIds[$document->instance_id], $document->id)
            );
            $document->feed_publish = $publishByIds[$document->instance_id];
            $document->tag_list = $document->rss_tag->pluck(['word'])->toArray();
        }

        $info = [
            'title' => $title,
            'link' => $link,
            'description' => $description,
            'date' => $date,
            'language' => app('xe.translator')->getLocale(),
            'copyright' => $copyright,
            'image' => '',
        ];

        if ($config->get('feed_image_path') != '') {
            $info['image'] = url($config->get('feed_image_path'));
        }

        $request->setRequestFormat('xml');
        XePresenter::htmlRenderPartial();

        return XePresenter::make('xe_rss::views.user.' . $format, [
            'info' => $info,
            'config' => $config,
            'documentList' => $documentList,
        ]);
    }

    protected function notSupportException()
    {
        $exception = new \Xpressengine\Support\Exceptions\HttpXpressengineException(
            [], 500
        );
        $exception->setMessage('xe_rss::rssNorSupport');
        throw $exception;
    }
}
