<?php

namespace core\api\rest;

use core\contracts\Application;
use PHPKitchen\DI\Contracts\ServiceLocatorAware;
use PHPKitchen\DI\Mixins\ServiceLocatorAccess;

/**
 * AssetBundle extends {\yii\web\AssetBundle} to implement functionality
 * required to register JS templates.
 *
 * @property Application $serviceLocator
 * @method Application getServiceLocator()
 *
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
class AssetBundle extends \yii\web\AssetBundle implements ServiceLocatorAware {
    use ServiceLocatorAccess;

    /**
     * @var View
     */
    public $view;
    /**
     * @var array list of js templates in format:
     * <pre>
     *      'templateDirInBundle/template.php',
     *      'templateDirInBundle/template-2.tpl' => ['html options'],
     * </pre>
     */
    public $jsTemplates = [];
    public $jsTemplatesParams = [];
    /**
     * @var string path to js templates. If this property not set means that templates located under {@link sourcePath.}
     */
    public $jsTemplatesPath;

    /**
     * Registers the CSS and JS files with the given view.
     *
     * @param View $view the view that the asset files are to be registered with.
     */
    public function registerAssetFiles($view) {
        parent::registerAssetFiles($view);
        $templatesPath = $this->jsTemplatesPath ?: $this->sourcePath;
        foreach ($this->jsTemplates as $jsTemplate => $htmlParams) {
            if (is_numeric($jsTemplate)) {
                $jsTemplate = $htmlParams;
                $htmlParams = [];
            }
            // check set alias in $jsTemplate in order to use the template of the different folders
            if (str_starts_with((string)$jsTemplate, '@')) {
                $templateFullPath = $jsTemplate;
            } elseif ($templatesPath) {
                $templateFullPath = $templatesPath . '/' . $jsTemplate;
            } else {
                $templateFullPath = $jsTemplate;
            }
            $view->registerJsTemplate($templateFullPath, $htmlParams, $this->jsTemplatesParams);
        }
    }

    public function buildUrlTo($asset) {
        return $this->view->getAssetManager()->getAssetUrl($this, $asset);
    }
}
