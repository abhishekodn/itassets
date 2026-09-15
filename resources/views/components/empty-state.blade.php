@props(['colspan' => 1, 'message' => 'Nothing here yet.'])

<tr>
    <td colspan="{{ $colspan }}" class="px-4 py-14 text-center">
        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-400">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0l-2.5 6.5a1 1 0 01-.94.5H9.44a1 1 0 01-.94-.5L6 13m14 0H6" />
            </svg>
        </div>
        <p class="mt-3 text-sm text-gray-500">{{ $message }}</p>
    </td>
</tr>
