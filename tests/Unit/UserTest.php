<?php

namespace Tests\Unit;

use App\User;
use Tests\TestCase;
use App\Traits\AuthTrait;
use Facades\Tests\Setup\ProjectFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase, AuthTrait;

    /** @test */
    public function a_user_has_projects()
    {
        $user = factory('App\User')->create();
        $this->assertInstanceOf(Collection::class, $user->projects);
    }


    /** @test */
    public function a_user_has_accessible_projects()
    {
        $john = $this->authorizeUser();

        ProjectFactory::ownedBy($john)->create();

        $this->assertCount(1, $john->accessibleProjects());

        [$sally, $jason] = factory(User::class,2)->create();

        $project = tap(ProjectFactory::ownedBy($sally)->create())->invite($jason);
		
        $this->assertCount(1, $john->accessibleProjects());

        $project->invite($john);
        $this->assertCount(2, $john->accessibleProjects());


    }
    
}
