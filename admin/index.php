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

$iaWallpost = $iaCore->factoryPlugin('wall', iaCore::ADMIN, 'wallpost');

$iaDb->setTable('wall_posts');

if ($iaView->getRequestType() == iaView::REQUEST_JSON) {
    switch ($pageAction) {
        case iaCore::ACTION_READ:
            switch ($_GET['get']) {
                default:
                    $params = array();
                    if (isset($_GET['text']) && $_GET['text']) {
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

if ($iaView->getRequestType() == iaView::REQUEST_HTML) {
    if (iaCore::ACTION_READ == $pageAction) {
        $iaView->grid('_IA_URL_modules/wall/js/admin/posts');
    }
}

$iaDb->resetTable();