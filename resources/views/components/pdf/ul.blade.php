<div class="text-justify" style="margin-bottom: 7px">
    <ul start="{{ $start ?? '1' }}"
        style="list-style-type: lower-alpha; margin-left: {{ $marleft ?? '19' }}px; margin-top: 0px; margin-bottom: 0px">
        {{ $slot }}
    </ul>
</div>
