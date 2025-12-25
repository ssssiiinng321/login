# Vercel Environment Configuration

To ensure your application works on Vercel, you must configure the following Environment Variables in your Vercel Project Settings.

## Database Connection
Since Vercel fails to connect to "localhost", you must provide the details of your remote cloud database (e.g., Aiven, PlanetScale, AWS RDS).

Go to **Settings > Environment Variables** and add:

| Key | Value Example | Description |
|-----|---------------|-------------|
| `DB_HOST` | `mysql-324...aivencloud.com` | Your cloud database hostname |
| `DB_PORT` | `12345` | Your cloud database port (default is 3306) |
| `DB_NAME` | `defaultdb` | The name of your database |
| `DB_USER` | `avnadmin` | Your database username |
| `DB_PASS` | `password123...` | Your database password |

## Troubleshooting
- **500 Internal Server Error**: Usually means the database connection failed. Check the Vercel Function Logs.
- **Login fails/loops**: The `sessions` table might be missing in your cloud database. Run the `database.sql` script on your cloud database.
