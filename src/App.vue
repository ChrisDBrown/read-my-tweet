<template>
  <div id="app">
    <input v-model="searchterm" placeholder="twitter search term">
    <button v-on:click="readTweet">Search</button>
  </div>
</template>

<script>
import axios from 'axios'

function readPhrase(phrase) {
  const tokenOptions = {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
      'Ocp-Apim-Subscription-Key': process.env.VUE_APP_TTS_KEY
    },
    data: '',
    url: 'https://westus.api.cognitive.microsoft.com/sts/v1.0/issueToken'
  }

  const testData = `<speak version='1.0' xmlns="http://www.w3.org/2001/10/synthesis" xml:lang='en-US'><voice  name='Microsoft Server Speech Text to Speech Voice (en-US, BenjaminRUS)'>${phrase}</voice> </speak>`

  axios(tokenOptions)
    .then(response => {
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

function getTweets(searchterm) {
  const tokenOptions = {
    method: 'GET',
    headers: {
      'Authorization': `Bearer tokengoeshere`
    },
    url: `https://api.twitter.com/1.1/search/tweets.json?q=${searchterm}`
  }

  axios(tokenOptions)
    .then(response => {
      console.log(response.data.statuses[0].text);
      readPhrase(response.data.statuses[0].text);
    })
}

export default {
  name: 'app',
  data () {
    return {
      searchterm: '',
      readingTweet: ''
    }
  },
  methods: {
    readTweet: function() {
      getTweets(this.searchterm);
    }
  }
}
</script>

<style lang="scss">

</style>
