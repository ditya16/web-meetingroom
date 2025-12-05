pipeline {
    agent any

    triggers { pollSCM('H/1 * * * *') }

    stages {

        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Prepare SSL') {
            steps {
                sh """
                mkdir -p docker/ssl
                if [ ! -f docker/ssl/server.key ]; then
                    openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
                    -keyout docker/ssl/server.key \
                    -out docker/ssl/server.crt \
                    -subj "/CN=pklntp.com"
                fi
                """
            }
        }

        stage('Deploy Docker') {
            steps {
                script {

                    echo "DOWN old containers…"
                    sh "docker compose down || true"

                    echo "BUILD + UP…"
                    sh "docker compose up -d --build"

                    echo "WAITING FOR MYSQL…"
                    sh '''
DB=$(docker compose ps -q db)
for i in {1..40}; do
    STATUS=$(docker inspect --format='{{.State.Health.Status}}' $DB)
    echo "MySQL status: $STATUS"
    [ "$STATUS" = "healthy" ] && break
    sleep 3
done
'''

                    echo "CREATE DB + USER…"
                    sh '''
docker compose exec -T db mysql -uroot -ppassword <<EOF
CREATE DATABASE IF NOT EXISTS meetingroom;
CREATE USER IF NOT EXISTS 'mr_user'@'%' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON meetingroom.* TO 'mr_user'@'%';
FLUSH PRIVILEGES;
EOF
'''

                    echo "LARAVEL SETUP…"
                    sh """
                    # Fix git safe.directory supaya composer ga error
                    docker compose exec -T app git config --global --add safe.directory /var/www/html || true

                    # Generate env hanya kalau belum ada
                    docker compose exec -T app bash -c 'cp -n /var/www/html/.env.example /var/www/html/.env'

                    docker compose exec -T app composer install --no-dev --prefer-dist
                    docker compose exec -T app php artisan key:generate --force
                    docker compose exec -T app php artisan config:clear

                    # MIGRATE tapi jangan gagal kalau tabel sudah ada
                    docker compose exec -T app php artisan migrate --force || true
                    """
                }
            }
        }
    }

    post {
        success { echo "Deploy FINISHED ✔" }
        failure { echo "Deploy FAILED ❌" }
    }
}
