export class EventView {
    constructor() {
        this.previousButton = document.getElementById('previousButton');
        this.nextButton = document.getElementById('nextButton');
        this.eventsList = document.querySelector('.event-list');
        this.pageNr = document.getElementById('pageNr');
    }

    setNextButtonHandler(handler) {
        this.nextButton.addEventListener('click', (e) => {
            handler(e);
        });
    }

    setPrevButtonHandler(handler) {
        this.previousButton.addEventListener('click', (e) => {
            handler(e);
        });
    }

    createEventCard(event) {
        const item = document.createElement('div');
        item.classList.add('event-card');

        const h2 = document.createElement('h2');
        h2.textContent = event.name;
        item.appendChild(h2);

        const pStart = document.createElement('p');
        pStart.textContent = event.start_date;
        item.appendChild(pStart);

        const pEnd = document.createElement('p');
        pEnd.textContent = event.end_date;
        item.appendChild(pEnd);

        const pOrganizer = document.createElement('p');
        pOrganizer.textContent = event.organizer.full_name;
        item.appendChild(pOrganizer);

        const link = document.createElement('a');
        link.href = `${BASE_URL}/events/${event.id}`;
        link.textContent = 'Event Detail';
        item.appendChild(link);

        return item;
    }

    renderEvents(events, pageNr, maxPages) {
        this.eventsList.innerHTML = '';

        events.forEach(event => {
            const card = this.createEventCard(event);
            this.eventsList.appendChild(card);
        });

        this.pageNr.textContent = 'Page ' + pageNr + ' of ' + maxPages;
    }
}