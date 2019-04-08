<?php

namespace Tests\Setup;

use App\Task;
use App\User;
use App\Project;

class ProjectFactory
{
    
    protected $tasksCount;
    protected $user;
    
    public function withTasks($count = 0)
    {
        $this->tasksCount = $count;

        return $this;
    }

    public function ownedBy($user)
    {
        $this->user = $user;

        return $this;
    }

    public function create()
    {
        $project = factory(Project::class)->create([
            'owner_id' => $this->user ?? factory(User::class)
        ]);

        factory(Task::class)->create([
            'project_id' => $project->id
        ]);

        return $project;
    }
}