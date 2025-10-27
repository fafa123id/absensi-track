pipeline {
  agent any

  stages {
    stage('Checkout (scm)') {
      steps {
        script {
          // bersihin workspace via Jenkins API (aman buat FS bandel)
          deleteDir()
          // retry biar lebih tahan kalau ada file ke-lock sebentar
          retry(2) {
            echo 'Mengambil kode terbaru (checkout scm)...'
            checkout scm
          }
        }
      }
    }

    stage('Create .env from Credentials') {
      steps {
        withCredentials([file(credentialsId: 'absensi-track-env-prod', variable: 'DOTENV_FILE')]) {
            sh "cp \$DOTENV_FILE .env"
        }
      }
    }

    stage('Deploy (docker compose)') {
      steps {
        sh '''
          echo '--- stop lama ---'
          docker compose down --remove-orphans || true

          echo '--- pull (jika ada) ---'
          docker compose pull || true

          echo '--- build ---'
          docker compose build --no-cache

          echo '--- up -d ---'
          docker compose up -d

          echo '--- prune image gantung ---'
          docker image prune -f || true
        '''
      }
    }
  }

  post {
    always {
      cleanWs deleteDirs: true, notFailBuild: true
    }
    success { echo 'Pipeline berhasil!' }
    failure { echo 'Pipeline GAGAL!' }
  }
}
