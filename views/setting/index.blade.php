@section('page_title')
    <h2>{{xe_trans('xe_rss::rssSetup')}}</h2>
@stop

<style>
    .__xe_rss_section_module table td label:not(:first-child) {margin-left:10px;}
</style>

<div class="container-fluid container-fluid--part site-manager">
    <div class="row">
        <div class="col-sm-12">
            <div class="panel-group">
                <div class="panel">
                    <div class="panel-heading">
                        <div class="pull-left">
                            <h3 class="panel-title">{{xe_trans('xe_rss::baseConfig')}}</h3>
                        </div>
                    </div>
                    <div id="collapseOne" class="panel-collapse collapse in">
                        <div class="panel-body">
                            <div class="__xe_rss_section_default">
                                <form method="post" action="{{ route('xe_rss::setting.update.config') }}" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <ul class="list-unstyled">
                                        <li>
                                            <label>
                                                {{ xe_trans('xe_rss::URL') }}
                                            </label>
                                            <a href="{{route('xe_rss::index')}}" target="_blank">{{route('xe_rss::index')}}</a>
                                        </li>
                                        <li>
                                            <label>
                                                {{ xe_trans('xe_rss::rssTotalFeedFunctionOn') }}
                                            </label>
                                            {!! uio('formSelect', [
                                                'name'=> 'total_function_use',
                                                'value'=> $baseConfig['total_function_use'],
                                                'options' => [
                                                    ['text' => xe_trans('xe::use'), 'value' => 'use'],
                                                    ['text' => xe_trans('xe::disuse'), 'value' => 'disuse'],
                                                ],
                                                'description' => ''
                                            ]) !!}
                                        </li>
                                        <li>
                                            <label>
                                                {{ xe_trans('xe_rss::feedTitle') }}
                                            </label>
                                            {!! uio('formText', ['name'=> 'feed_title', 'value'=> $baseConfig['feed_title'], 'type'=>'text', 'description'=>''])  !!}
                                        </li>
                                        <li>
                                            <label>
                                                {{ xe_trans('xe_rss::feedDescription') }}
                                            </label>
                                            {!! uio('formTextarea', ['name'=> 'feed_description', 'value'=> $baseConfig['feed_description'], 'description'=>xe_trans('xe_rss::feedDescriptionDescription')])  !!}
                                        </li>
                                        <li>
                                            <label>
                                                {{ xe_trans('xe_rss::feedImage') }}
                                            </label>
                                            {!! uio('formImage', ['name'=> 'feed_image', 'value'=> $baseConfig['feed_image'], 'image' => $baseConfig['feed_image_path'], 'description'=>''])  !!}
                                        </li>
                                        <li>
                                            <label>
                                                {{ xe_trans('xe_rss::copyright') }}
                                            </label>
                                            {!! uio('formText', ['name'=> 'feed_copyright', 'value'=> $baseConfig['feed_copyright'], 'type'=>'text', 'description'=>''])  !!}
                                        </li>
                                        <li>
                                            <label>
                                                {{ xe_trans('xe_rss::feedCountPerPage') }}
                                            </label>
                                            {!! uio('formText', ['name'=> 'per_page', 'value'=> $baseConfig['per_page'], 'type'=>'number', 'description'=>''])  !!}
                                        </li>
                                    </ul>
                                    <button type="submit" class="btn btn-primary">{{xe_trans('xe::save')}}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-heading">
                        <div class="pull-left">
                            <h3 class="panel-title">{{xe_trans('xe_rss::moduleFeedPublishSetup')}}</h3>
                            <p>{{xe_trans('xe_rss::moduleFeedPublishSetupDescription')}}</p>
                        </div>
                    </div>
                    <div id="collapseOne" class="panel-collapse collapse in">
                        <div class="panel-body">
                            <div class="__xe_rss_section_module">
                                <form method="post" action="{{ route('xe_rss::setting.update.module.config') }}" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th scope="col">{{xe_trans('xe_rss::moduleName')}}</th>
                                                <th scope="col">{{xe_trans('xe_rss::feedDescription')}}</th>
                                                <th scope="col">{{xe_trans('xe_rss::feedPublish')}}</th>
                                                <th scope="col">{{xe_trans('xe_rss::includeTotalFeed')}}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($instanceItems as $item)
                                                <tr>
                                                    <td>
                                                        <input type="hidden" name="id[]" value="{{$item['menu_item']->id}}" />
                                                        <input type="hidden" name="title_{{$item['menu_item']->id}}" value="{{$item['menu_item']->title}}" />
                                                        <a href="{{route('xe_rss::module_rss', ['module_url' => $item['menu_item']->url])}}" target="_blank">{{ xe_trans($item['menu_item']->title) }}</a>
                                                    </td>
                                                    <td>{{ $item['config']->get('feed_description')}}</td>
                                                    <td>
                                                        <label>
                                                            <input type="radio" name="feed_publish_{{$item['menu_item']->id}}" value="all" @if($item['config']->get('feed_publish') == 'all') checked="checked" @endif />
                                                            {{xe_trans('xe_rss::publishAllContent')}}
                                                        </label>
                                                        <label>
                                                            <input type="radio" name="feed_publish_{{$item['menu_item']->id}}" value="simple" @if($item['config']->get('feed_publish') == 'simple') checked="checked" @endif />
                                                            {{xe_trans('xe_rss::publishSimpleContent')}}
                                                        </label>
                                                        <label>
                                                            <input type="radio" name="feed_publish_{{$item['menu_item']->id}}" value="private" @if($item['config']->get('feed_publish') == 'private') checked="checked" @endif />
                                                            {{xe_trans('xe_rss::NotPublish')}}
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label>
                                                            <input type="radio" name="include_total_feed_{{$item['menu_item']->id}}" value="use" @if($item['config']->get('include_total_feed') == 'use') checked="checked" @endif />
                                                            {{xe_trans('xe::use')}}
                                                        </label>
                                                        <label>
                                                            <input type="radio" name="include_total_feed_{{$item['menu_item']->id}}" value="disuse" @if($item['config']->get('include_total_feed') == 'disuse') checked="checked" @endif  />
                                                            {{xe_trans('xe::disuse')}}
                                                        </label>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <button type="submit" class="btn btn-primary">{{xe_trans('xe::save')}}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
