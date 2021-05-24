RunroomSortableBehaviorBundle
========================

[![Latest Stable Version](https://poser.pugx.org/runroom-packages/sortable-behavior-bundle/v/stable)](https://packagist.org/packages/runroom-packages/sortable-behavior-bundle)
[![Latest Unstable Version](https://poser.pugx.org/runroom-packages/sortable-behavior-bundle/v/unstable)](https://packagist.org/packages/runroom-packages/sortable-behavior-bundle)
[![License](https://poser.pugx.org/runroom-packages/sortable-behavior-bundle/license)](https://packagist.org/packages/runroom-packages/sortable-behavior-bundle)

[![Total Downloads](https://poser.pugx.org/runroom-packages/sortable-behavior-bundle/downloads)](https://packagist.org/packages/runroom-packages/sortable-behavior-bundle)
[![Monthly Downloads](https://poser.pugx.org/runroom-packages/sortable-behavior-bundle/d/monthly)](https://packagist.org/packages/runroom-packages/sortable-behavior-bundle)
[![Daily Downloads](https://poser.pugx.org/runroom-packages/sortable-behavior-bundle/d/daily)](https://packagist.org/packages/runroom-packages/sortable-behavior-bundle)

This bundle gives the ability to define sortable entities and to be able to sort the using Sonata Backoffice. It is inspired on: [pixSortableBehaviorBundle](https://github.com/pix-digital/pixSortableBehaviorBundle).

## Installation

Open a command console, enter your project directory and execute the following command to download the latest stable version of this bundle:

```
composer require runroom-packages/sortable-behavior-bundle
```

### Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles in `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    Runroom\SortableBehaviorBundle\RunroomSortableBehaviorBundle::class => ['all' => true],
];
```

### Configure Twig

```yaml
// config/packages/twig.yaml
...
    paths:
        '%kernel.project_dir%/vendor/runroom-packages/sortable-behavior-bundle/src/Resources/views': SortableBehavior
```

## License

This bundle is under the [MIT license](LICENSE).
