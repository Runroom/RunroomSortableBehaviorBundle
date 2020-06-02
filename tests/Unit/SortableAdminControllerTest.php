<?php

declare(strict_types=1);

/*
 * This file is part of the Runroom package.
 *
 * (c) Runroom <runroom@runroom.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Runroom\SortableBehaviorBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Runroom\SortableBehaviorBundle\Controller\SortableAdminController;
use Runroom\SortableBehaviorBundle\Services\PositionHandlerInterface;
use Runroom\SortableBehaviorBundle\Tests\Fixtures\ChildSortableEntity;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Admin\BreadcrumbsBuilderInterface;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\AdminBundle\Templating\TemplateRegistry;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Contracts\Translation\TranslatorInterface;

class SortableAdminControllerTest extends TestCase
{
    use ProphecyTrait;

    /** @var PropertyAccessor */
    private $propertyAccessor;

    /** @var ObjectProphecy<PositionHandlerInterface> */
    private $positionHandler;

    /** @var ObjectProphecy<ContainerInterface> */
    private $container;

    /** @var ObjectProphecy<AdminInterface> */
    private $admin;

    /** @var Request */
    private $request;

    /** @var SortableAdminController */
    private $controller;

    protected function setUp(): void
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
        $this->positionHandler = $this->prophesize(PositionHandlerInterface::class);
        $this->container = $this->prophesize(ContainerInterface::class);
        $this->admin = $this->prophesize(AdminInterface::class);
        $this->request = new Request();

        $this->configureCRUDController();
        $this->configureRequest();

        $this->controller = new SortableAdminController(
            $this->propertyAccessor,
            $this->positionHandler->reveal()
        );
        $this->controller->setContainer($this->container->reveal());
    }

    /** @test */
    public function itSetsAndGetsPosition(): void
    {
        $entity = new ChildSortableEntity();

        $translator = $this->prophesize(TranslatorInterface::class);
        $session = $this->prophesize(Session::class);
        $flashBag = $this->prophesize(FlashBagInterface::class);

        $this->admin->isGranted('EDIT')->willReturn(true);
        $this->admin->getSubject()->willReturn($entity);
        $this->admin->generateUrl('list', ['filter' => []])->willReturn('https://localhost');
        $this->admin->getFilterParameters()->willReturn([]);
        $this->admin->update($entity)->shouldBeCalled();
        $this->admin->getTranslationDomain()->willReturn('domain');
        $this->positionHandler->getLastPosition($entity)->willReturn(2);
        $this->positionHandler->getPosition($entity, 'up', 2)->willReturn(1);
        $this->positionHandler->getPositionFieldByEntity($entity)->willReturn('position');
        $this->container->get('translator')->willReturn($translator->reveal());
        $this->container->has('session')->willReturn(true);
        $this->container->get('session')->willReturn($session->reveal());
        $translator->trans(Argument::any(), [], 'domain', null)->willReturn('trans');
        $session->getFlashBag()->willReturn($flashBag);

        $response = $this->controller->moveAction('up');

        $this->assertInstanceOf(RedirectResponse::class, $response);
    }

    private function configureCRUDController(): void
    {
        $pool = $this->prophesize(Pool::class);
        $breadcrumbsBuilder = $this->prophesize(BreadcrumbsBuilderInterface::class);

        $this->configureGetCurrentRequest();
        $pool->getAdminByAdminCode('admin_code')->willReturn($this->admin->reveal());
        $this->container->get('sonata.admin.pool')->willReturn($pool->reveal());
        $this->container->get('sonata.admin.breadcrumbs_builder')->willReturn($breadcrumbsBuilder->reveal());
        $this->admin->getTemplate('layout')->willReturn('layout.html.twig');
        $this->admin->isChild()->willReturn(false);
        $this->admin->setRequest($this->request)->shouldBeCalled();
        $this->container->get('admin_code.template_registry')->willReturn(new TemplateRegistry());
        $this->admin->getCode()->willReturn('admin_code');
    }

    private function configureRequest(): void
    {
        $this->request->query->set('_sonata_admin', 'admin_code');
    }

    private function configureGetCurrentRequest(): void
    {
        $requestStack = new RequestStack();
        $requestStack->push($this->request);

        $this->container->get('request_stack')->willReturn($requestStack);
    }
}
