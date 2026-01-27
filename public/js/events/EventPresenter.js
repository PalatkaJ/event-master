export class EventPresenter {
    constructor(model, view) {
        this.eventModel = model;
        this.eventView = view;
        this.currentPage = 1;
    }

    handleCurrentPage() {
        const events = this.eventModel.getPage(this.currentPage);
        this.eventView.renderEvents(events);
    }

    nextPageAvailable() {
        return this.currentPage < this.eventModel.getTotalPages();
    }

    prevPageAvailable() {
        return this.currentPage > 1;
    }

    handleNextPage(event) {
        if (this.nextPageAvailable()) {
            this.currentPage++;
            this.handleCurrentPage();
        }
    }


    handlePrevPage(event) {
        if (this.prevPageAvailable()) {
            this.currentPage--;
            this.handleCurrentPage();
        }
    }

    registerHandlers() {
        this.eventView.setNextButtonHandler(this.handleNextPage.bind(this));
        this.eventView.setPrevButtonHandler(this.handlePrevPage.bind(this));
    }

    initialize() {
        this.registerHandlers();
        this.handleCurrentPage();
    }

}