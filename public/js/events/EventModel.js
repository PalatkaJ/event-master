export class EventModel {
    constructor(allEvents) {
        this.events = allEvents;
        this.pageSize = 7;
    }

    // counting from one
    getPage(pageNumber) {
        const start = (pageNumber - 1) * this.pageSize;
        return this.events.slice(start, start + this.pageSize);
    }

    getTotalPages() {
        return Math.ceil(this.events.length / this.pageSize);
    }
}