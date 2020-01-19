<?php

namespace App\Http\Controllers;

use App\Http\Transformers\ProjectTransformer;
use App\Project;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use League\Fractal;
use League\Fractal\Manager;
use Response;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class ProjectController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('web');
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $all = isset($request->all) ? true : false;
        $dropDown = isset($request->dropDown) ? true : false;
        $projectData = Project::all();

        if($dropDown) {
            $allOption = new Project();
            $allOption->id = 0;
            $allOption->name = "All Projects";
            $projectData->prepend($allOption);
        }

        $fractal = new Manager();
        $perPage = ($all) ? $projectData->count() : 10;
        $paginator = $this->paginate($projectData, $perPage, $page = null, $options = []);
        $resource = new Fractal\Resource\Collection($projectData, new ProjectTransformer());
        $resource->setPaginator(new IlluminatePaginatorAdapter($paginator));
        $data = $fractal->createData($resource)->toArray();

        return Response::json($data);
    }

    /**
     * @param $items
     * @param int $perPage
     * @param null $page
     * @param array $options
     * @return LengthAwarePaginator
     */
    public function paginate($items, $perPage = 15, $page = null, $options = [])
    {
        $page = $page ?: (\Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1);
        $items = new Collection($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}
