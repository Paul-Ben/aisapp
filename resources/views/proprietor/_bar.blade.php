{{-- Labelled horizontal bar. Props: $label, $value (display text), $percent (0-100), $color (bootstrap colour) --}}
<div class="mb-3">
    <div class="d-flex justify-content-between small mb-1">
        <span class="text-truncate me-2">{{ $label }}</span>
        <span class="fw-semibold text-nowrap">{{ $value }}</span>
    </div>
    <div class="progress" style="height: 8px;">
        <div class="progress-bar bg-{{ $color ?? 'primary' }}" style="width: {{ max(0, min(100, $percent)) }}%"></div>
    </div>
</div>
