{{-- resources/views/admin/profile.blade.php --}}
@extends('admin.layouts.app')
@section('title', 'الملف الشخصي')

@section('content')
  <div class="max-w-3xl mx-auto space-y-6">
    {{-- تحديث المعلومات الأساسية + الصورة --}}
    @if (Laravel\Fortify\Features::canUpdateProfileInformation())
      @livewire('profile.update-profile-information-form')
    @endif

    <x-section-border />

    {{-- تحديث كلمة المرور --}}
    @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
      @livewire('profile.update-password-form')
      <x-section-border />
    @endif

    {{-- التحقق الثنائي (2FA) --}}
    @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
      @livewire('profile.two-factor-authentication-form')
      <x-section-border />
    @endif

    {{-- الجلسات الأخرى --}}
    @livewire('profile.logout-other-browser-sessions-form')

    {{-- حذف الحساب (إن كانت مفعلة في Jetstream) --}}
    @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
      <x-section-border />
      @livewire('profile.delete-user-form')
    @endif
  </div>
@endsection
