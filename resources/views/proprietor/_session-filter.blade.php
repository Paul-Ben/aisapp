<form method="get" class="d-flex align-items-center gap-2">
    <label for="session_id" class="form-label mb-0 text-muted small">Period</label>
    <select id="session_id" name="session_id" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
        @foreach ($sessions as $s)
            <option value="{{ $s->id }}" @selected($session && $session->id === $s->id)>
                {{ $s->session }} · {{ ucfirst($s->term) }} term{{ $s->is_active ? ' (active)' : '' }}
            </option>
        @endforeach
    </select>
</form>
