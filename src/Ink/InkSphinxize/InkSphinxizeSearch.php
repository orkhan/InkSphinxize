<?php namespace Ink\InkSphinxize;

class InkSphinxizeSearch {

	/**
	 * SphinxClient
	 *
	 * @var \SphinxClient
	 */
	private $connection;
	
	/**
	 * Index name
	 *
	 * @var string
	 */
	private $index         = '';
	
	/**
	 * Search phrase
	 *
	 * @var string
	 */
	private $searchPhrase  = '';

	/**
	 * if TRUE return result IDs
	 *
	 * @var bool
	 */
	private $ids           = FALSE;

	/**
	 * Eloquent model name
	 *
	 * @var string
	 */
	private $model         = '';

	/**
	 * Primari key
	 *
	 * @var string
	 */
	private $pk            = '';

	/**
	 * Realation for eager loading
	 *
	 * @var null|array
	 */
	private $relations     = NULL;

	/**
	 * Skip (Offset)
	 *
	 * @var int
	 */
	private $skip          = 0;

	/**
	 * Take (Limit)
	 *
	 * @var int
	 */
	private $take          = 15;

	/**
	 * Total result count
	 *
	 * @var int
	 */
	private $totalCount    = 0;

	/**
	 * Called methods
	 *
	 * @var array
	 */
	private $calledMethods = [];

	/**
	 * Result
	 *
	 * @var null|array|\Illuminate\Database\Eloquent\Collection
	 */
	private $result        = NULL;

	/**
	 * Construct
	 * 
	 * @param array $config
	 */
	public function __construct($config = [])
	{
		$this->connection = new \SphinxClient();
		$this->connection->setServer($config['host'], $config['port']);
		$this->connection->setMatchMode(SPH_MATCH_ANY);
		$this->connection->setSortMode(SPH_SORT_RELEVANCE);
	}

	/**
	 * Set params
	 * 
	 * @param string $searchPhrase
	 * @param string $index
	 * @param string $model
	 * @param string $pk
	 * @param int $perPage
	 * @param mixed[] $relations
	 * @return $this
	 */
	public function setParams($searchPhrase, $index, $model, $pk, $perPage, $relations = NULL)
	{
		$this->searchPhrase = $searchPhrase;
		$this->index        = $index;
		$this->model        = $model;
		$this->pk           = $pk;
		$this->take         = $perPage;
		$this->relations    = $relations;

		$this->connection->resetFilters();
		$this->connection->resetGroupBy();
		return $this;
	}

	/**
	 * setMatchMode
	 * 
	 * @param CONST $mode
	 * @return $this
	 */
	public function setMatchMode($mode)
	{
		$this->connection->setMatchMode($mode);
		return $this;
	}

	/**
	 * setSortMode
	 * 
	 * @param CONST $mode
	 * @param null|string $mode
	 * @return $this
	 */
	public function setSortMode($mode, $par = NULL)
	{
		$this->connection->setSortMode($mode, $par);
		return $this;
	}

	/**
	 * setRankingMode
	 * 
	 * @param CONST $ranker
	 * @return $this
	 */
	public function setRankingMode($ranker)
	{
		$this->connection->setRankingMode($ranker);
		return $this;
	}

	/**
	 * setFieldWeights
	 * 
	 * @param array $weights
	 * @return $this
	 */
	public function setFieldWeights($weights)
	{
		$this->connection->setFieldWeights($weights);
		return $this;
	}

	/**
	 * setSelect
	 * 
	 * @param string $clause
	 * @return $this
	 */
	public function select($clause)
	{
		$this->connection->setSelect($clause);
		return $this;
	}

	/**
	 * Skip (Offset)
	 * 
	 * @param int $skip
	 * @return $this
	 */
	public function skip($skip)
	{
		$this->skip = $skip;
		return $this;
	}

	/**
	 * Take (Limit)
	 * 
	 * @param int $take
	 * @return $this
	 */
	public function take($take)
	{
		$this->take = $take;
		return $this;
	}

	/**
	 * setLimits
	 * 
	 * @param int $skip
	 * @param int $take
	 * @param int $maxMatches
	 * @param int $cutoff
	 * @return $this
	 */
	public function limit($skip = 0, $take = 0, $maxMatches = 1000, $cutoff = 1000)
	{
		if ( in_array(__METHOD__, $this->calledMethods) ) return $this;
		$this->calledMethods[] = __METHOD__;

		if ( $skip > 0 ) $this->skip($skip);
		if ( $take > 0 ) $this->take($take);

		$this->connection->setLimits($this->skip, $this->take, $maxMatches, $cutoff);
		return $this;
	}

	/**
	 * setFilter
	 * 
	 * @param string $attribute
	 * @param array|string $values
	 * @param bool $exclude
	 * @return $this
	 */
	public function filter($attribute, $values, $exclude = FALSE)
	{
		if ( is_array($values) )
		{
			$val = [];
			foreach($values as $v) $val[] = (int) $v;
		}
		else
		{
			$val = [(int) $values];
		}

		$this->connection->setFilter($attribute, $val, $exclude);
		return $this;
	}

	/**
	 * setFilterRange
	 * 
	 * @param string $attribute
	 * @param int $min
	 * @param int $max
	 * @param bool $exclude
	 * @return $this
	 */
	public function range($attribute, $min, $max, $exclude = FALSE)
	{
		$this->connection->setFilterRange($attribute, $min, $max, $exclude);
		return $this;
	}

	/**
	 * setFilterFloatRange
	 * 
	 * @param string $attribute
	 * @param float $min
	 * @param float $max
	 * @param bool $exclude
	 * @return $this
	 */
	public function rangeFloat($attribute, $min, $max, $exclude = FALSE)
	{
		$this->connection->setFilterFloatRange($attribute, $min, $max, $exclude);
		return $this;
	}

	/**
	 * If this method called $this->get() return array of result IDs
	 * 
	 * @return $this
	 */
	public function ids()
	{
		$this->ids = TRUE;
		return $this;
	}

	/**
	 * Eager Loading (Relations)
	 * 
	 * @param array|string $relations
	 * @return $this
	 */
	public function with($relations)
	{
		$this->relations = $relations;
		return $this;
	}

	/**
	 * Paginate
	 * 
	 * @param int $perPage
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function paginate($perPage = 0)
	{
		if ( $perPage > 0 ) $this->take($perPage);

		if ( \Input::has('page') )
		{
			$page = \Input::get('page');
			$skip = ( $page * $this->getPerPage() ) - $this->getPerPage();
			$this->skip($skip);
		}

		return $this->get();
	}

	/**
	 * Get result
	 * 
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function get()
	{
		$this->limit();

		$this->result = $this->connection->query($this->searchPhrase, $this->index);

		if ( $this->result )
		{
			$this->totalCount = (int) $this->result['total_found'];

			if( $this->result['total'] > 0 && isset($this->result['matches']) )
			{
				$this->result = array_keys($this->result['matches']);

				if ( $this->ids === TRUE ) return $this->result;
				
				$model   = $this->model;
				$prepare = $model::whereIn($this->pk, $this->result);

				if ( $this->relations !== NULL ) $prepare = $prepare->with($this->relations);

				$this->result = $prepare->get();
			}
			else
			{
				$this->result = NULL;
			}
		}

		return $this->result;    
	}

	/**
	 * Get $take value
	 * 
	 * @return int
	 */
	public function getPerPage()
	{
		return $this->take;
	}

	/**
	 * Get $totalCount value
	 * 
	 * @return int
	 */
	public function getTotalCount()
	{
		return $this->totalCount;
	}

	/**
	 * Get $connection last error
	 * 
	 * @return string
	 */
	public function getErrorMessage()
	{
		return $this->connection->getLastError();
	}

	/**
	 * Calls $connection methods
	 * 
	 * @return $this
	 */
	public function __call($method, $args)
	{
		call_user_func_array([$this->connection, $method], $args);
		return $this;
	}
}