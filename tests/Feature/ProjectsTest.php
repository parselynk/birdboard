<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Traits\AuthTrait;
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
            'description' => $this->faker->paragraph
        ];
        $this->post('/projects', $attributes)->assertRedirect('/projects');
        $this->assertDatabaseHas('projects', $attributes);
        $this->get('/projects')->assertSee($attributes['title']);
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
        $this->authorizeUser();
        $project = factory('App\Project')->create(['owner_id' => auth()->id()]);
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
}
