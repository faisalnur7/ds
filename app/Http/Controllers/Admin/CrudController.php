<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;

abstract class CrudController extends Controller
{
    abstract protected function modelClass(): string;

    abstract protected function title(): string;

    abstract protected function viewPrefix(): string;

    abstract protected function routeParameter(): string;

    abstract protected function rules(?Model $record = null): array;

    protected function columns(): array
    {
        return [];
    }

    protected function with(): array
    {
        return [];
    }

    protected function formFields(?Model $record = null): array
    {
        return [];
    }

    protected function pageDescription(): string
    {
        return '';
    }

    protected function query()
    {
        $model = $this->modelClass();

        $query = $model::query();

        if ($relations = $this->with()) {
            $query->with($relations);
        }

        return $query;
    }

    protected function resolveRecord(Request $request): Model
    {
        $model = $this->modelClass();
        $record = $request->route($this->routeParameter());

        if ($record instanceof Model) {
            return $record;
        }

        return $model::query()->findOrFail($record);
    }

    protected function transformInput(array $input, ?Model $record = null): array
    {
        return $input;
    }

    protected function afterSave(Model $record, array $data, Request $request): void
    {
        //
    }

    public function index(Request $request): View
    {
        return view("admin.crud.index", [
            'title' => $this->title(),
            'description' => $this->pageDescription(),
            'columns' => $this->columns(),
            'records' => $this->query()->latest()->paginate(10),
            'routePrefix' => $this->viewPrefix(),
        ]);
    }

    public function create(): View
    {
        $model = $this->modelClass();

        return view('admin.crud.form', [
            'title' => $this->title(),
            'description' => $this->pageDescription(),
            'fields' => $this->formFields(),
            'record' => new $model(),
            'routePrefix' => $this->viewPrefix(),
            'action' => route("admin.{$this->viewPrefix()}.store"),
            'method' => 'POST',
            'submitLabel' => 'Create',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules());
        $data = $this->transformInput($data);

        $model = $this->modelClass();
        $record = $model::create($data);
        $this->afterSave($record, $data, $request);

        return redirect()->route("admin.{$this->viewPrefix()}.show", $record)->with('status', 'created');
    }

    public function show(Request $request): View
    {
        $record = $this->resolveRecord($request);

        return view('admin.crud.show', [
            'title' => $this->title(),
            'description' => $this->pageDescription(),
            'record' => $record,
            'fields' => $this->formFields($record),
            'routePrefix' => $this->viewPrefix(),
        ]);
    }

    public function edit(Request $request): View
    {
        $record = $this->resolveRecord($request);

        return view('admin.crud.form', [
            'title' => $this->title(),
            'description' => $this->pageDescription(),
            'fields' => $this->formFields($record),
            'record' => $record,
            'routePrefix' => $this->viewPrefix(),
            'action' => route("admin.{$this->viewPrefix()}.update", $record),
            'method' => 'PUT',
            'submitLabel' => 'Update',
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $record = $this->resolveRecord($request);
        $data = $request->validate($this->rules($record));
        $data = $this->transformInput($data, $record);

        $record->update($data);
        $this->afterSave($record, $data, $request);

        return redirect()->route("admin.{$this->viewPrefix()}.show", $record)->with('status', 'updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $record = $this->resolveRecord($request);
        $record->delete();

        return redirect()->route("admin.{$this->viewPrefix()}.index")->with('status', 'deleted');
    }
}
