# esnspain-web-template

## Environment variables

The project requires a `.env` file with the environment-specific configuration values. A `.env.example` file is provided as a template with the variables that need to be defined.

Copy the example file and fill in the values for your environment:

```bash
cp .env.example .env
```

### HASH_SALT

`HASH_SALT` is one of the required values in the `.env` file. Drupal uses it as a site-specific secret when generating secure hashes, so each environment should use its own unique random value.

Generate a value with:

```bash
php -r "echo bin2hex(random_bytes(32)) . PHP_EOL;"
```

Example:

```text
bf4a2deada231975d99bb2c4dc0eeef4deb09852ffc60387fb4bed7bcc66b7bb
```

### TRUSTED_HOST_PATTERNS

`TRUSTED_HOST_PATTERNS` defines which hostnames Drupal should accept for incoming requests. Values are regular expressions separated by commas.

Local example:

```env
TRUSTED_HOST_PATTERNS=^localhost$,^127\.0\.0\.1$
```

Domain example:

```env
TRUSTED_HOST_PATTERNS=^example\.org$,^www\.example\.org$
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
