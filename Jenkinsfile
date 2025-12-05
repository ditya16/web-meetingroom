pipeline {
    agent any
    triggers { pollSCM('*/1 * * * *') }

    environment {
        DB_ROOT_PASS = "password"
        DB_NAME      = "meetingroom"
        DB_USER      = "mr_user"
        DB_PASS      = "password"
    }

    stages {

        stage('Checkout') {
            steps { checkout scm }
        }

        stage('Prepare SSL') {
            steps {
                sh '''
                mkdir -p docker/ssl
                if [ ! -f docker/ssl/server.key ]; then
                    openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
                        -keyout docker/ssl/server.key \
                        -out docker/ssl/server.crt \
                        -subj "/CN=pklntp.com"
                fi
                '''
            }
        }

        stage('Docker Build (Fixed DNS)') {
            steps {
                sh '''
                echo "Building with forced DNS..."
                docker compose build --build-arg DNS=8.8.8.8
                '''
            }
        }

        stage('Deploy Containers') {
            steps {
                script {

                    echo "STOP OLD CONTAINERS"
                    sh "docker compose down -v || true"

                    echo "START NEW CONTAINERS"
                    sh "docker compose up -d"
                    
                    echo "WAIT DB HEALTH..."
                    sh '''
DB=$(docker compose ps -q db)
for i in {1..40}; do
    STATUS=$(docker inspect --format='{{.State.Health.Status}}' $DB)
    echo "DB health: $STATUS"
    [ "$STATUS" = "healthy" ] && break
    sleep 3
done
'''
                }
            }
        }

        stage('Init Database') {
            steps {
                sh '''
docker compose exec -T db mysql -uroot -p${DB_ROOT_PASS} <<EOF
CREATE DATABASE IF NOT EXISTS ${DB_NAME};
CREATE USER IF NOT EXISTS '${DB_USER}'@'%' IDENTIFIED BY '${DB_PASS}';
GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'%';
FLUSH PRIVILEGES;
EOF
'''
            }
        }

        stage('Laravel Setup') {
            steps {
                sh '''
docker compose exec -T app git config --global --add safe.directory /var/www/html || true

docker compose exec -T app bash -c 'cp -n /var/www/html/.env.example /var/www/html/.env'

docker compose exec -T app composer install --no-dev --prefer-dist

docker compose exec -T app php artisan key:generate --force
docker compose exec -T app php artisan config:clear
docker compose exec -T app chmod -R 777 storage bootstrap/cache

docker compose exec -T app php artisan migrate --force || true
'''
            }
        }
    }

    post {
        success { echo "Deploy FINISHED ✔" }
        failure { echo "Deploy FAILED ❌" }
    }
}
