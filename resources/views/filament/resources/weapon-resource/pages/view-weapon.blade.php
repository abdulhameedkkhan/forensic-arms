@php
    use Illuminate\Support\Facades\Storage;

    $armDealer = $record->armDealer;
    $attachments = collect($record->attachments ?? []);
@endphp

<x-filament::page>
    <div class="space-y-6">
        {{-- Header Section --}}
        <x-filament::section>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Weapon Details</h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Weapon Number: {{ $record->weapon_no ?? 'N/A' }}</p>
                    </div>
                    <x-filament::badge color="primary" size="lg">{{ $record->weapon_no ?? 'N/A' }}</x-filament::badge>
                </div>
            </div>
        </x-filament::section>

        <div class="grid gap-6 lg:grid-cols-3">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Basic Information --}}
                <x-filament::section heading="Basic Information">
                    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">CNIC</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $record->cnic ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Weapon Number</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $record->weapon_no ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">FSL Diary Number</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $record->fsl_diary_no ?? '—' }}</dd>
                        </div>
                    </dl>
                </x-filament::section>

                {{-- Arm Dealer Information --}}
                @if($armDealer)
                <x-filament::section heading="Arm Dealer Information">
                    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Dealer Name</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $armDealer->name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Shop Name</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $armDealer->shop_name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Contact</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $armDealer->cell ?? $armDealer->phone ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $armDealer->email ?? '—' }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Address</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $armDealer->address ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">City</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $armDealer->city ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">District</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $armDealer->district ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Police Station</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $armDealer->police_station ?? '—' }}</dd>
                        </div>
                    </dl>
                </x-filament::section>
                @endif

                {{-- Activity --}}
                <x-filament::section heading="Activity">
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created At</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ optional($record->created_at)->format('d M Y, h:i A') ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ optional($record->updated_at)->format('d M Y, h:i A') ?? '—' }}</dd>
                        </div>
                    </dl>
                </x-filament::section>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Attachments --}}
                <x-filament::section heading="Attachments">
                    @if ($attachments->isEmpty())
                        <p class="text-sm text-gray-500 dark:text-gray-400">No attachments uploaded.</p>
                    @else
                        <div class="space-y-3">
                            @foreach ($attachments as $index => $path)
                                @php
                                    // Clean the path - remove any query parameters if present
                                    $cleanPath = ltrim(explode('?', $path)[0], '/');
                                    
                                    // Try public disk first
                                    $publicDisk = Storage::disk('public');
                                    $fileExists = $publicDisk->exists($cleanPath);
                                    
                                    // If not in public, try private disk
                                    if (!$fileExists) {
                                        $privateDisk = Storage::disk('local');
                                        $fileExists = $privateDisk->exists($cleanPath);
                                        
                                        if ($fileExists) {
                                            // For private files, serve them through authenticated route
                                            $url = url('/admin/attachments/' . base64_encode($cleanPath));
                                        }
                                    } else {
                                        // Generate permanent public URL
                                        $url = $publicDisk->url($cleanPath);
                                        if (!str_starts_with($url, 'http')) {
                                            $url = asset('storage/' . $cleanPath);
                                        }
                                    }
                                    
                                    $fileName = basename($cleanPath);
                                @endphp
                                <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $fileName }}</p>
                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 truncate">
                                                Path: {{ $path }}
                                                @if(!$fileExists)
                                                    <span class="text-red-500"> (File not found)</span>
                                                @endif
                                            </p>
                                        </div>
                                        <x-filament::badge color="{{ $fileExists ? 'success' : 'danger' }}" class="ml-2">
                                            #{{ $index + 1 }}
                                        </x-filament::badge>
                                    </div>
                                    @if($fileExists)
                                    <div class="mt-3 flex gap-2">
                                        <x-filament::button size="sm" tag="a" :href="$url" target="_blank" icon="heroicon-o-eye">
                                            View
                                        </x-filament::button>
                                        <x-filament::button size="sm" color="gray" tag="a" :href="$url" download icon="heroicon-o-arrow-down-tray">
                                            Download
                                        </x-filament::button>
                                    </div>
                                    @else
                                    <div class="mt-3">
                                        <x-filament::badge color="danger" size="sm">File not found in storage</x-filament::badge>
                                    </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </x-filament::section>
            </div>
        </div>
    </div>
</x-filament::page>
