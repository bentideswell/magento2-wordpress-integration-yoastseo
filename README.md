# FishPig_WordPress_Yoast

A free Magento 2 module that integrates the Yoast SEO WordPress plugin with WordPress Integration.

# Installation

This module required <a href="https://fishpig.com/magento-2-wordpress-integration" target="_blank">FishPig_WordPress</a> be installed.

## Composer
```
composer require fishpig/magento2-wordpress-integration-yoastseo
bin/magento module:enable FishPig_WordPress_Yoast && bin/magento setup:upgrade --keep-generated
```

## Manual
```
mkdir -p app/code/FishPig && cd app/code/FishPig && \
curl -sS https://codeload.github.com/bentideswell/magento2-wordpress-integration-yoastseo/zip/master -o master.zip && \
unzip -q master.zip && rm -rf master.zip && mv magento2-wordpress-integration-yoastseo-master WordPress_Yoast && \
cd - && bin/magento module:enable FishPig_WordPress_Yoast && bin/magento setup:upgrade --keep-generated
```

# WordPress Integration Add-ons

The following add-ons are currently available and more are on the way.

- <a href="https://fishpig.com/magento-2-wordpress-integration/multisite" target="_blank">Multisite</a>
- <a href="https://fishpig.com/magento-2-wordpress-integration/root" target="_blank">Root</a>
- <a href="https://fishpig.com/magento-2-wordpress-integration/post-types-taxonomies" target="_blank">Post Types and Taxonomies</a>
- <a href="https://fishpig.com/magento-2-wordpress-integration/shortcodes" target="_blank">Shortcodes</a>
- <a href="https://fishpig.com/magento-2-wordpress-integration/advanced-custom-fields" target="_blank">Advanced Custom Fields (ACF)</a>
- <a href="https://fishpig.com/magento-2-wordpress-integration/related-products" target="_blank">Related Products</a>
- <a href="https://fishpig.com/magento-2-wordpress-integration/integrated-search" target="_blank">Integrated Search</a>
- <a href="https://fishpig.com/magento-2-wordpress-integration/wpml" target="_blank">WPML</a>

For an up to date list, check the <a href="https://fishpig.com/magento-2-wordpress-integration/add-ons" target="_blank">Magento 2 WordPress Integration Add-ons</a> page.
