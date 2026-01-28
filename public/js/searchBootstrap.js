import { EventModel } from './events/EventModel.js';
import { SearchView } from './events/SearchView.js';
import { SearchPresenter } from './events/SearchPresenter.js';

function bootstrap() {
    const eventModel = new EventModel(allEvents);
    const searchView = new SearchView();

    const searchPresenter = new SearchPresenter(eventModel, searchView);
    searchPresenter.initialize();
}

window.addEventListener("DOMContentLoaded", () => bootstrap());