<?php
//##copyright##

$iaWallpost = $iaCore->factoryPlugin('wall', iaCore::ADMIN, 'wallpost');

$iaDb->setTable('wall_posts');

if ($iaView->getRequestType() == iaView::REQUEST_JSON)
{
	switch ($pageAction)
	{
		case iaCore::ACTION_READ:
			switch ($_GET['get'])
			{
				default:
					$params = array();
					if (isset($_GET['text']) && $_GET['text'])
					{
						$stmt = '(`body` LIKE :text)';
						$iaDb->bind($stmt, array('text' => '%' . $_GET['text'] . '%'));

						$params[] = $stmt;
					}

					$output = $iaWallpost->gridRead($_GET,
						array('body', 'member_id', 'date', 'status'),
						array('status' => 'equal'),
						$params
					);
			}

			break;

		case iaCore::ACTION_EDIT:
			$output = $iaWallpost->gridUpdate($_POST);

			break;

		case iaCore::ACTION_DELETE:
			$output = $iaWallpost->gridDelete($_POST);
	}

	$iaView->assign($output);
}

if ($iaView->getRequestType() == iaView::REQUEST_HTML)
{
	if (iaCore::ACTION_READ == $pageAction)
	{
		$iaView->grid('_IA_URL_plugins/wall/js/admin/posts');
	}
}

$iaDb->resetTable();