document.addEventListener('DOMContentLoaded', function() {
    const deleteLinks = document.querySelectorAll('.btn-danger');
    deleteLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            if(!confirm('Are you sure you want to delete this record?')) {
                e.preventDefault();
            }
        });
    });
});
