<form method="POST" action="{{ route('linkedin.post') }}">
    @csrf
    <textarea name="message" rows="4" cols="50" required></textarea>
    <br>
    <button type="submit">Опубликовать в LinkedIn</button>
</form>

@if (session('success'))
    <p style="color:green;">{{ session('success') }}</p>
@endif

@if (session('error'))
    <p style="color:red;">{{ session('error') }}</p>
@endif
