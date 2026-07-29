<div class="flex flex-wrap gap-1 mb-6 text-sm border-b border-slate-200 pb-3">
    <a href="{{ route('admin.landing.settings.edit') }}" class="px-3 py-1.5 rounded-md {{ request()->routeIs('admin.landing.settings.*') ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">{{ __('admin.landing.nav_settings') }}</a>
    <a href="{{ route('admin.landing.features.index') }}" class="px-3 py-1.5 rounded-md {{ request()->routeIs('admin.landing.features.*') ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">{{ __('admin.landing.nav_features') }}</a>
    <a href="{{ route('admin.landing.showcase.index') }}" class="px-3 py-1.5 rounded-md {{ request()->routeIs('admin.landing.showcase.*') ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">{{ __('admin.landing.nav_showcase') }}</a>
    <a href="{{ route('admin.landing.testimonials.index') }}" class="px-3 py-1.5 rounded-md {{ request()->routeIs('admin.landing.testimonials.*') ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">{{ __('admin.landing.nav_testimonials') }}</a>
    <a href="{{ route('admin.landing.packages.index') }}" class="px-3 py-1.5 rounded-md {{ request()->routeIs('admin.landing.packages.*') ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">{{ __('admin.landing.nav_packages') }}</a>
    <a href="{{ route('admin.landing.faqs.index') }}" class="px-3 py-1.5 rounded-md {{ request()->routeIs('admin.landing.faqs.*') ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">{{ __('admin.landing.nav_faqs') }}</a>
</div>
