<h1> Event Detail </h1>

<div>
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
    {foreach $event['workshops'] as $workshop}
        <p>
            {= $workshop['name'] }
        </p>
    {/foreach}

    {if isset($user['email'])}
        {if $isOwner}
        <a href="{= BASE_URL}/events/{=$event['id']}/edit">Edit Event</a>
        {/if}
        {if !$isRegistered && !$isOwner}
        <a href="{= BASE_URL}/events/{=$event['id']}/register">Register for Event</a>
        {/if}

        {if $isRegistered && !$isOwner}
    <form action="{=BASE_URL}/events/{=$event['id']}/cancel" method="POST" enctype="multipart/form-data">
        <button type="submit">Cancel Registration</button>
    </form>
        {/if}
    {/if}

</div>