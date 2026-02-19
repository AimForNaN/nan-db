<?php

namespace NaN\Database\Sql\Query\Statements\Clauses;

use NaN\Database\Ast;
use NaN\Database\Ast\Node;
use NaN\Database\Query\Statements\Interfaces\ClauseInterface;
use NaN\Database\Quotes;

class WhereClause implements ClauseInterface, \Countable {
	protected array $_data = [];

	/**
	 * Add AND where expression.
	 *
	 * @param \Closure|string $column
	 * @param string|null $operator =, >=, <=, IN...
	 * @param mixed $value
	 *
	 * @return static
	 *
	 * @see _addColumn()
	 */
	public function and(\Closure|string $column, ?string $operator = null, mixed $value = null): static {
		if ($column instanceof \Closure) {
			return $this->_addGroup('AND', $column);
		}

		return $this->_addColumn('AND', $column, $operator, $value);
	}

	public function count(): int {
		return count($this->_data);
	}

	/**
	 * Add where expression.
	 *
	 * @param \Closure|string $column
	 * @param string|null $operator =, >=, <=, IN...
	 * @param mixed $value
	 *
	 * @return static
	 *
	 * @see _addColumn()
	 */
	public function is(\Closure|string $column, ?string $operator = null, mixed $value = null): static {
		if ($column instanceof \Closure) {
			return $this->_addGroup(null, $column);
		}

		return $this->_addColumn(null, $column, $operator, $value);
	}

	/**
	 * Add OR where expression.
	 *
	 * @param \Closure|string $column
	 * @param string|null $operator =, >=, <=, IN...
	 * @param mixed $value
	 *
	 * @return static
	 *
	 * @see _addColumn()
	 */
	public function or(\Closure|string $column, ?string $operator = null, mixed $value = null): static {
		if ($column instanceof \Closure) {
			return $this->_addGroup('OR', $column);
		}

		return $this->_addColumn('OR', $column, $operator, $value);
	}

	public function toAst(): Node {
		$ast = Ast::tree('where');
		$space = false;

		foreach ($this->_data as $data) {
			[$joining_operator, $column, $operator, $value] = $data + [null, null, null, null];

			if ($space) {
				$ast->push(Ast::space());
			}

			if ($joining_operator) {
				$ast->push(Ast::raw($joining_operator));
				$ast->push(Ast::space());
			}

			if ($column instanceof WhereClause) {
				$group = Ast::group([
					$column->toAst(),
				]);
				$ast->push($group);
			} else {
				$ast->push(Ast::identifier($column));
				$ast->push(Ast::space());
				$ast->push(Ast::raw($operator));
				$ast->push(Ast::space());
				$ast->push(Ast::value($value, Quotes::Auto, true));
			}

			$space = true;
		}

		return $ast;
	}

	/**
	 * Add where expression.
	 *
	 * @param ?string $joining_operator AND, OR... Use null for first where expression.
	 * @param string $column
	 * @param string $operator =, >=, <=, IN...
	 * @param mixed $value
	 *
	 * @return static
	 */
	protected function _addColumn(?string $joining_operator, string $column, string $operator, mixed $value): static {
		if (empty($joining_operator)) {
			$joining_operator = null;
		}

		$this->_data[] = [$joining_operator, $column, $operator, $value];

		return $this;
	}

	/**
	 * Add sub where clause.
	 *
	 * @param ?string $joining_operator AND, OR...
	 * @param \Closure $fn
	 *
	 * @return static
	 */
	protected function _addGroup(?string $joining_operator, \Closure $fn): static {
		if (empty($joining_operator)) {
			$joining_operator = null;
		}

		$where = new static();

		$this->_data[] = [$joining_operator, $where];

		$fn($where);

		return $this;
	}
}
