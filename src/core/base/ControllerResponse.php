<?php

namespace core\base;

use Stringable;
use yii\base\Arrayable;
use yii\base\ArrayableTrait;

/**
 * Designed to simplify controllers actions response building by providing OO interface for common actions required to build action response.
 * Implements {@link Arrayable} interface so will be converted to array by Yii response and transformed into format specified in {@link setFormat()}
 * or into default response format.
 *
 * @package core\base
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
class ControllerResponse extends BaseObject implements Arrayable, Stringable {
    use ArrayableTrait;

    protected $_name;
    protected $_message;
    protected $_code;
    protected $_status;
    protected $_content;
    protected $_successful = true;

    public function __toString(): string {
        return (string)json_encode([
            'success' => $this->_successful,
            'data' => $this->prepareDataField(),
        ]);
    }

    public function setFormat($format) {
        $this->serviceLocator->response->format = $format;

        return $this;
    }

    public function setMessage($message) {
        $this->_message = $message;

        return $this;
    }

    public function setName($name) {
        $this->_name = $name;

        return $this;
    }

    public function setCode($code) {
        $this->_code = $code;

        return $this;
    }

    public function setStatus($status) {
        $this->_status = $status;

        return $this;
    }

    public function setContent($content) {
        $this->_content = $content;

        return $this;
    }

    public function flagAsSuccessful() {
        $this->_successful = true;

        return $this;
    }

    public function flagAsFailed() {
        $this->_successful = false;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function fields() {
        $data = $this->prepareDataField();

        return [
            'success' => fn() => $this->_successful,
            'data' => function () use (&$data) {
                return $data;
            },
        ];
    }

    /**
     * @return array
     */
    protected function prepareDataField(): array {
        $data = [];

        if (!$this->_successful) {
            $data['name'] = $this->_name;
            $data['message'] = $this->_message;
            $data['code'] = $this->_code;
            $data['status'] = $this->_status;
        }

        $data['content'] = $this->_content;

        return $data;
    }
}
