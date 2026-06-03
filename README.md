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
