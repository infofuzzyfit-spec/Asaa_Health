/**
 * DataTable Component JavaScript
 * Handles data table functionality with search, pagination, and sorting
 */

class DataTable {
    constructor(tableId, options = {}) {
        this.tableId = tableId;
        this.table = $(`#${tableId}`);
        this.options = {
            searchable: true,
            sortable: true,
            pagination: true,
            pageSize: 10,
            ...options
        };
        
        this.currentPage = 1;
        this.totalPages = 1;
        this.filteredData = [];
        this.originalData = [];
        
        this.init();
    }
    
    init() {
        this.loadData();
        this.setupSearch();
        this.setupPagination();
        this.setupSorting();
    }
    
    loadData() {
        // Get data from table rows
        this.originalData = [];
        this.table.find('tbody tr').each((index, row) => {
            const rowData = {
                element: row,
                data: []
            };
            
            $(row).find('td').each((cellIndex, cell) => {
                rowData.data.push($(cell).text().trim());
            });
            
            this.originalData.push(rowData);
        });
        
        this.filteredData = [...this.originalData];
        this.updateDisplay();
    }
    
    setupSearch() {
        if (!this.options.searchable) return;
        
        const searchContainer = $(`
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" class="form-control" id="${this.tableId}-search" placeholder="Search...">
                        <button class="btn btn-outline-secondary" type="button" id="${this.tableId}-clear-search">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        `);
        
        this.table.before(searchContainer);
        
        const searchInput = $(`#${this.tableId}-search`);
        const clearButton = $(`#${this.tableId}-clear-search`);
        
        searchInput.on('input', () => {
            this.search(searchInput.val());
        });
        
        clearButton.on('click', () => {
            searchInput.val('');
            this.search('');
        });
    }
    
    setupPagination() {
        if (!this.options.pagination) return;
        
        const paginationContainer = $(`
            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="dataTables-info">
                        Showing <span id="${this.tableId}-start">0</span> to <span id="${this.tableId}-end">0</span> 
                        of <span id="${this.tableId}-total">0</span> entries
                    </div>
                </div>
                <div class="col-md-6">
                    <nav aria-label="Table pagination">
                        <ul class="pagination justify-content-end" id="${this.tableId}-pagination">
                        </ul>
                    </nav>
                </div>
            </div>
        `);
        
        this.table.after(paginationContainer);
    }
    
    setupSorting() {
        if (!this.options.sortable) return;
        
        this.table.find('thead th').each((index, th) => {
            if ($(th).data('sortable') !== false) {
                $(th).addClass('sortable').css('cursor', 'pointer');
                $(th).append(' <i class="fas fa-sort"></i>');
                
                $(th).on('click', () => {
                    this.sort(index);
                });
            }
        });
    }
    
    search(query) {
        if (!query.trim()) {
            this.filteredData = [...this.originalData];
        } else {
            this.filteredData = this.originalData.filter(row => {
                return row.data.some(cell => 
                    cell.toLowerCase().includes(query.toLowerCase())
                );
            });
        }
        
        this.currentPage = 1;
        this.updateDisplay();
    }
    
    sort(columnIndex) {
        const currentSort = this.table.data('sort-column');
        const currentDirection = this.table.data('sort-direction') || 'asc';
        
        let newDirection = 'asc';
        if (currentSort === columnIndex && currentDirection === 'asc') {
            newDirection = 'desc';
        }
        
        this.filteredData.sort((a, b) => {
            const aValue = a.data[columnIndex] || '';
            const bValue = b.data[columnIndex] || '';
            
            let comparison = 0;
            if (aValue < bValue) comparison = -1;
            if (aValue > bValue) comparison = 1;
            
            return newDirection === 'desc' ? -comparison : comparison;
        });
        
        this.table.data('sort-column', columnIndex);
        this.table.data('sort-direction', newDirection);
        
        this.updateSortIcons(columnIndex, newDirection);
        this.updateDisplay();
    }
    
    updateSortIcons(activeColumn, direction) {
        this.table.find('thead th i').removeClass('fa-sort fa-sort-up fa-sort-down').addClass('fa-sort');
        
        const activeTh = this.table.find('thead th').eq(activeColumn);
        const icon = activeTh.find('i');
        
        if (direction === 'asc') {
            icon.removeClass('fa-sort').addClass('fa-sort-up');
        } else {
            icon.removeClass('fa-sort').addClass('fa-sort-down');
        }
    }
    
    updateDisplay() {
        this.updateTable();
        this.updatePagination();
        this.updateInfo();
    }
    
    updateTable() {
        const tbody = this.table.find('tbody');
        tbody.empty();
        
        const startIndex = (this.currentPage - 1) * this.options.pageSize;
        const endIndex = startIndex + this.options.pageSize;
        const pageData = this.filteredData.slice(startIndex, endIndex);
        
        pageData.forEach(row => {
            tbody.append(row.element);
        });
    }
    
    updatePagination() {
        if (!this.options.pagination) return;
        
        this.totalPages = Math.ceil(this.filteredData.length / this.options.pageSize);
        const pagination = $(`#${this.tableId}-pagination`);
        pagination.empty();
        
        if (this.totalPages <= 1) return;
        
        // Previous button
        const prevButton = $(`
            <li class="page-item ${this.currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${this.currentPage - 1}">Previous</a>
            </li>
        `);
        pagination.append(prevButton);
        
        // Page numbers
        const startPage = Math.max(1, this.currentPage - 2);
        const endPage = Math.min(this.totalPages, this.currentPage + 2);
        
        for (let i = startPage; i <= endPage; i++) {
            const pageButton = $(`
                <li class="page-item ${i === this.currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `);
            pagination.append(pageButton);
        }
        
        // Next button
        const nextButton = $(`
            <li class="page-item ${this.currentPage === this.totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${this.currentPage + 1}">Next</a>
            </li>
        `);
        pagination.append(nextButton);
        
        // Bind click events
        pagination.find('a[data-page]').on('click', (e) => {
            e.preventDefault();
            const page = parseInt($(e.target).data('page'));
            if (page >= 1 && page <= this.totalPages) {
                this.currentPage = page;
                this.updateDisplay();
            }
        });
    }
    
    updateInfo() {
        if (!this.options.pagination) return;
        
        const start = this.filteredData.length === 0 ? 0 : (this.currentPage - 1) * this.options.pageSize + 1;
        const end = Math.min(this.currentPage * this.options.pageSize, this.filteredData.length);
        const total = this.filteredData.length;
        
        $(`#${this.tableId}-start`).text(start);
        $(`#${this.tableId}-end`).text(end);
        $(`#${this.tableId}-total`).text(total);
    }
    
    refresh() {
        this.loadData();
    }
    
    addRow(rowData) {
        // This would be implemented based on specific needs
        console.log('Add row functionality not implemented');
    }
    
    removeRow(index) {
        // This would be implemented based on specific needs
        console.log('Remove row functionality not implemented');
    }
}

// Auto-initialize data tables
$(document).ready(function() {
    $('.datatable').each(function() {
        const tableId = $(this).attr('id');
        if (tableId) {
            new DataTable(tableId);
        }
    });
});

// Add CSS for sortable columns
const style = document.createElement('style');
style.textContent = `
    .sortable:hover {
        background-color: #f8f9fa;
    }
    
    .dataTables-info {
        color: #6c757d;
        font-size: 0.875rem;
    }
`;
document.head.appendChild(style);
