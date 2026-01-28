const startInput = document.getElementById('start_date');
const endInput = document.getElementById('end_date');

startInput.addEventListener('change', () => {
    endInput.min = startInput.value;
    if (endInput.value && endInput.value < startInput.value) {
        endInput.value = startInput.value;
    }
});

const today = new Date();
const tomorrow = new Date(today);
tomorrow.setDate(tomorrow.getDate() + 1);

const tomorrowStr = tomorrow.toISOString().split('T')[0];

startInput.setAttribute('min', tomorrowStr);