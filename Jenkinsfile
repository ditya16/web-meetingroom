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

        stage('Deploy Docker') {
            steps {
                script {

                    // STOP DAN BERSIHKAN
                    sh "docker compose down -v || true"
                    sh "docker volume prune -f || true"

                    // UP + BUILD
                    sh "docker compose up -d --build"

                    // TUNGGU MYSQL HEALTHY
                    sh """
                    echo "Waiting for MySQL health..."
                    DB_CONTAINER=\$(docker compose ps -q db)

                    for i in {1..30}; do
                        STATUS=\$(docker inspect --format='{{.State.Health.Status}}' \$DB_CONTAINER)
                        echo "Status: \$STATUS"
                        
                        if [ "\$STATUS" = "healthy" ]; then
                            echo "MySQL is healthy!"
                            break
                        fi

                        sleep 3
                    done

                    sleep 5
                    """

                    // FIX: JANGAN PAKAI -h !!!!
                    sh '''
docker compose exec -T db mysql -uroot -ppassword <<EOF
CREATE DATABASE IF NOT EXISTS meetingroom;
CREATE USER IF NOT EXISTS 'mr_user'@'%' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON meetingroom.* TO 'mr_user'@'%';
FLUSH PRIVILEGES;
EOF
'''

                    // LARAVEL
                    sh """
                    docker compose exec -T app cp -n /var/www/html/.env.example /var/www/html/.env || true
                    docker compose exec -T app composer install --no-dev --prefer-dist
                    docker compose exec -T app php artisan key:generate --force
                    docker compose exec -T app php artisan migrate --force
                    docker compose exec -T app php artisan config:clear
                    docker compose exec -T app php artisan cache:clear
                    docker compose exec -T app php artisan view:clear
                    """
                }
            }
        }
    }

    post {
        success {
            echo "Deployment berhasil!"
        }
        failure {
            echo "Deployment gagal!"
        }
    }
}
