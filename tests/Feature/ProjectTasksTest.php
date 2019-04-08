<?php

namespace Tests\Feature;

use App\Task;
use App\Project;
use Tests\TestCase;
use App\Traits\AuthTrait;
use Facades\Tests\Setup\ProjectFactory;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectTasksTest extends TestCase
{
    use WithFaker, RefreshDatabase, AuthTrait;


    /** @test */
    public function guests_cannot_add_tasks_to_projects()
    {
        $project = factory(Project::class)->create();
        $this->post($project->path().'/tasks')->assertRedirect('login');
    }


    /** @test */
    public function only_the_owner_of_project_may_add_tasks()
    {
        $this->authorizeUser();
        $project = factory(Project::class)->create();
        
        $this->post($project->path().'/tasks', ['body' => 'Test Task'])->assertStatus(403);
        $this->assertDatabaseMissing('tasks', ['body' => 'Test Task']);
    }
    
    

    /** @test */
    public function a_project_can_have_tasks()
    {
        $this->withoutExceptionHandling();

        $project = ProjectFactory::ownedBy($this->authorizeUser())->create();

        $this->post($project->path().'/tasks', ['body' => 'Test Task']);

        $this->get($project->path())->assertSee('Test Task');
    }

    /** @test */
    public function a_task_can_be_updated()
    {
        $this->withoutExceptionHandling();

        $project = ProjectFactory::withTasks(1)
                    ->create();

        $this->actingAs($project->owner)
             ->patch($project->tasks->first()->path(), [
                'body' => 'test Task',
                'completed' => true
             ])->assertRedirect($project->path());

        $this->assertDatabaseHas('tasks', [
            'body' => 'test Task',
            'completed' => true
        ]);
    }

    /** @test */
    public function only_the_owner_of_project_may_update_a_task()
    {
       
        $this->authorizeUser();
        $project = ProjectFactory::withTasks(1)->create();

        $this->patch($project->tasks->first()->path(), ['body' => 'changed'])->assertStatus(403);

        $this->assertDatabaseMissing('tasks', ['body' => 'changed']);
    }


    /** @test */
    public function a_task_requires_body()
    {
        $project = ProjectFactory::create();

        $atributes = factory(Task::class)->raw(['body' => '']);
        
        $this->actingAs($project->owner)->post($project->path().'/tasks', $atributes)->assertSessionHasErrors('body');
    }
}
