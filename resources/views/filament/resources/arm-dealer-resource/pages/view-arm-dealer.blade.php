<x-filament-panels::page>
    <div class="grid grid-cols-1 gap-6">
        <!-- Arm Dealer Information Section -->
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-header flex items-center gap-3 px-2 py-2 sm:px-6">
                <div class="flex flex-col gap-1">
                    <div class="flex items-center gap-2">
                        <h3 class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                            Arm Dealer Information
                        </h3>
                    </div>
                </div>
            </div>
            
            <div class="fi-section-content-ctn p-6">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            Name
                        </label>
                        <div class="text-base font-medium text-gray-950 dark:text-white py-2 px-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            {{ $record->name }}
                        </div>
                    </div>
                    
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            Shop Name
                        </label>
                        <div class="text-base font-medium text-gray-950 dark:text-white py-2 px-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            {{ $record->shop_name ?? '-' }}
                        </div>
                    </div>
                    
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            Cell Number
                        </label>
                        <div class="text-base font-medium text-gray-950 dark:text-white py-2 px-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            {{ $record->cell ?? '-' }}
                        </div>
                    </div>
                    
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            Phone Number
                        </label>
                        <div class="text-base font-medium text-gray-950 dark:text-white py-2 px-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            {{ $record->phone ?? '-' }}
                        </div>
                    </div>
                    
                    <div class="flex flex-col gap-1 sm:col-span-2">
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            Email
                        </label>
                        <div class="text-base font-medium text-gray-950 dark:text-white py-2 px-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            {{ $record->email ?? '-' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Address & Location Section -->
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-header flex items-center gap-3 px-2 py-2 sm:px-6">
                <div class="flex flex-col gap-1">
                    <div class="flex items-center gap-2">
                        <h3 class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                            Address & Location
                        </h3>
                    </div>
                </div>
            </div>
            
            <div class="fi-section-content-ctn p-6">
                <div class="grid grid-cols-1 gap-4">
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            Address
                        </label>
                        <div class="text-base font-medium text-gray-950 dark:text-white py-2 px-3 bg-gray-50 dark:bg-gray-800 rounded-lg min-h-[60px]">
                            {{ $record->address ?? '-' }}
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div class="flex flex-col gap-1">
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                City
                            </label>
                            <div class="text-base font-medium text-gray-950 dark:text-white py-2 px-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                {{ $record->city ?? '-' }}
                            </div>
                        </div>
                        
                        <div class="flex flex-col gap-1">
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                District
                            </label>
                            <div class="text-base font-medium text-gray-950 dark:text-white py-2 px-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                {{ $record->district ?? '-' }}
                            </div>
                        </div>
                        
                        <div class="flex flex-col gap-1">
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                Police Station
                            </label>
                            <div class="text-base font-medium text-gray-950 dark:text-white py-2 px-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                {{ $record->police_station ?? '-' }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div class="flex flex-col gap-1">
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                Postal Code
                            </label>
                            <div class="text-base font-medium text-gray-950 dark:text-white py-2 px-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                {{ $record->postal_code ?? '-' }}
                            </div>
                        </div>
                        
                        <div class="flex flex-col gap-1">
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                Latitude
                            </label>
                            <div class="text-base font-medium text-gray-950 dark:text-white py-2 px-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                {{ $record->latitude ?? '-' }}
                            </div>
                        </div>
                        
                        <div class="flex flex-col gap-1">
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                Longitude
                            </label>
                            <div class="text-base font-medium text-gray-950 dark:text-white py-2 px-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                {{ $record->longitude ?? '-' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- License Information Section -->
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-header flex items-center gap-3 px-2 py-2 sm:px-6">
                <div class="flex flex-col gap-1">
                    <div class="flex items-center gap-2">
                        <h3 class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                            License Information
                        </h3>
                    </div>
                </div>
            </div>
            
            <div class="fi-section-content-ctn p-6">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            License Number
                        </label>
                        <div class="text-base font-medium text-gray-950 dark:text-white py-2 px-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            {{ $record->license_number ?? '-' }}
                        </div>
                    </div>
                    
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            License Expiry
                        </label>
                        <div class="text-base font-medium text-gray-950 dark:text-white py-2 px-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            {{ $record->license_expiry ? \Carbon\Carbon::parse($record->license_expiry)->format('d/m/Y') : '-' }}
                        </div>
                    </div>
                    
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            Status
                        </label>
                        <div class="py-2 px-3">
                            @php
                                $statusColors = [
                                    'active' => 'bg-success-100 text-success-800 dark:bg-success-900 dark:text-success-200',
                                    'inactive' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
                                    'suspended' => 'bg-danger-100 text-danger-800 dark:bg-danger-900 dark:text-danger-200',
                                ];
                                $statusLabels = [
                                    'active' => 'Active',
                                    'inactive' => 'Inactive',
                                    'suspended' => 'Suspended',
                                ];
                            @endphp
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold {{ $statusColors[$record->status] ?? $statusColors['inactive'] }}">
                                {{ $statusLabels[$record->status] ?? 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Information Section -->
        @if($record->notes)
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-header flex items-center gap-3 px-2 py-2 sm:px-6">
                <div class="flex flex-col gap-1">
                    <div class="flex items-center gap-2">
                        <h3 class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                            Additional Information
                        </h3>
                    </div>
                </div>
            </div>
            
            <div class="fi-section-content-ctn p-6">
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Notes
                    </label>
                    <div class="text-base text-gray-950 dark:text-white py-2 px-3 bg-gray-50 dark:bg-gray-800 rounded-lg min-h-[80px] whitespace-pre-wrap">
                        {{ $record->notes }}
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</x-filament-panels::page>