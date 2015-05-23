# Metaslider as page option
WordPress plugin, adds an extra metabox to your pages with a dropdown to select the metaslider for the specific page.

This plugin works with the metaslider plugin https://wordpress.org/plugins/ml-slider/

Instead of using the shortcode in the editor it gives you the option to add a dropdown in the page form to select the slider that should display in that specific page. 
And then just calling the function from your theme file (header.php for example) to implement the slider.

# Usage:
Just install the plugin, activate and add the following code in your theme where you wish to display the slider.

```php
if( class_exists( 'MTSPageOption') ){
	MTSPageOption::get_slider();
}
```


