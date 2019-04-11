<?php

namespace Tests\Feature;

use Tests\TestCase;
use Facades\Tests\Setup\ProjectFactory;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ActivityFeedTest extends TestCase
{
    use RefreshDatabase;


    /** @test */
    public function creating_a_project_generates_activity()
    {
        $project = ProjectFactory::create();

        $this->assertCount(1, $project->activities);
        $this->assertEquals('created', $project->activities->first()->description);
    }

    /** @test */
    public function updating_a_project_generates_activity()
    {
        $this->withoutExceptionHandling();
        $project = ProjectFactory::create();

        $project->update(['title' => 'changed']);
        $this->assertCount(2, $project->activities);

        $this->assertEquals('updated', $project->activities->last()->description);
    }
    
}
