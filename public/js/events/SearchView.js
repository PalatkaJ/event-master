export class SearchView {
    constructor() {
        this.eventsList = document.querySelector('.event-list');
        this.searchInput = document.getElementById('recommended_event');
        this.idInput = document.getElementById('recommended_event_id');
    }

    setSearchInputHandler(handler) {
        this.searchInput.addEventListener('input', (e) => {
            handler(e);
        })
    }

    createSmallEventCard(event) {
        const item = document.createElement('div');
        item.className = 'event-card';

        const link = document.createElement('button');
        link.textContent = event.name;
        item.appendChild(link);

        link.addEventListener('click', (e) => {
            e.preventDefault();
            this.searchInput.value = event.name;
            this.idInput.value = event.id;
        })

        return item;
    }

    renderSearchEvents(events) {
        console.log("rendering...:");
        console.log(events);

        this.eventsList.innerHTML = '';
        events.forEach(event => {
            const card = this.createSmallEventCard(event);
            this.eventsList.appendChild(card);
        })
    }
}