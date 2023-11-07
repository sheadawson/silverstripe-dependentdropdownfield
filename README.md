# silverstripe-dependentdropdownfield

A SilverStripe dropdown field that has its options populated via ajax, based on the value of the field it depends on.

## Requirements

SilverStripe 4 || 5

## Installation

```
composer require sheadawson/silverstripe-dependentdropdownfield
```

## Usage example

```php
// 1. Create a callable function that returns an array of options for the DependentDropdownField.
// When the value of the field it depends on changes, this function is called passing the
// updated value as the first parameter ($val)
$datesSource = function($val) {
	if ($val == 'one') {
		// return appropriate options array if the value is one.
	}
	if ($val == 'two') {
		// return appropriate options array if the value is two.
	}
};

$fields = FieldList::create(
	// 2. Add your first field to your field list,
	$fieldOne = DropdownField::create('FieldOne', 'Field One', ['one' => 'One', 'two' => 'Two']),
	// 3. Add your DependentDropdownField, setting the source as the callable function
	// you created and setting the field it depends on to the appropriate field
	DependentDropdownField::create('FieldTwo', 'Field Two', $datesSource)->setDepends($fieldOne)
);
```

You can also create a ListboxField with `DependentListboxField`.
