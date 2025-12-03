pipeline {
    agent any

    stages {
        stage('Checkout') {
            steps { checkout scm }
        }

        stage('Docker Build & Deploy') {
            steps {
                script {
                    echo 'Stopping old containers...'
                    sh "docker compose down -v"

                    echo 'Starting new containers...'
                    sh "docker compose up -d --build"

                    echo 'Waiting for MySQL...'
                    sh """
                    until [ "\$(docker inspect --format='{{.State.Health.Status}} room-db')" = "healthy" ]; do
                        echo "Waiting for MySQL..."
                        sleep 5
                    done
                    """

                    echo 'Setup Laravel'
                    sh """
                    docker compose exec -T app rm -rf /var/www/html/.env
                    docker compose exec -T app cp /var/www/html/.env.example /var/www/html/.env
                    docker compose exec -T app composer install --no-dev --prefer-dist
                    docker compose exec -T app php artisan key:generate --force
                    docker compose exec -T app php artisan migrate --force
                    docker compose exec -T app php artisan cache:clear
                    docker compose exec -T app php artisan view:clear
                    """
                }
            }
        }
    }

    post {
        success { echo 'Deployment berhasil!' }
        failure { echo 'Deployment GAGAL. Cek log Jenkins.' }
    }
}
