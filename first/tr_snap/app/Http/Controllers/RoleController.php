<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * create a new instance of the class
     *
     * @return void
     */
    function __construct()
    {
        $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index','store']]);
        $this->middleware('permission:role-create', ['only' => ['create','store']]);
        $this->middleware('permission:role-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }

//Функция отвечающая на get при выводе всего
    public function index(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application
    {
//        Если пользователь имеет право edit-superadmin(только суперадмин, тогда выводим все роли по правам edit )
        $user = auth()->user();
        $permArray = $user->getAllPermissions()->pluck('name')->toArray();
        if ( in_array( "edit-superadmin" ,$permArray )) {
            $data = Role::join('role_has_permissions', 'role_has_permissions.role_id', 'roles.id')->whereIn('permission_id', ['20','21', '22', '23'])->paginate(25);
        } elseif ( in_array( "edit-admin" ,$permArray )) {
            $data = Role::join('role_has_permissions', 'role_has_permissions.role_id', 'roles.id')->whereIn('permission_id', ['21', '22', '23'])->paginate(25);
        } elseif ( in_array( "edit-redactor" ,$permArray )) {
            $data = Role::join('role_has_permissions', 'role_has_permissions.role_id', 'roles.id')->whereIn('permission_id', ['22', '23'])->paginate(25);
        } elseif ( in_array( "edit-user" ,$permArray )) {
            $data = Role::join('role_has_permissions', 'role_has_permissions.role_id', 'roles.id')->whereIn('permission_id', ['23'])->paginate(25);
        }


//        for ($i = 20; $i < 24; $i++) {
//            $perm = DB::table('permissions')->where('permissions_id', $i)->first()->name;
//            if (in_array($perm, $permArray)) {
//                array_push($data,  )
//            }
//
//            $data = Role::join('role_has_permissions', 'role_has_permissions.role_id', 'roles.id')->where('role_has_permissions.permission_id', $i)->where('roles.id', not())->toArray();
//        }
////       $data = Role::join('role_has_permissions', 'role_has_permissions.role_id', 'roles.id')->where('role_has_permissions.permission_id', $i)->where('roles.id', $roleId)->paginate(5)

//        $permArr = $user->getAllPermissions()->pluck('name')->toArray();

//        $data = Role::join('role_has_permissions', 'role_has_permissions.role_id', 'roles.id')->where('role_has_permissions.permission_id', 23)->paginate(5);

        return view('roles.index', compact('data'));
    }
//    Функция отвечающая на get при создании ресурса роли
    public function create(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application
    {
        //  Берем права, которые есть у пользователя, те, которых у него нет, тут нет

        $user = auth()->user();
        $roleIdStr = DB::table('model_has_roles')->where('model_id', $user->id)->first();
        $roleId = $roleIdStr->role_id;
        $rolePermissions = Permission::join('role_has_permissions', 'role_has_permissions.permission_id', 'permissions.id')
            ->where('role_has_permissions.role_id', $roleId)
            ->get();
        return view('roles.create', compact('rolePermissions'));
    }

//    Функция отвечающая на post при создании ресурса роли

    /**
     * @throws ValidationException
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        ]);

        $role = Role::create(['name' => $request->input('name')]);
        $role->syncPermissions($request->input('permission'));

        return redirect()->route('roles.index')
            ->with('success', 'Роль успешно создана');
    }

    public function show(int $id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $role = Role::find($id);
        $rolePermissions = Permission::join('role_has_permissions', 'role_has_permissions.permission_id', 'permissions.id')
            ->where('role_has_permissions.role_id',$id)
            ->get();

        return view('roles.show', compact('role', 'rolePermissions'));
    }

    public function edit(int $id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $role = Role::find($id);
        $user = auth()->user();
        $roleIdStr = DB::table('model_has_roles')->where('model_id', $user->id)->first();
        $roleId = $roleIdStr->role_id;
        $permission = Permission::join('role_has_permissions', 'role_has_permissions.permission_id', 'permissions.id')
            ->where('role_has_permissions.role_id',$roleId)
            ->get();

        $rolePermissions = DB::table('role_has_permissions')
            ->where('role_has_permissions.role_id', $id)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();

        return view('roles.edit', compact('role', 'permission', 'rolePermissions'));
    }


    public function update(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required',
            'permission' => 'required',
        ]);

        $role = Role::find($id);
        $role->name = $request->input('name');
        $role->save();

        $role->syncPermissions($request->input('permission'));

        return redirect()->route('roles.index')
            ->with('success', 'Роль успешно изменена');
    }


    public function destroy(int $id): \Illuminate\Http\RedirectResponse
    {
        Role::find($id)->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Роль удалена');
    }
}
