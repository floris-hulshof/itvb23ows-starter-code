pipeline {
agent { label '!windows' }
stage('SonarQube') {
  steps {
    script { scannerHome = tool 'SonarQube' }
    withSonarQubeEnv('SonarQube') {
      sh "${scannerHome}/bin/sonar-scanner
          -Dsonar.projectKey=tutorial"
    }
  }
}

}
