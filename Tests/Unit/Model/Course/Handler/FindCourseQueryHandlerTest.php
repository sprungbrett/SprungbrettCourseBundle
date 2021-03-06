<?php

declare(strict_types=1);

namespace Sprungbrett\Bundle\CourseBundle\Tests\Unit\Model\Course\Handler;

use PHPUnit\Framework\TestCase;
use Sprungbrett\Bundle\ContentBundle\Stages;
use Sprungbrett\Bundle\CourseBundle\Model\Course\CourseInterface;
use Sprungbrett\Bundle\CourseBundle\Model\Course\CourseRepositoryInterface;
use Sprungbrett\Bundle\CourseBundle\Model\Course\Exception\CourseNotFoundException;
use Sprungbrett\Bundle\CourseBundle\Model\Course\Handler\FindCourseQueryHandler;
use Sprungbrett\Bundle\CourseBundle\Model\Course\Query\FindCourseQuery;

class FindCourseQueryHandlerTest extends TestCase
{
    /**
     * @var CourseRepositoryInterface
     */
    private $repository;

    /**
     * @var FindCourseQueryHandler
     */
    private $handler;

    /**
     * @var FindCourseQuery
     */
    private $query;

    /**
     * @var CourseInterface
     */
    private $course;

    protected function setUp()
    {
        $this->repository = $this->prophesize(CourseRepositoryInterface::class);

        $this->handler = new FindCourseQueryHandler($this->repository->reveal());

        $this->query = $this->prophesize(FindCourseQuery::class);
        $this->query->getUuid()->willReturn('123-123-123');
        $this->query->getLocale()->willReturn('en');
        $this->query->getStage()->willReturn(Stages::LIVE);

        $this->course = $this->prophesize(CourseInterface::class);
    }

    public function testInvoke(): void
    {
        $this->repository->findById('123-123-123', Stages::LIVE, 'en')->willReturn($this->course->reveal());

        $result = $this->handler->__invoke($this->query->reveal());
        $this->assertEquals($this->course->reveal(), $result);
    }

    public function testInvokeNotFound(): void
    {
        $this->expectException(CourseNotFoundException::class);

        $this->repository->findById('123-123-123', Stages::LIVE, 'en')->willReturn(null);

        $this->handler->__invoke($this->query->reveal());
    }
}
