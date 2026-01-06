<?php

namespace NaN\Database\Sql\Query\Statements\Clauses;

use NaN\Database\Ast;
use NaN\Database\Ast\Tree;
use NaN\Database\Query\Statements\Traits\ClauseTrait;
use NaN\Database\Quotes;

final class WhereClause {
	use ClauseTrait;

	public function __construct(Tree $parent) {
		$this->_data = Ast::tree('where');
		$parent->push($this->_data);
	}

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
	public function and(\Closure|string $column, ?string $operator = null, mixed $value = null): self {
		if ($column instanceof \Closure) {
			return $this->_addGroup('AND', $column);
		}

		return $this->_addColumn('AND', $column, $operator, $value);
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
	public function is(\Closure|string $column, ?string $operator = null, mixed $value = null): self {
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
	public function or(\Closure|string $column, ?string $operator = null, mixed $value = null): self {
		if ($column instanceof \Closure) {
			return $this->_addGroup('OR', $column);
		}

		return $this->_addColumn('OR', $column, $operator, $value);
	}

	/**
	 * Add where expression.
	 *
	 * @param ?string $delimiter AND, OR... Use null for first where expression.
	 * @param string $column
	 * @param string $operator =, >=, <=, IN...
	 * @param mixed $value
	 *
	 * @return static
	 */
	protected function _addColumn(?string $delimiter, string $column, string $operator, mixed $value): self {
		if (!empty($delimiter)) {
			$this->_data->push(Ast::expr($delimiter, quotes: [Quotes::None]));
		}

		$this->_data->push(Ast::expr($column, $operator, $value));

		return $this;
	}

	/**
	 * Add sub where clause.
	 *
	 * @param ?string $delimiter AND, OR...
	 * @param \Closure $fn
	 *
	 * @return static
	 */
	protected function _addGroup(?string $delimiter, \Closure $fn): self {
		if (!empty($delimiter)) {
			$this->_data->push(Ast::expr($delimiter, quotes: [Quotes::None]));
		}

		$where_group = new WhereClause($this->_data);

		$fn($where_group);

		return $this;
	}
}
