Using a Custom Theme
======

How does it work?
---------
The Theme System in FluxCP is based on an "inheritance structure". In simple terms, this means you only need to add files to your new theme folder that you want to change.

It all works similar to the config import system in rAthena. The default theme is read first, then if there are any files matching the required view in the custom theme, then it gets loaded instead. This means that that **you don't need to copy/paste the default theme every time you create a new custom theme**.

The manifest.php file controls inheritance with `'inherit'     => 'default',`.

How should my theme look?
---------
This is an example directory structure for a custom theme in a fresh install of FluxCP:
```
.
├── addons
├── config
├── data
├── doc
├── lang
├── lib
├── modules
├── themes
|   ├── bootstrap
|   └── cust_theme1
|       └── css
|           ├── flux.css
|           └── customstyle.css
|       └── img
|           ├── bg.jpg
|           └── logo.png
|       └── js
|           ├── flux.unitip.js
|           └── ie9.js
|       └── main
|           ├── index.php
|           └── sidebar.php
|       ├── footer.php
|       ├── header.php
|       └── manifest.php
|   ├── default
|   └── installer
├── .gitignore
├── .htaccess
├── LICENSE
├── README.md
├── error.php
└── index.php
```

As you can see, there are only a few files in the **cust_theme1** folder.


How do I make it display on my website?
---------
To enable your theme, simply add it to the theme array in /config/application.php:
```'ThemeName'					=> array('default', 'bootstrap', 'cust_theme1'),```

If you want your new theme to always display and remove the theme selection box in the footer, remove the other themes from this array so it looks like:
```'ThemeName'					=> array('cust_theme1'),```


How do I know if a theme I downloaded will work?
---------
As a general rule of thumb, if your new theme has a `manifest.php` file in the theme folder, it will work with current versions of FluxCP just fine.

If it doesn't have `manifest.php`, you will need to create one. This will make the new theme able to load, but you will still have problems.

In the past, even after this theme system was introduced, theme designers have still opted to create themes reliant on old versions of FluxCP. They are lazy. Use at your own risk.
