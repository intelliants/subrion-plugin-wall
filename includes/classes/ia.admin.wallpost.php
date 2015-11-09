<?php
//##copyright##

class iaWallpost extends abstractPlugin
{
	protected static $_table = 'wall_posts';

	public $dashboardStatistics = true;

	public function getDashboardStatistics()
	{
		$statuses = array(iaCore::STATUS_ACTIVE, iaCore::STATUS_INACTIVE);
		$rows = $this->iaDb->keyvalue('`status`, COUNT(*)', '1 GROUP BY `status` ORDER BY `status` DESC', self::getTable());
		$total = 0;

		foreach ($statuses as $status)
		{
			isset($rows[$status]) || $rows[$status] = 0;
			$total += $rows[$status];
		}

		return array(
			'icon' => 'bubbles-2',
			'item' => iaLanguage::get('wall_posts'),
			'rows' => $rows,
			'total' => $total,
			'url' => 'wall-posts/'
		);
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
			. 'INET_NTOA(`ip`) `ip`, '
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

	public function gridRead($params, $columns, array $filterParams = array(), array $persistentConditions = array())
	{
		$params || $params = array();
		$start = isset($params['start']) ? (int)$params['start'] : 0;
		$limit = isset($params['limit']) ? (int)$params['limit'] : 15;
		$sort = $params['sort'];
		$dir = in_array($params['dir'], array(iaDb::ORDER_ASC, iaDb::ORDER_DESC)) ? $params['dir'] : iaDb::ORDER_ASC;
		$order = ($sort && $dir) ? "`{$sort}` {$dir}" : 't1.`date` DESC';

		$where = $values = array();
		foreach ($filterParams as $name => $type)
		{
			if (isset($params[$name]) && $params[$name])
			{
				$value = iaSanitize::sql($params[$name]);

				switch ($type)
				{
					case 'equal':
						$where[] = sprintf('t1.`%s` = :%s', $name, $name);
						$values[$name] = $value;
						break;
					case 'like':
						$where[] = sprintf('t1.`%s` LIKE :%s', $name, $name);
						$values[$name] = '%' . $value . '%';
				}
			}
		}

		$where = array_merge($where, $persistentConditions);
		$where || $where[] = iaDb::EMPTY_CONDITION;
		$where = implode(' AND ', $where);
		$this->iaDb->bind($where, $values);

		if (is_array($columns))
		{
			$columns = array_merge(array('id', 'delete' => 1), $columns);
		}

		return array(
			'data' => $this->get($columns, $where, $start, $limit, $order),
			'total' => (int)$this->iaDb->one(iaDb::STMT_COUNT_ROWS, $where)
		);
	}
}