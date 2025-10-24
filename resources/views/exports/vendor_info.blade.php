<table>
    <thead>
        <tr>
            <th>Vendor Name</th>
            <th>Vendor Email</th>
            <th>Corporate Name</th>
            <th>Description</th>
            <th>City</th>
            <th>Status</th>
            <th>Phone</th>
            <th>Address</th>
            <th>Latitude</th>
            <th>Longitude</th>
            <th>Landmark Description</th>
            <th>Created At</th>
            <th>Updated At</th>
        </tr>
    </thead>
    <tbody>
        @foreach($vendorInfos as $vendorInfo)
        <tr>
            <td>{{ $vendorInfo->vendor->name ?? '' }}</td>
            <td>{{ $vendorInfo->vendor->email ?? '' }}</td>
            <td>{{ $vendorInfo->name_corporate ?? '' }}</td>
            <td>{{ $vendorInfo->desc ?? '' }}</td>
            <td>{{ $vendorInfo->city->name ?? '' }}</td>
            <td>{{ $vendorInfo->vendor->is_active ? 'Active' : 'Inactive' }}</td>
            <td>{{ $vendorInfo->phone ?? '' }}</td>
            <td>{{ $vendorInfo->address ?? '' }}</td>
            <td>{{ $vendorInfo->coordinate_latitude ?? '' }}</td>
            <td>{{ $vendorInfo->coordinate_longitude ?? '' }}</td>
            <td>{{ $vendorInfo->landmark_description ?? '' }}</td>
            <td>{{ $vendorInfo->created_at ? $vendorInfo->created_at->format('Y-m-d H:i:s') : '' }}</td>
            <td>{{ $vendorInfo->updated_at ? $vendorInfo->updated_at->format('Y-m-d H:i:s') : '' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
