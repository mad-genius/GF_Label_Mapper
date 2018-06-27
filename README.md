# GF_Label_Mapper

BETA: Gives you a way to reference Gravity Forms `$_REQUEST` values by field label rather than ID.

## The Problem

You've got, say, a Gravity Forms submission hook. You've written some code that uses some values posted by the form. This code needs to run on multiple installs—local, staging, production. You try to make sure your form is identical everywhere but somehow the IDs have gotten out of sync. Now your code is broken.

## The Solution

GF_Label_Mapper maps your field labels to their IDs. Since labels are more easily changed than IDs, it's easier to keep your various copies of the form in sync.

## Usage

Provide the `$_REQUEST` variable and the Form object.

```
$mapper = new GF_Label_Mapper( $_REQUEST, $form );
```

You can no access posted values like this:

```
$mapper->fields['my nice input'];
```

This is equivalent to:

```
$_REQUEST['input_14'];
```

Now it's no problem if your IDs get a little out of sync—so long as you keep your labels in sync.

### IDs for front-end code

If your code is primarily JavaScript, you can keep from using IDs in your selectors by getting a map from GF_Label_Mapper. By passing `true` as the third parameter, the mapper creates a JavaScript optimized map.

A good method for implementing this is `wp_localize_script`. So for example, you would add something like this to `functions.php`:

```
/**
 * Put this in your wp_enqueue_scripts action
 */
wp_localize_script( 'my-script', 'utilities', array(
    'input_map' => get_input_map( 'form name' )
) );
```

Then later:

```
/**
 * Lookup form inputs based on labels
 */
function get_input_map( $form_name ) {
	$forms = GFAPI::get_forms();
	
	foreach( $forms as $form ) {
		if( strtolower( $form['title'] ) !== strtolower( $form_name ) ) continue;

		$mapper = new GF_Label_Mapper( null, $form, true );
		return $mapper->map;
	}
}
```

Now your JavaScript has access to the map:

```
utilities.input_map['my nice input'];
```

As a bonus, you could do something like this (assuming you're using jQuery):

```
/**
 * Reads the GF input map and returns appropriate jQuery object.
 */
function $input(key) {
    return $(utilities.input_map[key]);
}
```

Now you can select inputs like so:

```
$input('my nice input');
```
