<?php

namespace core\api\rest;

use core\domain\Web\Action\AddEntity;
use core\domain\Web\Action\DeleteEntity;
use core\domain\Web\Action\EditEntity;
use core\domain\Web\Action\ListRecords;
use core\domain\Web\Model\ListingModel;
use core\domain\Web\Model\ViewModel;
use core\filters\Policy\Contract\Policy;
use core\frontend\Application;
use PHPKitchen\Domain\Web\Mixins\EntityManagement;

/**
 * Represents
 *
 * @property $defaultViewPath
 * @property Application $serviceLocator
 *
 * @method Application getServiceLocator()
 *
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
abstract class ManagementController extends Controller {
    use EntityManagement;

    public const EVENT_BEFORE_RENDER = 'beforeRender';
    /**
     * Policy class name, must implement @see Policy
     *
     * @var string
     */
    protected $policyClassName;
    private $_viewPath;
    private $_defaultViewPath;
    /**
     * @var string class name of a view model associated with this controller.
     */
    public string $classNameOfManagementModel = ViewModel::class;
    public $classNameOfListingModel = ListingModel::class;
    public $redirectUrlOfAddAction = null;
    public $redirectUrlOfEditAction = false;
    public $redirectUrlOfDeleteAction = ['list'];

    public function __construct($id, $module, $config = []) {
        parent::__construct($id, $module, $config);

        $this->initDefaultManagementActions();
        $this->configureActions();
        $this->initDefaultViewPath();
    }

    public function render($view, $params = []) {
        $this->trigger(self::EVENT_BEFORE_RENDER);

        return parent::render($view, $params);
    }

    protected function initDefaultManagementActions() {
        $this->addAction('list', [
            'class' => ListRecords::class,
            'viewModelClassName' => $this->classNameOfListingModel,
            'prepareViewParams' => [$this, 'prepareListingPageViewParams'],
        ]);
        $this->addAction('add', [
            'class' => AddEntity::class,
            'viewModelClassName' => $this->classNameOfManagementModel,
            'prepareViewParams' => [$this, 'prepareAddPageViewParams'],
            'redirectUrl' => $this->redirectUrlOfAddAction,
        ]);
        $this->addAction('edit', [
            'class' => EditEntity::class,
            'viewModelClassName' => $this->classNameOfManagementModel,
            'prepareViewParams' => [$this, 'prepareEditPageViewParams'],
            'redirectUrl' => $this->redirectUrlOfEditAction,
        ]);
        $this->addAction('delete', [
            'class' => DeleteEntity::class,
            'redirectUrl' => $this->redirectUrlOfDeleteAction,
        ]);
    }

    protected function configureActions() {
    }

    protected function initDefaultViewPath(): void {
        $className = static::class;
        $path = substr($className, 0, strpos($className, 'App\\Controller') - 1);

        $controller = implode('', array_map('ucfirst', explode('-', (string)$this->id)));

        $this->defaultViewPath = '@' . str_replace('\\', '/', $path) . '/View/Template/' . $controller;
    }

    /**
     * Prepares view params for listing page.
     * Override this method to pass custom params to view.
     *
     * @param array $params view params
     *
     * @return array prepared params.
     */
    public function prepareListingPageViewParams($params) {
        return $params;
    }

    /**
     * Prepares view params for Add page.
     * Override this method to pass custom params to view.
     *
     * @param array $params view params
     *
     * @return array prepared params.
     */
    public function prepareAddPageViewParams($params) {
        return $params;
    }

    /**
     * Prepares view params for Edit page.
     * Override this method to pass custom params to view.
     *
     * @param array $params view params
     *
     * @return array prepared params.
     */
    public function prepareEditPageViewParams($params) {
        return $params;
    }

    //region ~~~~ GETTERS / SETTERS ~~~~
    public function getViewPath() {
        return $this->_viewPath ?? $this->_defaultViewPath;
    }

    public function setViewPath($path) {
        $this->_viewPath = $this->serviceLocator->getAlias($path);
    }

    public function setDefaultViewPath($path) {
        $this->_defaultViewPath = $this->serviceLocator->getAlias($path);
    }

    public function setPolicyClassName(string $policyClassName) {
        $this->policyClassName = $policyClassName;
    }
    //endregion
}
