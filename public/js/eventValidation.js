const startInput = document.getElementById('start_date');
const endInput = document.getElementById('end_date');

startInput.addEventListener('change', () => {
    endInput.min = startInput.value;
    if (endInput.value && endInput.value < startInput.value) {
        endInput.value = startInput.value;
    }
});