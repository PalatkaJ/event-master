<h1>My Events</h1>

{foreach $events as $event}
<div class="event-card">
    <h2> {= $event['name']} </h2>

    <div class="detail-group">
        <span class="fake-label">start:</span>
        <p>{= $event['start_date'] }</p>
    </div>

    <div class="detail-group">
        <span class="fake-label">end:</span>
        <p>{= $event['end_date'] }</p>
    </div>

    <div class="detail-image-box">
        <img src="{= BASE_URL }/data/{= $event['hero_image'] }" alt="Event Image">
    </div>

    <fieldset>
        <legend>Workshops</legend>
        <div class="workshop-list">
            {foreach $event['registeredWorkshops'] as $workshop}
            <p class="workshop-item">{= $workshop['name'] }</p>
            {/foreach}
        </div>
    </fieldset>
</div>
{/foreach}