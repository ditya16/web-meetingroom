#!/bin/bash
set -e
cd ~/repo/room
git pull origin main
docker compose down
docker compose build --no-cache
docker compose up -d
chmod +x docker/entrypoint.sh
./docker/entrypoint.sh
