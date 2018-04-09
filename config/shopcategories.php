<?php
// These are categories for the item shop. Pay close attention to the numbers,
// as these numbers are stored in the database when you add an item to a specific
// category, so it knows which category the item belongs to.
// You may add/remove categories from here as you see fit.
return array(
	0 => 'Headgears',      // Headgears category, most likely doesn't include wings.
	1 => 'Wings',          // Wings category, though most of the time wings are headgear, this is more dedicated.
	2 => 'Armors',         // Armors category, most likely doesn't include headgears.
	3 => 'Weapons',        // Weapons category.
	4 => 'Healing Items',  // Good category to place healing items under, e.g., Yggdrasil Berries and the like.
	5 => 'Pets',           // Some people may choose to sell pet eggs or pet-related items under here.
	6 => 'Miscellaneous',  // Anything you can't really categorize, you can put under this category.
	7 => 'Cards'           // Cards category.
);
?>
