pipeline {
    agent any

    options {
        skipDefaultCheckout(true)
        timestamps()
        disableConcurrentBuilds()
    }

    environment {
        IMAGE = 'dimasdwiaja/parkirpintar'
        SMOKE_URL = 'http://127.0.0.1:8000/health'
        PATH = "C:\\Users\\ACER\\AppData\\Local\\Programs\\DockerDesktop\\resources\\bin;C:\\Program Files\\Docker\\Docker\\resources\\bin;${env.PATH}"
    }

    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Test') {
            steps {
                bat 'powershell -NoProfile -ExecutionPolicy Bypass -File scripts\\jenkins-test.ps1'
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
                    bat 'docker compose config -q'
                    bat 'docker compose pull app'
                    bat 'docker compose up -d db'
                    bat 'docker compose run --rm app php artisan migrate --force'
                    bat 'docker compose up -d app'
                    bat 'docker compose ps'
                }
            }
        }

        stage('Smoke Test') {
            steps {
                bat 'powershell -NoProfile -ExecutionPolicy Bypass -File scripts\\smoke-test.ps1'
            }
        }
    }

    post {
        always {
            script {
                def junitFile = fileExists('build/logs/junit.xml') ? 'build/logs/junit.xml' : (fileExists('test-results/junit.xml') ? 'test-results/junit.xml' : null)
                if (junitFile) {
                    junit testResults: junitFile,
                          allowEmptyResults: false

                    archiveArtifacts(
                        artifacts: junitFile,
                        allowEmptyArchive: false
                    )
                } else {
                    echo 'JUnit XML belum tersedia.'
                }
            }
        }

        success {
            echo 'Pipeline sukses: aplikasi berhasil di-deploy.'
        }

        failure {
            echo 'Pipeline gagal: periksa stage yang merah.'
        }
    }
}
