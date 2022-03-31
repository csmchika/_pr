<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function create()
    {
        $user = auth()->user();
        $roleIdStr = DB::table('model_has_roles')->where('model_id', $user->id)->first();
        $roleId = $roleIdStr->role_id;
        $rolePermissions = Permission::join('role_has_permissions', 'role_has_permissions.permission_id', 'permissions.id')
            ->where('role_has_permissions.role_id', $roleId)
            ->get();
        return view('roles.create', compact('rolePermissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = Role::find($id);
        $rolePermissions = Permission::join('role_has_permissions', 'role_has_permissions.permission_id', 'permissions.id')
            ->where('role_has_permissions.role_id',$id)
            ->get();

        return view('roles.show', compact('role', 'rolePermissions'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function edit($id)
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        Role::find($id)->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Роль удалена');
    }
}
