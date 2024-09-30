<?php

require 'vendor/autoload.php'; // Autoload Google Cloud and other dependencies

use Google\Cloud\Speech\V1\RecognitionConfig;
use Google\Cloud\Speech\V1\RecognitionAudio;
use Google\Cloud\Speech\V1\SpeechClient;
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;
use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;

// Replace with your OpenAI API key
$openai_api_key = 'YOUR_OPENAI_API_KEY';

// Google Cloud Speech-to-Text function
function transcribeAudio($audioFilePath) {
    $client = new SpeechClient();

    $audioData = file_get_contents($audioFilePath);
    $audio = (new RecognitionAudio())->setContent($audioData);

    $config = (new RecognitionConfig())
        ->setEncoding(RecognitionConfig\AudioEncoding::LINEAR16)
        ->setSampleRateHertz(16000) // Adjust this according to your audio file
        ->setLanguageCode('en-US'); // Set the language code

    $response = $client->recognize($config, $audio);
    $transcription = '';

    foreach ($response->getResults() as $result) {
        $transcription .= $result->getAlternatives()[0]->getTranscript();
    }

    $client->close();
    return $transcription;
}

// ChatGPT API function to get a response
function getChatGPTResponse($text) {
    global $openai_api_key;

    $url = 'https://api.openai.com/v1/chat/completions';

    $data = array(
        'model' => 'gpt-4',
        'messages' => array(
            array('role' => 'user', 'content' => $text)
        )
    );

    $options = array(
        'http' => array(
            'header'  => "Content-Type: application/json
" .
                         "Authorization: Bearer $openai_api_key
",
            'method'  => 'POST',
            'content' => json_encode($data),
        ),
    );

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    if ($result === FALSE) {
        die('Error occurred while accessing ChatGPT API.');
    }

    $response = json_decode($result, true);
    return $response['choices'][0]['message']['content'];
}

// Google Cloud Text-to-Speech function
function convertTextToSpeech($text) {
    $client = new TextToSpeechClient();

    $input = new SynthesisInput();
    $input->setText($text);

    // Define voice parameters
    $voice = new VoiceSelectionParams();
    $voice->setLanguageCode('en-US');
    $voice->setSsmlGender(VoiceSelectionParams\SsmlVoiceGender::NEUTRAL);

    // Define audio configuration
    $audioConfig = new AudioConfig();
    $audioConfig->setAudioEncoding(AudioEncoding::MP3);

    // Perform the text-to-speech request
    $response = $client->synthesizeSpeech($input, $voice, $audioConfig);

    // Save the audio to a file
    $outputFile = 'output.mp3';
    file_put_contents($outputFile, $response->getAudioContent());

    $client->close();

    return $outputFile;
}

// Handle file upload and processing
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Save the uploaded audio file
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["audio"]["name"]);
    move_uploaded_file($_FILES["audio"]["tmp_name"], $target_file);

    // Transcribe the uploaded audio file using Google Cloud Speech-to-Text API
    $transcribedText = transcribeAudio($target_file);

    // Get the response from ChatGPT API
    $chatGPTResponse = getChatGPTResponse($transcribedText);

    // Convert ChatGPT response to speech using Google Cloud Text-to-Speech API
    $audioFile = convertTextToSpeech($chatGPTResponse);

    // Return JSON response
    echo json_encode([
        'transcription' => $transcribedText,
        'chatGPTResponse' => $chatGPTResponse,
        'audioFile' => $audioFile
    ]);
    exit;
}

?>
