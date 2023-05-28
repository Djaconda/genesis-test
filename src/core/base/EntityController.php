<?php

namespace core\base;

use core\db\ActiveRecord;
use core\domain\Entity;

/**
 * Class EntityController
 *
 * @package core\base
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
interface EntityController {
    /**
     * Returns the domain entity based on the data model's primary key.
     * If the data model is not found, a {@link notFoundException} will be raised.
     *
     * @param mixed $pk the primary key of the model to be loaded.
     *
     * @return Entity new model instance.
     */
    public function loadEntityByPk($pk);

    /**
     * Returns the data model based on the primary key.
     * If the data model is not found, an HTTP exception will be raised.
     *
     * @param mixed $pk the primary key of the model to be loaded.
     *
     * @return ActiveRecord new model instance.
     */
    public function loadModelByPk($pk);

    /**
     * Returns the new domain entity, with default values.
     * Such entity should be used for the insert scenario.
     *
     * @return Entity new model instance.
     */
    public function createEntity();

    /**
     * Returns the new data model, with default values.
     * Such model should be used for the insert scenario.
     *
     * @return ActiveRecord new model instance.
     */
    public function createModel();

    /**
     * Returns the new data model, with default values are cleared.
     * Such model should be used for the list scenario: filter + list of records.
     *
     * @return ActiveRecord search model instance.
     */
    public function createSearchModel();

    // Set / Get :

    public function setModelClassName($modelClassName);

    public function getModelClassName();

    public function setSearchModelClassName($searchModelClassName);

    public function getSearchModelClassName();

    public function setModelSearchScenarioName($modelSearchScenarioName);

    public function getModelFinder();

    public function setModelFinder($modelFinder);

    public function getEntityClassName();

    public function setEntityClassName($entityClassName);

    public function getEntityListQuery();
}
