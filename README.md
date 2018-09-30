Configure the bot
--------------------

Create a `config_local.yaml` file inside the `config` folder with this content:
```yaml
# config/config_local.yaml
imports:
  - { resource: config.yaml }

bot:
  token:
    - 'YOUR_TOKEN_HERE'
    - 'YOUR_SECOND_TOKEN_HERE'
    - '...'

```

Starting the bot
--------------------
This application will run as CLI application, long running PHP process and in long polling mode Telegram Bot. Go into a terminal and simply run `php antiflood-bot` command.
