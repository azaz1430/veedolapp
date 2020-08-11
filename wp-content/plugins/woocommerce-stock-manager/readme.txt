=== WooCommerce Stock Manager ===
Contributors: storeapps, niravmehta, mansi shah, Tarun.Parswani, Musilda
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=CPTHCDC382KVA
Author URI: https://www.storeapps.org/
Plugin URI: https://www.storeapps.org/woocommerce-plugins/?utm_source=wprepo&utm_medium=web&utm_campaign=wsm_readme
Tags: woocommerce, stock manager, stock management, inventory
Requires at least: 5.0.0
Tested up to: 5.4.2
Requires PHP: 5.6+
Stable tag: 2.4.0
License: GPLv3

== Description ==

WooCommerce Stock Manager allows you manage stock for products and their variables from one screen. 

Plugin is compatible with WordPress 5.0+ and is tested on 5.2.2. vesrion 
Plugin is compatible with WooCommerce 3.5+ and is tested on 3.6.4 version. 

GDPR ready - plugin don't collect personal data.

**What you can do using WooCommerce Stock Manager:**

* You can set "Manage stock" for each product and variation
* You can set "Stock status" for each product and variation
* You can set "Backorders" for each product and variation
* You can set "Stock" for each product and variation
* You can set "Price" for each product and variation
* You can set "Sale price" for each product and variation
* You can set "Tax status" for each product and variation
* You can set "Tax class" for each product and variation
* You can set "Shipping class" for each product and variation
* You can set "Sku" for each product and variation
* You can set "Product name" for each product and variable product
* You can display product thumbnail image.
* You can filter products by type, category, stock manage or stock status.
* You can sort products by name or sku.
* Searching products by name or sku.
* For better usabillity it is possible hide some table cells. 
* Variants for variable product is possible to edit after click on "Show variables" button.
* Each product or variation, can be save separatelly, or you can save all displayed data.

**Product stock history**

In Stock log page, you can filter product and see the products stock history. 

**Import/Export**

With this plugin, it is possible export all stock data from your shop, edit them and import back with csv file.
(This feature needs refactoring, so use this only on your own risk).

Export file structure:

SKU - product unique identificator, required.
Manage stock - values: "yes", "notify", "no". If is empty "no" will be save.
Stock status - values: "instock", "outofstock". If is empty "outofstock" will be save.
Backorders - values: "yes", "notify", "no". If is empty "no" will be save.
Stock - quantity value.
Product type - type of product.
Parent SKU - if product is variant, parent product SKU is displayed for better filtering csv file.

Roadmap:

Bulk editing.
Display stock history in visually graph.
Hooks for the 3rd party plugins.

**Some of our other free plugins**

1. [Smart Manager For WooCommerce](https://wordpress.org/plugins/smart-manager-for-wp-e-commerce/) - Manage and bulk edit WooCommerce products, variations, orders, coupons, any WordPress post type. All from a single screen using Excel-Like Spreadsheet.
2. [Temporary Login Without Password](https://wordpress.org/plugins/temporary-login-without-password/)
3. [Icegram](https://wordpress.org/plugins/icegram/) - Popups, Welcome Bar, Optins and Lead Generation Plugin
4. [Email Subscribers & Newsletters](https://wordpress.org/plugins/email-subscribers/)

**StoreApps’ other Pro plugins**

1. [Bulk Variations Manager](https://www.storeapps.org/product/bulk-variations-manager/?utm_source=wprepo&utm_medium=web_bvm&utm_campaign=wsm_readme)
2. [WooCommerce One Click Upsell](https://www.storeapps.org/how-to-create-1-click-upsells-in-woocommerce/?utm_source=wprepo&utm_medium=web_mb&utm_campaign=wsm_readme)
3. [Smart Offers For WooCommerce](https://www.storeapps.org/product/smart-offers/?utm_source=wprepo&utm_medium=web_mb&utm_campaign=wsm_readme)
4. [Email Customizer for WooCommerce](https://www.storeapps.org/product/smart-emails/?utm_source=wprepo&utm_medium=web_se&utm_campaign=wsm_readme)
5. [WooCommerce Name Your Price Plugin](https://www.storeapps.org/product/offer-your-price/?utm_source=wprepo&utm_medium=web_se&utm_campaign=smwp_readme)
6. [Frequently Bought Together For WooCommerce](https://www.storeapps.org/product/frequently-bought-together-woocommerce/?utm_source=wprepo&utm_medium=web_se&utm_campaign=smwp_readme)
7. [Express Checkout For WooCommerce](https://www.storeapps.org/product/express-checkout-for-woocommerce/?utm_source=wprepo&utm_medium=web_se&utm_campaign=smwp_readme)
8. [WooCommerce Update Variations In Cart](https://www.storeapps.org/product/woocommerce-update-variations-in-cart/?utm_source=wprepo&utm_medium=web_se&utm_campaign=smwp_readme)
9. [Express Login For WordPress](https://www.storeapps.org/product/express-login-for-wordpress/?utm_source=wprepo&utm_medium=web_se&utm_campaign=smwp_readme)

[Check out all our WooCommerce plugins and bundles](https://www.storeapps.org/woocommerce-plugins/?utm_source=wprepo&utm_medium=web_others&utm_campaign=wsm_readme)

== Installation ==

= Using The WordPress Dashboard =

1. Navigate to the Plugins > Add New in the WordPress dashboard
2. Search for 'WooCommerce Stock Manager'
3. Click 'Install Now'
4. Activate the plugin

= Uploading in WordPress Dashboard =

1. Navigate to the Plugins > Add New in the WordPress dashboard
2. Navigate to the 'Upload' area
3. Select `woocommerce-stock-manager.zip` from your computer
4. Click 'Install Now'
5. Activate the plugin

== Frequently Asked Questions ==

= How to show hide columns in the Stock Manager Table? =

Go to WooCommerce Stock Manager and on 'Screen Options' to show/hide any columns in the Stock Manager Table.

= Quantity change not working. =

Be sure, that you have active stock manage.

= Plugin not working. =

Be sure, that you have WooCommerce 3.5+ and WordPress 5.+

== Screenshots ==

1. Edit stock product data
2. Import/Export
3. See product edit history
4. Product stock as on date

== Changelog ==

= 2.4.0 (07.08.2020) =
* New: WooCommerce 4.3.1 compatible
* New: Provision to show/hide 'SKU' column
* Fix: 'Create export File' not exporting all the products in the generated CSV
* Update: Title to product thumbnail column
* Update: Disable autoload of few options
* Update: Product icon
* Update: POT file

= 2.3.0 (20.06.2020) =
* Fix: Product Variation thumbnail not loading in dashboard
* Update: Updated POT file
* Fix: Minor fixes

= 2.2.0 (06.06.2020) =
* Update: Compatibility with latest versions of WordPress & WooCommerce (v4.2+)
* Update: Product low stock handling as per WooCommerce low stock threshold setting
* Update: WooCommerce Stock Manager menu position
* Fix: Default delimiter not set to "," for Export CSV
* Fix: Product variation Id displaying as blank in exported CSV
* Fix: Dashboard going blank after enabling the 'thumbnail' column in some cases
* Update: Updated POT file

= 2.1.1 (02.05.2020) =
* Update: Added stock related columns in the dashboard by default
* Update: Compatibility with latest version of WooCommerce
* Update: Added option for 'No Shipping Class' for 'Shipping Class' product field
* Fix: 'Sale Price' product field showing as blank in case of '0' value 
* Fix: Table headers of the Stock Manager dashboard table not translating
* Update: Updated POT file

= 2.1.0 (06.04.2020) =
* New: New contributor's name added
* New: Added POT file
* Update: Display the date in localized format in Stock log history
* Update: Removed language specific translation files

**Earlier Versions**

For the changelog of earlier versions, please refer to the separate [changelog.txt](https://plugins.svn.wordpress.org/woocommerce-stock-manager/trunk/changelog.txt) file.
