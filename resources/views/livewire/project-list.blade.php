<div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6 overflow-hidden" wire:poll.3s>
    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
        <h2 class="text-base font-semibold text-gray-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            My Projects Library
        </h2>
    </div>

    @if($projects->isEmpty())
        <div class="text-center py-12">
            <div class="bg-gray-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
            </div>
            <p class="text-gray-500 font-medium">No projects imported yet.</p>
            <p class="text-gray-400 text-sm mt-1">Select a Revit file from the browser below to start.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-500 font-semibold uppercase text-xs tracking-wider">
                    <tr>
                        <th class="px-6 py-3">Project Name</th>
                        <th class="px-6 py-3 text-center">Status</th>
                        <th class="px-6 py-3 text-center">Elements</th>
                        <th class="px-6 py-3 text-right">Created</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($projects as $project)
                        <tr class="hover:bg-blue-50/50 transition-colors group">
                            <td class="px-6 py-4">
                                <a href="{{ route('project-dashboard', $project->id) }}" class="font-semibold text-gray-800 hover:text-blue-600 flex items-center gap-2">
                                    {{ $project->name }}
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                </a>
                            </td>
                            
                            <td class="px-6 py-4 text-center">
                                @if($project->status == 'processing')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200 animate-pulse">
                                        Processing
                                    </span>
                                @elseif($project->status == 'ready')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        Ready
                                    </span>
                                @else
                                    <div class="group/err relative inline-block">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200 cursor-help">
                                            Error
                                        </span>
                                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover/err:block w-64 p-3 bg-gray-800 text-white text-xs rounded-lg shadow-xl z-50">
                                            {{ $project->error_message }}
                                        </div>
                                    </div>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-center text-gray-600 font-mono">
                                {{ $project->elements()->count() > 0 ? number_format($project->elements()->count()) : '-' }}
                            </td>

                            <td class="px-6 py-4 text-right text-gray-400 text-xs">
                                {{ $project->created_at->diffForHumans() }}
                            </td>

                            <td class="px-6 py-4 text-right flex justify-end gap-2 items-center">
                                <button wire:click="viewLogs({{ $project->id }})" class="p-1.5 text-gray-400 hover:text-gray-800 hover:bg-gray-100 rounded-md transition" title="View Logs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                </button>
                                
                                <button wire:click="deleteProject({{ $project->id }})" wire:confirm="Are you sure?" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-md transition" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
    
    @if($showLogModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm">
            <div class="bg-gray-900 w-full max-w-3xl rounded-lg shadow-2xl overflow-hidden border border-gray-700 flex flex-col h-[500px]">
                <div class="bg-gray-800 px-4 py-3 border-b border-gray-700 flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-red-500"></div>
                        <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                        <div class="w-3 h-3 rounded-full bg-green-500"></div>
                        <span class="ml-2 text-gray-300 text-xs font-mono">Debug Console</span>
                    </div>
                    <button wire:click="closeLogs" class="text-gray-400 hover:text-white">&times;</button>
                </div>
                <div wire:poll.1s="refreshLogs" class="flex-1 bg-black p-4 overflow-y-auto font-mono text-xs text-green-400 space-y-1">
                    @if(empty($selectedProjectLogs)) <div class="text-gray-500 italic">Waiting...</div> @else
                        @foreach($selectedProjectLogs as $log) <div>{{ $log }}</div> @endforeach
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>