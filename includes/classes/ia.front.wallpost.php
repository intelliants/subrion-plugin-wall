<?php
//##copyright##

class iaWallpost extends abstractPlugin
{
	protected static $_table = 'wall_posts';

	public function insert($data)
	{
		$id = $this->iaDb->insert($data, array('date' => iaDb::FUNCTION_NOW), self::getTable());

		return $id;
	}

	public function update($data)
	{
		$id = $this->iaDb->update($data, null, null, self::getTable());

		return $id;
	}

	public function delete($id)
	{
		$this->iaDb->delete('`id` = ' . $id, self::getTable());

		return $this->iaDb->delete(array('id' => $id), self::getTable());
	}

	public function get($columns = null, $stmt = null, $start = 0, $limit = 0, $order = 't1.`date` DESC')
	{
		$stmtFields = $columns ? $columns : 't1.' . iaDb::ALL_COLUMNS_SELECTION;

		if (is_array($columns))
		{
			$stmtFields = '';
			foreach ($columns as $key => $field)
			{
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
			. " ORDER BY " . $order
			. ($limit ? " LIMIT " . $start . ", " . $limit : '');

		return $this->iaDb->getAll($sql);
	}

	public function getById($id)
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
		if (!$memberId)
		{
			return array();
		}

		$stmt = "t1.`status` = '" . iaCore::STATUS_ACTIVE . "' AND t1.`member_id` = " . $memberId;

		return $this->get(null, $stmt, 0, $limit);
	}
}