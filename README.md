RunroomSortableBehaviorBundle
=============================

[![Latest Stable Version](https://poser.pugx.org/runroom-packages/sortable-behavior-bundle/v/stable)](https://packagist.org/packages/runroom-packages/sortable-behavior-bundle)
[![Latest Unstable Version](https://poser.pugx.org/runroom-packages/sortable-behavior-bundle/v/unstable)](https://packagist.org/packages/runroom-packages/sortable-behavior-bundle)
[![License](https://poser.pugx.org/runroom-packages/sortable-behavior-bundle/license)](https://packagist.org/packages/runroom-packages/sortable-behavior-bundle)

[![Total Downloads](https://poser.pugx.org/runroom-packages/sortable-behavior-bundle/downloads)](https://packagist.org/packages/runroom-packages/sortable-behavior-bundle)
[![Monthly Downloads](https://poser.pugx.org/runroom-packages/sortable-behavior-bundle/d/monthly)](https://packagist.org/packages/runroom-packages/sortable-behavior-bundle)
[![Daily Downloads](https://poser.pugx.org/runroom-packages/sortable-behavior-bundle/d/daily)](https://packagist.org/packages/runroom-packages/sortable-behavior-bundle)

This bundle gives the ability to define sortable entities and to be able to sort the using Sonata Backoffice. It is inspired on: [pixSortableBehaviorBundle](https://github.com/pix-digital/pixSortableBehaviorBundle).

## Installation

Open a command console, enter your project directory and execute the following command to download the latest stable version of this bundle:

```bash
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

## Usage

This bundle checks if you are using [Gedmo Sortable](https://github.com/doctrine-extensions/DoctrineExtensions/blob/main/doc/sortable.md) to handle the sort order of your entities, if not, it will use the default ORM implementation, where you will need to add entities on the configuration of the bundle manually. If you are using Gedmo, and don't want to change the default field name `position`, you don't need to configure anything for the bundle.

We provide a trait, so you can easily add the position field with the Gedmo configuration on each entity you want to be able to sort:

```php
# src/Entity/Example.php

namespace App\Entity;

use Runroom\SortableBehaviorBundle\Behaviors\Sortable;

class Example
{
    use Sortable;
    // ... rest of your class
}
```

And then, on your admin class, you can add the `SortableAdminTrait` trait to be able to sort the entities on the list view:

```php
# src/Admin/ExampleAdmin.php

namespace App\Admin;

use Runroom\SortableBehaviorBundle\Admin\SortableAdminTrait;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;

class ExampleAdmin extends AbstractAdmin
{
    use SortableAdminTrait;

    protected function configureListFields(ListMapper $list): void
    {
        $list
            // ... rest of your list fields
            ->add(ListMapper::NAME_ACTIONS, ListMapper::TYPE_ACTIONS, [
                'actions' => [
                    // ... rest of your actions
                    'move' => [
                        'template' => '@RunroomSortableBehavior/sort.html.twig',
                    ],
                ],
            ]);
    }
}
```

And that's all, you should now see the sort buttons on the list view of your admin class.

### Configuration

```yaml
# config/packages/runroom_sortable_behavior.yaml

runroom_sortable_behavior:
    # position_handler can be any service id that implements the PositionHandlerInterface
    position_handler: Runroom\SortableBehaviorBundle\Service\GedmoPositionHandler # or Runroom\SortableBehaviorBundle\Service\ORMPositionHandler if gedmo is not found
    position_field:
        default: position # Default field name for the position
        # Only needed when not using Gedmo
        entities:
            App\Entity\Foobar: order
            App\Entity\Baz: rang
    # Only needed when not using Gedmo
    sortable_groups:
        entities:
            App\Entity\Baz: [group]
```

### Use a draggable list instead of up/down buttons

In order to use a draggable list instead of up/down buttons, change the template in the `move` action to `@RunroomSortableBehavior/sort_drag_drop.html.twig`.

```php
protected function configureListFields(ListMapper $list): void
{
    $list
        // ... rest of your list fields
        ->add(ListMapper::NAME_ACTIONS, ListMapper::TYPE_ACTIONS, [
            'actions' => [
                // ... rest of your actions
                'move'   => [
                    'template' => '@RunroomSortableBehavior/sort_drag_drop.html.twig',
                    'enable_top_bottom_buttons' => true, // optional
                ],
            ],
        ]);
}
```

## Contribute

The sources of this package are contained in the Runroom monorepo. We welcome contributions for this package on [runroom/runroom-packages](https://github.com/Runroom/runroom-packages).

## License

This bundle is under the [MIT license](LICENSE).
