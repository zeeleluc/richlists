const buttons = document.querySelectorAll('.btn.btn-richlist-details');
if (buttons) {
    buttons.forEach(button => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            const parentRow = button.closest('tr');
            const nextRow = parentRow.nextElementSibling;
            nextRow.classList.toggle('hide');
        });
    });
}
