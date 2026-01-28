export class SearchPresenter {
    constructor(model, view) {
        this.eventModel = model;
        this.searchView = view;
    }

    handleSearchInput(event) {
        const eventName = event.target.value;

        this.searchView.renderSearchEvents(this.eventModel.getEventsWithName(eventName));
    }

    registerHandlers() {
        this.searchView.setSearchInputHandler(this.handleSearchInput.bind(this));
    }

    initialize() {
        this.registerHandlers();
        this.searchView.renderSearchEvents(this.eventModel.getEventsWithName(''));
    }

}