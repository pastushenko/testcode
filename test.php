<?php
/**
 * Модель Геолокаций 
 * @author pastushenko
 */
class Geo extends Dictionary implements IEmbeddedable, ISuggestable {
	
	use TEmbeddedable;
	
    /**
     * Страна
     * @var int
     * @link http://api.yandex.ru/maps/doc/geocoder/desc/reference/kind.xml
     */
    const GT_COUNTRY = 1;
    
    /**
     * Область
     * @var int
     * @link http://api.yandex.ru/maps/doc/geocoder/desc/reference/kind.xml
     */
    const GT_PROVINCE = 2;
    
    /**
     * Район области
     * @var int
     * @link http://api.yandex.ru/maps/doc/geocoder/desc/reference/kind.xml
     */
    const GT_AREA = 3;
    
    /**
     * Населённый пункт
     * @var int
     * @link http://api.yandex.ru/maps/doc/geocoder/desc/reference/kind.xml
     */
    const GT_LOCALITY = 4;
    
    /**
     * Идентификатор
     * @var int
     */
    public $id;
    
    /**
     * Идентификатор предка
     * @var int
     */
    public $parent;
    
    /**
     * Идентификаторы потомков
     * @var array
     */
    public $children;
    
    /**
     * Домен страны
     * @example ru
     * @var string
     */
    public $country;
    
    /**
     * Домен региона
     * @example ivanovo
     * @var string
     */
    public $domain;
    
    /**
     * Назваие в родительном падеже
     * @example Ивановская область
     * @var string
     */
    public $name;
    
    /**
     * Назваие в родительном падеже
     * @example Ивановской области
     * @var string
     */
    public $genitive;
    
    /**
     * Назваие в предложном падеже
     * @example Ивановской области
     * @var string
     */
    public $prepositional;

    /**
     * Тип объекта (из констант)
     * @var int
     * @link http://api.yandex.ru/maps/doc/geocoder/desc/reference/kind.xml
     */
    public $type;
    
    /**
     * Флаг важных объектов
     * @example Может стоять на Москве, СПБ чтоб подсвечивать или сортировать
     * @var bool
     */
    public $important;
    
    /**
     * (non-PHPdoc)
     * @see EMongoDocument::model()
     * @param system $className
     * @return Geo
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }
    
    /**
     * (non-PHPdoc)
     * @see EMongoDocument::getCollectionName()
     */
    public function getCollectionName() {
        return 'geos';
    }
    
    /**
     * Только населённые пункты
     * @return Geo
     */
    public function localities() {
    	$this->getDbCriteria()->addCond('type', '==', self::GT_LOCALITY);
    	return $this;
    }
    
    /**
     * (non-PHPdoc)
     * @see ISuggestable::getSuggest()
     */
    public function getSuggest($source, $term, $exclude = null, $limit = 15) {
    	
    	$allowedFilters = arary('localities');
    	if (in_array($source, $allowedFilters)) {
    		$this->$source();
    	}

        if (is_array($exclude)) {
        	$exclude = array_map('intval', $exclude);
        }
        
        return $this->_getSuggest(
        	array(
            	'id' => 'id',
            	'label' => 'name',
            	'value' => 'name' 
        	), 
        	$term, 
        	$exclude, 
        	$limit
        );
        
    }
    
}

