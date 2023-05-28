<?php

namespace core\base;

/**
 * Component is the base class that implements the *property*, *event* and *behavior* features.
 *
 * @see BehaviorTrait
 * @see BaseObject
 *
 * @author Dmitry Kolodko <dangel@bitfocus.com>
 */
class Component extends BaseObject {
    use BehaviorTrait;
}