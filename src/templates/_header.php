<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSWI142 final project</title>
    <link rel="stylesheet" href="{= BASE_URL }/styles.css">
</head>
<body>
<header>
    <nav>
        <div class="nav-left">
            <a href="{= BASE_URL}/" class="logo"><strong>EventMaster</strong></a>
            <a href="{= BASE_URL}/events">All Events</a>
        </div>
        <div class="nav-right">
            {if !isset($user['email'])}
            <a href="{= BASE_URL}/login">Login</a>
            {/if}

            {if isset($user['email'])}
            <a href="{= BASE_URL}/settings"><strong>{= $user['full_name']}</strong></a>
            <a href="{= BASE_URL}/events/new">Create Event</a>
            <a href="{= BASE_URL}/events/mine">My Events</a>
            <a href="{= BASE_URL}/logout">Logout</a>
            {/if}
        </div>
    </nav>
</header>