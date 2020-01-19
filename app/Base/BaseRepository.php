<?php
namespace App\Base;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Input;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Scope;
use League\Fractal\TransformerAbstract;
use League\Fractal\Resource\Collection as FractalCollection;
use League\Fractal\Resource\Item as FractalItem;
use App\Base\BaseInterface;
use Highlight\Highlighter;

abstract class BaseRepository implements BaseInterface
{
    protected $model;
    /**
     * BaseRepository constructor.
     * @param Model $model
     */
    function __construct(Model $model)
    {
        $this->model = $model;
    }
    /**
     * @param array $attributes
     * @return mixed
     */
    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    /**
     * @param array $columns
     * @param string $orderBy
     * @param string $sortBy
     * @return mixed
     */
    public function all($columns = ['*'], string $orderBy = 'id', string $sortBy = 'asc')
    {
        return $this->model->orderBy($orderBy, $sortBy)->get($columns);
    }
    /**
     * @param string $id
     * @return mixed
     */
    public function find(int $id)
    {
        return $this->model->find($id);
    }
    /**
     * @param  $id
     * @return mixed
     * @throws ModelNotFoundException
     */
    public function findOneOrFail(int $id)
    {
        return $this->model->findOrFail($id);
    }
    /**
     * @param array $data
     * @return Collection
     */
    public function findBy(array $data)
    {
        return $this->model->where($data)->get();
    }
    /**
     * @param array $data
     * @return mixed
     */
    public function findOneBy(array $data)
    {
        return $this->model->where($data)->first();
    }
    /**
     * @param array $data
     * @return mixed
     * @throws ModelNotFoundException
     */
    public function findOneByOrFail(array $data)
    {
        return $this->model->where($data)->firstOrFail();
    }
    /**
     * @return bool
     * @throws \Exception
     */
    public function delete(int $id) : bool
    {
        return $this->model->delete();
    }
    /**
     * @param array $data
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function paginateArrayResults(array $data, int $perPage = 50)
    {
        $page = Input::get('page', 1);
        $offset = ($page * $perPage) - $perPage;
        return new LengthAwarePaginator(
            array_slice($data, $offset, $perPage, true),
            count($data),
            $perPage,
            $page,
            [
                'path' => app('request')->url(),
                'query' => app('request')->query()
            ]
        );
    }
    /**
     * @param Model $model
     * @param TransformerAbstract $transformer
     * @param string $resourceKey
     * @param string $includes
     * @return Scope
     */
    public function processItemTransformer(
        Model $model,
        TransformerAbstract $transformer,
        string $resourceKey,
        string $includes = null
    ) : Scope {
        $manager = new ItemAndCollectionManager(new Manager);
        $item = new FractalItem($model, $transformer, $resourceKey);
        $included = explode(',', $includes);
        return $manager->createItemData(
            $item,
            $included
        );
    }
    /**
     * @param Collection $collection
     * @param TransformerAbstract $transformer
     * @param string $resourceKey
     * @param string $includes
     * @param int $perPage
     * @return Scope
     */
    public function processCollectionTransformer(
        Collection $collection,
        TransformerAbstract $transformer,
        string $resourceKey,
        string $includes = null,
        $perPage = 25
    ) : Scope {
        $manager = new ItemAndCollectionManager(new Manager);
        $page = app('request')->input('page', 1);
        $fractalCollection = new FractalCollection($collection->forPage($page, $perPage), $transformer, $resourceKey);
        $paginator = $this->paginateArrayResults($collection->all(), $perPage);
        $queryParams = array_diff_key($_GET, array_flip(['page']));
        $paginator->appends($queryParams);
        $fractalCollection->setPaginator(new IlluminatePaginatorAdapter($paginator));
        if (!is_null($includes)) {
            $included = explode(',', $includes);
            return $manager->createCollectionData(
                $fractalCollection,
                $included
            );
        } else {
            return $manager->createCollectionData(
                $fractalCollection
            );
        }
    }

    public function makeWithoutOrder(array $with = array())
    {
        return $this->model->with($with);
    }

    /**
	 * Formats a JSON string for pretty printing
	 *
	 * @param string $json The JSON to make pretty
	 * @param bool $html Insert nonbreaking spaces and <br />s for tabs and linebreaks
	 * @return string The prettified output
	 * @author Jay Roberts
	 */
    public function format_json($json, $html = false) {
		$tabcount = 0;
		$result = '';
		$inquote = false;
		$ignorenext = false;
		if ($html) {
		    $tab = "&nbsp;&nbsp;&nbsp;";
		    $newline = "<br/>";
		} else {
		    $tab = "\t";
		    $newline = "\n";
		}
		for($i = 0; $i < strlen($json); $i++) {
		    $char = $json[$i];
		    if ($ignorenext) {
		        $result .= $char;
		        $ignorenext = false;
		    } else {

		        switch($char) {
		            case '{':
		                $tabcount++;
		                $result .= $char. $newline . str_repeat($tab, $tabcount);
		                break;
		            case '}':
		                $tabcount--;
		                $result = trim($result) . $newline . str_repeat($tab, $tabcount) . $char;
		                break;
		            case ',':
		                $result .= $char . $newline . str_repeat($tab, $tabcount);
		                break;
                    case '[':
                        $inquote = !$inquote;
                        $result .= $char;
                        break;
                    case ']':
                        $inquote = !$inquote;
                        $result .= $char;
                        break;
                    case '"':
		                $inquote = !$inquote;
		                $result .= $char;
		                break;
		            case '\\':
		                if ($inquote) $ignorenext = true;
		                $result .= $char;
		                break;
		            default:
		                $result .= $char;
		        }

		    }
		}

        $hl = new Highlighter();

        $r = $hl->highlight("json", $json);


        return view('highlight.highlight', ['r' => $r, 'result' => $result]);
	}
}
