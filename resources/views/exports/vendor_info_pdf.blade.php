<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Vendor Details Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
            color: #333;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .status-active {
            color: #10b981;
            font-weight: bold;
        }
        .status-inactive {
            color: #ef4444;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .page-break {
            page-break-before: always;
        }
        @media print {
            body { margin: 0; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Vendor Details Report</h1>
        <p>Generated on: {{ date('Y-m-d H:i:s') }}</p>
        <p>Total Records: {{ $vendorInfos->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 15%;">Vendor Name</th>
                <th style="width: 20%;">Vendor Email</th>
                <th style="width: 15%;">Corporate Name</th>
                <th style="width: 10%;">City</th>
                <th style="width: 8%;">Status</th>
                <th style="width: 12%;">Phone</th>
                <th style="width: 15%;">Address</th>
            </tr>
        </thead>
        <tbody>
            @foreach($vendorInfos as $index => $vendorInfo)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $vendorInfo->vendor->name ?? 'N/A' }}</td>
                <td>{{ $vendorInfo->vendor->email ?? 'N/A' }}</td>
                <td>{{ $vendorInfo->name_corporate ?? 'N/A' }}</td>
                <td>{{ $vendorInfo->city->name ?? 'N/A' }}</td>
                <td>
                    <span class="{{ $vendorInfo->vendor->is_active ? 'status-active' : 'status-inactive' }}">
                        {{ $vendorInfo->vendor->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td>{{ $vendorInfo->phone ?? 'N/A' }}</td>
                <td>{{ $vendorInfo->address ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>This report was generated automatically by the OTA Website system.</p>
        <p>© {{ date('Y') }} OTA Website - All rights reserved.</p>
    </div>
</body>
</html>
