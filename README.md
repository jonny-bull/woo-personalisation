# WooCommerce Personalisation Blocks

A skeleton plugin to be used as the base for new Box UK WordPress plugins.

Built using [the BoxUK plugin skeleton](https://github.com/boxuk/wp-plugin-skeleton).

Blocks included:
- user's recently viewed products
- store's most popular products over a given timeframe
- frequently bought together products 
- alternatives for out of stock products

All blocks can be toggled via the plugin setting page.
## Recently viewed products

Displays a user's recently viewed products. By default, four are displayed.

Can be added to pages via:
- the Recently Viewed Products Gutenberg block
- the `[personalisation-recently-viewed]` shortcode
- calling the `woo_personalisation_recently_viewed` function in a theme template

## Most popular products

Displays the store's four most popular products over the past 30 days.

Both the timeframe and number of products can be changed in settings.

Can be added to pages via: 
- the Most Popular Products Gutenberg block
- the `[personalisation-most-popular]` shortcode
- calling the `woo_personalisation_most_popular` function in a theme template

The threshold for most popular products can be altered via the settings page. Whether or not out of stock products are included can also be controlled here.

## Frequently bought together products

Determines the most frequently purchased together products with any given product.

By default:
- two extra products are displayed
- all products must be in stock

Each product has a list of all the products bought with it added to its admin panel. The products that will display as 'frequently bought together' are highlighted in bold. Administrators can manually filter out incompatible or unwanted products using this panel.

## Out of stock products

This block displays at the top of an out of stock product's page when alternatives have been defined.

HOW TO SET ALTERNATIVE PRODUCTS

## Examples

Functionality is in use on [OKdo.com](https://okdo.com).
