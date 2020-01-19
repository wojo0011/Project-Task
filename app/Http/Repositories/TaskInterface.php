<?php
namespace App\Http\Repositories;

use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Boolean;

interface TaskInterface
{
    public function getByFilters($filters, $with = array(), $page, $limit);

    public function applyFilters($filters);

    public function store(Request $request);

    public function update(Request $request);

    public function show($id);

    public function destroy(Request $request);

}
