pipeline {
    agent any

    triggers {
        pollSCM('H/1 * * * *') // cek setiap 1 menit
    }

    stages {
        stage('Checkout') {
            steps {
                echo "Pulling latest code..."
                checkout scm
            }
        }

        stage('Deploy with Docker') {
            steps {
                script {
                    echo "Stopping old containers..."
                    sh "docker compose down -v || true"

                    echo "Building & starting new containers..."
                    sh "docker compose up -d --build"

                    echo "Waiting for MySQL to be healthy..."
                    sh """
                        DB_CONTAINER=\$(docker compose ps -q db)
                        until [ "\$(docker inspect --format='{{.State.Health.Status}}' \$DB_CONTAINER)" = "healthy" ]; do
                            echo 'Waiting for MySQL...'
                            sleep 5
                        done
                    """

                    echo "Laravel setup..."
                    sh """
                        if [ -f artisan ]; then
                            docker compose exec -T app cp -n /var/www/html/.env.example /var/www/html/.env || true
                            docker compose exec -T app composer install --no-dev --prefer-dist || true
                            docker compose exec -T app php artisan key:generate --force || true

                            # Jalankan migrate tapi skip tabel yang sudah ada
                            docker compose exec -T app php artisan migrate --force || true

                            docker compose exec -T app php artisan cache:clear || true
                            docker compose exec -T app php artisan view:clear || true
                        fi
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
