function showNono($cat='')
{
        $tags = ($cat != '')?'photo':'nonovrac';
        $LINKSDB=new linkdb(false); // Only public link
        $LINKSDB=$LINKSDB->filterTags($tags);
        $to=isset($_GET['to'])?$_GET['to']:'99999999_999999';
        $from=isset($_GET['from'])?$_GET['from']:'00000000_000000';
        $limit=isset($_GET['limit'])?$_GET['limit']:'9999999';
        if (!preg_match('/^\d{4}(\d{2})?(\d{2})?(_\d{2})?(\d{2})?(\d{2})?$/', $from)) { die('Wrong FROM format.'); }
        if (!preg_match('/^\d{4}(\d{2})?(\d{2})?(_\d{2})?(\d{2})?(\d{2})?$/', $to)) { die('Wrong TO format.'); }
        if (!preg_match('/^\d{1,7}$/', $limit)) { die('Wrong LIMIT format.'); }
        if (strcmp($from,$to) > 0) { $temp = $to; $to = $from; $from = $temp; }
        $linkarray = array();
        $tagcloud = array();
        echo '<ul>'."\n";
        foreach($LINKSDB as $link)
        {
                $date=$link['linkdate'];
                if ($from <= $date && $date <= $to)
                {
                        if ($cat == '' || ($cat == 'photo' && !preg_match('/nonovrac/i',$link['tags'])))
                                array_push($linkarray, ''.$link['description'].' [<a href="'.$link['url'].'" target="_blank">'.$date.'</a>]');
                        foreach (explode(' ',$link['tags']) as $t)
                                array_push($tagcloud, $t);
                }
        }

        if ($limit>count($linkarray))
                $limit=count($linkarray);

        $linkarray = array_reverse($linkarray);

        for ($i=0;$i<$limit;$i++)
        {
                $l = $linkarray[isset($_GET['sort'])?$limit-1-$i:$i];
                echo '<li>('.($i+1).') '.preg_replace('/(\d{4})(\d{2})(\d{2})_(\d{2})(\d{2})(\d{2})/','\3/\2/\1 @ \4:\5:\6',$l).'</li>'."\n";
        }

        echo '</ul>'."\n";
        $uniqueTags = array_unique($tagcloud);
        unset($uniqueTags[array_search($tags,$uniqueTags)]);
        echo implode(', ',$uniqueTags);
        exit;
}

if (isset($_SERVER["QUERY_STRING"]) && startswith($_SERVER["QUERY_STRING"],'do=nono')) { showNono(); exit; }
if (isset($_SERVER["QUERY_STRING"]) && startswith($_SERVER["QUERY_STRING"],'do=photo')) { showNono('photo'); exit; }
