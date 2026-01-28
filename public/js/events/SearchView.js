export class SearchView {
    constructor() {
        this.eventsList = document.querySelector('.event-btn-list');
        this.searchInput = document.getElementById('recommended_event');
        this.idInput = document.getElementById('recommended_event_id');
    }

    setSearchInputHandler(handler) {
        this.searchInput.addEventListener('input', (e) => {
            handler(e);
        })
    }

    createSmallEventCard(event) {

        const btn = document.createElement('button');
        btn.textContent = event.name;
        btn.classList.add('event-btn');

        btn.addEventListener('click', (e) => {
            e.preventDefault();
            this.searchInput.value = event.name;
            this.idInput.value = event.id;
        })

        return btn;
    }

    renderSearchEvents(events) {
        this.eventsList.innerHTML = '';
        events.forEach(event => {
            const card = this.createSmallEventCard(event);
            this.eventsList.appendChild(card);
        })
    }
}