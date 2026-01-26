<h1>My Events</h1>

{foreach $events as $event}
<div class="event-card">
    <h2>
        {= $event['name'] }
    </h2>
    <p>
        description: {= $event['description'] }
    </p>
    <p>
        start: {= $event['start_date'] }
    </p>
    <p>
        end: {= $event['end_date'] }
    </p>
    <p>
        img: <img src="{= BASE_URL }/data/{= $event['hero_img'] }" alt="Event Image">
    </p>
    {foreach $event['registeredWorkshops'] as $workshop}
    <p>
        {= $workshop['name'] }
    </p>
    {/foreach}

</div>
{/foreach}