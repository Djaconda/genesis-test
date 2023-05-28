<?php

namespace core\frontend;

use Composer\Autoload\ClassLoader;
use core\base\MagicAccessTrait;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\UnknownClassException;
use yii\web\UrlRule;

/**
 * Represents service for configuring urlManager component
 *
 * @property string $urlRulesCachePath
 *
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
class UrlManagerConfigurator {
    use MagicAccessTrait;

    public $moduleDefinitions = [];
    public $urlRules = [];
    protected $loader;
    protected $application;
    protected $_urlRulesCachePath = '@runtime/urlRules.php';

    public function __construct(ClassLoader $loader, Application $application) {
        $this->loader = $loader;
        $this->application = $application;
    }

    public function configure(): void {
        if (FORCE_REGENERATE_URL_RULE_CACHE || !file_exists($this->urlRulesCachePath)) {
            $this->generateUrlRules();
        }
        $rules = require $this->urlRulesCachePath;
        $this->application->urlManager->addRules($rules, false);
    }

    public function generateUrlRules(): bool {
        $this->parseModuleConfigs($this->application->modules);
        $sortedUrlRules = $this->sortUrlRules();

        return $this->writeUrlRules($sortedUrlRules);
    }

    public function parseModuleConfigs(array $modules): void {
        foreach ($modules as $currentModuleId => $module) {
            $moduleClass = $this->getModuleClassName($module);

            if (is_array($module)) {
                $moduleUrlRules = $this->prepareRules($module['urlRules'] ?? []);
                $this->urlRules[$currentModuleId] = array_merge($this->urlRules[$currentModuleId] ?? [], $moduleUrlRules);
                $this->parseModuleConfigs($module['modules'] ?? []);
            }

            $config = $this->getModuleConfig($moduleClass, $currentModuleId);
            $moduleUrlRules = $this->prepareRules($config['urlRules'] ?? []);
            $this->urlRules[$currentModuleId] = array_merge($this->urlRules[$currentModuleId] ?? [], $moduleUrlRules);
            $this->parseModuleConfigs($config['modules'] ?? []);
        }
    }

    protected function getModuleClassName($module): string {
        if (is_object($module)) {
            $moduleClass = $module::class;
        } elseif (is_array($module)) {
            $moduleClass = $module['class'];
        } elseif (is_string($module)) {
            $moduleClass = $module;
        } else {
            $moduleClass = 'UnknownClass123456';
        }

        $this->ensureClassExists($moduleClass);

        return $moduleClass;
    }

    protected function prepareRules(array $rules): array {
        $builtRules = [];
        $verbs = 'GET|HEAD|POST|PUT|PATCH|DELETE|OPTIONS';
        foreach ($rules as $key => $rule) {
            if (is_string($rule)) {
                $rule = ['route' => $rule];
                if (preg_match("/^((?:($verbs),)*($verbs))\\s+(.*)$/", (string)$key, $matches)) {
                    $rule['verb'] = explode(',', (string)$matches[1]);
                    // rules that are not applicable for GET requests should not be used to create URLs
                    if (!in_array('GET', $rule['verb'], true)) {
                        $rule['mode'] = UrlRule::PARSING_ONLY;
                    }
                    $key = $matches[4];
                }
                $rule['pattern'] = $key;
            }
            $builtRules[] = $rule;
        }

        return $builtRules;
    }

    protected function ensureClassExists($moduleClass): void {
        if (!class_exists($moduleClass)) {
            throw new UnknownClassException($moduleClass);
        }
    }

    protected function getModuleConfig(string $moduleClass, string $currentModuleId): array {
        $configPath = $this->getConfigPath($moduleClass);

        $app = $this->application;
        /** @noinspection PhpUnusedLocalVariableInspection can be needed in configs */
        $currentModule = $app;
        if (file_exists($configPath)) {
            $config = include($configPath);
        } else {
            $config = [];
        }

        if (!is_array($config)) {
            throw new InvalidConfigException(sprintf('Module "%s" doesn\'t configured properly. See %s.', $currentModuleId, $configPath));
        }

        return $config;
    }

    protected function sortUrlRules(): array {
        $sortedUrlRules = [];
        //reversing is necessary for proper url rules sorting
        $modulesOrder = array_reverse($this->application->modulesOrder);
        foreach ($modulesOrder as $moduleId) {
            if (!array_key_exists($moduleId, $this->urlRules)) {
                continue;
            }
            $sortedUrlRules[] = $this->urlRules[$moduleId];
        }

        return $this->removeDuplicates(array_merge([], ...$sortedUrlRules));
    }

    protected function removeDuplicates(array $urlRules): array {
        return array_map('unserialize', array_unique(array_map('serialize', $urlRules)));
    }

    protected function writeUrlRules(array $sortedUrlRules): bool {
        $rulesConfig = var_export($sortedUrlRules, true);

        $config = <<<TEXT
<?php
/* ATTENTION! This file was automatically generated by UrlRulesGenerator */
return $rulesConfig;
TEXT;

        return strlen($config) === file_put_contents($this->urlRulesCachePath, $config, LOCK_EX);
    }

    protected function getConfigPath(string $moduleClass): string {
        $path = dirname(realpath($this->loader->findFile($moduleClass)));
        $configName = '/main.php';
        $configPath = $path . '/Config' . $configName;

        if (file_exists($configPath)) {
            return $configPath;
        }

        return $path . '/Config/Frontend' . $configName;
    }

    protected function getUrlRulesCachePath(): string {
        return Yii::getAlias($this->_urlRulesCachePath);
    }
}
