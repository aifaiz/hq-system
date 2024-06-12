<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div>
        {{$calculateUnpaid()}}
    </div>
</x-dynamic-component>
