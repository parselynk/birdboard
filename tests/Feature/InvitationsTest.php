<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Traits\AuthTrait;
use Facades\Tests\Setup\ProjectFactory;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InvitationsTest extends TestCase
{
    use RefreshDatabase, AuthTrait;


    
    /** @test */
    public function non_owners_may_not_invite_users()
    {
        //$this->withoutExceptionHandling();
        $this->actingAs($this->createUser())
             ->post(ProjectFactory::create()->path() . '/invitations')
             ->assertStatus(403);
    }
    
    /** @test */
    public function a_project_owner_can_invite_a_user()
    {
        
        $project = ProjectFactory::create();

        $userToInvite = $this->createUser();

        $this->actingAs($project->owner)->post($project->path() . '/invitations', [
            'email' => $userToInvite->email
        ])->assertRedirect($project->path());

        $this->assertTrue($project->members->contains($userToInvite));
    }


    /** @test */
    public function the_email_address_must_be_associated_with_a_valid_birdboard_account()
    {
        $project = ProjectFactory::create();

        $this->actingAs($project->owner)->post($project->path() . '/invitations', [
            'email' => 'nouser@example.com'
        ])->assertSessionHasErrors(['email' => 'The user you are inviting must have a Birdboard account.']);

    }
    
    

    /** @test */
    public function invited_users_may_update_project_details()
    {       
        
        $project = ProjectFactory::create();

        $project->invite($newUser = $this->createUser());

        $this->authorizeUser($newUser);

        $this->post(action('projectTasksController@store', $project), $task = ['body' => 'new task']);

        $this->assertDatabaseHas('tasks', $task);
    }
    
}
