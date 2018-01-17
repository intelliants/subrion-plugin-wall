<?php
/******************************************************************************
 *
 * Subrion - open source content management system
 * Copyright (C) 2017 Intelliants, LLC <https://intelliants.com>
 *
 * This file is part of Subrion.
 *
 * Subrion is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Subrion is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Subrion. If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * @link https://subrion.org/
 *
 ******************************************************************************/

if (iaView::REQUEST_HTML == $iaView->getRequestType()) {
    $iaWallpost = $iaCore->factoryPlugin('wall', iaCore::FRONT, 'wallpost');

    if ($iaView->blockExists('wall')) {
        $array = $iaWallpost->getLatest($iaCore->get('posts_per_load'));
        $iaView->assign('latest_wall_posts', $array);

        $iaView->assign('num_total_wall_posts', $iaDb->foundRows());
    }

    if ('view_member' == $iaView->name()) {
        $memberInfo = $iaView->getValues('item');
        $array = $iaWallpost->getLatestByMemberId($memberInfo['id'], $iaCore->get('posts_per_load'));
        $iaView->assign('latest_wall_posts', $array);

        $iaView->assign('num_total_wall_posts', $iaDb->foundRows());
    }
}