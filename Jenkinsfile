pipeline {
    agent { label '!windows' }

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
	stage("Test"){
	sh 'vendor/bin/phpunit tests/'
	}
    }
}
