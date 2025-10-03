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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Runroom\SortableBehaviorBundle\Action\MoveAction;
use Runroom\SortableBehaviorBundle\Service\PositionHandlerInterface;
use Runroom\SortableBehaviorBundle\Tests\App\Entity\ChildSortableEntity;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\AdminBundle\Request\AdminFetcher;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Translation\Translator;

final class MoveActionTest extends TestCase
{
    private PropertyAccessor $propertyAccessor;
    private MockObject&PositionHandlerInterface $positionHandler;
    private Container $container;

    /**
     * @var MockObject&AdminInterface<object>
     */
    private MockObject&AdminInterface $admin;

    private Request $request;
    private MoveAction $action;

    protected function setUp(): void
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
        $this->container = new Container();
        $this->request = new Request();
        $this->positionHandler = $this->createMock(PositionHandlerInterface::class);
        $this->admin = $this->createMock(AdminInterface::class);

        $this->configureRequest();
        $this->configureContainer();

        $this->action = new MoveAction(
            $this->propertyAccessor,
            new Translator('en'),
            new AdminFetcher(new Pool($this->container, [
                'admin.code' => 'admin_code',
            ])),
            $this->positionHandler
        );
        $this->action->setContainer($this->container);
    }

    public function testItRedirectsWhenMissingPermissions(): void
    {
        $this->admin->method('isGranted')->with('EDIT')->willReturn(false);
        $this->admin->method('generateUrl')->with('list', ['filter' => []])->willReturn('https://localhost');
        $this->admin->method('getFilterParameters')->willReturn([]);
        $this->admin->method('getTranslationDomain')->willReturn('domain');

        $response = ($this->action)($this->request, 'up');

        static::assertInstanceOf(RedirectResponse::class, $response);
    }

    public function testItMovesPositions(): void
    {
        $entity = new ChildSortableEntity();

        $this->admin->expects(static::once())->method('isGranted')->with('EDIT')->willReturn(true);
        $this->admin->expects(static::once())->method('hasSubject')->willReturn(true);
        $this->admin->expects(static::once())->method('getSubject')->willReturn($entity);
        $this->admin->expects(static::once())->method('generateUrl')->with('list', ['filter' => []])->willReturn('https://localhost');
        $this->admin->expects(static::once())->method('getFilterParameters')->willReturn([]);
        $this->admin->expects(static::once())->method('update')->with($entity);
        $this->positionHandler->expects(static::once())->method('getLastPosition')->with($entity)->willReturn(2);
        $this->positionHandler->expects(static::once())->method('getPosition')->with($entity, 'up', 2)->willReturn(1);
        $this->positionHandler->expects(static::once())->method('getPositionFieldByEntity')->with($entity)->willReturn('position');

        $response = ($this->action)($this->request, 'up');

        static::assertInstanceOf(RedirectResponse::class, $response);
    }

    public function testItMovesPositionsWithAjax(): void
    {
        $entity = new ChildSortableEntity();

        $this->request->headers->set('X-Requested-With', 'XMLHttpRequest');
        $this->admin->expects(static::once())->method('isGranted')->with('EDIT')->willReturn(true);
        $this->admin->expects(static::once())->method('hasSubject')->willReturn(true);
        $this->admin->expects(static::once())->method('getSubject')->willReturn($entity);
        $this->admin->expects(static::once())->method('update')->with($entity);
        $this->admin->expects(static::once())->method('getNormalizedIdentifier')->with($entity)->willReturn('identifier');
        $this->positionHandler->expects(static::once())->method('getLastPosition')->with($entity)->willReturn(2);
        $this->positionHandler->expects(static::once())->method('getPosition')->with($entity, 'up', 2)->willReturn(1);
        $this->positionHandler->expects(static::once())->method('getPositionFieldByEntity')->with($entity)->willReturn('position');

        $response = ($this->action)($this->request, 'up');

        static::assertInstanceOf(JsonResponse::class, $response);
    }

    private function configureRequest(): void
    {
        $this->request->query->set('_sonata_admin', 'admin_code');
    }

    private function configureContainer(): void
    {
        $session = static::createStub(Session::class);

        $flashBag = new FlashBag();
        $requestStack = new RequestStack();

        $session->method('getFlashBag')->willReturn($flashBag);

        $requestStack->push($this->request);
        $this->request->setSession($session);
        $this->container->set('admin_code', $this->admin);
        $this->container->set('request_stack', $requestStack);
        $this->container->set('session', $session);
    }
}
