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

    protected function permissionPrefix(): string
    {
        return Str::snake(str_replace('-', '_', $this->viewPrefix()));
    }

    protected function permissionFor(string $action): string
    {
        return "{$action}_{$this->permissionPrefix()}";
    }

    protected function can(Request $request, string $action): bool
    {
        return (bool) $request->user()?->hasPermission($this->permissionFor($action));
    }

    protected function requirePermission(Request $request, string $action): void
    {
        abort_unless($this->can($request, $action), 403, 'You do not have permission to access this area.');
    }

    protected function showContext(Model $record): array
    {
        return [];
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
            return $record->loadMissing($this->with());
        }

        return $this->query()->findOrFail($record);
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
        $this->requirePermission($request, 'view');

        return view("admin.crud.index", [
            'title' => $this->title(),
            'description' => $this->pageDescription(),
            'columns' => $this->columns(),
            'records' => $this->query()->latest()->paginate(10),
            'routePrefix' => $this->viewPrefix(),
            'canCreate' => $this->can($request, 'create'),
            'canView' => $this->can($request, 'view'),
            'canEdit' => $this->can($request, 'edit'),
            'canDelete' => $this->can($request, 'delete'),
            'canUpdate' => $this->can($request, 'update'),
            'canApprove' => $this->can($request, 'approve'),
        ]);
    }

    public function create(): View
    {
        $model = $this->modelClass();
        $request = request();
        $this->requirePermission($request, 'create');

        return view('admin.crud.form', [
            'title' => $this->title(),
            'description' => $this->pageDescription(),
            'fields' => $this->formFields(),
            'record' => new $model(),
            'routePrefix' => $this->viewPrefix(),
            'action' => route("admin.{$this->viewPrefix()}.store"),
            'method' => 'POST',
            'submitLabel' => 'Create',
            'canSubmit' => $this->can($request, 'create'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->requirePermission($request, 'create');

        $data = $request->validate($this->rules());
        $data = $this->transformInput($data);

        $model = $this->modelClass();
        $record = $model::create($data);
        $this->afterSave($record, $data, $request);

        return redirect()->route("admin.{$this->viewPrefix()}.show", $record)->with('status', 'created');
    }

    public function show(Request $request): View
    {
        $this->requirePermission($request, 'view');

        $record = $this->resolveRecord($request);

        return view('admin.crud.show', [
            'title' => $this->title(),
            'description' => $this->pageDescription(),
            'record' => $record,
            'fields' => $this->formFields($record),
            'routePrefix' => $this->viewPrefix(),
            'showContext' => $this->showContext($record),
            'canEdit' => $this->can($request, 'edit'),
        ]);
    }

    public function edit(Request $request): View
    {
        $this->requirePermission($request, 'edit');

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
            'canSubmit' => $this->can($request, 'update'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $this->requirePermission($request, 'update');

        $record = $this->resolveRecord($request);
        $data = $request->validate($this->rules($record));
        $data = $this->transformInput($data, $record);

        $record->update($data);
        $this->afterSave($record, $data, $request);

        return redirect()->route("admin.{$this->viewPrefix()}.show", $record)->with('status', 'updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $this->requirePermission($request, 'delete');

        $record = $this->resolveRecord($request);
        $record->delete();

        return redirect()->route("admin.{$this->viewPrefix()}.index")->with('status', 'deleted');
    }
}
