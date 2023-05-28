<?php

namespace core\web;

use core\AppLoader;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\AssetBundle;

/**
 * Extends {@link \yii\web\AssetManager} to init default value
 *
 * @see \yii\web\AssetManager
 *
 * @package core\web
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
class AssetManager extends \yii\web\AssetManager {
    public const GENERATED_ASSERTS_CONFIG_PATH = '/assets/assets_bundles.php';

    /**
     * @inheritdoc
     */
    public function init(): void {
        parent::init();

        $this->initDefaultValues();
    }

    protected function initDefaultValues() {
        $this->fileMode = 0777;
        $this->dirMode = 0777;

        $this->fillBundlesConfig();

        $this->hashCallback = [$this, 'generateAssetHashCallback'];
    }

    /**
     * Method was overridden to fill bundles config that could be previously cleared by some modules (e.g. DebugModule)
     *
     * @param string $name
     * @param bool $publish
     *
     * @return AssetBundle
     * @throws InvalidConfigException
     */
    public function getBundle($name, $publish = true) {
        if ($this->bundles === []) {
            $this->fillBundlesConfig();
        }

        return parent::getBundle($name, $publish);
    }

    protected function generateAssetHashCallback($path) {
        if (is_file($path)) {
            $path = dirname((string)$path) . filemtime($path);
        } else {
            $subDirectoriesMTime = 0;
            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $file) {
                $subDirectoriesMTime += $file->getMTime();
            }
            $dirModificationTime = filemtime($path) + $subDirectoriesMTime;
            $path .= $dirModificationTime;
        }

        return sprintf('%x', crc32($path . Yii::getVersion()));
    }

    protected function fillBundlesConfig(): void {
        $configManager = AppLoader::getInstance();
        $generatedAssetsConfig = $configManager->rootPath . static::GENERATED_ASSERTS_CONFIG_PATH;
        $this->bundles = OVERRIDE_ASSET_BUNDLES && file_exists($generatedAssetsConfig) ? require($generatedAssetsConfig) : [];
    }
}
