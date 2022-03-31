<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * create a new instance of the class
     *
     * @return void
     */
    function __construct()
    {
        $this->middleware('permission:permission-list|permission-create|permission-edit|permission-delete', ['only' => ['index','store']]);
        $this->middleware('permission:permission-create', ['only' => ['create','store']]);
        $this->middleware('permission:permission-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:permission-delete', ['only' => ['destroy']]);
    }

    //    Берем все права из модели и закидываем в blade permission.index
    public function index(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application
    {
        $data = Permission::orderBy('id','DESC')->paginate(50);

        return view('permissions.index', compact('data'));
    }

    //    Get запрос на создание
    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('permissions.create');
    }

    //    Post запрос с сохранением права
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required|unique:permissions,name',
        ]);

        Permission::create(['name' => $request->input('name')]);

        return redirect()->route('permissions.index')
            ->with('success', 'Права успешно созданы');
    }

    //    Ищем по ID право, показываем
    public function show(int $id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $permission = Permission::find($id);

        return view('permissions.show', compact('permission'));
    }

    //   Get запрос на изменение
    public function edit(int $id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $permission = Permission::find($id);

        return view('permissions.edit', compact('permission'));
    }


    //   Post запрос на изменение и обновление по тому же ID
    public function update(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required'
        ]);

        $permission = Permission::find($id);
        $permission->name = $request->input('name');
        $permission->save();

        return redirect()->route('permissions.index')
            ->with('success', 'Права успешно измененены');
    }


    //    Удаление и редирект на главную
    public function destroy(int $id): \Illuminate\Http\RedirectResponse
    {
        Permission::find($id)->delete();

        return redirect()->route('permissions.index')
            ->with('success', 'Права удалены');
    }
}
