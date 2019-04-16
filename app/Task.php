<?php

namespace App;

use App\Project;
use App\Activity;
use App\RecordsActivity;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    
    use RecordsActivity;

    protected $guarded = [];
    protected $touches = ['project'];

    public static $recordableEvents = ['created', 'deleted'];

    public $old = [];

    protected $casts = [
        'completed' => 'boolean'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function path()
    {
        return '/projects/' . $this->project->id . '/tasks/' . $this->id;
    }

    public function complete(){
        $this->update(['completed' => true ]);
        $this->recordActivity('completed_task');
    }

    public function incomplete(){
        $this->update(['completed' => false ]);
        $this->recordActivity('incompleted_task');
    }
}
