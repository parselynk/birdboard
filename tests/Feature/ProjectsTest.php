<?php

namespace Tests\Feature;

use App\Project;
use Tests\TestCase;
use App\Traits\AuthTrait;
use Facades\Tests\Setup\ProjectFactory;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ManageProjectsTest extends TestCase
{
    use WithFaker, RefreshDatabase, AuthTrait;

    /** @test */
    public function guests_cannot_manage_projects()
    {
        $project = factory('App\Project')->create();
        $this->get('/projects')->assertRedirect('/login');
        $this->get('/projects/create')->assertRedirect('/login');
        $this->get($project->path())->assertRedirect('/login');
        $this->post('/projects', $project->toArray())->assertRedirect('/login');
    }


    /** @test */
    public function a_user_can_create_a_project()
    {
        $this->withoutExceptionHandling();
        $this->authorizeUser();

        $this->get('/projects/create')->assertOk();

        $attributes = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->sentence,
            'notes' => 'General note here.',
        ];

        $response = $this->post('/projects', $attributes);
        $project = Project::where($attributes)->first();
        //pay attention to "response"
        $response->assertRedirect($project->path());

        $this->get($project->path())
            ->assertSee($attributes['title'])
            ->assertSee($attributes['description'])
            ->assertSee($attributes['notes']);
    }

    /** @test */
    public function a_user_can_update_a_project()
    {

        $project = ProjectFactory::create();

        $response = $this->actingAs($project->owner)->patch($project->path(), $attributes = ['notes' => 'changed']);


        $this->assertDatabaseHas('projects', $attributes);
    }

    /** @test */
    public function a_project_requries_title()
    {
        $this->authorizeUser();
        $attributes = factory('App\Project')->raw(['title'=>'']);
        $this->post('/projects', $attributes)->assertSessionHasErrors('title');
    }

    /** @test */
    public function a_project_requries_description()
    {
        $this->authorizeUser();
        $attributes = factory('App\Project')->raw(['description'=>'']);
        $this->post('/projects', $attributes)->assertSessionHasErrors('description');
    }

    /** @test */
    public function a_user_can_view_their_project()
    {
        $this->withoutExceptionHandling();
        $project = ProjectFactory::ownedBy($this->authorizeUser())->create();
        $this->get($project->path())
             ->assertSee($project->title)
             ->assertSee($project->description);
    }

    /** @test */
    public function an_authenticated_user_cannot_view_projects_of_others()
    {
        $this->authorizeUser();
        $project = factory('App\Project')->create();
        $this->get($project->path())->assertStatus(403);
    }

    /** @test */
    public function an_authenticated_user_cannot_update_projects_of_others()
    {
        $this->authorizeUser();
        $project = factory('App\Project')->create();
        $this->patch($project->path(), [])->assertStatus(403);
    }
}
