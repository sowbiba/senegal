<?php

namespace Senegal\ApiBundle\Tests\Entity;

use Senegal\ApiBundle\Entity\Zone;
use Senegal\ApiBundle\Tests\BaseUnitTestCase;

class ZoneTest extends BaseUnitTestCase
{
    public function testConstants()
    {
        $zone = new Zone();
        $this->assertEquals(1, $zone::DRAFT_ID);
        $this->assertEquals(2, $zone::DEMO_ID);
        $this->assertEquals(3, $zone::PUBLISH_ID);
        $this->assertEquals('draft', $zone::DRAFT_SLUG);
        $this->assertEquals('demo', $zone::DEMO_SLUG);
        $this->assertEquals('publish', $zone::PUBLISH_SLUG);
    }

    public function testId()
    {
        $zone = new Zone();
        $this->assertNull($zone->getId());

        $zone->setId(1);
        $this->assertEquals(1, $zone->getId());
    }

    public function testName()
    {
        $zone = new Zone();
        $this->assertNull($zone->getName());

        $zone->setName('My zone name');
        $this->assertEquals('My zone name', $zone->getName());
    }

    public function testSlug()
    {
        $zone = new Zone();
        $this->assertNull($zone->getSlug());

        $zone->setSlug('my-zone-slug');
        $this->assertEquals('my-zone-slug', $zone->getSlug());
    }

    public function testZone()
    {
        $zone = new Zone();

        $zone->setId(1);
        $this->assertTrue($zone->isDraft());
        $this->assertFalse($zone->isDemo());
        $this->assertFalse($zone->isPublish());

        $zone->setId(2);
        $this->assertFalse($zone->isDraft());
        $this->assertTrue($zone->isDemo());
        $this->assertFalse($zone->isPublish());

        $zone->setId(3);
        $this->assertFalse($zone->isDraft());
        $this->assertFalse($zone->isDemo());
        $this->assertTrue($zone->isPublish());
    }

    public function testPreviousSlug()
    {
        $zone = new Zone();

        $zone->setSlug(Zone::DRAFT_SLUG);
        $this->assertEquals(Zone::PUBLISH_SLUG, $zone->getPreviousSlug());

        $zone->setSlug(Zone::DEMO_SLUG);
        $this->assertEquals(Zone::DRAFT_SLUG, $zone->getPreviousSlug());

        $zone->setSlug(Zone::PUBLISH_SLUG);
        $this->assertEquals(Zone::DEMO_SLUG, $zone->getPreviousSlug());
    }

    public function testNextSlug()
    {
        $zone = new Zone();

        $zone->setSlug(Zone::DRAFT_SLUG);
        $this->assertEquals(Zone::DEMO_SLUG, $zone->getNextSlug());

        $zone->setSlug(Zone::DEMO_SLUG);
        $this->assertEquals(Zone::PUBLISH_SLUG, $zone->getNextSlug());

        $zone->setSlug(Zone::PUBLISH_SLUG);
        $this->assertEquals(Zone::DRAFT_SLUG, $zone->getNextSlug());
    }
}
