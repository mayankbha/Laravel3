<?php

return [
    'client_id' => env('MIXER_KEY', ''),
    'client_secret' => env('MIXER_SECRET', ''),
    'redirect_url' => env('MIXER_REDIRECT_URI', ''),
    'scopes' => [
        /*"achievement:view:self", //   View your earned achievements.
        "channel:analytics",   // View analytics for a channel.
        "channel:analytics:self", // View your channel analytics.
        "channel:costream:self",  // Manage your costreaming requests.
        "channel:deleteBanner",//    Delete a channel's banner
        "channel:deleteBanner:self",//   Delete your channel banner
        "channel:details:self", //    View your channel details.
        "channel:follow:self", //  Follow and unfollow other channels.
        "channel:partnership", //  Create and view partnership applications.
        "channel:partnership:self", //     Manage your partnership status.
        "channel:streamKey:self", //   View your channel's stream key.
        "channel:update:self", //  Update your channel settings
        "chat:bypass_links", //    Bypass links being disallowed in chat.
        "chat:bypass_slowchat", //     Bypass slowchat settings on channels.
        "chat:change_ban", //  Manage bans in chats.
        "chat:change_role", //     Manage roles in chats. */
        "chat:chat", //    Interact with chats on your behalf.
        "chat:connect", //     Connect to chat.
        /*"chat:clear_messages", //  Clear messages in chats where authorized.
        "chat:edit_options", //    Edit chat options, including links settings and slowchat.
        "chat:giveaway_start", //  Start a giveaway in chats where authorized.
        "chat:poll_start", //  Start a poll in chats where authorized.
        "chat:poll_vote", //   Vote in chat polls.
        "chat:purge", //   Clear all messages from a specific user in chat.
        "chat:remove_message", //  Remove own and other's messages in chat.
        "chat:timeout", //     Change timeout settings in chats.
        "chat:view_deleted ", //   View deleted messages in chat.
        "chat:whisper", //     Gives the ability to whisper in a channel
        "interactive:manage:self", //  Create, update and delete the interactive games in your account.
        "interactive:robot:self", //   Run as an interactive game in your channel.
        "invoice:view:self", //    View the users invoices.
        "log:view:self", //    View and manage your security log.
        "notification:update:self", //     Create and manage your notifications.
        "notification:view:self", //   View your notifications.
        "recording:manage:self ", //   Manage the users VODs.
        "redeemable:create:self", //   Create redeemables after performing a purchase.
        "redeemable:redeem:self", //   Use users redeemable.
        "redeemable:view:self", //     View users redeemables.
        "resource:find:self", //   View emoticons and other graphical resources you have access to.
        "subscription:cancel:self", //    Cancel your subscriptions.
        "subscription:create:self", //     Create new subscriptions.
        "subscription:renew:self", //  Renew your existing subscriptions.
        "subscription:view:self", //   View who you're subscribed to.
        "team:administer", //  Administrate teams the user has rights in.
        "team:manage:self", //     Create, join, leave teams and set the users primary team.
        "transaction:cancel:self", //  Cancel pending transactions.
        "transaction:view:self", //    View your pending transactions.
        "type:viewHidden", //  Gives the ability to view hidden types in ES
        "user:analytics:self", //  View your user analytics */
        "user:details:self", //    View your email address and other private details.
        /*"user:getDiscordInvite:self", //   View users discord invites.
        "user:notification:self", //   View and manage your notifications.
        "user:log:self", //    View your user security log.
        "user:seen:self", //   Mark a VOD as seen for the user.
        "user:update:self", //     Update your account, including your email but not your password.
        "user:updatePassword:self", //     Update your password. */
    ],
];
