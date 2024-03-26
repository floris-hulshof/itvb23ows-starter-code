pipeline {
    agent any

    stages {
        stage('SonarQube') {
            steps {
                script {
                    def scannerHome = tool 'SonarQube'
                    withSonarQubeEnv('SonarQube') {
                        sh "${scannerHome}/bin/sonar-scanner -Dsonar.projectKey=tutorial"
                    }
                }
            }
        }
        stage('Test') {
            steps {
                dir("/app") {
                    sh 'composer install'
                    sh 'php vendor/bin/phpunit tests --configuration phpunit.xml'
                }
            }
        }
    }
}
