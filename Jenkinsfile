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
	        steps{
	            sh 'vendor/bin/phpunit tests/'
	        }
	    }
    }
}
