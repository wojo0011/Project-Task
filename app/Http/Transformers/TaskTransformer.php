<?php

namespace App\Http\Transformers;
use App\Task;
use League\Fractal\TransformerAbstract;
use App\AppleCareVsFinetPrices;

class TaskTransformer extends TransformerAbstract
{


    protected $availableIncludes = [
        'project'
    ];

    protected $defaultIncludes = [
        'project'
    ];

    /**
     * @param Task $data
     * @return array
     */
    public function transform(Task $data)
    {
        return [
            "id"=> $data['id'],
            "name"=> $data['name'],
            "priority"=> $data['priority'],
            "created_at"=> \Carbon\Carbon::parse($data['created_at'])->diffForHumans(),
            "updated_at"=> isset($data['updated_at']) ? \Carbon\Carbon::parse($data['updated_at'])->diffForHumans(): null,
            "deleted_at"=> isset($data['deleted_at']) ? \Carbon\Carbon::parse($data['deleted_at'])->diffForHumans(): null,
        ];
    }

    /**
     * @param Task $task
     * @return \League\Fractal\Resource\Item
     */
    public function includeProject(Task $task)
    {
        return $this->item($task->project, new ProjectTransformer());
    }
}
