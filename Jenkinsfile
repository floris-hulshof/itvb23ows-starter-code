pipeline {
    agent any

    stages {
        stage('Checkout') {
            steps {
                // Checkout your PHP project from version control (e.g., Git)
                checkout scm
            }
        }

        stage('Build and Print PHP Version') {
            steps {
                script {
                    // Define the PHP executable path (adjust as needed)
                    def phpExecutable = '/path/to/php'

                    // Run a shell command to get the PHP version
                    def phpVersion = sh(script: "${phpExecutable} -v | awk 'NR==1{print \$2}'", returnStdout: true).trim()

                    // Print the PHP version
                    echo "PHP Version: ${phpVersion}"
                }
            }
        }
    }
}