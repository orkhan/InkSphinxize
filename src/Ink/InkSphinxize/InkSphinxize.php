<?php namespace Ink\InkSphinxize;

trait InkSphinxize {

	/**
	 * Sphinx Search (ss)
	 * 
	 * @param string $phrase
	 * @return InkSphinxSearch
	 */
	public static function ss($phrase)
	{
		$model = $index = $pk = $perPage = $relations = NULL;
		extract( self::$sphinxize, EXTR_IF_EXISTS );
		$model = __CLASS__;
		
		$sphinxize = new InkSphinxizeSearch(\Config::get('ink-sphinxize::config'));
		return $sphinxize->setParams($phrase, $index, $model, $pk, $perPage, $relations);
	}

}