<?php

namespace App\Http\Controllers;

use App\Http\Repositories\TaskInterface;
use App\Http\Transformers\TaskTransformer;
use App\Task;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use League\Fractal;
use League\Fractal\Manager;
use Illuminate\Support\Collection;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\Rule;


class TaskController extends Controller
{
    use SoftDeletes;
    private $taskRepository;

    /**
     * Create a new controller instance.
     *
     * @param TaskInterface $taskRepository
     * @param Manager $fractal
     */
    public function __construct(TaskInterface $taskRepository, Manager $fractal)
    {
        $this->middleware('web');
        $this->taskRepository = $taskRepository;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $fractal = new Manager();
        $filters = $request->all();
        $page = 1; $limit = 100;

        $taskData = $this->taskRepository->getByFilters($filters, $with = array(), $page, $limit);

        $paginator = $this->paginate($taskData->items, $perPage = ($taskData->totalItems !== 0) ? $taskData->totalItems : 10, $page = null, $options = []);
        $taskData = $paginator->getCollection();
        $resource = new Fractal\Resource\Collection($taskData, new TaskTransformer());
        $resource->setPaginator(new IlluminatePaginatorAdapter($paginator));

        return Response::json($fractal->createData($resource)->toArray());
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:task|max:20',
        ]);


        if ($validator->fails()) {
            $errors = $validator->errors();
            return Response::json($errors, 300);
        }

        $newTask = $this->taskRepository->store($request);

        if($newTask) {
            $newTask = fractal($newTask, new TaskTransformer())->toArray();
            return Response::json($newTask, 200);
        }

        return Response::json("Server Error", 500);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:task',
            'name' => ['required','max:20', Rule::unique('task')->whereNULL('deleted_at')->ignore($request->id)],
            'priority' => 'required|integer'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return Response::json($errors, 300);
        }

        $updateTask = $this->taskRepository->update($request);

        if($updateTask) {
            $updateTask = fractal($updateTask, new TaskTransformer())->toArray();
            return Response::json($updateTask, 200);
        } else {
            return Response::json("Server Error", 500);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function updateAllPriorities(Request $request)
    {
        $allTasks = $request->all();
        if(isset($allTasks['tasks'])) {
            $taskCounter = 0;
            foreach($allTasks['tasks'] as $count => $task) {
                $editTask = Task::find($task['id']);
                $editTask->priority=$taskCounter;
                $editTask->save();
                $taskCounter++;
            }
        }
        return $this->index($request);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:task',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return Response::json($errors, 300);
        }

        $taskToDelete = $this->taskRepository->destroy($request);

        if($taskToDelete) {
            $taskToDelete = fractal($taskToDelete, new TaskTransformer())->toArray();
            return Response::json($taskToDelete, 200);
        } else {
            return Response::json("Server Error", 500);
        }
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
