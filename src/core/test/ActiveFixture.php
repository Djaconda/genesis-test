<?php

namespace core\test;

use ReflectionClass;
use RuntimeException;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\test\ActiveFixture as BaseActiveFixture;

/**
 * A fixture backed up by a {@link modelClass} class or a {@link tableName}.
 * Extends {@link BaseActiveFixture} to integrate application DI and Faker.
 *
 * @see FixtureTrait
 *
 * @package core\test
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
class ActiveFixture extends BaseActiveFixture {
    use FixtureTrait;

    protected $fixturesDefaultLocation = '@Fixture/Data';

    public function load(): void {
        try {
            parent::load();
        } catch (Throwable $e) {
            throw new RuntimeException(
                sprintf('Error in fixture %s', $this->dataFile),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Returns the fixture data.
     *
     * The default implementation will try to return the fixture data by including the external file specified by [[dataFile]].
     * The file should return an array of data rows (column name => column value), each corresponding to a row in the table.
     *
     * If the data file does not exist, an empty array will be returned.
     *
     * @return array the data rows to be inserted into the database table.
     */
    protected function getData() {
        if ($this->dataFile === null && ($data = $this->loadFixturesFromDefaultLocation())) {
            return $data;
        }

        if ($this->dataFile === null) {
            return $this->loadFromParentDataDir();
        }

        return $this->loadFormDataFile();
    }

    protected function loadFormDataFile() {
        if ($this->dataFile === false || $this->dataFile === null) {
            return [];
        }
        $faker = $this->getFaker();
        $dataFile = Yii::getAlias($this->dataFile);
        if (is_file($dataFile)) {
            return require($dataFile);
        }

        throw new InvalidConfigException("Fixture data file does not exist: $this->dataFile");
    }

    protected function loadFromParentDataDir() {
        $faker = $this->getFaker();
        $class = new ReflectionClass($this);
        $dataFile = dirname($class->getFileName()) . '/data/' . $this->getTableSchema()->fullName . '.php';

        return is_file($dataFile) ? require($dataFile) : [];
    }

    protected function loadFixturesFromDefaultLocation() {
        $faker = $this->getFaker();
        $dataFile = $this->fixturesDefaultLocation . DIRECTORY_SEPARATOR . $this->getTableSchema()->fullName . '.php';
        $dataFilePath = Yii::getAlias($dataFile);

        return is_file($dataFilePath) ? require($dataFilePath) : false;
    }
}
