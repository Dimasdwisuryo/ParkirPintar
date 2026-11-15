
pipeline {
    agent any

    options {
        skipDefaultCheckout(true)
        timestamps()
        disableConcurrentBuilds()
    }

    environment {
        IMAGE = 'dimasdwiaja/parkirpintar'
        APP_IMAGE = 'dimasdwiaja/parkirpintar'
        SMOKE_URL = 'http://127.0.0.1:8000/health'
    }

    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Test') {
            steps {
                bat 'docker version'
                bat 'if not exist test-results mkdir test-results'
                bat 'docker build --target test -t parkirpintar-test:%BUILD_NUMBER% .'
                bat 'docker run --rm -v "%WORKSPACE%/test-results:/var/www/html/test-results" parkirpintar-test:%BUILD_NUMBER% php artisan test --log-junit=test-results/junit.xml'
            }
        }

        stage('Build Image') {
            steps {
                bat 'docker build --target production -t %IMAGE%:%BUILD_NUMBER% -t %IMAGE%:latest .'
            }
        }

        stage('Push Image') {
            steps {
                withCredentials([
                    usernamePassword(
                        credentialsId: 'dockerhub-credentialss',
                        usernameVariable: 'DH_USER',
                        passwordVariable: 'DH_PASS'
                    )
                ]) {
                    bat '@powershell -NoProfile -Command "$env:DH_PASS | docker login -u $env:DH_USER --password-stdin"'
                    bat 'docker push %IMAGE%:%BUILD_NUMBER%'
                    bat 'docker push %IMAGE%:latest'
                }
            }
        }

        stage('Deploy') {
            steps {
                withCredentials([
                    file(
                        credentialsId: 'parkirpintar-env',
                        variable: 'DEPLOY_ENV_FILE'
                    )
                ]) {
                    bat 'copy /Y "%DEPLOY_ENV_FILE%" ".env" >NUL'
                    bat 'set APP_IMAGE=%IMAGE%&& docker compose config -q'
                    bat 'set APP_IMAGE=%IMAGE%&& docker compose up -d --wait db'
                    bat 'set APP_IMAGE=%IMAGE%&& docker compose pull app'
                    bat 'set APP_IMAGE=%IMAGE%&& docker compose run --rm app php artisan migrate --force'
                    bat 'set APP_IMAGE=%IMAGE%&& docker compose up -d app'
                    bat 'docker compose ps'
                }
            }
        }

        stage('Smoke Test') {
            steps {
                script {
                    def healthy = false

                    for (int i = 1; i <= 12; i++) {
                        def result = bat(
                            script: '@powershell -NoProfile -Command "try { $r = Invoke-RestMethod -Uri \'http://127.0.0.1:8000/health\' -TimeoutSec 5; if ($r.status -eq \'healthy\') { exit 0 } else { exit 1 } } catch { exit 1 }"',
                            returnStatus: true
                        )

                        if (result == 0) {
                            healthy = true
                            break
                        }

                        echo "Menunggu aplikasi siap: ${i}/12"
                        sleep(time: 5, unit: 'SECONDS')
                    }

                    if (!healthy) {
                        error 'Smoke Test gagal: endpoint /health belum healthy.'
                    }
                }
            }
        }
    }

    post {
        always {
            script {
                if (fileExists('test-results/junit.xml')) {
                    junit(
                        testResults: 'test-results/junit.xml',
                        allowEmptyResults: false
                    )

                    archiveArtifacts(
                        artifacts: 'test-results/junit.xml',
                        allowEmptyArchive: false
                    )
                } else {
                    echo 'JUnit XML belum tersedia.'
                }
            }
        }

        success {
            echo 'Pipeline sukses: ParkirPintar berhasil di-deploy.'
        }

        failure {
            echo 'Pipeline gagal. Periksa stage yang merah.'
        }
    }
}
