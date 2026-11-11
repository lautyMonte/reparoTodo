@if ($errors->any())
    <div class="alert alert-danger" id=alert role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@elseif (session('error'))
    <div class="alert alert-danger" id=alert role="alert">
        <h3>{{ session('error') }}</h3>
    </div>
@endif
