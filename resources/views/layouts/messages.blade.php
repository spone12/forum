
    {{-- Добавить сессию
        return redirect()->route('name')->with('success', 'Сообщение было добавлено');
        
        
    @if($errors->any())
    <div class='alert alert-danger'>
        <ul>
            @foreach($errors->any() as $error)
                <li> {{ $error }}</li>
            @endforeach
        </ul>    
    </div>
    @endif
    --}}
    
    @if(session('success'))
    <div class='alert alert-success'>
        {{session('success') }}
    </div>
    @endif