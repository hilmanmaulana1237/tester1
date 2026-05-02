@php
    use Illuminate\Support\Facades\Storage;
    
    // Use the passed data directly
    $submissionData = [
        'timestamp' => $submittedAt ?? null,
        'description' => $description ?? null,
        'files' => $files ?? []
    ];
@endphp

<div class="space-y-4">
    @if($submissionData && ($submissionData['timestamp'] || $submissionData['description'] || !empty($submissionData['files'])))
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
            {{-- Header --}}
            <div class="flex items-start mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-md">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <h4 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                        {{ $proofType ?? 'Proof' }} Submission
                    </h4>
                    @if($submissionData['timestamp'])
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1.5 flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-medium">{{ $submissionData['timestamp'] }}</span>
                        </p>
                    @endif
                </div>
            </div>
            
            {{-- Description --}}
            @if($submissionData['description'])
                <div class="mb-6">
                    <h5 class="text-sm font-bold text-gray-900 dark:text-gray-100 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                        </svg>
                        Description
                    </h5>
                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                        <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-wrap">{{ $submissionData['description'] }}</p>
                    </div>
                </div>
            @endif
            
            {{-- Files --}}
            @if(!empty($submissionData['files']))
                <div>
                    <h5 class="text-sm font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Uploaded Files
                        <span class="ml-auto text-xs font-semibold px-2.5 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full">
                            {{ count($submissionData['files']) }} file{{ count($submissionData['files']) > 1 ? 's' : '' }}
                        </span>
                    </h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($submissionData['files'] as $filePath)
                            @php
                                $fileName = basename($filePath);
                                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                                $isImage = in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
                                $storageUrl = Storage::url($filePath);
                            @endphp
                            
                            <div class="group bg-white dark:bg-gray-800 rounded-xl border-2 border-gray-200 dark:border-gray-700 overflow-hidden hover:border-green-400 dark:hover:border-green-600 hover:shadow-lg transition-all duration-200">
                                @if($isImage)
                                    {{-- Image Preview with Fixed Size --}}
                                    <a href="{{ $storageUrl }}" target="_blank" class="block relative h-48 bg-gray-100 dark:bg-gray-900 overflow-hidden">
                                        <img src="{{ $storageUrl }}" alt="{{ $fileName }}" 
                                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/0 to-black/0 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
                                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                            <div class="bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 px-4 py-2 rounded-lg text-sm font-semibold shadow-xl flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                View Full Size
                                            </div>
                                        </div>
                                    </a>
                                @else
                                    {{-- File Icon with Fixed Size --}}
                                    <div class="h-48 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-800 dark:to-gray-900 flex items-center justify-center">
                                        <div class="text-center">
                                            <svg class="w-16 h-16 text-gray-400 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                            </svg>
                                            <span class="inline-block px-3 py-1 text-xs font-bold text-gray-600 dark:text-gray-400 bg-gray-200 dark:bg-gray-700 rounded-full uppercase">
                                                {{ $fileExtension }}
                                            </span>
                                        </div>
                                    </div>
                                @endif
                                
                                {{-- File Info --}}
                                <div class="p-4 bg-gray-50 dark:bg-gray-900/50 border-t-2 border-gray-200 dark:border-gray-700">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate mb-3 leading-tight" title="{{ $fileName }}">
                                        {{ $fileName }}
                                    </p>
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                            </svg>
                                            {{ strtoupper($fileExtension) }}
                                        </span>
                                        <a href="{{ $storageUrl }}" target="_blank" 
                                           class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-bold bg-green-600 text-white hover:bg-green-700 transition-colors shadow-sm hover:shadow-md">
                                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                @if($isImage)
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                @else
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                @endif
                                            </svg>
                                            {{ $isImage ? 'View' : 'Download' }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @else
        <div class="text-center py-16">
            <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-800 dark:to-gray-700 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm">
                <svg class="w-10 h-10 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">No Submission Available</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 max-w-sm mx-auto">No submission details have been provided for this proof yet.</p>
        </div>
    @endif
</div>
