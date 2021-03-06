<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    function __construct()
    {
        $this->middleware('permission:user-list|user-create|user-edit|user-delete', ['only' => ['index','store']]);
        $this->middleware('permission:user-create', ['only' => ['create','store']]);
        $this->middleware('permission:user-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:user-delete', ['only' => ['destroy']]);
    }

    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {

        $user = auth()->user();
        $permArray = $user->getAllPermissions()->pluck('name')->toArray();
        if ( in_array( "edit-superadmin" ,$permArray )) {
            $data = User::join('model_has_roles', 'model_has_roles.model_id', 'users.id')->join('role_has_permissions', 'role_has_permissions.role_id', 'model_has_roles.role_id')->whereIn('permission_id', ['20','21', '22', '23'])->paginate(25);;
        } elseif ( in_array( "edit-admin" ,$permArray )) {
            $data = User::join('model_has_roles', 'model_has_roles.model_id', 'users.id')->join('role_has_permissions', 'role_has_permissions.role_id', 'model_has_roles.role_id')->whereIn('permission_id', ['21', '22', '23'])->paginate(25);;
        } elseif ( in_array( "edit-redactor" ,$permArray )) {
            $data = User::join('model_has_roles', 'model_has_roles.model_id', 'users.id')->join('role_has_permissions', 'role_has_permissions.role_id', 'model_has_roles.role_id')->whereIn('permission_id', ['22', '23'])->paginate(25);;
        } elseif ( in_array( "edit-user" ,$permArray )) {
            $data = User::join('model_has_roles', 'model_has_roles.model_id', 'users.id')->join('role_has_permissions', 'role_has_permissions.role_id', 'model_has_roles.role_id')->whereIn('permission_id', ['23'])->paginate(25);;
        }

        return view('users.index', compact('data'));
    }

    public function create(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application
    {
        $user = auth()->user();
        $permArray = $user->getAllPermissions()->pluck('name')->toArray();
        if (in_array( "edit-superadmin" ,$permArray )) {
            $roles = Role::join('role_has_permissions', 'role_has_permissions.role_id', 'roles.id')->whereIn('permission_id', ['20','21', '22', '23'])->pluck('name','name');
        } elseif ( in_array( "edit-admin" ,$permArray )) {
            $roles = Role::join('role_has_permissions', 'role_has_permissions.role_id', 'roles.id')->whereIn('permission_id', ['21', '22', '23'])->pluck('name','name');
        } elseif ( in_array( "edit-redactor" ,$permArray )) {
            $roles = Role::join('role_has_permissions', 'role_has_permissions.role_id', 'roles.id')->whereIn('permission_id', ['22', '23'])->pluck('name','name');
        } elseif ( in_array( "edit-user" ,$permArray )) {
            $roles = Role::join('role_has_permissions', 'role_has_permissions.role_id', 'roles.id')->whereIn('permission_id', ['23'])->pluck('name','name');
        }

        return view('users.create', compact('roles'));
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required',
            'login' => 'required|unique:users,login|max:55',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed',
            'roles' => 'required'
        ]);

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);

        $user = User::create($input);
        $user->assignRole($request->input('roles'));

        return redirect()->route('users.index')
            ->with('success', '???????????????????????? ?????????????? ????????????');
    }

    public function show(int $id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $user = User::find($id);

        return view('users.show', compact('user'));
    }

    public function edit(int $id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $user = User::find($id);
        $roles = Role::pluck('name', 'name')->all();
        $userRole = $user->roles->pluck('name', 'name')->all();

        return view('users.edit', compact('user', 'roles', 'userRole'));
    }


    public function update(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required',
            'login' => 'required|unique:users,login|max:55',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'confirmed',
            'roles' => 'required'
        ]);

        $input = $request->all();

        if(!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = Arr::except($input, array('password'));
        }

        $user = User::find($id);
        $user->update($input);

        DB::table('model_has_roles')
            ->where('model_id', $id)
            ->delete();

        $user->assignRole($request->input('roles'));

        return redirect()->route('users.index')
            ->with('success', '?????????????????? ???????????????????????? ??????????????');
    }

    public function destroy(int $id): \Illuminate\Http\RedirectResponse
    {
        User::find($id)->delete();

        return redirect()->route('users.index')
            ->with('success', '???????????????????????? ?????????????? ????????????');
    }
}
