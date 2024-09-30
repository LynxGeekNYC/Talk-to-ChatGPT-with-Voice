# PHP and NodeJS Script Integration with ChatGPT and Google Cloud Speech & Text-to-Speech APIs

# Requirements and Setup

# Prerequisites

1. **Google Cloud Account**: 
   - Enable the **Speech-to-Text API** and **Text-to-Speech API** in the Google Cloud Console.
   - Create a **service account key** in JSON format and download it.

2. **OpenAI API Key**:
   - Sign up for OpenAI and get your **OpenAI API Key** for accessing the ChatGPT API.

3. **PHP Composer**:
   - Install **Composer** to manage dependencies:
     ```
     curl -sS https://getcomposer.org/installer | php
     sudo mv composer.phar /usr/local/bin/composer
     ```

# Setting Up

#### 1. Install Google Cloud Libraries
To interact with Google Cloud APIs (Speech-to-Text and Text-to-Speech), install the required libraries:

   ```
   composer require google/cloud-speech
   composer require google/cloud-text-to-speech
   ```

#### 2. Set Google Cloud Credentials

After downloading your service account key from the Google Cloud Console, set up the credentials using the following command:
   ```
   export GOOGLE_APPLICATION_CREDENTIALS="/path/to/your-service-account-file.json"
   ```

#### 3. PHP Setup for OpenAI

   - Make sure to have the **OpenAI API Key** handy.
   - The script uses PHP `cURL` to communicate with the OpenAI API.

### Full Setup Process

1. Install Composer and the necessary Google Cloud libraries for Speech and Text-to-Speech.
   ```
   composer require google/cloud-speech
   composer require google/cloud-text-to-speech
   ```

2. Set the Google Cloud credentials environment variable:
   ```
   export GOOGLE_APPLICATION_CREDENTIALS="/path/to/your-service-account-file.json"
   ```

3. Download or copy the PHP script and place it in a directory on your server or local machine.

4. Modify the script to include your **OpenAI API key** and ensure the path to your Google Cloud JSON file is set.

5. Create the `uploads/` directory where uploaded audio files will be saved.

6. Run the script in your local server or hosting environment.

7. Access the form via a browser, upload an audio file, and test the interaction.

# Usage

1. **Voice Input**:
   - Use the form to upload an audio file (`.wav`, `.mp3`, etc.).
   - The script will transcribe the audio using Google Cloud Speech-to-Text.

2. **ChatGPT Response**:
   - The transcribed text will be sent to ChatGPT via the OpenAI API.
   - The response from ChatGPT will be displayed as text.

3. **Text-to-Speech Output**:
   - The ChatGPT response will be converted back to speech using Google Cloud Text-to-Speech.
   - An audio player will allow you to listen to the ChatGPT response, and an option to download the audio will be provided.

# Troubleshooting

- Ensure that your **Google Cloud Credentials** are set correctly.
- Make sure the audio file is of a supported format and matches the required **sample rate** and **encoding**.
- Double-check your **OpenAI API Key** is valid and has sufficient quota.
- Review file permissions on the `uploads/` folder to ensure the script can save files there.
