<?php

namespace Sheadawson\DependentDropdown\Forms;

use SilverStripe\Admin\LeftAndMain;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\ORM\Map;
use SilverStripe\View\Requirements;
use SilverStripe\Forms\FormField;

/**
 * Class DependentDropdownField
 *
 * A dropdown that depends on another dropdown for populating values, and calls
 * a callback when that dropdown is updated.
 *
 * @package SilverStripe\Forms
 */
class DependentDropdownField extends DropdownField
{
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
     * DependentDropdownField constructor.
     * @param string $name
     * @param string $title
     * @param \Closure $source
     * @param string $value
     * @param $form
     * @param string $emptyString
     */
    public function __construct($name, $title = null, \Closure $source = null, $value = '', $form = null, $emptyString = null)
    {
        parent::__construct($name, $title, [], $value, $form, $emptyString);

        // we are unable to store Closure as a normal source
        $this->sourceCallback = $source;
        $this
            ->addExtraClass('dependent-dropdown')
            ->addExtraClass('dropdown');
    }

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
     * @return array|\ArrayAccess|mixed
     */
    public function getSource()
    {
        $val = $this->depends->Value();

        if (
            !$val
            && method_exists($this->depends, 'getHasEmptyDefault')
            && !$this->depends->getHasEmptyDefault()
        ) {
            $dependsSource = array_keys($this->depends->getSource());
            $val = isset($dependsSource[0]) ? $dependsSource[0] : null;
        }

        if (!$val) {
            $source = [];
        } else {
            $source = call_user_func($this->sourceCallback, $val);
            if ($source instanceof Map) {
                $source = $source->toArray();
            }
        }

        if ($this->getHasEmptyDefault()) {
            return ['' => $this->getEmptyString()] + (array) $source;
        } else {
            return $source;
        }
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

    /**
     * @param array $properties
     * @return string
     */
    public function Field($properties = [])
    {
        if (!is_subclass_of(Controller::curr(), LeftAndMain::class)) {
            Requirements::javascript('silverstripe/admin:thirdparty/jquery-entwine/dist/jquery.entwine-dist.js');
        }

        Requirements::javascript(
            'sheadawson/silverstripe-dependentdropdownfield:client/js/dependentdropdownfield.js'
        );

        $this->setAttribute('data-link', $this->Link('load'));
        $this->setAttribute('data-depends', $this->getDepends()->getName());
        $this->setAttribute('data-empty', $this->getEmptyString());
        $this->setAttribute('data-unselected', $this->getUnselectedString());

        return parent::Field($properties);
    }
}
