<table id="unitsTable" class="table shadow-sm table-hover table-striped">
    <thead class="shadow">
        <tr>
            <th>ID</th>
            <th>Picture</th>
            <th>Name</th>
            <th>Type</th>
            <th>Price</th>
            <th>Status</th>
            <th>Sale or Rent</th>
            <th>Availability Status</th>
            <th>Building</th>
            <th>Level</th>
            <th>Organization</th>
            <th class="w-170 text-center">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($units as $unit)
            <tr>
                <td>{{ $unit->id }}</td>
                <td>
                    @if ($unit->pictures && $unit->pictures->count() > 0)
                        <img src="{{ asset($unit->pictures->first()->file_path) }}" alt="Unit Picture" style="border-radius: 5px;" width="100" height="50">
                    @else
                        <img src="https://via.placeholder.com/150" alt="Placeholder Image" style="border-radius: 5px;" width="100" height="50">
                    @endif
                </td>
                <td>{{ $unit->unit_name }}</td>
                <td>
                    {{ $unit->unit_type }}
                </td>
                <td>{{ $unit->price ?? 'N/A' }}</td>
                <td>{{ $unit->status ?? 'N/A' }}</td>
                <td>{{ $unit->sale_or_rent ?? 'N/A' }}</td>
                <td>{{ $unit->availability_status ?? 'N/A' }}</td>
                <td>{{ $unit->level->building->name ?? 'N/A' }}</td>
                <td>{{ $unit->level->level_name ?? 'N/A' }}</td>
                <td>{{ $unit->organization->name ?? 'N/A' }}</td>
                <td class="text-center">
                    <a href="{{ route('units.edit', $unit->id) }}" class="text-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                        <i class="fa fa-pencil mx-2" style="font-size: 20px;"></i>
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="12" class="text-center">No units found.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="mt-3">
    {{ $units->links('pagination::bootstrap-5') }}
</div>