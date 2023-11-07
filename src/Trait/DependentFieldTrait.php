<?php

namespace Sheadawson\DependentDropdown\Forms;

use SilverStripe\Control\HTTPResponse;
use SilverStripe\Forms\FormField;

trait DependentFieldTrait {
    /**
     * @var array
     */
    private static $allowed_actions = [
        'load',
    ];

    /**
     * @var
     */
    protected $depends;

    /**
     * @var
     */
    protected $unselected;

    /**
     * @var \Closure
     */
    protected $sourceCallback;

    /**
     * @param $request
     * @return HTTPResponse
     */
    public function load($request)
    {
        $response = new HTTPResponse();
        $response->addHeader('Content-Type', 'application/json');

        $items = call_user_func($this->sourceCallback, $request->getVar('val'));
        $results = [];
        if ($items) {
            foreach ($items as $k => $v) {
                $results[] = ['k' => $k, 'v' => $v];
            }
        }

        $response->setBody(json_encode($results));

        return $response;
    }

    /**
     * @return mixed
     */
    public function getDepends()
    {
        return $this->depends;
    }

    /**
     * @param FormField $field
     * @return $this
     */
    public function setDepends(FormField $field)
    {
        $this->depends = $field;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUnselectedString()
    {
        return $this->unselected;
    }

    /**
     * @param $string
     * @return $this
     */
    public function setUnselectedString($string)
    {
        $this->unselected = $string;

        return $this;
    }

     /**
     * @param \Closure $source
     * @return $this
     */
    public function setSource($source)
    {
        $this->sourceCallback = $source;
        return $this;
    }
}
