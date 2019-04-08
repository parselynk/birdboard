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
        $attributes = request()->validate([
            'title'=>'required',
            'description'=>'required',
            'notes' => 'min:3|max:255'
        ]);

        $project = auth()->user()->projects()->create($attributes);
        return redirect($project->path());
    }

    public function update(Project $project)
    {
        $this->authorize('update', $project);
        
        $attributes = request()->validate([
            'notes' => 'min:3|max:255'
        ]);

        $project->update($attributes);
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
}
