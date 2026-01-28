<h1> {= $event['name'] } </h1>

<div class="event-detail">
    <div class="detail-image-box">
        <img src="{= BASE_URL }/data/{= $event['hero_image'] }" alt="Event Image">
    </div>

    <div class="detail-group">
        <span class="fake-label stacked">Description:</span>
        <p>{= $event['description'] }</p>
    </div>

    <div class="detail-group">
        <span class="fake-label">Start Date:</span>
        <p>{= $event['start_date'] }</p>
    </div>

    <div class="detail-group">
        <span class="fake-label">End Date:</span>
        <p>{= $event['end_date'] }</p>
    </div>

    <fieldset>
        <legend>Workshops</legend>
        <div class="workshop-list">
            {foreach $event['workshops'] as $workshop}
            <p class="workshop-item">{= $workshop['name'] }</p>
            {/foreach}
        </div>
    </fieldset>

    {if isset($user['email'])}
        {if $isOwner}
    <form action="{=BASE_URL}/events/{=$event['id']}/edit" method="GET">
        <button type="submit">Edit Event</button>
    </form>
        {/if}
        {if !$isRegistered && !$isOwner}
    <form action="{=BASE_URL}/events/{=$event['id']}/register" method="GET">
        <button type="submit">Register for Event</button>
    </form>
        {/if}

        {if $isRegistered && !$isOwner}
    <form action="{=BASE_URL}/events/{=$event['id']}/cancel" method="POST" enctype="multipart/form-data">
        <button type="submit" class="deleteBtn">Cancel Registration</button>
    </form>
        {/if}
    {/if}

    {if isset($event['recommended_event'])}
    <div class="event-card">
        <h2> {= $event['recommended_event']['name']} </h2>

        <div class="detail-group">
            <span class="fake-label">start:</span>
            <p>{= $event['recommended_event']['start_date'] }</p>
        </div>

        <div class="detail-group">
            <span class="fake-label">end:</span>
            <p>{= $event['recommended_event']['end_date'] }</p>
        </div>

        <a href="{= BASE_URL}/events/{=$event['recommended_event']['id']}">Event Detail</a>
    </div>
    {/if}
</div>