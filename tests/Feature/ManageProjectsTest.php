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
        $this->get($project->path().'/edit')->assertRedirect('/login');
        $this->post('/projects', $project->toArray())->assertRedirect('/login');
    }


    /** @test */
    public function a_user_can_create_a_project()
    {
        $user = $this->authorizeUser();

        $this->followingRedirects()->post('/projects', $attributes = factory(Project::class)->raw())        
            ->assertSee($attributes['title'])
            ->assertSee($attributes['description'])
            ->assertSee($attributes['notes']);
    }

    /** @test */
    public function a_user_can_update_a_project()
    {

        $this->withoutExceptionHandling();
        $project = ProjectFactory::create();
        $response = $this->actingAs($project->owner)->patch($project->path(), 
            $attributes = ['title' => 'changed', 
            'description' => 'changed', 
            'notes' => 'changed'
        ])->assertRedirect($project->path());

        $this->get($project->path())
            ->assertSee($attributes['title'])
            ->assertSee($attributes['description'])
            ->assertSee($attributes['notes']);
        
        $this->get($project->path().'/edit')->assertOk();

        $this->assertDatabaseHas('projects', $attributes);
    }

    /** @test */
    function unauthorized_users_cannot_delete_projects()
    {
        $project = ProjectFactory::create();
        $this->delete($project->path())
            ->assertRedirect('/login');
        
        $user = $this->authorizeUser();
        $this->delete($project->path())
             ->assertStatus(403);

        $project->invite($user);

        $this->actingAs($user)->delete($project->path())
             ->assertStatus(403);
    }
    /** @test */
    public function a_user_can_delete_a_project()
    {
        $this->withoutExceptionHandling();
        $project = ProjectFactory::create();
        $response = $this->actingAs($project->owner)->delete($project->path())
                         ->assertRedirect('/projects');

        $this->assertDatabaseMissing('projects', $project->only('id'));
    }


    /** @test */
    public function a_user_can_see_all_projects_they_have_been_invited_to_on_their_dashboard()
    {
        $project = tap(ProjectFactory::create())->invite($this->authorizeUser());

        $this->get('/projects')->assertSee($project->title);
    }
    
    


    /** @test */
    public function a_user_can_update_general_notes()
    {
        $project = ProjectFactory::create();
        $response = $this->actingAs($project->owner)->patch($project->path(), 
        $attributes = [ 
            'notes' => 'changed'
        ])->assertRedirect($project->path());

        $this->get($project->path())
            ->assertSee($attributes['notes']);
        
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
