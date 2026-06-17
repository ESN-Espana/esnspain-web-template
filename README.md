# esnspain-web-template

## Environment variables

Copy the example file:

```bash
cp .env.example .env
```

### HASH_SALT

Generate a unique random value:

```bash
php -r "echo bin2hex(random_bytes(32)) . PHP_EOL;"
```

Example:

```text
bf4a2deada231975d99bb2c4dc0eeef4deb09852ffc60387fb4bed7bcc66b7bb
```

<details>
<summary>Local Docker</summary>

## Local Docker

The Docker containers included in this repository are intended to make local testing easier. They are not intended for deploying the site to Plesk.

Start the local environment:

```bash
docker compose up -d
```

The site will be available at:

```text
http://localhost:8080
```

View the logs:

```bash
docker compose logs -f
```

Stop the environment:

```bash
docker compose down
```

Remove the local database volume as well:

```bash
docker compose down -v
```

</details>
