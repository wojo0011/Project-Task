<?php
namespace App\Http\Repositories;

use App\Task;
use Illuminate\Database\Eloquent\Model;
use App\Http\Repositories\TaskInterface;
use App\Base\BaseRepository;
use Illuminate\Http\Request;

class TaskRepository extends BaseRepository implements TaskInterface
{
    protected $model;

    /**
     * TaskRepository constructor.
     * @param Model $model
     */
    function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @param $filters
     * @param array $with
     * @param $page
     * @param $limit
     * @return \StdClass
     */
    public function getByFilters($filters, $with = array(), $page, $limit)
    {
        $result = new \StdClass;
        $result->page = $page;

        $result->limit = $limit;
        $result->totalItems = 0;
        $result->items = array();
        $query = $this->applyFilters($filters);

        //total of query
        $result->totalItems = $query->count();

        $query = $query->skip($limit * ($page - 1))->take($limit);
        $model = $query->get();

        //total of model
        $result->items = $model->all();

        return $result;
    }

    /**
     * @param $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function applyFilters($filters)
    {

        $query = $this->makeWithoutOrder([]);

        if (isset($filters) && !empty($filters['projectId'])) {
            if($filters['projectId'] !== '0') {
                $query = $query->where('project_id',$filters['projectId']);
                $query = $query->orderBy('priority');
            }
        }

        if(isset($filters) && empty($filters['projectId'])) {
            $query = $query->orderBy('project_id')->orderBy('priority', 'ASC');
        }

        return $query;
    }

    /**
     * @param Request $request
     * @return Task|bool
     */
    public function store(Request $request)
    {
        # GET THE LATEST PRIORITY AND ADD ONE - PROJECT ID WILL ALWAYS BE SET BUT THIS WILL STOP ANY ERRORS IN CASE IT IS NOT
        if(isset($request->project_id)) {
            $priority = $this->model->where('project_id', $request->project_id)->max('priority') + 1;
        } else {
            $priority = $this->model->max('priority') + 1;
        }

        if($priority) {
            $newTask = new Task;
            $newTask->name = $request->name;
            $newTask->priority = $priority;
            $newTask->project_id = $request->project_id;
            $newTask->save();

            return $newTask;
        }

        return false;
    }

    /**
     * @param Request $request
     * @return bool
     */
     public function update(Request $request)
     {
         $taskToUpdate = $this->model->findOrFail($request->id);
         $originalProjectIdForReorder = $taskToUpdate->project_id;
         $projectUpdate = json_decode($request->input('project'))->data->id;
         $projectIdWasChangedReorderTasksInOldProject = false;

         if($taskToUpdate && $projectUpdate) {
             # TASK HAS CHANGED PROJECTS -> THE PROJECT ID WAS UPDATED SET THE PRIORITY TO BOTTOM OF THE NEW PROJECT LIST
             if($taskToUpdate->project_id != $projectUpdate) {
                 $priority = $this->model->where('project_id', $projectUpdate)->max('priority') + 1;
                 $taskToUpdate->priority = $priority;
                 $taskToUpdate->project_id = $projectUpdate;
                 $projectIdWasChangedReorderTasksInOldProject = true;
             }

             $taskToUpdate->name = $request->name;
             $taskToUpdate->save();

             # RE-ORDER PRIORITIES IN THE ORIGINAL LIST TO SO THERE ARE NO MISSING PRIORITIES WHEN ONE IS REMOVED CAUSED BY UPDATE
             if($projectIdWasChangedReorderTasksInOldProject) {
                 $reorderTasks = $this->model->where('project_id', $originalProjectIdForReorder)->get();
                 $taskCounter = 0;
                 foreach($reorderTasks as $count => $task) {
                     $editTask = $this->model->find($task['id']);
                     $editTask->priority = $taskCounter;
                     $editTask->save();
                     $taskCounter++;
                 }
             }
             return $taskToUpdate;
         }

         return false;
     }

    /**
     * @param $id
     */
    public function show($id)
    {

    }

    /**
     * @param Request $request
     * @return bool
     */
    public function destroy(Request $request)
    {
        $taskToDelete = $this->model->findOrFail($request->id);

        # REORDER TASK PRIORITIES AFTER 1 IS DELETED SO THAT THERE IS NO MISSING PRIORITIES
        if($taskToDelete) {
            $taskToDelete->delete();
            $reorderTasks = $this->model->where('project_id', $taskToDelete->project_id)->get();
            $taskCounter = 0;
            foreach($reorderTasks as $count => $task) {
                $editTask = $this->model->find($task['id']);
                $editTask->priority = $taskCounter;
                $editTask->save();
                $taskCounter++;
            }
            return $taskToDelete;
        }

        return false;
    }
}
