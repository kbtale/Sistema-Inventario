# SIOTIC: Inventory MVP



https://github.com/user-attachments/assets/2e03a818-d928-4d48-a304-ae43c351bf1d



This is an old inventory project I started a long time ago. I finally wanted to finish it as a basic MVP to honor a legacy system that really helped me out back in the day. 

It's pretty much archived now, but I'm putting it out here in case it helps someone looking for a simple asset manager with some basic health tracking and a dark mode.

## Main features

- Asset tracking for hardware, internal components, and mobile devices.
- Maintenance workflow (check-in/out) with condition photos for auditing.
- Automated health "Pulse" score and basic forecasting/decay analytics.
- QR code generation for asset labeling and scanning.
- Native dark mode.
- Fully containerized (Docker) for easier deployment.

## How to run it

If you want to try it out, here's the easiest way to get it running:

1. **Setup your environment**: Copy `.env.example` to `.env` and set a password.
2. **Start the containers**: Run `docker-compose -f docker-compose.prod.yml up -d --build` from the root.

It'll be running on port **80**.

## Extra bits

### Backups
```bash
docker exec siotic-db-prod mysqldump -u root -p$(grep DB_ROOT_PASSWORD .env | cut -d '=' -f 2) siotic > backup_$(date +%F).sql
```

### Logs
```bash
docker-compose -f docker-compose.prod.yml logs -f webserver
```
