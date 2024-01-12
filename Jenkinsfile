pipeline {
    agent { label '!windows' }
    
    stages {
        stage('SonarQube') {
            steps {
                script {
                    def scannerHome = tool 'SonarQube Scanner'
                    withSonarQubeEnv('SonarQube') {
                        sh """
                            ${scannerHome}/bin/sonar-scanner \
                            -Dsonar.projectKey=tutorial
                        """
                    }
                }
            }
        }
    }
}