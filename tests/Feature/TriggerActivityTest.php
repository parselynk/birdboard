<?php

namespace Tests\Feature;

use Tests\TestCase;
use Facades\Tests\Setup\ProjectFactory;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TriggerActivityTest extends TestCase
{
    use RefreshDatabase;


    /** @test */
    public function creating_a_project()
    {
        $project = ProjectFactory::create();

        $this->assertCount(1, $project->activities);
        $this->assertEquals('created', $project->activities->first()->description);
    }

    /** @test */
    public function updating_a_project()
    {
        $project = ProjectFactory::create();

        $project->update(['title' => 'changed']);
        $this->assertCount(2, $project->activities);

        $this->assertEquals('updated', $project->activities->last()->description);
    }

    /** @test */
    public function creating_a_new_task()
    {
        $project = ProjectFactory::create();

        $project->addTask('new task');
        $this->assertCount(2, $project->activities);

        $this->assertEquals('created_task', $project->activities->last()->description);

    }

    /** @test */
    public function completing_a_new_task()
    {
        $project = ProjectFactory::withTasks(1)->create();

        $this->actingAs($project->owner)
             ->patch($project->tasks->first()->path(), [
                'body' => 'updated',
                'completed' => true
             ]);

        $this->assertCount(3, $project->activities);

        $this->assertEquals('completed_task', $project->activities->last()->description);
    }

    /** @test */
    public function incompleting_a_new_task()
    {
        $project = ProjectFactory::withTasks(1)->create();

        $this->actingAs($project->owner)
             ->patch($project->tasks->first()->path(), [
                'body' => 'updated',
                'completed' => true
             ]);

        $this->assertCount(3, $project->activities);

        $this->actingAs($project->owner)
             ->patch($project->tasks->first()->path(), [
                'body' => 'updated',
                'completed' => false
             ]);

        $project->refresh();

        $this->assertCount(4, $project->activities);

        $this->assertEquals('incompleted_task', $project->activities->last()->description);
    }


    /** @test */
    public function deleting_a_task()
    {
        $project = ProjectFactory::withTasks(1)->create();

        $project->tasks->first()->delete();

        $this->assertCount(3, $project->activities);
    }
    
    
    
    
}
