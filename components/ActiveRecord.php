<?php
abstract class ActiveRecord extends Base {
    public static $db;

    public static $operators = array(
        'equal' => '=', 'eq' => '=',
        'notequal' => '<>', 'ne' => '<>',
        'greaterthan' => '>', 'gt' => '>',
        'lessthan' => '<', 'lt' => '<',
        'greaterthanorequal' => '>=', 'ge' => '>=','gte' => '>=',
        'lessthanorequal' => '<=', 'le' => '<=','lte' => '<=',
        'between' => 'BETWEEN',
        'like' => 'LIKE',
        'in' => 'IN',
        'notin' => 'NOT IN',
        'isnull' => 'IS NULL',
        'isnotnull' => 'IS NOT NULL', 'notnull' => 'IS NOT NULL',
    );

    public static $sqlParts = array(
        'select' => 'SELECT',
        'from' => 'FROM',
        'set' => 'SET',
        'where' => 'WHERE',
        'group' => 'GROUP BY','groupby' => 'GROUP BY',
        'having' => 'HAVING',
        'order' => 'ORDER BY','orderby' => 'ORDER BY',
        'limit' => 'limit',
        'top' => 'TOP',
    );

    public static $defaultSqlExpressions = array('expressions' => array(), 'wrap' => false,
        'select'=>null, 'insert'=>null, 'update'=>null, 'set' => null, 'delete'=>'DELETE ', 'join' => null,
        'from'=>null, 'values' => null, 'where'=>null, 'having'=>null, 'limit'=>null, 'order'=>null, 'group' => null);

    protected $sqlExpressions = array();

    public $table;

    public $primaryKey = 'id';

    public $dirty = array();

    public $params = array();
    const BELONGS_TO = 'belongs_to';
    const HAS_MANY = 'has_many';
    const HAS_ONE = 'has_one';

    public $relations = array();

    public static $count = 0;
    const PREFIX = ':ph';

    public function reset() {
        $this->params = array();
        $this->sqlExpressions = array();
        return $this;
    }

    public function dirty($dirty = array()){
        $this->data = array_merge($this->data, $this->dirty = $dirty);
        return $this;
    }

    public static function setDb($db) {
        self::$db = $db;
    }

    public function find($id = null) {
        if ($id) $this->reset()->eq($this->primaryKey, $id);
        return self::_query($this->limit(1)->_buildSql(array('select', 'from', 'join', 'where', 'group', 'having', 'order', 'limit')), $this->params, $this->reset(), true);
    }

    public function findAll() {
        return self::_query($this->_buildSql(array('select', 'from', 'join', 'where', 'group', 'having', 'order', 'limit')), $this->params, $this->reset());
    }

    public function delete() {
        return self::execute($this->eq($this->primaryKey, $this->{$this->primaryKey})->_buildSql(array('delete', 'from', 'where')), $this->params);
    }

    public function update() {
        if (count($this->dirty) == 0) return true;
        foreach($this->dirty as $field => $value) $this->addCondition($field, '=', $value, ',' , 'set');
        if(self::execute($this->eq($this->primaryKey, $this->{$this->primaryKey})->_buildSql(array('update', 'set', 'where')), $this->params))
            return $this->dirty()->reset();
        return false;
    }

    public function insert() {
        if (count($this->dirty) == 0) return true;
        $value = $this->_filterParam($this->dirty);
        $this->insert = new Expressions(array('operator'=> 'INSERT INTO '. $this->table,
            'target' => new WrapExpressions(array('target' => array_keys($this->dirty)))));
        $this->values = new Expressions(array('operator'=> 'VALUES', 'target' => new WrapExpressions(array('target' => $value))));
        if (self::execute($this->_buildSql(array('insert', 'values')), $this->params)) {
            $this->{$this->primaryKey} = self::$db->lastInsertId();
            return $this->dirty()->reset();
        }
        return false;
    }

    public static function execute($sql, $param = array()) {
        $res = (($sth = self::$db->prepare($sql)) && $sth->execute($param));
        if (!$res) throw new Exception($sth->errorInfo()[2]);
        return $res;
    }

    public static function _query($sql, $param = array(), $obj = null, $single=false) {
        if ($sth = self::$db->prepare($sql)) {
            $called_class = get_called_class();
	    $sth->setFetchMode( PDO::FETCH_INTO , ($obj ? $obj : new $called_class ));
            $sth->execute($param);
            if ($single) return $sth->fetch( PDO::FETCH_INTO ) ? $obj->dirty() : false;
            $result = array();
            while ($obj = $sth->fetch( PDO::FETCH_INTO )) $result[] = clone $obj->dirty();
            return $result;
        }
        return false;
    }

    protected function & getRelation($name) {
        $relation = $this->relations[$name];
        if ($relation instanceof self || (is_array($relation) && $relation[0] instanceof self))
            return $relation;
        $this->relations[$name] = $obj = new $relation[1];
        if (isset($relation[3]) && is_array($relation[3]))
            foreach((array)$relation[3] as $func => $args)
                call_user_func_array(array($obj, $func), (array)$args);
        $backref = isset($relation[4]) ? $relation[4] : '';
        if ((!$relation instanceof self) && self::HAS_ONE == $relation[0])
            $obj->eq($relation[2], $this->{$this->primaryKey})->find() && $backref && $obj->__set($backref, $this);
        elseif (is_array($relation) && self::HAS_MANY == $relation[0]) {
            $this->relations[$name] = $obj->eq($relation[2], $this->{$this->primaryKey})->findAll();
            if ($backref)
                foreach($this->relations[$name] as $o)
                    $o->__set($backref, $this);
        } elseif ((!$relation instanceof self) && self::BELONGS_TO == $relation[0])
            $obj->eq($obj->primaryKey, $this->{$relation[2]})->find() && $backref && $obj->__set($backref, $this);
        else throw new Exception("Relation $name not found.");
        return $this->relations[$name];
    }

    private function _buildSqlCallback(&$n, $i, $o){
        if ('select' === $n && null == $o->$n) $n = strtoupper($n). ' '.$o->table.'.*';
        elseif (('update' === $n||'from' === $n) && null == $o->$n) $n = strtoupper($n).' '. $o->table;
        elseif ('delete' === $n) $n = strtoupper($n). ' ';
        else $n = (null !== $o->$n) ? $o->$n. ' ' : '';
    }

    protected function _buildSql($sqls = array()) {
        array_walk($sqls, array($this, '_buildSqlCallback'), $this);

        return implode(' ', $sqls);
    }

    public function __call($name, $args) {
        if (is_callable($callback = array(self::$db,$name)))
            return call_user_func_array($callback, $args);
        if (in_array($name = strtolower($name), array_keys(self::$operators)))
            $this->addCondition($args[0], self::$operators[$name], isset($args[1]) ? $args[1] : null, (is_string(end($args)) && 'or' === strtolower(end($args))) ? 'OR' : 'AND');
        else if (in_array($name= str_replace('by', '', $name), array_keys(self::$sqlParts)))
            $this->$name = new Expressions(array('operator'=>self::$sqlParts[$name], 'target' => implode(', ', $args)));
        else throw new Exception("Method $name not exist.");
        return $this;
    }

    public function wrap($op = null) {
        if (1 === func_num_args()){
            $this->wrap = false;
            if (is_array($this->expressions) && count($this->expressions) > 0)
            $this->_addCondition(new WrapExpressions(array('delimiter' => ' ','target'=>$this->expressions)), 'or' === strtolower($op) ? 'OR' : 'AND');
            $this->expressions = array();
        } else $this->wrap = true;
        return $this;
    }

    protected function _filterParam($value) {
        if (is_array($value)) foreach($value as $key => $val) $this->params[$value[$key] = self::PREFIX. ++self::$count] = $val;
        else if (is_string($value)){
            $this->params[$ph = self::PREFIX. ++self::$count] = $value;
            $value = $ph;
        }
        return $value;
    }

    public function addCondition($field, $operator, $value, $op = 'AND', $name = 'where') {
        $value = $this->_filterParam($value);
        if ($exp =  new Expressions(array('source'=>('where' == $name? $this->table.'.' : '' ) .$field, 'operator'=>$operator, 'target'=>(is_array($value)
            ? new WrapExpressions('between' === strtolower($operator)
                ? array('target' => $value, 'start' => ' ', 'end' => ' ', 'delimiter' => ' AND ')
                : array('target' => $value)
            ) : $value)))) {
            if (!$this->wrap)
                $this->_addCondition($exp, $op, $name);
            else
                $this->_addExpression($exp, $op);
        }
    }

    public function join($table, $on, $type='LEFT'){
        $this->join = new Expressions(array('source' => $this->join ?: '', 'operator' => $type. ' JOIN', 'target' => new Expressions(
            array('source' => $table, 'operator' => 'ON', 'target' => $on)
        )));
        return $this;
    }

    protected function _addExpression($exp, $operator) {
        if (!is_array($this->expressions) || count($this->expressions) == 0)
            $this->expressions = array($exp);
        else
            $this->expressions[] = new Expressions(array('operator'=>$operator, 'target'=>$exp));
    }

    protected function _addCondition($exp, $operator, $name ='where' ) {
        if (!$this->$name)
            $this->$name = new Expressions(array('operator'=>strtoupper($name) , 'target'=>$exp));
        else
            $this->$name->target = new Expressions(array('source'=>$this->$name->target, 'operator'=>$operator, 'target'=>$exp));
    }

    public function __set($var, $val) {
        if (array_key_exists($var, $this->sqlExpressions) || array_key_exists($var, self::$defaultSqlExpressions))
            $this->sqlExpressions[$var] = $val;
        else if (array_key_exists($var, $this->relations) && $val instanceof self)
            $this->relations[$var] = $val;
        else $this->dirty[$var] = $this->data[$var] = $val;
    }

    public function __unset($var) {
        if (array_key_exists($var, $this->sqlExpressions)) unset($this->sqlExpressions[$var]);
        if(isset($this->data[$var])) unset($this->data[$var]);
        if(isset($this->dirty[$var])) unset($this->dirty[$var]);
    }

    public function & __get($var) {
        if (array_key_exists($var, $this->sqlExpressions)) return  $this->sqlExpressions[$var];
        else if (array_key_exists($var, $this->relations)) return $this->getRelation($var);
        else return  parent::__get($var);
    }
}

abstract class Base {

    public $data = array();
    public function __construct($config = array()) {
        foreach($config as $key => $val) $this->$key = $val;
    }
    public function __set($var, $val) {
        $this->data[$var] = $val;
    }
    public function & __get($var) {
        $result = isset($this->data[$var]) ? $this->data[$var] : null;
        return $result;
    }
}

class Expressions extends Base {
    public function __toString() {
        return $this->source. ' '. $this->operator. ' '. $this->target;
    }
}

class WrapExpressions extends Expressions {
    public function __toString() {
        return ($this->start ? $this->start: '('). implode(($this->delimiter ? $this->delimiter: ','), $this->target). ($this->end?$this->end:')');
    }
}
