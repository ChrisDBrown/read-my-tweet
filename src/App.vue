<template>
  <div id="app">
    {{token}}
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'app',
  data () {
    return {
      token: ''
    }
  },
  mounted () {
    const tokenOptions = {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        'Ocp-Apim-Subscription-Key': process.env.TTS_KEY
      },
      data: '',
      url: 'https://westus.api.cognitive.microsoft.com/sts/v1.0/issueToken'
    }

    const testData = `<speak version='1.0' xmlns="http://www.w3.org/2001/10/synthesis" xml:lang='en-US'><voice  name='Microsoft Server Speech Text to Speech Voice (en-US, BenjaminRUS)'> Welcome to use Microsoft Cognitive Services <break time="100ms" /> Text-to-Speech API.</voice> </speak>`

    axios(tokenOptions)
      .then(response => {
        this.token = response.data

        const thing = {
          method: 'POST',
          responseType: 'arraybuffer',
          headers: {
            'Authorization': `Bearer ${response.data}`,
            'Content-Type': 'application/ssml+xml',
            'X-Microsoft-OutputFormat': 'audio-16khz-32kbitrate-mono-mp3'
          },
          data: testData,
          url: 'https://westus.tts.speech.microsoft.com/cognitiveservices/v1?language=en-US'
        }

        return axios(thing)
      })
      .then(response => {
        let audioContext = new (window.AudioContext || window.webkitAudioContext)()
        let bufferSource = audioContext.createBufferSource()

        audioContext.decodeAudioData(response.data, function (buf) {
          bufferSource.buffer = buf
          bufferSource.connect(audioContext.destination)
          bufferSource.start(0)
        })
      })
  }
}
</script>

<style lang="scss">

</style>
