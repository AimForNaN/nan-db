<?php

namespace NaN\Database\Sql\Schema;

use NaN\Database\Ast;
use NaN\Database\Ast\Node;
use NaN\Database\Ast\Tree;
use RavingDev\CaseConverter\CaseConverter;

class SqlField extends Tree implements Interfaces\SqlFieldInterface {
	public function __construct(public readonly string $name, string $type) {
		if (empty($name)) {
			throw new \InvalidArgumentException('Field name cannot be empty!');
		}

		parent::__construct($type, [
			Ast::identifier($name),
			Ast::raw($type),
		]);
	}

	public function __call(string $name, array $args): static {
		$name = CaseConverter::toSnakeCase($name);
		[$value] = $args + [true];

		$this->__set($name, $value);

		return $this;
	}

	public static function __callStatic(string $type, array $args) {
		$type = CaseConverter::toUpperFlatCase($type);
		[$name] = $args;

		switch ($type) {
			case 'ID': {
				$ret = new static($name, 'BIGINT')
					->primaryKey() //<< unique + not null
					->autoincrement()
					->unsigned()
				;
				break;
			}
			default: {
				$ret = new static($name, $type);
			}
		}

		return $ret;
	}

	public function isPrimary(): bool {
		return (bool)$this->__get('primary_key');
	}

	public function toAst(): Node {
		$ast = Ast::tree('field', [
			Ast::identifier($this->name),
			Ast::space(),
			Ast::raw($this->type),
		]);

		if ($this->max) {
			$ast->push(Ast::group([
				Ast::value($this->max),
			]));
		}

		foreach ($this->_data as $key => $value) {
//			switch ($key) {
//				case 'max':
//					continue 2;
//			}

			if ($value === true) {
				$split = CaseConverter::splitWords($key);
				$name = \strtoupper(\implode(' ', $split));
				$ast->push(Ast::space());
				$ast->push(Ast::raw($name));
			}
		}

		return $ast;
	}
}
