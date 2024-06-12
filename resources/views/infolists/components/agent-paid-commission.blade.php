<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div>
        {{ $calculatePaid() }}
    </div>
</x-dynamic-component>
