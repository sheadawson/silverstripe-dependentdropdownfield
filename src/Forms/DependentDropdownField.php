<?php

namespace Sheadawson\DependentDropdown\Forms;

use Closure;
use Sheadawson\DependentDropdown\Traits\DependentFieldTrait;
use SilverStripe\Admin\LeftAndMain;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\DropdownField;
use SilverStripe\View\Requirements;

/**
 * Class DependentDropdownField.
 *
 * A dropdown that depends on another dropdown for populating values, and calls
 * a callback when that dropdown is updated.
 */
class DependentDropdownField extends DropdownField
{
    use DependentFieldTrait;

    /**
     * DependentDropdownField constructor.
     *
     * @param string $name
     * @param string $title
     * @param Closure $source
     * @param string $value
     * @param         $form
     * @param string $emptyString
     */
    public function __construct($name, $title = null, ?Closure $source = null, $value = '', $form = null, $emptyString = null)
    {
        parent::__construct($name, $title, [], $value, $form, $emptyString);

        // we are unable to store Closure as a normal source
        $this->setSourceCallback($source);
        $this
            ->addExtraClass('dependent-dropdown')
            ->addExtraClass('dropdown');
    }

    /**
     * @param array $properties
     *
     * @return string
     */
    public function Field($properties = [])
    {
        if (!is_subclass_of(Controller::curr(), LeftAndMain::class)) {
            Requirements::javascript('silverstripe/admin:thirdparty/jquery-entwine/jquery.entwine.js');
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
