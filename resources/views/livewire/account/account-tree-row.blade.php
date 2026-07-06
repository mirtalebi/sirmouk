@php
    $hasChildren = isset($account['children']) && count($account['children']) > 0;
    $isExpanded = in_array($account['id'], $expandedAccounts ?? []);
    $paddingLeft = ($level ?? 0) * 2; // 2rem per level
    $backgroundColor = $level === 0 ? 'bg-white dark:bg-neutral-900' : 'bg-gray-50 dark:bg-neutral-800';
@endphp

<tr
    class="{{ $backgroundColor }} hover:bg-gray-100 dark:hover:bg-neutral-700 border-b border-gray-200 dark:border-neutral-700">
    <td class="px-6 py-4 text-right">
        <div style="padding-right: {{ $paddingLeft }}rem;">
            {{ $account['code'] ?? '-' }}
        </div>
    </td>
    <td class="px-6 py-4 text-right">
        <div class="flex items-center gap-2">
            @if ($hasChildren)
                <button wire:click="toggleExpand({{ $account['id'] }})"
                    class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                    @if ($isExpanded)
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                    @else
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                    @endif
                </button>
            @else
                <span class="w-5"></span>
            @endif
            <span class="dark:text-white font-medium">{{ $account['name'] }}</span>
        </div>
    </td>
    <td class="px-6 py-4 text-right">
        <span
            class="px-3 py-1 rounded-full text-xs font-semibold
            @if ($account['type'] === 'asset') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
            @elseif ($account['type'] === 'liability')
                bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
            @elseif ($account['type'] === 'equity')
                bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
            @elseif ($account['type'] === 'revenue')
                bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
            @elseif ($account['type'] === 'expense')
                bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200 @endif
        ">
            @php
                $typeLabels = [
                    'asset' => 'دارایی',
                    'liability' => 'بدهی',
                    'equity' => 'سرمایه',
                    'revenue' => 'درآمد',
                    'expense' => 'هزینه',
                ];
            @endphp
            {{ $typeLabels[$account['type']] ?? $account['type'] }}
        </span>
    </td>
    <td class="px-6 py-4 text-center">
        <span class="font-bold text-lg dark:text-white">
            {{ number_format($account['balance'] ?? 0) }}
        </span>
    </td>
    <td class="px-6 py-4 text-center">
        <div class="flex justify-center gap-2">
            <a href="{{ route('accounts.edit', $account['id']) }}"
                class="px-3 py-1 bg-orange-500 hover:bg-orange-600 text-white text-xs rounded font-bold">
                ویرایش
            </a>
            <button wire:click="delete({{ $account['id'] }})"
                class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs rounded font-bold"
                onclick="return confirm('آیا مطمئن هستید؟')">
                حذف
            </button>
        </div>
    </td>
</tr>

<!-- Children Rows -->
@if ($hasChildren && $isExpanded)
    @foreach ($account['children'] as $child)
        @include('livewire.account.account-tree-row', [
            'account' => $child,
            'level' => ($level ?? 0) + 1,
            'expandedAccounts' => $expandedAccounts,
        ])
    @endforeach
@endif
