@extends('layouts.superadmin')

@section('title', 'Rekon Management')

@section('welcome')
Rekon Management
@endsection

@section('logout_route', route('super_admin.logout'))

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="space-y-6">
        <!-- Rekon Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Margin Value</dt>
                                <dd class="text-lg font-medium text-gray-900">Rp {{ number_format($marginValue, 0, ',', '.') }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-indigo-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Nominal Transaction</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($nominalTransaction) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rekon Table -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Reconciliation Records</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Financial reconciliation between system and bank records</p>
                </div>
                <div class="flex space-x-3">
                    <button class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export Report
                    </button>
                </div>
            </div>

            <!-- Filters -->
            <div class="px-4 py-4 bg-gray-50 border-b border-gray-200">
                <form method="GET" action="{{ route('super_admin.rekon') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <input type="search" name="search" id="search" value="{{ request('search') }}" class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="Transaction ID atau Product...">
                        </div>
                        <div>
                            <label for="vendor_id" class="block text-sm font-medium text-gray-700 mb-1">Vendor</label>
                            <select id="vendor_id" name="vendor_id" class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Semua Vendor</option>
                                @foreach($vendors as $id => $name)
                                    <option value="{{ $id }}" {{ request('vendor_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                            <select id="type" name="type" class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Semua Type</option>
                                <option value="product" {{ request('type') == 'product' ? 'selected' : '' }}>Product</option>
                                <option value="addon" {{ request('type') == 'addon' ? 'selected' : '' }}>Addon</option>
                                <option value="package" {{ request('type') == 'package' ? 'selected' : '' }}>Package</option>
                            </select>
                        </div>
                        <div>
                            <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Dari</label>
                            <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Hingga</label>
                            <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Reconciliation Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction ID</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Name</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Paid</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profit</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($rekonDetailsPaginated as $detail)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ $detail->transaction_id }}</td>
                            <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($detail->date)->format('M d, Y') }}</td>
                            <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900">{{ $detail->vendor_name }}</td>
                            <td class="px-3 py-4 text-sm text-gray-900">{{ $detail->product_name }}</td>
                            <td class="px-3 py-4 whitespace-nowrap text-sm">
                                @if($detail->type === 'product')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Product</span>
                                @elseif($detail->type === 'addon')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">Addon</span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Package</span>
                                @endif
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900">Rp {{ number_format($detail->pax_paid, 0, ',', '.') }}</td>
                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium {{ $detail->profit >= 0 ? 'text-green-600' : 'text-red-600' }}">Rp {{ number_format($detail->profit, 0, ',', '.') }}</td>
                            <td class="px-3 py-4 whitespace-nowrap text-sm">
                                <button type="button" onclick="showDetail({{ json_encode($detail) }})" class="text-blue-600 hover:text-blue-800 font-medium">Detail</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                No reconciliation records found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    {{ $rekonDetailsPaginated->links() }}
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing <span class="font-medium">{{ $rekonDetailsPaginated->firstItem() }}</span> to <span class="font-medium">{{ $rekonDetailsPaginated->lastItem() }}</span> of <span class="font-medium">{{ $rekonDetailsPaginated->total() }}</span> results
                        </p>
                    </div>
                    <div>
                        {{ $rekonDetailsPaginated->links() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Modal -->
        <div id="detailModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg max-w-2xl w-full max-h-96 overflow-y-auto">
                <div class="sticky top-0 bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex justify-between items-center">
                    <h2 class="text-xl font-bold text-white">Rekon Detail</h2>
                    <button type="button" onclick="closeDetail()" class="text-white hover:text-gray-200 text-2xl">&times;</button>
                </div>
                <div id="detailContent" class="p-6">
                    <!-- Detail akan ditampilkan di sini -->
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function showDetail(detail) {
        let typeLabel = detail.type === 'product' ? 'Product' : (detail.type === 'addon' ? 'Addon' : 'Package');
        
        let packageContentsHtml = '';
        if (detail.type === 'package' && (detail.package_products || detail.package_addons)) {
            packageContentsHtml = `
                <div class="border-t pt-4 mt-4">
                    <h3 class="font-bold text-gray-900 mb-3">Package Contents:</h3>
                    <div class="space-y-2">
            `;
            
            if (detail.package_products && detail.package_products.length > 0) {
                packageContentsHtml += `<div class="text-sm font-semibold text-gray-700 mb-2">Products:</div>`;
                detail.package_products.forEach(product => {
                    packageContentsHtml += `
                        <div class="bg-blue-50 p-2 rounded flex justify-between items-center">
                            <span class="text-sm">${product.name} (${product.pax} pax)</span>
                            <span class="text-sm font-medium">Rp ${product.sub_total.toLocaleString('id-ID')}</span>
                        </div>
                    `;
                });
            }
            
            if (detail.package_addons && detail.package_addons.length > 0) {
                packageContentsHtml += `<div class="text-sm font-semibold text-gray-700 mb-2 mt-3">Addons:</div>`;
                detail.package_addons.forEach(addon => {
                    packageContentsHtml += `
                        <div class="bg-purple-50 p-2 rounded flex justify-between items-center">
                            <span class="text-sm">${addon.name} (${addon.pax} pax)</span>
                            <span class="text-sm font-medium">Rp ${addon.sub_total.toLocaleString('id-ID')}</span>
                        </div>
                    `;
                });
            }
            
            packageContentsHtml += `
                    </div>
                </div>
            `;
        }

        const html = `
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-gray-600 text-sm">Transaction ID</span>
                        <p class="text-lg font-bold text-gray-900">#${detail.transaction_id}</p>
                    </div>
                    <div>
                        <span class="text-gray-600 text-sm">Date</span>
                        <p class="text-lg font-bold text-gray-900">${new Date(detail.date).toLocaleDateString('id-ID')}</p>
                    </div>
                    <div>
                        <span class="text-gray-600 text-sm">Vendor</span>
                        <p class="text-lg font-bold text-gray-900">${detail.vendor_name}</p>
                    </div>
                    <div>
                        <span class="text-gray-600 text-sm">Type</span>
                        <p class="text-lg font-bold text-gray-900">${typeLabel}</p>
                    </div>
                </div>
                <div class="border-t pt-4">
                    <h3 class="font-bold text-gray-900 mb-3">Items: ${detail.product_name}</h3>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-3 rounded">
                        <span class="text-gray-600 text-sm">Basic Price</span>
                        <p class="text-lg font-bold text-gray-900">Rp ${detail.basic_price.toLocaleString('id-ID')}</p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded">
                        <span class="text-gray-600 text-sm">Tax</span>
                        <p class="text-lg font-bold text-gray-900">Rp ${detail.tax.toLocaleString('id-ID')}</p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded">
                        <span class="text-gray-600 text-sm">Discount</span>
                        <p class="text-lg font-bold text-gray-900">Rp ${detail.discount.toLocaleString('id-ID')}</p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded">
                        <span class="text-gray-600 text-sm">NTA</span>
                        <p class="text-lg font-bold text-gray-900">Rp ${detail.nta.toLocaleString('id-ID')}</p>
                    </div>
                    <div class="bg-blue-50 p-3 rounded border border-blue-200">
                        <span class="text-blue-600 text-sm">Total Paid</span>
                        <p class="text-lg font-bold text-blue-900">Rp ${detail.pax_paid.toLocaleString('id-ID')}</p>
                    </div>
                    <div class="bg-green-50 p-3 rounded border border-green-200">
                        <span class="text-green-600 text-sm">Profit</span>
                        <p class="text-lg font-bold text-green-900">Rp ${detail.profit.toLocaleString('id-ID')}</p>
                    </div>
                </div>
                ${packageContentsHtml}
            </div>
        `;
        document.getElementById('detailContent').innerHTML = html;
        document.getElementById('detailModal').classList.remove('hidden');
    }

    function closeDetail() {
        document.getElementById('detailModal').classList.add('hidden');
    }

    // Close modal when clicking outside
    document.getElementById('detailModal').addEventListener('click', function(e) {
        if (e.target === this) closeDetail();
    });

    // Profit Chart
    const ctx = document.getElementById('profitChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($labels) !!},
                datasets: [{
                    label: 'Monthly Profit',
                    data: {!! json_encode($monthlyProfit) !!},
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 6,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    }
</script>
@endsection
