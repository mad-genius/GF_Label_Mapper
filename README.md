# GF_Label_Mapper

BETA: Gives you a way to reference Gravity Forms `$_REQUEST` values by field label rather than ID.

## The Problem

You've got, say, a Gravity Forms submission hook. You've written some code that uses some values posted by the form. This code needs to run on multiple installs—local, staging, production. You try to make sure your form is identical everywhere but somehow the IDs have gotten out of sync. Now your code is broken.

## The Solution

GF_Label_Mapper maps your field labels to their IDs. Since labels are more easily changed than IDs, it's easier to keep your various copies of the form in sync.

## Usage

Provide the Form object and the `$_REQUEST` array.

```php
$mapper = new GF_Label_Mapper( $form, $_REQUEST );
```

You can now access posted values like this:

```php
$mapper->fields['my nice input'];
```

This is equivalent to:

```php
$_REQUEST['input_14'];
```

Now it's no problem if your IDs get a little out of sync—so long as you keep your labels in sync.

### Parameters

**`$form`** (required) - Gravity Forms [Form Object](https://docs.gravityforms.com/form-object/)

**`$request`** (optional) - the `$_REQUEST`, `$_POST`, or `$_GET` array containing the form-submitted values. Default: `NULL`.

**`$for_js`** (optional) - `bool` flag indicating whether the map should be optimized for JavaScript usage. Default: `false`.

### `map` and `fields`

GF_Label_Mapper provides two arrays. The `fields` array contains labels mapped to posted values:

```
Array
(
    [name] => Gus Maiden
)
```

Which you can access like:

```
$mapper->fields['name']; // Gus Maiden
```

For the `fields` array to be available, you must provide both the `$_REQUEST` (or `$_POST`) array and the Form object.

The `map` array, on the other hand, is made available by passing in, at minimum, the Form object. It provides a map of field labels to IDs:

```
Array
(
    [name] => input_1
)
```

Which you can access like:

```
$mapper->map['name']; // input_1
```

This is useful in cases where you need to reference a field by ID.

### IDs for front-end code

If your code is primarily JavaScript, you can keep from using IDs in your selectors by getting a map from GF_Label_Mapper. By passing `true` as the third parameter, the mapper creates a JavaScript optimized map.

```
$mapper = new GF_Label_Mapper( $form, null, true );

$mapper->map['name']; // #input_3_1 (form 3, input 1)
```

A good method for implementing this is `wp_localize_script`. So for example, you would add something like this to `functions.php`:

```php
/**
 * Put this in your wp_enqueue_scripts action
 */
wp_localize_script( 'my-script', 'utilities', array(
    'input_map' => get_input_map( 'form name' )
) );
```

Then later:

```php
/**
 * Lookup form inputs based on labels
 */
function get_input_map( $form_name ) {
	$forms = GFAPI::get_forms();
	
	foreach( $forms as $form ) {
		if( strtolower( $form['title'] ) !== strtolower( $form_name ) ) continue;

		$mapper = new GF_Label_Mapper( $form, null, true );
		return $mapper->map;
	}
}
```

Now your JavaScript has access to the map:

```js
utilities.input_map['my nice input'];
```

As a bonus, you could do something like this (assuming you're using jQuery):

```js
/**
 * Reads the GF input map and returns appropriate jQuery object.
 */
function $input(key) {
    return $(utilities.input_map[key]);
}
```

Now you can select inputs like so:

```js
$input('my nice input');
```
