import { EventModel } from './events/EventModel.js';
import { EventView } from './events/EventView.js';
import { EventPresenter } from './events/EventPresenter.js';

function bootstrap() {
    const eventModel = new EventModel(allEvents);
    const eventView = new EventView();

    const eventPresenter = new EventPresenter(eventModel, eventView);
    eventPresenter.initialize();

    console.log(allEvents);
}

window.addEventListener("DOMContentLoaded", () => bootstrap());