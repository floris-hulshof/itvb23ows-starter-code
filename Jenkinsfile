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
        stage('Install Dependencies') {
            steps {
                // Change to the /app directory
                dir('/app') {
                    // Install dependencies using Composer
                    sh 'composer install'
                }
            }
        }
	    stage("Test"){
	        steps{
	            sh "ls app"
	            sh 'app/vendor/bin/phpunit app/tests'
	        }
	    }
    }
}
