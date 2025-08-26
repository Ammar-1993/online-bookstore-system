<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $role = $request->get('role');

        // فرز آمن
        $sort = $request->get('sort', 'id');            // id | name | email | books_count | created_at
        $dir = strtolower($request->get('dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $allowedSorts = ['id', 'name', 'email', 'books_count', 'created_at'];
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'id';
        }

        $query = User::query()
            ->when($q !== '', function (Builder $b) use ($q) {
                $b->where(fn($x) => $x->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%"));
            })
            ->when($role, fn($b) => $b->role($role))
            ->with('roles:name')   // لعرض الأدوار
            ->withCount('books');  // عدد كتب البائع (إن وُجد)

        // تطبيق الترتيب
        if ($sort === 'books_count') {
            $query->orderBy('books_count', $dir);
        } else {
            $query->orderBy($sort, $dir);
        }

        $users = $query->paginate(12)->withQueryString();
        $roles = Role::pluck('name'); // ['Admin','Seller', ...]

        return view('admin.users.index', compact('users', 'q', 'role', 'roles', 'sort', 'dir'));
    }


    public function edit(User $user)
    {
        $roles = Role::pluck('name'); // الأدوار المتاحة
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(UserUpdateRequest $request, User $user)
    {
        $data = $request->validated();

        // حماية: لا تزيل دور Admin من نفسك إذا كنت آخر أدمن
        if (
            $user->id === auth()->id()
            && !in_array('Admin', $data['roles'], true)
        ) {
            // لو المستخدم الحالي بيشيل نفسه من الأدمن، تأكد فيه أدمن غيره
            $otherAdmins = User::role('Admin')->where('id', '!=', $user->id)->exists();
            if (!$otherAdmins) {
                return back()->with('error', 'لا يمكن إزالة دور المدير عن نفسك لأنك آخر مدير!');
            }
        }

        // تحديث بيانات أساسية
        $user->name = $data['name'];
        $user->email = $data['email'];

        if (!empty($data['password'])) {
            $user->password = bcrypt($data['password']);
        }

        $user->save();

        // مزامنة الأدوار
        $user->syncRoles($data['roles']);

        return redirect()->route('admin.users.index')
            ->with('success', "تم تحديث المستخدم «{$user->name}» بنجاح.");
    }

    public function destroy(User $user)
    {
        // حماية: لا تحذف نفسك
        if ($user->id === auth()->id()) {
            return back()->with('error', 'لا يمكنك حذف نفسك.');
        }

        // حماية: لا تحذف آخر أدمن
        if ($user->hasRole('Admin')) {
            $otherAdmins = User::role('Admin')->where('id', '!=', $user->id)->exists();
            if (!$otherAdmins) {
                return back()->with('error', 'لا يمكن حذف هذا المستخدم لأنه آخر مدير.');
            }
        }

        // اختيار: منع حذف بائع لديه كتب
        if ($user->hasRole('Seller') && $user->books()->exists()) {
            return back()->with('error', 'لا يمكن حذف المستخدم لوجود كتب مرتبطة به.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "تم حذف المستخدم «{$name}» بنجاح.");
    }
}
