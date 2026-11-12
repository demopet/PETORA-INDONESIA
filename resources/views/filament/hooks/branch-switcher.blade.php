@props(['branches' => collect(), 'currentId' => null])

@if($branches->count() > 0)
    <div class="px-2 pb-2">
        <form method="POST" action="{{ route('branch.switch', ['branch' => $currentId ?: $branches->first()->id]) }}" x-data="{ branch: '{{ $currentId ?: $branches->first()->id }}' }" x-ref="form">
            @csrf
            <label class="sr-only" for="active-branch-switcher">Cabang aktif</label>
            <select id="active-branch-switcher" name="branch" x-model="branch"
                class="fi-input block w-full rounded-lg border-gray-300 text-sm shadow-sm dark:border-gray-600 dark:bg-gray-900"
                @change="$refs.form.action = '{{ url('/admin/branch-switch') }}/' + branch; $refs.form.submit()">
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" @selected((int)$currentId === (int)$branch->id)>
                        {{ $branch->name }}{{ (int)$currentId === (int)$branch->id ? ' (aktif)' : '' }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>
@endif
