import React, { useState } from 'react';
import { View, Button, Text, ActivityIndicator } from 'react-native';
import * as FileSystem from 'expo-file-system';
import * as Permissions from 'expo-permissions';
import * as Audio from 'expo-av';
import axios from 'axios';

const SERVER_URL = 'https://your-server-url.com'; // Your backend server where PHP script is hosted

const App = () => {
  const [recording, setRecording] = useState(null);
  const [transcription, setTranscription] = useState('');
  const [chatGPTResponse, setChatGPTResponse] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [audioUri, setAudioUri] = useState(null);

  // Function to request recording permission
  const requestPermissions = async () => {
    const response = await Permissions.askAsync(Permissions.AUDIO_RECORDING);
    if (response.status !== 'granted') {
      alert('You need to enable permission for recording audio');
    }
  };

  // Function to start recording audio
  const startRecording = async () => {
    try {
      await requestPermissions();
      await Audio.Recording.createAsync(
        Audio.RECORDING_OPTIONS_PRESET_HIGH_QUALITY
      ).then(({ recording }) => setRecording(recording));
    } catch (error) {
      console.log('Error starting recording', error);
    }
  };

  // Function to stop recording and process the audio
  const stopRecording = async () => {
    try {
      await recording.stopAndUnloadAsync();
      const uri = recording.getURI();
      setRecording(null);
      uploadAudio(uri); // Upload audio to server
    } catch (error) {
      console.log('Error stopping recording', error);
    }
  };

  // Function to upload audio file and get ChatGPT response
  const uploadAudio = async (uri) => {
    setIsLoading(true);
    const fileInfo = await FileSystem.getInfoAsync(uri);

    // Prepare form data for audio file upload
    const formData = new FormData();
    formData.append('audio', {
      uri,
      name: 'audio_recording.wav',
      type: 'audio/wav',
    });

    try {
      const response = await axios.post(SERVER_URL, formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });

      const { transcription, chatGPTResponse, audioFile } = response.data;
      setTranscription(transcription);
      setChatGPTResponse(chatGPTResponse);
      setAudioUri(`${SERVER_URL}/${audioFile}`);
    } catch (error) {
      console.log('Error uploading audio', error);
    } finally {
      setIsLoading(false);
    }
  };

  // Function to play audio response
  const playAudioResponse = async () => {
    const { sound } = await Audio.Sound.createAsync({ uri: audioUri });
    await sound.playAsync();
  };

  return (
    <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>
      <Button
        title={recording ? 'Stop Recording' : 'Start Recording'}
        onPress={recording ? stopRecording : startRecording}
      />

      {isLoading && <ActivityIndicator size="large" color="#0000ff" />}

      <View style={{ marginTop: 20 }}>
        <Text>Transcription: {transcription}</Text>
        <Text>ChatGPT Response: {chatGPTResponse}</Text>
      </View>

      {audioUri && (
        <View style={{ marginTop: 20 }}>
          <Button title="Play Response Audio" onPress={playAudioResponse} />
        </View>
      )}
    </View>
  );
};

export default App;
