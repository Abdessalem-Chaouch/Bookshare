let books = [
    { id: 1, title: "The Great Gatsby", author: "F. Scott Fitzgerald", isbn: "9780743273565", genre: "Classic", quantity: 5, available: 3, cover: "https://placehold.co/150x220/007bff/white?text=Gatsby", publishedDate: "1925-04-10" },
    { id: 2, title: "To Kill a Mockingbird", author: "Harper Lee", isbn: "9780061120084", genre: "Fiction", quantity: 3, available: 1, cover: "https://placehold.co/150x220/28a745/white?text=Mockingbird", publishedDate: "1960-07-11" },
    { id: 3, title: "1984", author: "George Orwell", isbn: "9780451524935", genre: "Dystopian", quantity: 7, available: 7, cover: "https://placehold.co/150x220/ffc107/black?text=1984", publishedDate: "1949-06-08" },
    { id: 4, title: "Pride and Prejudice", author: "Jane Austen", isbn: "9780141439518", genre: "Romance", quantity: 4, available: 2, cover: "https://placehold.co/150x220/dc3545/white?text=Pride+Prejudice", publishedDate: "1813-01-28" },
    { id: 5, title: "The Hobbit", author: "J.R.R. Tolkien", isbn: "9780547928227", genre: "Fantasy", quantity: 6, available: 6, cover: "https://placehold.co/150x220/17a2b8/white?text=Hobbit", publishedDate: "1937-09-21" },
];

let students = [
    { id: 101, name: "Alice Smith", email: "alice@example.com", grade: "10A", borrowedBooksCount: 0 },
    { id: 102, name: "Bob Johnson", email: "bob@example.com", grade: "9B", borrowedBooksCount: 0 },
    { id: 103, name: "Charlie Brown", email: "charlie@example.com", grade: "11C", borrowedBooksCount: 0 },
    { id: 104, name: "Diana Prince", email: "diana@example.com", grade: "10A", borrowedBooksCount: 0 }
];

let borrowedItems = [
    // Example: { id: 1, bookId: 1, studentId: 101, borrowDate: "2025-05-01", dueDate: "2025-05-15", returned: false, returnDate: null }
];

// Initialize borrowedBooksCount for students based on borrowedItems
function updateStudentBorrowedCounts() {
    students.forEach(student => {
        student.borrowedBooksCount = borrowedItems.filter(item => item.studentId === student.id && !item.returned).length;
    });
}


// --- Utility Functions ---
function showMessage(title, text, icon = 'success') {
    Swal.fire({
        title: title,
        text: text,
        icon: icon,
        confirmButtonColor: '#0d6efd',
        timer: icon === 'success' ? 2000 : 3500,
        timerProgressBar: true
    });
}

function generateId(arr) {
    return arr.length > 0 ? Math.max(...arr.map(item => item.id)) + 1 : 1;
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const options = { year: 'numeric', month: 'short', day: 'numeric' }; // Using short month for brevity
    return new Date(dateString).toLocaleDateString(undefined, options);
}

// --- DOM Elements ---
const mainContent = document.getElementById('main-content');
const sidebar = document.getElementById('sidebar');
const sidebarToggle = document.getElementById('sidebarToggle');
const pageContentWrapper = document.getElementById('page-content-wrapper');
const sidebarOverlay = document.getElementById('sidebarOverlay');


// --- Sidebar Toggle Logic ---
if (sidebarToggle) {
    sidebarToggle.addEventListener('click', () => {
        sidebar.classList.toggle('active');
        pageContentWrapper.classList.toggle('sidebar-active'); // For content shift if needed
        sidebarOverlay.style.display = sidebar.classList.contains('active') ? 'block' : 'none';
    });
}
if (sidebarOverlay) {
    sidebarOverlay.addEventListener('click', () => { // Close sidebar when overlay is clicked
        sidebar.classList.remove('active');
        pageContentWrapper.classList.remove('sidebar-active');
        sidebarOverlay.style.display = 'none';
    });
}


// --- Page Rendering Functions ---

// Dashboard
function renderDashboard() {
    updateStudentBorrowedCounts(); // Ensure counts are up-to-date
    const totalBooks = books.length;
    const totalBookCopies = books.reduce((sum, book) => sum + book.quantity, 0);
    const currentlyBorrowed = borrowedItems.filter(item => !item.returned).length;
    const totalStudents = students.length;
    const today = new Date().toISOString().split('T')[0];
    const overdueBooksCount = borrowedItems.filter(item => !item.returned && item.dueDate < today).length;

    mainContent.innerHTML = `
                <div class="content-section" data-aos="fade-up">
                    <h2><i class="fas fa-tachometer-alt me-2"></i>Dashboard Overview</h2>
                    <div class="row g-4">
                        <div class="col-xl-3 col-lg-6 col-md-6">
                            <div class="card dashboard-card bg-primary-custom text-white h-100">
                                <div class="card-body"><div><h5 class="card-title">Total Book Titles</h5><p class="card-text fs-2 fw-bold">${totalBooks}</p></div><div class="card-icon"><i class="fas fa-book"></i></div></div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-md-6">
                            <div class="card dashboard-card bg-success-custom text-white h-100">
                                <div class="card-body"><div><h5 class="card-title">Total Book Copies</h5><p class="card-text fs-2 fw-bold">${totalBookCopies}</p></div><div class="card-icon"><i class="fas fa-layer-group"></i></div></div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-md-6">
                            <div class="card dashboard-card bg-info-custom text-dark h-100">
                                <div class="card-body"><div><h5 class="card-title">Books Borrowed</h5><p class="card-text fs-2 fw-bold">${currentlyBorrowed}</p></div><div class="card-icon"><i class="fas fa-hand-holding-heart"></i></div></div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-md-6">
                            <div class="card dashboard-card bg-danger-custom text-white h-100">
                                <div class="card-body"><div><h5 class="card-title">Overdue Books</h5><p class="card-text fs-2 fw-bold">${overdueBooksCount}</p></div><div class="card-icon"><i class="fas fa-exclamation-triangle"></i></div></div>
                            </div>
                        </div>
                         <div class="col-xl-3 col-lg-6 col-md-6">
                            <div class="card dashboard-card bg-warning-custom text-dark h-100">
                                <div class="card-body"><div><h5 class="card-title">Registered Students</h5><p class="card-text fs-2 fw-bold">${totalStudents}</p></div><div class="card-icon"><i class="fas fa-users"></i></div></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-section mt-4" data-aos="fade-up" data-aos-delay="100">
                    <h2><i class="fas fa-chart-line me-2"></i>Quick Stats</h2>
                    <div class="row">
                        <div class="col-lg-12"><canvas id="quickStatsChart" style="max-height: 300px;"></canvas></div>
                    </div>
                </div>`;
    renderQuickStatsChart();
}

function renderQuickStatsChart() { /* Same as before, ensure it's called */
    const ctx = document.getElementById('quickStatsChart')?.getContext('2d');
    if (!ctx) return;
    const genres = [...new Set(books.map(book => book.genre))];
    const booksPerGenre = genres.map(genre => books.filter(book => book.genre === genre).length);
    new Chart(ctx, { /* ... chart config ... */
        type: 'bar',
        data: {
            labels: genres,
            datasets: [{
                label: 'Books per Genre', data: booksPerGenre,
                backgroundColor: ['rgba(255, 99, 132, 0.7)', 'rgba(54, 162, 235, 0.7)', 'rgba(255, 206, 86, 0.7)', 'rgba(75, 192, 192, 0.7)', 'rgba(153, 102, 255, 0.7)', 'rgba(255, 159, 64, 0.7)', 'rgba(199, 199, 199, 0.7)'],
                borderColor: ['rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)', 'rgba(75, 192, 192, 1)', 'rgba(153, 102, 255, 1)', 'rgba(255, 159, 64, 1)', 'rgba(199, 199, 199, 1)'],
                borderWidth: 1
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }, plugins: { legend: { display: false }, title: { display: true, text: 'Number of Book Titles by Genre' } } }
    });
}

// Book Catalog
function renderBookCatalog() { /* Mostly same, ensure DataTables re-init */
    let tableRows = books.map(book => `
                <tr data-aos="fade-up" data-aos-delay="${books.indexOf(book) * 50}">
                    <td><img src="${book.cover}" alt="${book.title}" width="40" class="img-thumbnail me-2 rounded" onerror="this.onerror=null;this.src='https://placehold.co/40x60/ccc/fff?text=N/A';"> ${book.title}</td>
                    <td>${book.author}</td>
                    <td>${book.isbn}</td>
                    <td>${book.genre}</td>
                    <td>${book.quantity}</td>
                    <td><span class="badge bg-${book.available > 0 ? 'success' : 'danger'}">${book.available > 0 ? book.available + ' Available' : 'Out of Stock'}</span></td>
                    <td class="d-flex">
                        <button class="btn btn-sm btn-primary borrow-book-btn ${book.available === 0 ? 'disabled' : ''}" data-book-id="${book.id}" title="Borrow ${book.title}" ${book.available === 0 ? 'disabled' : ''}><i class="fas fa-hand-holding-heart"></i></button>
                        <button class="btn btn-sm btn-info view-book-btn ms-1" data-book-id="${book.id}" title="View Details"><i class="fas fa-eye"></i></button>
                        <button class="btn btn-sm btn-warning edit-book-btn ms-1" data-book-id="${book.id}" title="Edit Book"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-danger delete-book-btn ms-1" data-book-id="${book.id}" title="Delete Book"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>`).join('');
    mainContent.innerHTML = `
                <div class="content-section" data-aos="fade-up">
                    <h2><i class="fas fa-book me-2"></i>Book Catalog</h2>
                    <div class="table-responsive">
                        <table id="booksTable" class="table table-striped table-hover" style="width:100%">
                            <thead><tr><th>Title</th><th>Author</th><th>ISBN</th><th>Genre</th><th>Total</th><th>Available</th><th>Actions</th></tr></thead>
                            <tbody>${tableRows}</tbody>
                        </table>
                    </div>
                </div>`;
    if ($.fn.DataTable.isDataTable('#booksTable')) { $('#booksTable').DataTable().destroy(); }
    $('#booksTable').DataTable({ responsive: true, "pageLength": 10, "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]], columnDefs: [{ targets: [6], orderable: false, searchable: false }] });
    // Re-attach event listeners
    document.querySelectorAll('.borrow-book-btn').forEach(btn => btn.addEventListener('click', e => navigateTo('borrowBook', { bookId: parseInt(e.currentTarget.dataset.bookId) })));
    document.querySelectorAll('.view-book-btn').forEach(btn => btn.addEventListener('click', e => viewBookDetails(parseInt(e.currentTarget.dataset.bookId))));
    document.querySelectorAll('.edit-book-btn').forEach(btn => btn.addEventListener('click', e => navigateTo('addBook', { bookId: parseInt(e.currentTarget.dataset.bookId), isEdit: true })));
    document.querySelectorAll('.delete-book-btn').forEach(btn => btn.addEventListener('click', e => deleteBook(parseInt(e.currentTarget.dataset.bookId))));
}

function viewBookDetails(bookId) { /* Same as before */
    const book = books.find(b => b.id === bookId);
    if (!book) { showMessage('Error', 'Book not found.', 'error'); return; }
    Swal.fire({
        ttitle: `<i class="fas fa-book-open me-2"></i> ${book.title}`,
        html: `<div class="text-start"><p class="mb-1"><strong>Author:</strong> ${book.author}</p><p class="mb-1"><strong>ISBN:</strong> ${book.isbn}</p><p class="mb-1"><strong>Genre:</strong> ${book.genre}</p><p class="mb-1"><strong>Published:</strong> ${formatDate(book.publishedDate)}</p><p class="mb-1"><strong>Total:</strong> ${book.quantity}</p><p class="mb-1"><strong>Available:</strong> ${book.available}</p><hr><img src="${book.cover}" alt="${book.title}" class="img-fluid rounded mx-auto d-block" style="max-height: 250px;" onerror="this.onerror=null;this.src='https://placehold.co/200x300/ccc/fff?text=N/A';"></div>`,
        icon: 'info', confirmButtonText: 'Close', confirmButtonColor: '#0d6efd', width: '500px'
    });
}

function deleteBook(bookId) { /* Same as before */
    Swal.fire({ title: 'Are you sure?', text: "You won't be able to revert this!", icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc3545', cancelButtonColor: '#6c757d', confirmButtonText: 'Yes, delete it!' })
        .then((result) => {
            if (result.isConfirmed) {
                const isBorrowed = borrowedItems.some(item => item.bookId === bookId && !item.returned);
                if (isBorrowed) { showMessage('Cannot Delete', 'This book is currently borrowed.', 'error'); return; }
                books = books.filter(b => b.id !== bookId);
                showMessage('Deleted!', 'The book has been deleted.', 'success');
                renderBookCatalog();
            }
        });
}

// Add New Book / Edit Book
function renderAddNewBookForm(params = {}) { /* Mostly same, ensure flatpickr re-init */
    const isEdit = params.isEdit || false;
    const bookIdToEdit = params.bookId;
    let bookToEdit = null;
    if (isEdit && bookIdToEdit) {
        bookToEdit = books.find(b => b.id === bookIdToEdit);
        if (!bookToEdit) { showMessage('Error', 'Book not found for editing.', 'error'); navigateTo('catalog'); return; }
    }
    mainContent.innerHTML = `
                <div class="content-section" data-aos="fade-up">
                    <h2><i class="fas ${isEdit ? 'fa-edit' : 'fa-plus-circle'} me-2"></i> ${isEdit ? 'Edit Book Details' : 'Add New Book'}</h2>
                    <form id="addBookForm" class="needs-validation" novalidate>
                        <input type="hidden" id="bookId" value="${isEdit && bookToEdit ? bookToEdit.id : ''}">
                        <div class="row g-3">
                            <div class="col-md-6"><label for="bookTitle" class="form-label">Title <span class="text-danger">*</span></label><input type="text" class="form-control" id="bookTitle" value="${isEdit && bookToEdit ? bookToEdit.title : ''}" required><div class="invalid-feedback">Title is required.</div></div>
                            <div class="col-md-6"><label for="bookAuthor" class="form-label">Author <span class="text-danger">*</span></label><input type="text" class="form-control" id="bookAuthor" value="${isEdit && bookToEdit ? bookToEdit.author : ''}" required><div class="invalid-feedback">Author is required.</div></div>
                            <div class="col-md-6"><label for="bookIsbn" class="form-label">ISBN <span class="text-danger">*</span></label><input type="text" class="form-control" id="bookIsbn" value="${isEdit && bookToEdit ? bookToEdit.isbn : ''}" required pattern="^(\\d{10}|\\d{13})$"><div class="invalid-feedback">Valid 10 or 13 digit ISBN is required.</div></div>
                            <div class="col-md-6"><label for="bookGenre" class="form-label">Genre <span class="text-danger">*</span></label><input type="text" class="form-control" id="bookGenre" value="${isEdit && bookToEdit ? bookToEdit.genre : ''}" required><div class="invalid-feedback">Genre is required.</div></div>
                            <div class="col-md-4"><label for="bookQuantity" class="form-label">Quantity <span class="text-danger">*</span></label><input type="number" class="form-control" id="bookQuantity" value="${isEdit && bookToEdit ? bookToEdit.quantity : '1'}" min="1" required><div class="invalid-feedback">Quantity must be at least 1.</div></div>
                            <div class="col-md-4"><label for="bookPublishedDate" class="form-label">Published Date</label><input type="text" class="form-control" id="bookPublishedDate" placeholder="YYYY-MM-DD" value="${isEdit && bookToEdit ? bookToEdit.publishedDate : ''}"></div>
                            <div class="col-md-4"><label for="bookAvailable" class="form-label">Available</label><input type="number" class="form-control" id="bookAvailable" value="${isEdit && bookToEdit ? bookToEdit.available : (bookToEdit ? bookToEdit.quantity : '1')}" min="0" readonly></div>
                            <div class="col-md-12"><label for="bookCover" class="form-label">Cover URL</label><input type="url" class="form-control" id="bookCover" placeholder="https://..." value="${isEdit && bookToEdit ? bookToEdit.cover : ''}"><div class="invalid-feedback">Please enter a valid URL.</div></div>
                        </div>
                        <div class="mt-4"><button type="submit" class="btn btn-primary me-2"><i class="fas fa-save me-1"></i> ${isEdit ? 'Save Changes' : 'Add Book'}</button><button type="button" class="btn btn-secondary" onclick="navigateTo('catalog')"><i class="fas fa-times me-1"></i> Cancel</button></div>
                    </form>
                </div>`;
    flatpickr("#bookPublishedDate", { dateFormat: "Y-m-d", altInput: true, altFormat: "F j, Y" });
    if (!isEdit) {
        document.getElementById('bookQuantity').addEventListener('input', (e) => { document.getElementById('bookAvailable').value = e.target.value; });
    } else if (bookToEdit) { // For edit mode, ensure available is not more than quantity
        document.getElementById('bookQuantity').addEventListener('input', (e) => {
            const newQuantity = parseInt(e.target.value) || 0;
            const currentBorrowed = bookToEdit.quantity - bookToEdit.available;
            document.getElementById('bookAvailable').value = Math.max(0, newQuantity - currentBorrowed);
        });
    }

    document.getElementById('addBookForm').addEventListener('submit', function (event) {
        event.preventDefault(); event.stopPropagation();
        const form = event.target;
        if (!form.checkValidity()) { form.classList.add('was-validated'); showMessage('Validation Error', 'Please fill all required fields correctly.', 'warning'); return; }

        const bookId = document.getElementById('bookId').value;
        const title = document.getElementById('bookTitle').value;
        const author = document.getElementById('bookAuthor').value;
        const isbn = document.getElementById('bookIsbn').value;
        const genre = document.getElementById('bookGenre').value;
        const quantity = parseInt(document.getElementById('bookQuantity').value);
        const publishedDate = document.getElementById('bookPublishedDate').value;
        const cover = document.getElementById('bookCover').value || `https://placehold.co/150x220/6c757d/white?text=${title.substring(0, 10).replace(/\s+/g, '+')}`;

        if (isEdit && bookId) {
            const bookIndex = books.findIndex(b => b.id === parseInt(bookId));
            if (bookIndex > -1) {
                const oldBook = books[bookIndex];
                const currentlyBorrowed = oldBook.quantity - oldBook.available;
                let newAvailable = quantity - currentlyBorrowed;
                if (newAvailable < 0) { // This case should ideally be prevented by disabling quantity reduction below borrowed count
                    showMessage('Error', `Cannot reduce quantity below the number of currently borrowed copies (${currentlyBorrowed}).`, 'error');
                    return;
                }

                books[bookIndex] = { ...oldBook, title, author, isbn, genre, quantity, publishedDate, cover, available: newAvailable };
                showMessage('Book Updated!', `${title} updated successfully.`, 'success');
            } else { showMessage('Error', 'Could not find book to update.', 'error'); }
        } else {
            const newBook = { id: generateId(books), title, author, isbn, genre, quantity, available: quantity, cover, publishedDate };
            books.push(newBook);
            showMessage('Book Added!', `${title} added to the library.`, 'success');
        }
        navigateTo('catalog');
    });
}

// Borrow Book
function renderBorrowBookForm(params = {}) { /* Mostly same, ensure flatpickr re-init */
    const preselectedBookId = params.bookId;
    const availableBooksOptions = books.filter(b => b.available > 0).map(b => `<option value="${b.id}" ${preselectedBookId && b.id === preselectedBookId ? 'selected' : ''}>${b.title} (${b.available} avail.)</option>`).join('');
    const studentOptions = students.map(s => `<option value="${s.id}">${s.name} (ID: ${s.id})</option>`).join('');
    mainContent.innerHTML = `
                <div class="content-section" data-aos="fade-up">
                    <h2><i class="fas fa-hand-holding-heart me-2"></i>Borrow a Book</h2>
                    <form id="borrowBookForm" class="needs-validation" novalidate>
                        <div class="mb-3"><label for="borrowStudent" class="form-label">Student <span class="text-danger">*</span></label><select class="form-select" id="borrowStudent" required><option value="" selected disabled>Choose student...</option>${studentOptions}</select><div class="invalid-feedback">Please select a student.</div></div>
                        <div class="mb-3"><label for="borrowBook" class="form-label">Book <span class="text-danger">*</span></label><select class="form-select" id="borrowBook" required><option value="" selected disabled>Choose book...</option>${availableBooksOptions}</select><div class="invalid-feedback">Please select a book.</div></div>
                        <div class="mb-3"><label for="borrowReturnDate" class="form-label">Return Date <span class="text-danger">*</span></label><input type="text" class="form-control" id="borrowReturnDate" placeholder="Select return date" required><div class="invalid-feedback">Please select a return date.</div></div>
                        <div class="mt-4"><button type="submit" class="btn btn-primary me-2"><i class="fas fa-check-circle me-1"></i> Borrow</button><button type="button" class="btn btn-secondary" onclick="navigateTo('catalog')"><i class="fas fa-times me-1"></i> Cancel</button></div>
                    </form>
                </div>`;
    flatpickr("#borrowReturnDate", { dateFormat: "Y-m-d", altInput: true, altFormat: "F j, Y", minDate: "today" });
    document.getElementById('borrowBookForm').addEventListener('submit', function (event) {
        event.preventDefault(); event.stopPropagation();
        const form = event.target;
        if (!form.checkValidity()) { form.classList.add('was-validated'); showMessage('Validation Error', 'Please fill all fields.', 'warning'); return; }

        const studentId = parseInt(document.getElementById('borrowStudent').value);
        const bookId = parseInt(document.getElementById('borrowBook').value);
        const returnDate = document.getElementById('borrowReturnDate').value;
        const book = books.find(b => b.id === bookId);
        const student = students.find(s => s.id === studentId);

        if (book && student && book.available > 0) {
            book.available--;
            const borrowEntry = { id: generateId(borrowedItems), bookId: book.id, studentId: student.id, borrowDate: new Date().toISOString().split('T')[0], dueDate: returnDate, returned: false, returnDate: null };
            borrowedItems.push(borrowEntry);
            updateStudentBorrowedCounts(); // Update student's count
            showMessage('Book Borrowed!', `${book.title} issued to ${student.name}.`, 'success');
            navigateTo('issuedBooks');
        } else { showMessage('Error', 'Borrow request failed. Book unavailable or student not found.', 'error'); }
    });
}

// Return Book
function renderReturnBookPage() { /* Mostly same, ensure DataTables re-init */
    let itemsToReturnHtml = borrowedItems.filter(item => !item.returned).map(item => {
        const book = books.find(b => b.id === item.bookId);
        const student = students.find(s => s.id === item.studentId);
        if (!book || !student) return '';
        const today = new Date(); const dueDate = new Date(item.dueDate); const isOverdue = today > dueDate;
        let lateFee = 0;
        if (isOverdue) { const diffDays = Math.ceil((today - dueDate) / (1000 * 60 * 60 * 24)); lateFee = diffDays * 1; } // $1/day
        return `<tr data-aos="fade-up" class="${isOverdue ? 'table-danger' : ''}"><td>${book.title}</td><td>${student.name}</td><td>${formatDate(item.borrowDate)}</td><td>${formatDate(item.dueDate)}</td><td>${isOverdue ? `<span class="badge bg-danger">Overdue</span>` : '<span class="badge bg-warning text-dark">Pending</span>'}</td><td>${isOverdue ? `$${lateFee.toFixed(2)}` : 'N/A'}</td><td><button class="btn btn-sm btn-success return-now-btn" data-borrow-id="${item.id}" data-late-fee="${lateFee}"><i class="fas fa-undo-alt me-1"></i> Return</button></td></tr>`;
    }).join('');
    if (!borrowedItems.filter(item => !item.returned).length) { itemsToReturnHtml = `<tr><td colspan="7" class="text-center py-3">No books currently pending return.</td></tr>`; }
    mainContent.innerHTML = `
                <div class="content-section" data-aos="fade-up">
                    <h2><i class="fas fa-undo-alt me-2"></i>Return a Book</h2>
                    <div class="table-responsive"><table id="returnBookTable" class="table table-striped table-hover" style="width:100%"><thead><tr><th>Book</th><th>Student</th><th>Borrowed</th><th>Due</th><th>Status</th><th>Late Fee</th><th>Action</th></tr></thead><tbody>${itemsToReturnHtml}</tbody></table></div>
                </div>`;
    if ($.fn.DataTable.isDataTable('#returnBookTable')) { $('#returnBookTable').DataTable().destroy(); }
    $('#returnBookTable').DataTable({ responsive: true, "pageLength": 10, "order": [[3, "asc"]], columnDefs: [{ targets: [6], orderable: false, searchable: false }] });
    document.querySelectorAll('.return-now-btn').forEach(button => { /* Event listener logic same as before */
        button.addEventListener('click', (e) => {
            const borrowId = parseInt(e.currentTarget.dataset.borrowId);
            const lateFee = parseFloat(e.currentTarget.dataset.lateFee);
            const borrowedItem = borrowedItems.find(item => item.id === borrowId);
            if (!borrowedItem) { showMessage('Error', 'Borrowed item not found.', 'error'); return; }
            let confText = "Mark this book as returned?";
            if (lateFee > 0) { confText += ` Late fee of $${lateFee.toFixed(2)} applies.`; }
            Swal.fire({ title: 'Confirm Return', text: confText, icon: 'question', showCancelButton: true, confirmButtonColor: '#28a745', confirmButtonText: 'Yes, return!' })
                .then((result) => {
                    if (result.isConfirmed) {
                        borrowedItem.returned = true; borrowedItem.returnDate = new Date().toISOString().split('T')[0];
                        const book = books.find(b => b.id === borrowedItem.bookId); if (book) { book.available++; }
                        updateStudentBorrowedCounts();
                        showMessage('Book Returned!', `Book marked as returned. ${lateFee > 0 ? 'Late fee processed.' : ''}`, 'success');
                        renderReturnBookPage();
                    }
                });
        });
    });
}

// Student Records (View-only with history)
function renderStudentRecords() {
    updateStudentBorrowedCounts(); // Make sure counts are fresh
    let tableRows = students.map(student => `
                <tr data-aos="fade-up" data-aos-delay="${students.indexOf(student) * 50}">
                    <td>${student.id}</td>
                    <td>${student.name}</td>
                    <td>${student.email}</td>
                    <td>${student.grade || 'N/A'}</td>
                    <td>${student.borrowedBooksCount}</td>
                    <td><button class="btn btn-sm btn-info view-student-history-btn" data-student-id="${student.id}" title="View Borrow History"><i class="fas fa-history"></i> History</button></td>
                </tr>`).join('');
    if (!students.length) { tableRows = `<tr><td colspan="6" class="text-center py-3">No students registered yet. Add students in 'Manage Students'.</td></tr>`; }
    mainContent.innerHTML = `
                <div class="content-section" data-aos="fade-up">
                    <h2><i class="fas fa-users me-2"></i>Student Records</h2>
                    <div class="table-responsive"><table id="studentsTable" class="table table-striped table-hover" style="width:100%"><thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Grade</th><th>Active Borrows</th><th>Actions</th></tr></thead><tbody>${tableRows}</tbody></table></div>
                </div>`;
    if ($.fn.DataTable.isDataTable('#studentsTable')) { $('#studentsTable').DataTable().destroy(); }
    $('#studentsTable').DataTable({ responsive: true, "pageLength": 10, columnDefs: [{ targets: [5], orderable: false, searchable: false }] });
    document.querySelectorAll('.view-student-history-btn').forEach(btn => btn.addEventListener('click', e => viewStudentBorrowHistory(parseInt(e.currentTarget.dataset.studentId))));
}

function viewStudentBorrowHistory(studentId) { /* Same as before */
    const student = students.find(s => s.id === studentId);
    if (!student) { showMessage('Error', 'Student not found.', 'error'); return; }
    const history = borrowedItems.filter(item => item.studentId === studentId);
    let historyHtml = '<p class="text-muted">No borrow history for this student.</p>';
    if (history.length > 0) {
        historyHtml = `<ul class="list-group list-group-flush">${history.map(item => {
            const book = books.find(b => b.id === item.bookId);
            return `<li class="list-group-item d-flex justify-content-between align-items-center"><div><strong>${book ? book.title : 'Unknown Book'}</strong><br><small class="text-muted">Borrowed: ${formatDate(item.borrowDate)} | Due: ${formatDate(item.dueDate)}</small></div><span class="badge bg-${item.returned ? 'success' : 'warning text-dark'}">${item.returned ? `Returned ${formatDate(item.returnDate)}` : 'Pending'}</span></li>`;
        }).join('')}</ul>`;
    }
    Swal.fire({ title: `<i class="fas fa-user-graduate me-2"></i> History for ${student.name}`, html: `<div class="text-start">${historyHtml}</div>`, icon: 'info', confirmButtonText: 'Close', width: '600px' });
}

// Manage Students (CRUD) - NEW
function renderManageStudentsPage() {
    updateStudentBorrowedCounts();
    let tableRows = students.map(student => `
                <tr data-aos="fade-up" data-aos-delay="${students.indexOf(student) * 50}">
                    <td>${student.id}</td>
                    <td>${student.name}</td>
                    <td>${student.email}</td>
                    <td>${student.grade || 'N/A'}</td>
                    <td>${student.borrowedBooksCount}</td>
                    <td>
                        <button class="btn btn-sm btn-warning edit-student-btn" data-student-id="${student.id}" title="Edit Student"><i class="fas fa-user-edit"></i></button>
                        <button class="btn btn-sm btn-danger delete-student-btn ms-1" data-student-id="${student.id}" title="Delete Student"><i class="fas fa-user-times"></i></button>
                    </td>
                </tr>`).join('');
    if (!students.length) { tableRows = `<tr><td colspan="6" class="text-center py-3">No students registered yet.</td></tr>`; }
    mainContent.innerHTML = `
                <div class="content-section" data-aos="fade-up">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2><i class="fas fa-user-cog me-2"></i>Manage Students</h2>
                        <button class="btn btn-primary" id="addNewStudentBtn"><i class="fas fa-user-plus me-2"></i>Add New Student</button>
                    </div>
                    <div class="table-responsive"><table id="manageStudentsTable" class="table table-striped table-hover" style="width:100%"><thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Grade</th><th>Active Borrows</th><th>Actions</th></tr></thead><tbody>${tableRows}</tbody></table></div>
                </div>`;
    if ($.fn.DataTable.isDataTable('#manageStudentsTable')) { $('#manageStudentsTable').DataTable().destroy(); }
    $('#manageStudentsTable').DataTable({ responsive: true, "pageLength": 10, columnDefs: [{ targets: [5], orderable: false, searchable: false }] });

    document.getElementById('addNewStudentBtn').addEventListener('click', () => renderAddEditStudentForm());
    document.querySelectorAll('.edit-student-btn').forEach(btn => btn.addEventListener('click', e => renderAddEditStudentForm({ studentId: parseInt(e.currentTarget.dataset.studentId), isEdit: true })));
    document.querySelectorAll('.delete-student-btn').forEach(btn => btn.addEventListener('click', e => deleteStudent(parseInt(e.currentTarget.dataset.studentId))));
}

function renderAddEditStudentForm(params = {}) {
    const isEdit = params.isEdit || false;
    const studentIdToEdit = params.studentId;
    let studentToEdit = null;
    if (isEdit && studentIdToEdit) {
        studentToEdit = students.find(s => s.id === studentIdToEdit);
        if (!studentToEdit) { showMessage('Error', 'Student not found for editing.', 'error'); navigateTo('manageStudents'); return; }
    }
    mainContent.innerHTML = `
                <div class="content-section" data-aos="fade-up">
                    <h2><i class="fas ${isEdit ? 'fa-user-edit' : 'fa-user-plus'} me-2"></i> ${isEdit ? 'Edit Student Details' : 'Add New Student'}</h2>
                    <form id="addEditStudentForm" class="needs-validation" novalidate>
                        <input type="hidden" id="studentId" value="${isEdit && studentToEdit ? studentToEdit.id : ''}">
                        <div class="row g-3">
                            <div class="col-md-6"><label for="studentName" class="form-label">Full Name <span class="text-danger">*</span></label><input type="text" class="form-control" id="studentName" value="${isEdit && studentToEdit ? studentToEdit.name : ''}" required><div class="invalid-feedback">Name is required.</div></div>
                            <div class="col-md-6"><label for="studentEmail" class="form-label">Email <span class="text-danger">*</span></label><input type="email" class="form-control" id="studentEmail" value="${isEdit && studentToEdit ? studentToEdit.email : ''}" required><div class="invalid-feedback">Valid email is required.</div></div>
                            <div class="col-md-6"><label for="studentGrade" class="form-label">Grade/Class</label><input type="text" class="form-control" id="studentGrade" value="${isEdit && studentToEdit ? studentToEdit.grade : ''}"></div>
                        </div>
                        <div class="mt-4"><button type="submit" class="btn btn-primary me-2"><i class="fas fa-save me-1"></i> ${isEdit ? 'Save Changes' : 'Add Student'}</button><button type="button" class="btn btn-secondary" onclick="navigateTo('manageStudents')"><i class="fas fa-times me-1"></i> Cancel</button></div>
                    </form>
                </div>`;
    document.getElementById('addEditStudentForm').addEventListener('submit', function (event) {
        event.preventDefault(); event.stopPropagation();
        const form = event.target;
        if (!form.checkValidity()) { form.classList.add('was-validated'); showMessage('Validation Error', 'Please fill all required fields.', 'warning'); return; }

        const studentId = document.getElementById('studentId').value;
        const name = document.getElementById('studentName').value;
        const email = document.getElementById('studentEmail').value;
        const grade = document.getElementById('studentGrade').value;

        if (isEdit && studentId) {
            const studentIndex = students.findIndex(s => s.id === parseInt(studentId));
            if (studentIndex > -1) {
                students[studentIndex] = { ...students[studentIndex], name, email, grade };
                showMessage('Student Updated!', `${name}'s details updated.`, 'success');
            } else { showMessage('Error', 'Could not find student to update.', 'error'); }
        } else {
            const newStudent = { id: generateId(students), name, email, grade, borrowedBooksCount: 0 };
            students.push(newStudent);
            showMessage('Student Added!', `${name} has been registered.`, 'success');
        }
        navigateTo('manageStudents');
    });
}

function deleteStudent(studentId) {
    const student = students.find(s => s.id === studentId);
    if (!student) { showMessage('Error', 'Student not found.', 'error'); return; }
    // Check if student has borrowed books
    if (borrowedItems.some(item => item.studentId === studentId && !item.returned)) {
        showMessage('Cannot Delete', `${student.name} has active borrowed books. Return books first.`, 'error');
        return;
    }
    Swal.fire({ title: 'Are you sure?', text: `Delete ${student.name}? This cannot be undone.`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc3545', confirmButtonText: 'Yes, delete student' })
        .then((result) => {
            if (result.isConfirmed) {
                students = students.filter(s => s.id !== studentId);
                // Optional: Remove student's past borrowing records from borrowedItems if desired (could be an archive step)
                // borrowedItems = borrowedItems.filter(item => item.studentId !== studentId); 
                showMessage('Student Deleted!', `${student.name} has been removed.`, 'success');
                renderManageStudentsPage();
            }
        });
}


// Issued Books
function renderIssuedBooks() { /* Mostly same, ensure DataTables re-init */
    const today = new Date().toISOString().split('T')[0];
    let tableRows = borrowedItems.filter(item => !item.returned).map(item => {
        const book = books.find(b => b.id === item.bookId);
        const student = students.find(s => s.id === item.studentId);
        if (!book || !student) return '';
        const isOverdue = item.dueDate < today;
        return `<tr class="${isOverdue ? 'table-danger' : ''}" data-aos="fade-up"><td>${book.title}</td><td>${student.name}</td><td>${formatDate(item.borrowDate)}</td><td>${formatDate(item.dueDate)}</td><td>${isOverdue ? '<span class="badge bg-danger">Overdue</span>' : '<span class="badge bg-primary">Issued</span>'}</td><td><button class="btn btn-sm btn-success mark-returned-quick-btn" data-borrow-id="${item.id}" title="Mark Returned"><i class="fas fa-check"></i> Return</button></td></tr>`;
    }).join('');
    if (!borrowedItems.filter(item => !item.returned).length) { tableRows = `<tr><td colspan="6" class="text-center py-3">No books are currently issued.</td></tr>`; }
    mainContent.innerHTML = `
                <div class="content-section" data-aos="fade-up">
                    <h2><i class="fas fa-tasks me-2"></i>Currently Issued Books</h2>
                    <div class="table-responsive"><table id="issuedBooksTable" class="table table-striped table-hover" style="width:100%"><thead><tr><th>Book</th><th>Student</th><th>Borrowed</th><th>Due</th><th>Status</th><th>Actions</th></tr></thead><tbody>${tableRows}</tbody></table></div>
                </div>`;
    if ($.fn.DataTable.isDataTable('#issuedBooksTable')) { $('#issuedBooksTable').DataTable().destroy(); }
    $('#issuedBooksTable').DataTable({ responsive: true, "pageLength": 10, "order": [[3, "asc"]], columnDefs: [{ targets: [5], orderable: false, searchable: false }] });
    document.querySelectorAll('.mark-returned-quick-btn').forEach(button => { /* Event listener logic same as before */
        button.addEventListener('click', (e) => {
            const borrowId = parseInt(e.currentTarget.dataset.borrowId);
            const borrowedItem = borrowedItems.find(item => item.id === borrowId);
            if (!borrowedItem) return;
            Swal.fire({ title: 'Confirm Return', text: `Mark "${books.find(b => b.id === borrowedItem.bookId)?.title}" as returned?`, icon: 'question', showCancelButton: true, confirmButtonColor: '#28a745', confirmButtonText: 'Yes, return!' })
                .then((result) => {
                    if (result.isConfirmed) {
                        borrowedItem.returned = true; borrowedItem.returnDate = new Date().toISOString().split('T')[0];
                        const book = books.find(b => b.id === borrowedItem.bookId); if (book) book.available++;
                        updateStudentBorrowedCounts();
                        showMessage('Book Returned!', 'Marked as returned.', 'success');
                        renderIssuedBooks();
                    }
                });
        });
    });
}

// Late Returns Management
function renderLateReturns() { /* Mostly same, ensure DataTables re-init */
    const today = new Date().toISOString().split('T')[0];
    let tableRows = borrowedItems.filter(item => !item.returned && item.dueDate < today).map(item => {
        const book = books.find(b => b.id === item.bookId);
        const student = students.find(s => s.id === item.studentId);
        if (!book || !student) return '';
        const diffDays = Math.max(0, Math.ceil((new Date(today) - new Date(item.dueDate)) / (1000 * 60 * 60 * 24)));
        const lateFee = (diffDays * 1.00).toFixed(2);
        return `<tr class="table-danger" data-aos="fade-up"><td>${book.title}</td><td>${student.name}</td><td>${formatDate(item.dueDate)}</td><td>${diffDays} days</td><td>$${lateFee}</td><td><button class="btn btn-sm btn-warning remind-student-btn" data-student-id="${student.id}" data-book-title="${book.title}"><i class="fas fa-bell"></i> Remind</button><button class="btn btn-sm btn-success mark-returned-late-btn ms-1" data-borrow-id="${item.id}" data-late-fee="${lateFee}"><i class="fas fa-check"></i> Return</button></td></tr>`;
    }).join('');
    if (!borrowedItems.filter(item => !item.returned && item.dueDate < today).length) { tableRows = `<tr><td colspan="6" class="text-center py-3">No late returns at the moment.</td></tr>`; }
    mainContent.innerHTML = `
                <div class="content-section" data-aos="fade-up">
                    <h2><i class="fas fa-exclamation-triangle me-2"></i>Late Return Management</h2>
                    <div class="table-responsive"><table id="lateReturnsTable" class="table table-striped table-hover" style="width:100%"><thead><tr><th>Book</th><th>Student</th><th>Due Date</th><th>Days Overdue</th><th>Fine</th><th>Actions</th></tr></thead><tbody>${tableRows}</tbody></table></div>
                </div>`;
    if ($.fn.DataTable.isDataTable('#lateReturnsTable')) { $('#lateReturnsTable').DataTable().destroy(); }
    $('#lateReturnsTable').DataTable({ responsive: true, "pageLength": 10, "order": [[3, "desc"]], columnDefs: [{ targets: [5], orderable: false, searchable: false }] });
    document.querySelectorAll('.remind-student-btn').forEach(button => { /* Event listener logic same as before */
        button.addEventListener('click', e => {
            const studentId = e.currentTarget.dataset.studentId; const bookTitle = e.currentTarget.dataset.bookTitle;
            const student = students.find(s => s.id === parseInt(studentId));
            showMessage('Reminder Sent (Simulated)', `Reminder sent to ${student.name} for "${bookTitle}".`, 'info');
        });
    });
    document.querySelectorAll('.mark-returned-late-btn').forEach(button => { /* Event listener logic same as before */
        button.addEventListener('click', e => {
            const borrowId = parseInt(e.currentTarget.dataset.borrowId); const lateFee = e.currentTarget.dataset.lateFee;
            const borrowedItem = borrowedItems.find(item => item.id === borrowId); if (!borrowedItem) return;
            Swal.fire({ title: 'Confirm Return & Fine', text: `Mark "${books.find(b => b.id === borrowedItem.bookId)?.title}" returned? Fine: $${lateFee}.`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#28a745', confirmButtonText: 'Yes, process!' })
                .then((result) => {
                    if (result.isConfirmed) {
                        borrowedItem.returned = true; borrowedItem.returnDate = new Date().toISOString().split('T')[0]; borrowedItem.finePaid = true; // Simulate fine paid
                        const book = books.find(b => b.id === borrowedItem.bookId); if (book) book.available++;
                        updateStudentBorrowedCounts();
                        showMessage('Book Returned & Fine Processed!', `Fine of $${lateFee} processed.`, 'success');
                        renderLateReturns();
                    }
                });
        });
    });
}

// Reports/Statistics
function renderReports() { /* Mostly same, ensure charts re-init */
    mainContent.innerHTML = `
                <div class="content-section" data-aos="fade-up">
                    <h2><i class="fas fa-chart-bar me-2"></i>Reports & Statistics</h2>
                    <div class="row g-4">
                        <div class="col-lg-6"><div class="card h-100"><div class="card-header">Books by Genre</div><div class="card-body"><canvas id="genrePieChart" style="max-height: 350px;"></canvas></div></div></div>
                        <div class="col-lg-6"><div class="card h-100"><div class="card-header">Monthly Borrows (Sample)</div><div class="card-body"><canvas id="monthlyBorrowsChart" style="max-height: 350px;"></canvas></div></div></div>
                    </div>
                    <div class="row g-4 mt-3"><div class="col-lg-12"><div class="card h-100"><div class="card-header">Book Availability</div><div class="card-body"><canvas id="availabilityChart" style="max-height: 350px;"></canvas></div></div></div></div>
                </div>`;
    renderGenrePieChart(); renderMonthlyBorrowsChart(); renderAvailabilityChart();
}
function renderGenrePieChart() { /* Same as before */
    const ctx = document.getElementById('genrePieChart')?.getContext('2d'); if (!ctx) return;
    const genreCounts = books.reduce((acc, book) => { acc[book.genre] = (acc[book.genre] || 0) + 1; return acc; }, {});
    new Chart(ctx, { type: 'pie', data: { labels: Object.keys(genreCounts), datasets: [{ data: Object.values(genreCounts), backgroundColor: ['rgba(255,99,132,0.8)', 'rgba(54,162,235,0.8)', 'rgba(255,206,86,0.8)', 'rgba(75,192,192,0.8)', 'rgba(153,102,255,0.8)', 'rgba(255,159,64,0.8)'], hoverOffset: 4 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'top' }, title: { display: true, text: 'Book Titles by Genre' } } } });
}
function renderMonthlyBorrowsChart() { /* Same as before */
    const ctx = document.getElementById('monthlyBorrowsChart')?.getContext('2d'); if (!ctx) return;
    const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    const borrowsPerMonth = [12, 19, 3, 5, 2, 3, 15, 8, 10, 6, 9, 11]; // Sample
    new Chart(ctx, { type: 'line', data: { labels: months, datasets: [{ label: 'Books Borrowed', data: borrowsPerMonth, fill: false, borderColor: 'rgb(75, 192, 192)', tension: 0.1 }] }, options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } }, plugins: { legend: { display: true, position: 'top' }, title: { display: true, text: 'Monthly Borrowing Trend (Sample)' } } } });
}
function renderAvailabilityChart() { /* Same as before */
    const ctx = document.getElementById('availabilityChart')?.getContext('2d'); if (!ctx) return;
    const totalCopies = books.reduce((sum, book) => sum + book.quantity, 0);
    const totalAvailable = books.reduce((sum, book) => sum + book.available, 0);
    const totalBorrowed = totalCopies - totalAvailable;
    new Chart(ctx, { type: 'doughnut', data: { labels: ['Available Copies', 'Borrowed Copies'], datasets: [{ data: [totalAvailable, totalBorrowed], backgroundColor: ['rgba(75,192,192,0.8)', 'rgba(255,159,64,0.8)'], hoverOffset: 4 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'top' }, title: { display: true, text: 'Overall Book Availability' } } } });
}

// FAQ/Help - New Layout
function renderFAQ() {
    const faqs = [
        { q: "How do I search for a book?", a: "Navigate to the 'Book Catalog' section. Use the search bar to find books by title, author, ISBN, or genre. Click column headers to sort." },
        { q: "How do I borrow a book?", a: "Go to 'Borrow Book', select student and book, set return date, and confirm. Or, use the 'Borrow' button in the 'Book Catalog'." },
        { q: "What is the late return policy?", a: "A late fee (e.g., $1.00/day) applies for overdue books. Check 'Late Returns' for details." },
        { q: "How to view my borrow history?", a: "Librarians can view student history in 'Student Records' or 'Manage Students'. A student portal is planned." },
        { q: "Adding new books (admin)?", a: "Use 'Add New Book', fill details, and save. Ensure ISBN and quantity are correct." },
        { q: "Damaged or lost books?", a: "Report to library staff immediately. A replacement fee may apply as per policy." }
    ];
    const faqHtml = faqs.map((faq, index) => `
                <div class="faq-item" data-aos="fade-up" data-aos-delay="${index * 100}">
                    <div class="faq-question"><i class="fas fa-question-circle text-primary me-2"></i>${faq.q}</div>
                    <div class="faq-answer">${faq.a}</div>
                </div>`).join('');
    mainContent.innerHTML = `
                <div class="content-section" data-aos="fade-up">
                    <h2><i class="fas fa-question-circle me-2"></i>FAQ & Help Center</h2>
                    ${faqHtml}
                </div>`;
}

// --- Navigation ---
const navLinks = document.querySelectorAll('#sidebar .nav-link, .footer a[data-page]');
function setActiveLink(page) {
    navLinks.forEach(link => {
        link.classList.remove('active');
        if (link.dataset.page === page) { link.classList.add('active'); }
    });
}

function navigateTo(page, params = {}) {
    mainContent.innerHTML = '<div class="d-flex justify-content-center align-items-center" style="min-height: 300px;"><div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"><span class="visually-hidden">Loading...</span></div></div>';
    // For mobile, close sidebar after navigation
    if (window.innerWidth < 992 && sidebar.classList.contains('active')) {
        sidebar.classList.remove('active');
        pageContentWrapper.classList.remove('sidebar-active');
        sidebarOverlay.style.display = 'none';
    }
    setTimeout(() => {
        switch (page) {
            case 'dashboard': renderDashboard(); break;
            case 'catalog': renderBookCatalog(); break;
            case 'addBook': renderAddNewBookForm(params); break;
            case 'borrowBook': renderBorrowBookForm(params); break;
            case 'returnBook': renderReturnBookPage(); break;
            case 'students': renderStudentRecords(); break;
            case 'manageStudents': renderManageStudentsPage(); break; // New case
            case 'issuedBooks': renderIssuedBooks(); break;
            case 'lateReturns': renderLateReturns(); break;
            case 'reports': renderReports(); break;
            case 'faq': renderFAQ(); break;
            default: mainContent.innerHTML = '<div class="alert alert-danger">Error: Page not found.</div>';
        }
        setActiveLink(page);
        AOS.refreshHard(); // Use refreshHard for dynamically added content
        // Scroll to top of main content area
        if (mainContent.scrollIntoView) {
            mainContent.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } else { // Fallback for older browsers
            window.scrollTo(0, pageContentWrapper.offsetTop);
        }
    }, 150); // Slightly shorter delay
}

navLinks.forEach(link => {
    link.addEventListener('click', (event) => {
        event.preventDefault();
        const page = event.currentTarget.dataset.page;
        if (page) { navigateTo(page); }
    });
});
document.querySelector('.sidebar-header a.navbar-brand').addEventListener('click', (e) => {
    e.preventDefault(); navigateTo('dashboard');
});

// --- Initial Load ---
document.addEventListener('DOMContentLoaded', () => {
    AOS.init({ duration: 500, once: true });
    updateStudentBorrowedCounts(); // Initial calculation
    navigateTo('dashboard'); // Default to dashboard
});