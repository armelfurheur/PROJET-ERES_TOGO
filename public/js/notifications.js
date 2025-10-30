// notifications.js
toastr.options = {
    closeButton: true,
    progressBar: true,
    positionClass: 'toast-top-right',
    timeOut: 5000
};

function showToast(message, type = 'success') {
    const options = { closeButton: true, progressBar: true };
    switch(type) {
        case 'success': toastr.success(message, 'Succès', options); break;
        case 'warning': toastr.warning(message, 'Attention', options); break;
        case 'error': toastr.error(message, 'Erreur', options); break;
        default: toastr.info(message, 'Info', options);
    }
}
