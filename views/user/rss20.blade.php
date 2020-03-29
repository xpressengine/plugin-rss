{!! '<?xml version="1.0" encoding="UTF-8" ?>' !!}<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:slash="http://purl.org/rss/1.0/modules/slash/">
    <channel>
        <title><![CDATA[{{xe_trans($info['title'])}}]]></title>
        <link>{{$info['link']}}</link>
        <description><![CDATA[{{$info['description']}}]]></description>
        <language>{{$info['language']}}</language>
        <pubDate>{{$info['date']}}</pubDate>
        <lastBuildDate>{{$info['date']}}</lastBuildDate>
        <generator>XpressEngine3</generator>
        @if($info[ 'copyright'] != '')
        <copyright><![CDATA[{{$info['copyright']}}]]></copyright>
        @endif
        @if($info['image'] != '')
        <image>
            <url>{{$info['image']}}</url>
            <title><![CDATA[{{xe_trans($info['title'])}}]]></title>
            <link>{{$info['link']}}</link>
        </image>
        @endif
    @foreach($documentList as $document)
        <item>
            <title><![CDATA[{{str_replace('\'', '&apos;',htmlspecialchars($document->title, ENT_COMPAT | ENT_HTML401, 'UTF-8', false))}}]]></title>
            <dc:creator><![CDATA[{{str_replace('\'', '&apos;',$document->writer)}}]]></dc:creator>
            <link>{{$document->url}}</link>
            <guid isPermaLink="true">{{$document->url}}</guid>
            <comments>{{$document->url}}#comment</comments>
            @if($document->feed_publish == 'all')
            <description><![CDATA[{{$document->content}}]]></description>
            @elseif($document->feed_publish == 'simple')
            <description><![CDATA[{{mb_substr($document->pure_content, 0, 100)}}]]></description>
            @endif
            <pubDate>{{$document->created_at}}</pubDate>
            @foreach($document->tag_list as $tag)
            <category><![CDATA[{{str_replace('\'', '&apos;', htmlspecialchars($tag, ENT_COMPAT | ENT_HTML401, 'UTF-8', false))}}]]></category>
            @endforeach
            @if($document->comment_count>0)
            <slash:comments>{{$document->comment_count}}</slash:comments>
            @endif
        </item>
        @endforeach
    </channel>
</rss>
