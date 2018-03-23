<?php
/******************************************************************************
 *
 * Subrion - open source content management system
 * Copyright (C) 2018 Intelliants, LLC <https://intelliants.com>
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

$iaWallpost = $iaCore->factoryModule('wall', IA_CURRENT_MODULE);


if (iaView::REQUEST_JSON == $iaView->getRequestType()) {
    $error = false;
    $messages = array();
    $output = array();

    if (isset($_GET['action'])) {
        if (iaCore::ACTION_READ == $_GET['action']) {
            $stmt = "t1.`status` = '" . iaCore::STATUS_ACTIVE . "'";
            $start = isset($_GET['start']) ? $_GET['start'] : 0;
            $limit = isset($_GET['limit']) ? $_GET['limit'] : 5;

            $posts = $iaWallpost->get(null, $stmt, $start, $limit);
            $total = $iaDb->foundRows();

            $htmlOutput = '';

            if ($posts) {
                $iaView->loadSmarty(true);
                $iaView->iaSmarty->assign('member', iaUsers::getIdentity(true));

                foreach ($posts as $post) {
                    $iaView->iaSmarty->assign('post', $post);
                    $htmlOutput .= $iaView->iaSmarty->fetch(IA_MODULES . 'wall/templates/front/list.tpl');
                }
            }

            $output['html'] = $htmlOutput;
            $output['total'] = $total;
        }
    } elseif (isset($_POST['action'])) {
        iaCore::util();

        if (iaCore::ACTION_ADD == $_POST['action'] || iaCore::ACTION_EDIT == $_POST['action']) {
            if (!iaCore::get('wall_allow_guests') && !iaUsers::hasIdentity()) {
                $error = true;
                $messages[] = iaLanguage::get('guests_warning');
            }

            $max_chars = $iaCore->get('post_max_chars');
            $post = array(
                'body' => iaUtil::checkPostParam('body'),
                'member_id' => (int)iaUsers::getIdentity()->id,
                'ip' => $iaCore->util()->getIp(),
                'status' => $iaCore->get('wall_auto_approval') || iaCore::ACTION_EDIT == $_POST['action'] ? iaCore::STATUS_ACTIVE : iaCore::STATUS_INACTIVE
            );

            iaUtil::loadUTF8Functions('ascii', 'validation', 'bad');

            // CHECK: body
            if (!utf8_is_valid($post['body'])) {
                $post['body'] = utf8_bad_replace($post['body']);
            }

            $len = utf8_is_ascii($post['body']) ? strlen($post['body']) : utf8_strlen($post['body']);

            if (empty($post['body'])) {
                $error = true;
                $messages[] = iaLanguage::get('error_empty_post');
            } elseif ($max_chars > 0 && $len > $max_chars) {
                $error = true;
                $messages[] = iaLanguage::getf('error_max_chars', array('length' => $max_chars));
            } else {
                $post['body'] = preg_replace('#(^|\s|\n)http(s)?\:\/\/([^\s^\n^\<^\>]+)(\s|\n|\<|\>|$)#i', '$1<a href="http$2://$3" target="_blank">http$2://$3</a>$4', $post['body']);
            }

            if (!$error) {
                if (iaCore::ACTION_ADD == $_POST['action']) {
                    $id = $iaWallpost->insert($post);

                    if ($iaCore->get('wall_auto_approval')) {
                        $iaView->loadSmarty(true);
                        $iaView->iaSmarty->assign('post', $iaWallpost->getById($id));
                        $iaView->iaSmarty->assign('member', iaUsers::getIdentity(true));

                        $output['html'] = $iaView->iaSmarty->fetch(IA_MODULES . 'wall/templates/front/list.tpl');
                        $messages[] = iaLanguage::get('post_added');
                    } else {
                        $messages[] = iaLanguage::get('post_added') . ' ' . iaLanguage::get('post_waits_for_approval');
                    }

                    // send notification
                    if ($iaCore->get('wall_admin_notification')) {
                        $iaMailer = $iaCore->factory('mailer');

                        $iaMailer->loadTemplate('wall_admin_notification');
                        $iaMailer->setReplacements(array(
                            'title' => $iaCore->get('site'),
                            'url' => IA_ADMIN_URL . 'wall-posts/',
                            'text' => $post['body']
                        ));
                        $iaMailer->sendToAdministrators();
                    }
                } elseif (iaCore::ACTION_EDIT == $_POST['action']) {
                    $id = $post['id'] = $_POST['id'];
                    $iaWallpost->update($post);
                    $messages[] = iaLanguage::get('saved');

                    $output['html'] = $post['body'];
                }

            }
        } elseif (iaCore::ACTION_DELETE == $_POST['action']) {
            if ($id = $_POST['id']) {
                $iaWallpost->delete($id);
                $messages[] = iaLanguage::get('deleted');
            } else {
                $error = true;
                $messages[] = iaLanguage::get('invalid_parameters');
            }
        }
    }

    $output['error'] = $error;
    $output['messages'] = $messages;

    $iaView->assign($output);
}