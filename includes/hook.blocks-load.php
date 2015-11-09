<?php
//##copyright##

if (iaView::REQUEST_HTML == $iaView->getRequestType())
{
	$iaWallpost = $iaCore->factoryPlugin('wall', iaCore::FRONT, 'wallpost');

	if ($iaView->blockExists('wall'))
	{
		$array = $iaWallpost->getLatest($iaCore->get('posts_per_load'));
		$iaView->assign('latest_wall_posts', $array);

		$iaView->assign('num_total_wall_posts', $iaDb->foundRows());
	}

	if ('view_member' == $iaView->name())
	{
		$memberInfo = $iaView->getValues('item');
		$array = $iaWallpost->getLatestByMemberId($memberInfo['id'], $iaCore->get('posts_per_load'));
		$iaView->assign('latest_wall_posts', $array);

		$iaView->assign('num_total_wall_posts', $iaDb->foundRows());
	}
}