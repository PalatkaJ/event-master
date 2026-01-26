<h1> Latest Events </h1>

{foreach $events as $event}
<div class="event-card">
<h2> {= $event['name']} </h2>
<p>
    {= $event['start_date']}
</p>
<p>
    {= $event['end_date']}
</p>
<p>
    {= $event['organizer']['full_name']}
</p>
    <a href="{= BASE_URL}/events/{=$event['id']}">Event Detail</a>
</div>
{/foreach}
