<?php
/**
 * A dropdown that depends on another dropdown for populating values, and calls
 * a callback when that dropdown is updated.
 *
 * @package callbackdropdownfield
 */
class DependentDropdownField extends DropdownField {

	public static $allowed_actions = array(
		'load'
	);

	protected $depends;
	protected $unselected;

	public function load($request) {
		$response = new SS_HTTPResponse();
		$response->addHeader('Content-Type', 'application/json');
		$response->setBody(Convert::array2json(call_user_func(
			$this->source, $request->getVar('val')
		)));
		return $response;
	}

	public function getDepends() {
		return $this->depends;
	}

	public function setDepends(FormField $field) {
		$this->depends = $field;
		return $this;
	}

	public function getUnselectedString() {
		return $this->unselected;
	}

	public function setUnselectedString($string) {
		$this->unselected = $string;
		return $this;
	}

	public function getSource() {
		if(!is_callable($this->source)) {
			return parent::getSource();
		}

		if(!$val = $this->depends->Value()) {
			$source = array();
		} else {
			$source = call_user_func($this->source, $val);
		}

		if($this->getHasEmptyDefault()) {
			return array('' => $this->getEmptyString()) + (array) $source;
		} else {
			return $source;
		}
	}

	public function Field($properties = array()) {
		Requirements::javascript(THIRDPARTY_DIR . '/jquery/jquery.js');
		Requirements::javascript(THIRDPARTY_DIR . '/jquery-entwine/dist/jquery.entwine-dist.js');
		Requirements::javascript('dependentdropdownfield/javascript/dependentdropdownfield.js');

		$this->addExtraClass('dependent-dropdown');
		$this->addExtraClass('dropdown');
		$this->setAttribute('data-link', $this->Link('load'));
		$this->setAttribute('data-depends', $this->getDepends()->getName());
		$this->setAttribute('data-empty', $this->getEmptyString());
		$this->setAttribute('data-unselected', $this->getUnselectedString());

		return parent::Field($properties);
	}

}