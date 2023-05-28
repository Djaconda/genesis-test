<?php

namespace core\base;

use common\contract\finders\ArFinder;
use core\contracts\Application;
use core\db\ActiveRecord;
use core\db\ActiveRecordInterface;
use core\domain\DomainEntity;
use core\domain\Entity;
use ReflectionClass;

/**
 * Allows to create and find data models and entities.
 * Designed for controllers but also can be used in any class extended from {@link BaseObject} or uses {@link \core\app\ApplicationInjectionTrait}
 *
 * @package core\base
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
trait EntityManageFunctions {
    public $notFoundException = Application::HTTP_NOT_FOUND_EXCEPTION;
    /**
     * @var ActiveRecordInterface name of model class.
     */
    protected $_modelClassName = '';
    /**
     * @var ArFinder name of model class.
     */
    protected $_modelFinder = '';
    /**
     * @var ArFinder name of model class.
     */
    protected $_entityClassName = DomainEntity::class;
    /**
     * @var string name of the model class, which should be used for the search.
     * If this field is empty the {@link modelClassName} value will be used.
     */
    protected $_searchModelClassName = '';
    /**
     * @var string name of model search scenario.
     */
    protected $_modelSearchScenarioName = 'search';

    public function getModelSearchScenarioName() {
        return $this->_modelSearchScenarioName;
    }

    /**
     * Returns the domain entity based on the data model's primary key.
     * If the data model is not found, a {@link notFoundException} will be raised.
     *
     * @param mixed $pk the primary key of the model to be loaded.
     *
     * @return Entity new model instance.
     */
    public function loadEntityByPk($pk) {
        $model = $this->loadModelByPk($pk);
        $this->createModel();

        return $this->container->create($this->getEntityClassName(), [$model]);
    }

    /**
     * Returns the data model based on the primary key.
     * If the data model is not found, an HTTP exception will be raised.
     *
     * @param mixed $pk the primary key of the model to be loaded.
     *
     * @return ActiveRecord new model instance.
     */
    public function loadModelByPk($pk) {
        $modelFinder = $this->getModelFinder();
        $model = $modelFinder->find()->oneWithPk($pk);
        if ($model === null) {
            $this->throwException($this->notFoundException, 'The requested entity does not exist.');
        }

        return $model;
    }

    /**
     * Returns the new domain entity, with default values.
     * Such entity should be used for the insert scenario.
     *
     * @return Entity new model instance.
     */
    public function createEntity() {
        $model = $this->createModel();

        return $this->container->create($this->getEntityClassName(), [$model]);
    }

    /**
     * Returns the new data model, with default values.
     * Such model should be used for the insert scenario.
     *
     * @return ActiveRecord new model instance.
     */
    public function createModel() {
        return $this->container->create($this->getModelClassName());
    }

    /**
     * Returns the new data model, with default values are cleared.
     * Such model should be used for the list scenario: filter + list of records.
     *
     * @return ActiveRecord search model instance.
     */
    public function createSearchModel() {
        $model = $this->container->create($this->getSearchModelClassName());
        $model->setScenario($this->getModelSearchScenarioName());

        return $model;
    }

    // Set / Get :

    public function setModelClassName($modelClassName) {
        if ($this->isClassOrInterfaceExists($modelClassName) && $this->isClassImplementsInterface($modelClassName, ActiveRecordInterface::class)) {
            $this->_modelClassName = $modelClassName;
        } else {
            $this->throwException(Application::INVALID_CONFIG_EXCEPTION, static::class . '::modelClassName should be a valid AR class or interface!');
        }

        return true;
    }

    public function getModelClassName() {
        return $this->_modelClassName;
    }

    public function setSearchModelClassName($searchModelClassName) {
        if ($this->isClassOrInterfaceExists($searchModelClassName) && $this->isClassImplementsInterface($searchModelClassName, ActiveRecordInterface::class)) {
            $this->_searchModelClassName = $searchModelClassName;
        } else {
            $this->throwException(Application::INVALID_CONFIG_EXCEPTION, static::class . '::searchModelClassName should be a valid AR class or interface!');
        }

        return true;
    }

    public function getSearchModelClassName() {
        if (empty($this->_searchModelClassName)) {
            $this->_searchModelClassName = $this->getModelClassName();
        }

        return $this->_searchModelClassName;
    }

    public function setModelSearchScenarioName($modelSearchScenarioName) {
        if (is_string($modelSearchScenarioName)) {
            $this->_modelSearchScenarioName = $modelSearchScenarioName;
        } else {
            $this->throwException(Application::INVALID_CONFIG_EXCEPTION, static::class . '::modelSearchScenarioName should be a string!');
        }

        return true;
    }

    public function getModelFinder() {
        return $this->_modelFinder;
    }

    public function setModelFinder($modelFinder) {
        if (is_object($modelFinder) && $modelFinder instanceof ArFinder) {
            $this->_modelFinder = $modelFinder;
        } elseif ($this->isClassOrInterfaceExists($modelFinder) && $this->isClassImplementsInterface($modelFinder, ArFinder::class)) {
            $this->_modelFinder = $this->container->create($modelFinder);
        } else {
            $this->throwException(Application::INVALID_CONFIG_EXCEPTION, static::class . '::modelFinder should be a valid finder or class(interface) that can be instantiated using container!');
        }
    }

    public function getEntityClassName() {
        return $this->_entityClassName;
    }

    public function setEntityClassName($entityClassName) {
        if ($this->isClassOrInterfaceExists($entityClassName) && $this->isClassImplementsInterface($entityClassName, DomainEntity::class)) {
            $this->_entityClassName = $entityClassName;
        } else {
            $this->throwException(Application::INVALID_CONFIG_EXCEPTION, static::class . '::entityClassName should be a valid entity class or interface!');
        }
    }

    public function getEntityListQuery() {
        $model = $this->createSearchModel();
        $model->load($this->serviceLocator->request->queryParams);

        return $model::find();
    }

    protected function isClassOrInterfaceExists($classOrInterfaceName) {
        return class_exists($classOrInterfaceName) || interface_exists($classOrInterfaceName);
    }

    protected function isClassImplementsInterface($classOrInterface, $interface) {
        $classReflection = new ReflectionClass($classOrInterface);

        return $classReflection->implementsInterface($interface);
    }
}
