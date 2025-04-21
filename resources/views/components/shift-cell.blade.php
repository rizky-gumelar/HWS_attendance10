@props(['data'])

@php $shiftData = json_decode($data, true); @endphp

<td>
    @if(isset($shiftData['libur']) && $shiftData['libur'])
    <span class="text-danger fw-bold">Libur{{ $shiftData['keterangan_libur'] ? ': ' . $shiftData['keterangan_libur'] : '' }}</span>
    @else
    {{ $shiftData['shift'] ?? '-' }}
    @endif
</td>