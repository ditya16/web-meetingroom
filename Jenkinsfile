pipeline {
    agent any

    triggers {
        pollSCM('H/1 * * * *')
    }

    stages {

        stage('Checkout') {
            steps {
                echo "📥 Pulling latest code..."
                checkout scm
            }
        }

        stage('Deploy with Docker') {
            steps {
                script {

                    echo "🐳 Stopping old containers..."
                    sh "docker compose down -v || true"

                    echo "🔨 Building & starting new containers..."
                    sh "docker compose up -d --build"

                    echo "⏳ Waiting for MySQL to be healthy..."
                    sh """
                        until [ "\$(docker inspect --format='{{.State.Health.Status}}' meetingapp-db-1)" = "healthy" ]; do
                            echo 'Waiting for MySQL...'
                            sleep 5
                        done
                    """

                    echo "⚙️ Running Laravel setup..."
                    sh """
                        docker compose exec -T app rm -f /var/www/html/.env
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
        success {
            echo "🎉 Deployment berhasil!"
        }
        failure {
            echo "❌ Deployment gagal!"
        }
    }
}
