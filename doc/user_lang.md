Language Files
======

How do they work?
---------
The **lang/** directory contains translations for use with FluxCP. The language used is controlled by the 'DefaultLanguage' setting in config/application.php.

Simply put, `'DefaultLanguage' => 'en_us'` will load the American English language file and use the contained strings wherever `Flux::message()` is used within theme files. There are a few others within the `lang/` directory, but unfortunately they aren't maintained.


How do we use them?
---------
For example, in a theme file that is displaying whether a players' character is male or female, you would see `<?php echo Flux::message('GenderTypeMale') ?>` or `<?php echo Flux::message('GenderTypeFemale') ?>`.

The menus that are defined in `config/application.php` are set to automatically use the language files. For example, lets look at this specific menu:

```
'MenuItems'		=> array(
		'MainMenuLabel'		=> array(
			'HomeLabel'			=> array('module' => 'main'),
			'NewsLabel'			=> array('module' => 'news'),
		),
),
```

When the page is rendered, you will see that these strings are replaced with their counterparts from the language file.
'MainMenuLabel' becomes 'Main Menu', 'HomeLabel' becomes 'Home', 'NewsLabel' becomes 'News'.


Common Misuse
---------
Many people still think that the 'Label' portion of these strings should be removed within the config file as it's outputting 'HomeLabel' to the page instead of 'Home'. **This is incorrect.** This simply means that the theme you're using was built earlier than August 2014 and you shouldn't be using it.
