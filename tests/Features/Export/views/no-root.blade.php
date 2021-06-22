<files>
    @foreach($files as $file)
        <file>
            <name>{{ $file['name'] }}</name>
            <type>{{ $file['type'] }}</type>
        </file>
    @endforeach
</files>