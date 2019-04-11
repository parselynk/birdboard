<?php

namespace App\Http\Controllers;

use App\Project;
use Illuminate\Http\Request;

class ProjectsController extends Controller
{
    public function index()
    {
        $projects = auth()->user()->projects()->get();

        return view('projects.index', compact('projects'));
    }

    public function store()
    {
        $project = auth()->user()->projects()->create($this->validateRequest());
        return redirect($project->path());
    }

    public function update(Project $project)
    {
        $this->authorize('update', $project);

        $project->update($this->validateRequest());

        return redirect($project->path());
    }

    public function show(Project $project)
    {
        $this->authorize('update', $project);
        return view('projects.show', compact('project'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function edit(Project $project){
        return view('projects.edit', compact('project'));    
    }

    protected function validateRequest(){
        return request()->validate([
            'title'=>'sometimes|required',
            'description'=>'sometimes|required',
            'notes' => 'min:3|max:255'
        ]);
    }
}
