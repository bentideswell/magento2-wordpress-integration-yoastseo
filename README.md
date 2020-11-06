# FishPig_WordPress_Yoast

A free Magento 2 module that integrates the Yoast SEO WordPress plugin with WordPress Integration.

# Installation

This module required <a href="https://fishpig.co.uk/magento/wordpress-integration/" target="_blank">FishPig_WordPress</a> be installed.

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

- <a href="https://fishpig.co.uk/magento/wordpress-integration/multisite/" target="_blank">FishPig_WordPress_Multisite</a>
- <a href="https://fishpig.co.uk/magento/wordpress-integration/root/" target="_blank">FishPig_WordPress_Root</a>
- <a href="https://fishpig.co.uk/magento/wordpress-integration/post-types-taxonomies/" target="_blank">FishPig_WordPress_PostTypeTaxonomy</a>
- <a href="https://fishpig.co.uk/magento/wordpress-integration/shortcodes-widgets/" target="_blank">FishPig_WordPress_ShortcodesWidgets</a>
- <a href="https://fishpig.co.uk/magento/wordpress-integration/advanced-custom-fields/" target="_blank">FishPig_WordPress_ACF</a>
- <a href="https://fishpig.co.uk/magento/wordpress-integration/related-products/" target="_blank">FishPig_WordPress_RelatedProducts</a>
- <a href="https://fishpig.co.uk/magento/wordpress-integration/customer-synchronisation/" target="_blank">FishPig_WordPress_CustomerSynchronisation</a>
- <a href="https://fishpig.co.uk/magento/wordpress-integration/integrated-search/" target="_blank">FishPig_WordPress_IntegratedSearch</a>
- <a href="https://fishpig.co.uk/magento/wordpress-integration/amp/" target="_blank">FishPig_WordPress_AMP</a>

For an up to date list, check the <a href="https://fishpig.co.uk/magento/wordpress-integration/add-ons/" target="_blank">Magento 2 WordPress Integration Add-ons page</a>.
