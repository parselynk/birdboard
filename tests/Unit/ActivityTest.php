<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Traits\AuthTrait;
use Facades\Tests\Setup\ProjectFactory;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ActivityTest extends TestCase
{
    use RefreshDatabase, AuthTrait;
   
    
    /** @test */
    public function it_has_a_user()
    {
        $user = $this->authorizeUser();

        $project = ProjectFactory::ownedBy($user)->create();
        $this->assertEquals($user->id, $project->activities->first()->user->id);
    }
}
