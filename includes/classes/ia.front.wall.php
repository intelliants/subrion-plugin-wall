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

class iaWall extends abstractModuleFront
{
    protected static $_table = 'wall_posts';

    public function insert(array $data)
    {
        if (!$this->iaCore->get('wall_allow_wysiwyg')) {
            $data['body'] = htmlspecialchars($data['body']);
            $data['ip'] = $this->iaCore->factory('util')->getIp();
        }

        $id = $this->iaDb->insert($data, array('date' => iaDb::FUNCTION_NOW), self::getTable());

        return $id;
    }

    public function update(array $data, $id = null)
    {
        return $this->iaDb->update($data, null, null, self::getTable());
    }

    public function delete($id)
    {
        return $this->iaDb->delete(iaDb::convertIds($id), self::getTable());
    }

    public function get($columns = null, $stmt = null, $start = 0, $limit = 0, $order = 't1.`date` DESC')
    {
        $stmtFields = $columns ? $columns : 't1.' . iaDb::ALL_COLUMNS_SELECTION;

        if (is_array($columns)) {
            $stmtFields = '';
            foreach ($columns as $key => $field) {
                $stmtFields .= is_int($key)
                    ? 't1.`' . $field . '`'
                    : sprintf('%s `%s`', is_numeric($field) ? $field : 't1.`' . $field . '`', $key);
                $stmtFields .= ', ';
            }
            $stmtFields = substr($stmtFields, 0, -2);
        }

        $sql = "SELECT SQL_CALC_FOUND_ROWS " . $stmtFields . ", t2.`username` `username`, "
            . "IF (t1.`member_id` > 0, IF (t2.`fullname` != '', t2.`fullname`, t2.`username`), '" . iaLanguage::get('guest') . "') `author`, "
            . "t2.`avatar` `author_avatar` "
            . "FROM `" . self::getTable(true) . "` t1 "
            . "LEFT JOIN `" . $this->iaDb->prefix . "members` t2 "
            . "ON t1.`member_id` = t2.`id` "
            . "WHERE " . ($stmt ? $stmt : '1 = 1')
            . " GROUP BY t1.`id`"
            . " ORDER BY " . $order
            . ($limit ? " LIMIT " . $start . ", " . $limit : '');

        return $this->iaDb->getAll($sql);
    }

    public function getById($id, $decorate = true)
    {
        $stmt = "t1.`id` = '{$id}' ";
        $post = $this->get(null, $stmt, 0, 1);
        $post = isset($post[0]) ? $post[0] : false;

        return $post;
    }

    public function getLatest($limit = 0)
    {
        $stmt = "t1.`status` = '" . iaCore::STATUS_ACTIVE . "'";

        return $this->get(null, $stmt, 0, $limit);
    }

    public function getLatestByMemberId($memberId, $limit = 0)
    {
        if (!$memberId) {
            return array();
        }

        $stmt = "t1.`status` = '" . iaCore::STATUS_ACTIVE . "' AND t1.`member_id` = " . $memberId;

        return $this->get(null, $stmt, 0, $limit);
    }
}