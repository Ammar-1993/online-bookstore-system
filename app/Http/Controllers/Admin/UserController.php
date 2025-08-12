<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('role','Admin');

        $s = $request->get('s');
        $users = User::when($s, fn($q)=>$q->where('name','like',"%{$s}%")
                                         ->orWhere('email','like',"%{$s}%"))
            ->with('roles')
            ->orderBy('id','desc')
            ->paginate(15)->withQueryString();

        $roles = ['Admin','Seller','User'];

        return view('admin.users.index', compact('users','roles'));
    }

    public function edit(User $user)
    {
        $roles = ['Admin','Seller','User'];
        return view('admin.users.edit', compact('user','roles'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'  => ['required','string','max:120'],
            'email' => ['required','email','max:150','unique:users,email,'.$user->id],
            'roles' => ['required','array'],
            'roles.*' => ['in:Admin,Seller,User'],
        ]);

        // لا تسمح بإزالة آخر Admin
        if ($user->hasRole('Admin') && !in_array('Admin', $data['roles'])) {
            $otherAdmins = \Spatie\Permission\Models\Role::findByName('Admin')
                            ->users()->where('users.id','!=',$user->id)->count();
            if ($otherAdmins === 0) {
                return back()->withErrors('لا يمكن إزالة آخر مدير في النظام.');
            }
        }

        $user->update(['name'=>$data['name'], 'email'=>$data['email']]);
        $user->syncRoles($data['roles']);

        return redirect()->route('admin.users.index')->with('success','تم تحديث المستخدم');
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->withErrors('لا يمكنك حذف نفسك.');
        }
        $user->delete();
        return back()->with('success','تم حذف المستخدم');
    }
}
