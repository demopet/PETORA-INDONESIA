@php
    use App\Support\BranchContext;
    $user = auth()->user();
    $branches = $user?->branches()->where('is_active', true)->orderBy('name')->get() ?? collect();
    $currentId = BranchContext::currentId();
    $isAll = BranchContext::isAllBranches();
    $canAll = $user?->can('platform.branches.view_all');
@endphp

@if($user && ($branches->count() > 1 || $canAll))
    <div class="px-2 py-1 flex items-center gap-2 text-xs">
        <span class="text-gray-500 dark:text-gray-400">Cabang:</span>
        @foreach($branches as $branch)
            <form method="POST" action="{{ route('branch.switch', $branch->id) }}" class="inline">
                @csrf
                <button type="submit" class="px-2 py-1 rounded {{ (!$isAll && (int)$currentId === (int)$branch->id) ? 'bg-primary-600 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200' }}">
                    {{ $branch->code ?? $branch->name }}
                </button>
            </form>
        @endforeach
        @if($canAll)
            <form method="POST" action="{{ route('branch.switch', 'all') }}" class="inline">
                @csrf
                <button type="submit" class="px-2 py-1 rounded {{ $isAll ? 'bg-primary-600 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200' }}">
                    Semua
                </button>
            </form>
        @endif
    </div>
@endif
