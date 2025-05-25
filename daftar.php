
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Laundry - londry.in</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1E40AF',
                        secondary: '#3B82F6',
                        light: '#EFF6FF',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    boxShadow: {
                        'custom': '0 10px 15px -3px rgba(59, 130, 246, 0.1), 0 4px 6px -2px rgba(59, 130, 246, 0.05)',
                    },
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        .gradient-bg {
            background: linear-gradient(to right, #1E40AF, #3B82F6);
        }

        .table-hover tr:hover td {
            background-color: #EFF6FF;
        }

        .pagination-btn {
            transition: all 0.3s ease;
        }

        .pagination-btn.active {
            background-color: #3B82F6;
            color: white;
        }

        .pagination-btn:hover:not(.active):not(.disabled) {
            background-color: #EFF6FF;
        }

        .pagination-btn.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>
</head>
<body class="bg-light min-h-screen font-sans">
    <?php
    include('includes/navbar.php');
    ?>

    <!-- Header Section -->
    <div class="gradient-bg text-white py-10">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold">Daftar Laundry</h1>
                    <p class="mt-2 opacity-90">Temukan layanan laundry terbaik di sekitar Anda</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <div class="relative">
                        <input type="text" id="search-input" placeholder="Cari nama atau alamat..." 
                            class="w-full md:w-80 py-2 px-4 pr-10 rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary text-gray-700">
                        <button class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-xl shadow-custom overflow-hidden">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-700">Semua Lokasi Laundry</h2>
                    <div class="text-gray-500" id="count-info">Memuat data...</div>
                </div>

                <!-- Table view for larger screens -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full table-hover">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 px-4 font-semibold text-sm text-gray-600">Nama</th>
                                <th class="text-left py-3 px-4 font-semibold text-sm text-gray-600">Alamat</th>
                                <th class="text-left py-3 px-4 font-semibold text-sm text-gray-600">Telepon</th>
                                <th class="text-center py-3 px-4 font-semibold text-sm text-gray-600">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody id="laundry-table-body">
                            <!-- Data will be loaded here -->
                            <tr>
                                <td colspan="4" class="text-center py-4 text-gray-500">
                                    <div class="flex justify-center items-center">
                                        <i class="fas fa-spinner fa-spin mr-2"></i> Memuat data...
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Card view for mobile -->
                <div class="md:hidden" id="mobile-cards">
                    <!-- Data will be loaded here -->
                    <div class="flex justify-center py-4 text-gray-500">
                        <i class="fas fa-spinner fa-spin mr-2"></i> Memuat data...
                    </div>
                </div>

                <!-- Pagination -->
                <div id="pagination-container" class="flex justify-center mt-6 flex-wrap">
                    <!-- Pagination will be generated here -->
                </div>
            </div>
        </div>
    </main>

    <?php
    include('includes/footer.php');
    ?>

    <script>
        // Pagination variables
        const itemsPerPage = 10;
        let currentPage = 1;
        let totalPages = 1;
        
        // Search functionality
        const searchInput = document.getElementById('search-input');
        searchInput.addEventListener('input', function() {
            currentPage = 1;
            filterAndDisplay();
        });
        
        let laundryData = [];
        let filteredData = [];
        
        // Fetch and display services from data.json
        fetch('data/data.json')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Filter out invalid entries (those without id or name)
                laundryData = data.filter(item => item.id && item.nama);
                filteredData = [...laundryData];
                totalPages = Math.ceil(filteredData.length / itemsPerPage);
                displayLaundries();
                updatePagination();
            })
            .catch(error => {
                console.error('Error loading data:', error);
                document.getElementById('laundry-table-body').innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center py-8 text-red-500">
                            <div class="inline-block p-4 rounded-full bg-red-100 mb-4">
                                <i class="fas fa-exclamation-triangle text-xl text-red-500"></i>
                            </div>
                            <p class="font-semibold">Gagal memuat data laundry</p>
                            <p class="text-gray-500 mt-1">Silakan coba lagi nanti</p>
                            <button onclick="location.reload()" class="mt-4 bg-secondary text-white px-5 py-2 rounded-lg hover:bg-primary transition">
                                <i class="fas fa-redo mr-2"></i> Muat Ulang
                            </button>
                        </td>
                    </tr>
                `;
                document.getElementById('mobile-cards').innerHTML = `
                    <div class="text-center py-8 text-red-500">
                        <div class="inline-block p-4 rounded-full bg-red-100 mb-4">
                            <i class="fas fa-exclamation-triangle text-xl text-red-500"></i>
                        </div>
                        <p class="font-semibold">Gagal memuat data laundry</p>
                        <p class="text-gray-500 mt-1">Silakan coba lagi nanti</p>
                        <button onclick="location.reload()" class="mt-4 bg-secondary text-white px-5 py-2 rounded-lg hover:bg-primary transition">
                            <i class="fas fa-redo mr-2"></i> Muat Ulang
                        </button>
                    </div>
                `;
                document.getElementById('pagination-container').innerHTML = '';
                document.getElementById('count-info').textContent = 'Error: ' + error.message;
            });
            
        function filterAndDisplay() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            
            if (searchTerm === '') {
                filteredData = [...laundryData];
            } else {
                filteredData = laundryData.filter(laundry => {
                    return laundry.nama.toLowerCase().includes(searchTerm) || 
                        (laundry.alamat && laundry.alamat.toLowerCase().includes(searchTerm));
                });
            }
            
            totalPages = Math.ceil(filteredData.length / itemsPerPage);
            
            // If current page is now beyond total pages, reset to page 1
            if (currentPage > totalPages) {
                currentPage = 1;
            }
            
            displayLaundries();
            updatePagination();
        }
            
        function displayLaundries() {
            const tableBody = document.getElementById('laundry-table-body');
            const mobileCards = document.getElementById('mobile-cards');
            
            tableBody.innerHTML = '';
            mobileCards.innerHTML = '';
            
            if (filteredData.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center py-8 text-gray-500">
                            <div class="inline-block p-4 rounded-full bg-gray-100 mb-4">
                                <i class="fas fa-search text-xl text-gray-400"></i>
                            </div>
                            <p class="font-semibold">Tidak ada hasil yang ditemukan</p>
                            <p class="mt-1">Coba kata kunci pencarian lainnya</p>
                        </td>
                    </tr>
                `;
                
                mobileCards.innerHTML = `
                    <div class="text-center py-8 text-gray-500">
                        <div class="inline-block p-4 rounded-full bg-gray-100 mb-4">
                            <i class="fas fa-search text-xl text-gray-400"></i>
                        </div>
                        <p class="font-semibold">Tidak ada hasil yang ditemukan</p>
                        <p class="mt-1">Coba kata kunci pencarian lainnya</p>
                    </div>
                `;
                
                document.getElementById('pagination-container').innerHTML = '';
                document.getElementById('count-info').textContent = '0 laundry ditemukan';
                return;
            }
            
            // Calculate start and end indices for current page
            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = Math.min(startIndex + itemsPerPage, filteredData.length);
            const currentItems = filteredData.slice(startIndex, endIndex);
            
            // Update count info
            document.getElementById('count-info').textContent = 
                `Menampilkan ${startIndex + 1}-${endIndex} dari ${filteredData.length} laundry`;
            
            // Display items in table
            currentItems.forEach(laundry => {
                const row = document.createElement('tr');
                row.className = 'border-b border-gray-100';
                
                row.innerHTML = `
                    <td class="py-4 px-4">
                        <div class="font-medium text-primary">${laundry.nama}</div>
                    </td>
                    <td class="py-4 px-4 text-gray-600">${laundry.alamat ? laundry.alamat : '-'}</td>
                    <td class="py-4 px-4">${laundry.no_telp ? laundry.no_telp : '-'}</td>
                    <td class="py-4 px-4 text-center">
                        <div class="flex justify-center space-x-2">
                            <a href="detail.php?id=${laundry.id}" class="bg-secondary text-white p-2 rounded hover:bg-primary transition" title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            ${laundry.link_gmaps ? 
                                `<a href="${laundry.link_gmaps}" target="_blank" class="bg-green-600 text-white p-2 rounded hover:bg-green-700 transition" title="Buka di Maps">
                                    <i class="fas fa-map-marker-alt"></i>
                                </a>` : ''
                            }
                            ${laundry.no_telp ? 
                                `<a href="https://wa.me/${laundry.no_telp.replace(/\s+/g, '')}" target="_blank" class="bg-green-500 text-white p-2 rounded hover:bg-green-600 transition" title="WhatsApp">
                                    <i class="fab fa-whatsapp"></i>
                                </a>` : ''
                            }
                        </div>
                    </td>
                `;
                
                tableBody.appendChild(row);
                
                // Create card for mobile view
                const card = document.createElement('div');
                card.className = 'bg-white rounded-lg border border-gray-200 mb-4 overflow-hidden';
                
                card.innerHTML = `
                    <div class="p-4">
                        <h3 class="font-semibold text-lg text-primary">${laundry.nama}</h3>
                        <div class="mt-2 flex items-start space-x-2">
                            <i class="fas fa-map-marker-alt text-secondary mt-1"></i>
                            <p class="text-gray-600 text-sm">${laundry.alamat ? laundry.alamat : 'Alamat tidak tersedia'}</p>
                        </div>
                        <div class="mt-2 flex items-center space-x-2">
                            <i class="fas fa-phone text-secondary"></i>
                            <p class="text-gray-600">${laundry.no_telp ? laundry.no_telp : 'Telepon tidak tersedia'}</p>
                        </div>
                        <div class="mt-4 flex justify-between">
                            <a href="detail.php?id=${laundry.id}" class="inline-flex items-center text-secondary hover:text-primary transition">
                                <span>Lihat Detail</span>
                                <i class="fas fa-chevron-right ml-1 text-xs"></i>
                            </a>
                            <div class="flex space-x-2">
                                ${laundry.link_gmaps ? 
                                    `<a href="${laundry.link_gmaps}" target="_blank" class="bg-green-600 text-white p-2 rounded-full hover:bg-green-700 transition" title="Buka di Maps">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </a>` : ''
                                }
                                ${laundry.no_telp ? 
                                    `<a href="https://wa.me/${laundry.no_telp.replace(/\s+/g, '')}" target="_blank" class="bg-green-500 text-white p-2 rounded-full hover:bg-green-600 transition" title="WhatsApp">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>` : ''
                                }
                            </div>
                        </div>
                    </div>
                `;
                
                mobileCards.appendChild(card);
            });
        }
        
        function updatePagination() {
            const paginationContainer = document.getElementById('pagination-container');
            paginationContainer.innerHTML = '';
            
            if (filteredData.length === 0) return;
            
            // Previous button
            const prevBtn = document.createElement('button');
            prevBtn.className = `pagination-btn mx-1 px-4 py-2 rounded-md border ${currentPage === 1 ? 'disabled' : ''}`;
            prevBtn.innerHTML = '<i class="fas fa-chevron-left"></i>';
            prevBtn.disabled = currentPage === 1;
            prevBtn.addEventListener('click', () => {
                if (currentPage > 1) {
                    currentPage--;
                    displayLaundries();
                    updatePagination();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            });
            paginationContainer.appendChild(prevBtn);
            
            // Determine page range to show
            let startPage = Math.max(1, currentPage - 2);
            let endPage = Math.min(totalPages, startPage + 4);
            
            // Adjust if we're near the end
            if (endPage - startPage < 4 && startPage > 1) {
                startPage = Math.max(1, endPage - 4);
            }
            
            // First page (if not in range)
            if (startPage > 1) {
                const firstBtn = document.createElement('button');
                firstBtn.className = 'pagination-btn mx-1 px-4 py-2 rounded-md border';
                firstBtn.textContent = '1';
                firstBtn.addEventListener('click', () => {
                    currentPage = 1;
                    displayLaundries();
                    updatePagination();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
                paginationContainer.appendChild(firstBtn);
                
                if (startPage > 2) {
                    const ellipsis = document.createElement('span');
                    ellipsis.className = 'mx-1 px-3 py-2';
                    ellipsis.textContent = '...';
                    paginationContainer.appendChild(ellipsis);
                }
            }
            
            // Page numbers
            for (let i = startPage; i <= endPage; i++) {
                const pageBtn = document.createElement('button');
                pageBtn.className = `pagination-btn mx-1 px-4 py-2 rounded-md border ${i === currentPage ? 'active' : ''}`;
                pageBtn.textContent = i;
                pageBtn.addEventListener('click', () => {
                    currentPage = i;
                    displayLaundries();
                    updatePagination();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
                paginationContainer.appendChild(pageBtn);
            }
            
            // Last page (if not in range)
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    const ellipsis = document.createElement('span');
                    ellipsis.className = 'mx-1 px-3 py-2';
                    ellipsis.textContent = '...';
                    paginationContainer.appendChild(ellipsis);
                }
                
                const lastBtn = document.createElement('button');
                lastBtn.className = 'pagination-btn mx-1 px-4 py-2 rounded-md border';
                lastBtn.textContent = totalPages;
                lastBtn.addEventListener('click', () => {
                    currentPage = totalPages;
                    displayLaundries();
                    updatePagination();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
                paginationContainer.appendChild(lastBtn);
            }
            
            // Next button
            const nextBtn = document.createElement('button');
            nextBtn.className = `pagination-btn mx-1 px-4 py-2 rounded-md border ${currentPage === totalPages ? 'disabled' : ''}`;
            nextBtn.innerHTML = '<i class="fas fa-chevron-right"></i>';
            nextBtn.disabled = currentPage === totalPages;
            nextBtn.addEventListener('click', () => {
                if (currentPage < totalPages) {
                    currentPage++;
                    displayLaundries();
                    updatePagination();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            });
            paginationContainer.appendChild(nextBtn);
        }
    </script>
</body>
</html>