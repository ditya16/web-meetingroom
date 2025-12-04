pipeline {
    agent any

    triggers {
        pollSCM('H/1 * * * *')
    }

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
                    -subj "/CN=localhost"
                fi
                """
            }
        }

        stage('Deploy with Docker') {
            steps {
                script {

                    sh "docker compose down -v || true"
                    sh "docker volume prune -f || true"

                    sh "docker compose up -d --build"

                    // Tunggu MySQL healthy
                    sh """
                    echo "Waiting for MySQL to be healthy..."
                    DB_CONTAINER=\$(docker compose ps -q db)

                    for i in {1..30}; do
                      STATUS=\$(docker inspect --format='{{.State.Health.Status}}' \$DB_CONTAINER)
                      if [ "\$STATUS" = "healthy" ]; then
                        echo "MySQL is healthy!"
                        break
                      fi
                      echo "MySQL not ready yet... (\$i)"
                      sleep 5
                    done
                    """

                    // Buat DB & User
                    sh '''
docker compose exec -T db mysql -u root -ppassword <<EOF
CREATE DATABASE IF NOT EXISTS meetingroom;
CREATE USER IF NOT EXISTS 'mr_user'@'%' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON meetingroom.* TO 'mr_user'@'%';
FLUSH PRIVILEGES;
EOF
'''
                    // Laravel setup
                    sh """
                    docker compose exec -T app cp -n /var/www/html/.env.example /var/www/html/.env || true
                    docker compose exec -T app composer install --no-dev --prefer-dist
                    docker compose exec -T app php artisan key:generate --force
                    docker compose exec -T app php artisan config:clear
                    docker compose exec -T app php artisan cache:clear
                    docker compose exec -T app php artisan view:clear
                    docker compose exec -T app php artisan migrate --force
                    """
                }
            }
        }
    }

    post {
        success { echo "Deployment berhasil!" }
        failure { echo "Deployment gagal!" }
    }
}
