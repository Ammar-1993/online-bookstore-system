<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q', ''));
        $roleFilter = trim((string) $request->get('role', ''));

        $users = User::query()
            ->when($q !== '', function (Builder $b) use ($q) {
                $b->where(fn ($x) =>
                    $x->where('name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%")
                );
            })
            ->when($roleFilter !== '', fn ($b) => $b->role($roleFilter)) // من Spatie
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $allRoles = Role::orderBy('name')->pluck('name'); // ['Admin','Seller',..]

        return view('admin.users.index', compact('users', 'q', 'roleFilter', 'allRoles'));
    }

    public function edit(User $user): View
    {
        $allRoles = Role::orderBy('name')->pluck('name'); // أسماء فقط
        $userRoleNames = $user->roles->pluck('name')->all();

        return view('admin.users.edit', compact('user', 'allRoles', 'userRoleNames'));
    }

    public function update(UserUpdateRequest $request, User $user): RedirectResponse
    {
        // حراسة "آخر أدمن" قبل أي تعديل أدوار
        $wasAdmin = $user->hasRole('Admin');

        $data = $request->validated();

        // حقول أساسية
        $user->name  = $data['name'];
        $user->email = $data['email'];

        // كلمة المرور (اختيارية)
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        // التحقق من البريد (اختياري)
        if ($request->boolean('mark_verified')) {
            if (is_null($user->email_verified_at)) {
                $user->email_verified_at = now();
            }
        }

        $user->save();

        // أدوار
        $selectedRoles = collect($data['roles'] ?? [])->filter()->values()->all();

        // منع إزالة دور Admin من آخر مشرف
        if ($wasAdmin && !in_array('Admin', $selectedRoles, true)) {
            if ($this->adminCountExcluding($user->id) === 0) {
                return back()->with('error', 'لا يمكن إزالة صلاحية المشرف عن هذا المستخدم لأنه آخر مشرف في النظام.');
            }
        }

        // مزامنة الأدوار (أسماء)
        if (!empty($selectedRoles)) {
            $user->syncRoles($selectedRoles);
        } else {
            // إن لم تُرسل أدوار، لا نزيل كل شيء تلقائيًا — إلا لو تحب ذلك:
            $user->syncRoles([]); // إن رغبت بإزالة كل الأدوار عند تفريغها.
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', "تم تحديث المستخدم «{$user->name}» بنجاح.");
    }

    public function destroy(User $user): RedirectResponse
    {
        // لا تحذف نفسك
        if (auth()->id() === $user->id) {
            return back()->with('error', 'لا يمكنك حذف حسابك الخاص.');
        }

        // لا تحذف آخر مشرف
        if ($user->hasRole('Admin') && $this->adminCountExcluding($user->id) === 0) {
            return back()->with('error', 'لا يمكن حذف هذا المستخدم لأنه آخر مشرف في النظام.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', "تم حذف المستخدم «{$name}» بنجاح.");
    }

    private function adminCountExcluding(int $excludingId): int
    {
        return User::role('Admin')->where('id', '!=', $excludingId)->count();
    }
}
