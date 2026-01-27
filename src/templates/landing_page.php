<h1> Latest Events </h1>

<div class="event-list">

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

<div class="detail-group">
    <span class="fake-label">organizer:</span>
    <p>{= $event['organizer']['full_name'] }</p>
</div>

    <a href="{= BASE_URL}/events/{=$event['id']}">Event Detail</a>
</div>
{/foreach}
