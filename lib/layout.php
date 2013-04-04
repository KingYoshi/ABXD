<?php
function gfxnumber($num)
{
	return $num;
	// 0123456789/NA-
	
	$sign = '';
	if ($num < 0)
	{
		$sign = '<span class="gfxnumber" style="background-position:-104px 0px;"></span>';
		$num = -$num;
	}
	
	$out = '';
	while ($num > 0)
	{
		$out = '<span class="gfxnumber" style="background-position:-'.(8*($num%10)).'px 0px;"></span>'.$out;
		$num = floor($num / 10);
	}
	
	return '<span style="white-space:nowrap;">'.$sign.$out.'</span>';
}

function makeLinks($links)
{
	global $layout_links;
	$layout_links = $links;
}

function makeForumCrumbs($crumbs, $forum)
{
	while(true)
	{
		$crumbs->addStart(new PipeMenuLinkEntry($forum['title'], "forum", $forum["id"]));
		if($forum["catid"] >= 0) break;
		$forum = Fetch(Query("SELECT * from {forums} WHERE id={0}", -$forum["catid"]));
	}
}

function makeBreadcrumbs($path)
{
	global $layout_crumbs;
	$path->addStart(new PipeMenuLinkEntry(Settings::get("breadcrumbsMainName"), "board"));
	$path->setClass("breadcrumbs");
	$bucket = "breadcrumbs"; include("lib/pluginloader.php");
	$layout_crumbs = $path;
	
	/*
	if(count($path) != 0)
	{
		$pathPrefix = array(Settings::get("breadcrumbsMainName") => actionLink(0));
		$pathPostfix = array(); //Not sure how this could be used, but...

		$bucket = "breadcrumbs"; include("lib/pluginloader.php");

		$path = $pathPrefix + $path + $pathPostfix;
	}

	$first = true;

	$crumbs = "";
	foreach($path as $text=>$link)
	{
		if(is_array($link))
		{
			$dalink = $text;
			$tags = $link[1];
			$text = $link[0];
			$link = $dalink;
		}
		else
			$tags = "";

		$link = str_replace("&","&amp;",$link);

		if(!$first)
			$crumbs .= " &raquo; ";
		$first = false;

		if(!$tags)
			$crumbs .= "<a href=\"".$link."\">".$text."</a>";
		else if (Settings::get("tagsDirection") === 'Left')
			$crumbs .= $tags." <a href=\"".$link."\">".$text."</a>";
		else
			$crumbs .= "<a href=\"".$link."\">".$text."</a> ".$tags;
	}

	if($links)
		$links = "<ul class=\"pipemenu smallFonts\">
			$links
		</ul>";

	$layout_crumbs = "
<div class=\"margin\">
	<div style=\"float: right;\">
		$links
	</div>
	$crumbs&nbsp;
</div>";*/
}

function makeForumList($fieldname, $selectedID)
{
	global $fid, $loguser;

	$pl = $loguser['powerlevel'];
	if($pl < 0) $pl = 0;

	$lastCatID = -1;
	$rFora = Query("	SELECT
							f.id, f.title, f.catid,
							c.name cname
						FROM
							{forums} f
							LEFT JOIN {categories} c ON c.id=f.catid
						WHERE f.minpower<={0}".(($pl < 1) ? " AND f.hidden=0" : '')."
						ORDER BY c.corder, c.id, f.forder, f.id", $pl);

	$theList = "";
	$optgroup = "";
	while($forum = Fetch($rFora))
	{
		if($forum['catid'] != $lastCatID)
		{
			$lastCatID = $forum['catid'];
			$theList .= format(
"
			{0}
			<optgroup label=\"{1}\">
", $optgroup, htmlspecialchars($forum['cname']));
			$optgroup = "</optgroup>";
		}

		$theList .= format(
"
				<option value=\"{0}\"{2}>{1}</option>
", $forum['id'], htmlspecialchars($forum['title']), ($forum['id'] == $selectedID ? " selected=\"selected\"" : ""));
	}

	return "<select id=\"$fieldname\" name=\"$fieldname\">$theList</select>";
}

?>
