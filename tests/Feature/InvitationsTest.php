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
    public function a_project_can_invite_a_user()
    {       
        $this->withoutExceptionHandling();
        
        $project = ProjectFactory::create();

        $project->invite($newUser = $this->createUser());

        $this->authorizeUser($newUser);

        $this->post(action('projectTasksController@store', $project), $task = ['body' => 'new task']);

        $this->assertDatabaseHas('tasks', $task);
    }
    
}
