# silverstripe-dependentdropdownfield

A SilverStripe dropdown field that has its options populated via ajax, based on the value of the field it depends on.

## Requirements

SilverStripe 6

## Installation

```
composer require sheadawson/silverstripe-dependentdropdownfield
```

## Usage example

### With dropdown field
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
	$fieldOne = DropdownField::create('FieldOneID', 'Field One', ['one' => 'One', 'two' => 'Two']),
	// 3. Add your DependentDropdownField, setting the source as the callable function
	// you created and setting the field it depends on to the appropriate field
	DependentDropdownField::create('FieldTwoID', 'Field Two', $datesSource)->setDepends($fieldOne)
);
```

### With listbox field
```php
// 1. Create a callable function that returns an array of options for the DependentListboxField.
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
	$fieldOne = ListboxField::create('FieldOneManyManyRelation', 'Field One', [1 => 'One', 2 => 'Two']),
	// 3. Add your DependentListboxField, setting the source as the callable function
	// you created and setting the field it depends on to the appropriate field
	DependentListboxField::create('FieldTwoManyManyRelation', 'Field Two', $datesSource)->setDepends($fieldOne)
);
```
