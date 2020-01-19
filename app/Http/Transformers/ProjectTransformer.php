<?php

namespace App\Http\Transformers;
use App\Project;
use League\Fractal\TransformerAbstract;

class ProjectTransformer extends TransformerAbstract
{


    protected $availableIncludes = [

    ];

    protected $defaultIncludes = [

    ];

    /**
     * @param Project $data
     * @return array
     */
    public function transform(Project $data)
    {
        return [
            "id"=> $data['id'],
            "name"=> $data['name'],
            "created_at"=> \Carbon\Carbon::parse($data['created_at'])->diffForHumans(),
            "updated_at"=> isset($data['updated_at']) ? \Carbon\Carbon::parse($data['updated_at'])->diffForHumans(): null,
            "deleted_at"=> isset($data['deleted_at']) ? \Carbon\Carbon::parse($data['deleted_at'])->diffForHumans(): null,
        ];
    }


}
