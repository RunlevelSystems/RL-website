<!-- Developed by Core Loop Development LLC -->
# Game XML Reference (GSP Fork)

Order matters in every game definition. Place **exactly one** `<game_config>` element per file and keep the sections below in sequence.

## Required Elements

```xml
<game_config>
  <game_key>valheim_linux64</game_key>
  <protocol>lgsl</protocol>
  <lgsl_query_name>valheim</lgsl_query_name>
  <installer>steamcmd</installer>
  <game_name>Valheim</game_name>
  <server_exec_name>start_server.sh</server_exec_name>
  <query_port type="add">1</query_port>
  <cli_template>./panelStart.sh ./start_server.sh %MAP% %PORT% %IP%</cli_template>
  <cli_params>
    <cli_param id="MAP" cli_string="-world=" options="q" />
    <cli_param id="PORT" cli_string="-port=" options="sq" />
    <cli_param id="IP" cli_string="-publicip=" options="q" />
  </cli_params>
</game_config>
```

- **`game_key`** – unique slug + OS suffix (`_linux64`, `_win64`, etc.) so the panel knows which agents can provision it.
- **`protocol`** – choose from `lgsl`, `gameq`, `rcon`, or `rcon2`. This controls live status polling.
- **`installer`** – `steamcmd`, `rsync`, `manual`, or `custom`. Steam titles almost always use `steamcmd`.
- **`cli_template`** – appended to `panelStart`. Variables wrapped in `%` are replaced at runtime.
- **`cli_params`** – formatting rules for every variable used in the template.

## Formatting Options

```xml
<!-- SPACE ONLY -->
<cli_param id="MAP" cli_string="-map" options="s" />
<!-- Result: -map de_dust2 -->

<!-- QUOTES ONLY -->
<cli_param id="CONFIG" cli_string="-config" options="q" />
<!-- Result: -config "server.cfg" -->

<!-- SPACE + QUOTES -->
<cli_param id="NAME" cli_string="-hostname" options="sq" />
<!-- Result: -hostname "My Server" -->
```

- `s` – prepend a single space.
- `q` – wrap the value in double quotes.
- `n` – omit the parameter entirely if the value is blank.
- Combine flags (e.g. `sq`) for complex formatting.

## Mods and Additional Ports

- Wrap optional builds inside `<mods>` with `<mod key="whatever">`.
- Use `<reserve_ports>` to map query, RCON, or Steam ports relative to `%PORT%`.
- Store map rotation paths in `<maps_location>` so the panel can populate dropdowns.

## panelStart Reminder

Every CLI template should ultimately call `panelStart.sh` or `panelStart.bat`. That wrapper:

- Records the launched PID to `*.pid`.
- Streams STDOUT/STDERR to `console.log` for the customer viewer.
- Handles Steam Workshop downloads, crash restarts, and billing hooks.
