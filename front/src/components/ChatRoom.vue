<template>
  <div class="min-h-screen bg-lime-50 flex flex-col">
    <!-- Hero Section con fondo animado -->
    <section class="relative overflow-hidden">
      <div class="bg-gradient-to-br from-lime-100 via-lime-200 to-green-200 py-8 relative">
        <div class="absolute inset-0 bg-white/30 backdrop-blur-sm"></div>
        <!-- Círculos decorativos animados -->
        <div class="absolute inset-0 overflow-hidden">
          <div
            class="absolute -left-10 -top-10 w-40 h-40 bg-yellow-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob">
          </div>
          <div
            class="absolute -right-10 -top-10 w-40 h-40 bg-lime-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000">
          </div>
          <div
            class="absolute -bottom-10 left-20 w-40 h-40 bg-green-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-4000">
          </div>
        </div>

        <div class="max-w-7xl mx-auto px-6 relative z-10">
          <div class="text-center">
            <h1 class="text-4xl tracking-tight font-extrabold text-lime-900 sm:text-5xl md:text-6xl">
              <span
                class="block bg-gradient-to-r from-lime-900 via-lime-700 to-green-800 bg-clip-text text-transparent">
                Live Chat
              </span>
              <span class="block text-2xl mt-3 text-lime-700 font-medium">
                Comparte y aprende en tiempo real
              </span>
            </h1>
          </div>
        </div>
      </div>
    </section>

    <!-- Mensaje de live finalizado -->
    <div v-if="showEndMessage" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 shadow-2xl transform transition-all">
        <div class="text-center">
          <div class="w-16 h-16 mx-auto mb-4 text-red-500">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M9 9.563C9.309 8.935 9.787 8.4 10.38 8.016C10.973 7.632 11.657 7.414 12.36 7.386C13.064 7.358 13.76 7.52 14.38 7.856C15 8.192 15.52 8.688 15.88 9.288" />
            </svg>
          </div>
          <h2 class="text-2xl font-bold text-lime-900 mb-2">El chef ha finalizado la transmisión</h2>
          <p class="text-lime-600 mb-6">Gracias por participar en el live</p>
          <button @click="goToLiveList"
            class="bg-gradient-to-r from-green-500 via-lime-400 to-lime-300 text-lime-900 px-8 py-3 rounded-full hover:from-green-600 hover:via-lime-500 hover:to-lime-400 transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 font-medium flex items-center justify-center mx-auto">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Volver a Lives</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Contenido normal del chat -->
    <template v-else>
      <!-- Página de espera para usuarios normales -->
      <div v-if="!isLiveStarted && !isChef" class="flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl p-6 max-w-lg w-full shadow-xl">
          <div class="text-center">
            <div class="relative w-24 h-24 mx-auto mb-2">
              <div class="w-full h-full text-lime-600">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                  stroke="currentColor" class="animate-bounce">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
              </div>
              <div class="absolute inset-0 bg-lime-400 rounded-full opacity-20 animate-ping"></div>
            </div>
            <h2 class="text-2xl font-bold text-lime-900 mb-4">Esperando a que el chef inicie el live...</h2>
            <div class="bg-lime-50 rounded-xl p-4 mb-6">
              <p class="flex items-center justify-center text-lime-700 text-lg font-semibold mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Usuarios esperando: <span class="font-semibold ml-2">{{ waitingUsers.length }}</span>
              </p>
              <p class="flex items-center justify-center text-lime-700 text-lg font-semibold">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
                Chef: <span class="font-semibold ml-2">{{ chefName || 'Conectando...' }}</span>
              </p>
            </div>
            <div class="flex justify-center">
              <div class="w-3 h-3 bg-lime-400 rounded-full animate-pulse mx-1"></div>
              <div class="w-3 h-3 bg-lime-400 rounded-full animate-pulse mx-1" style="animation-delay: 0.2s"></div>
              <div class="w-3 h-3 bg-lime-400 rounded-full animate-pulse mx-1" style="animation-delay: 0.4s"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Panel de control del chef -->
      <div v-else-if="isChef && !isLiveStarted" class="min-h-screen p-4">
        <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-xl p-6">
          <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-lime-900 flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-2" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
              </svg>
              Panel del Chef
            </h2>
            <p class="text-lime-600 mt-2">Preparado para iniciar tu transmisión en vivo</p>
          </div>

          <div class="bg-lime-50 rounded-xl p-4 mb-6 text-center">
            <span class="text-lime-700 font-semibold flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              {{ waitingUsers.length }} usuario(s) esperando
            </span>
          </div>

          <div class="mb-6">
            <div class="relative aspect-video bg-gray-900 rounded-xl overflow-hidden mb-4">
              <video ref="chefPreviewVideo" autoplay muted playsinline class="w-full h-full object-cover"
                :class="{ 'opacity-50': !isCameraOn }">
              </video>
              <div v-if="!isCameraOn"
                class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 text-white">
                <p>Cámara apagada</p>
              </div>
            </div>

            <div class="flex justify-center gap-4">
              <button @click="toggleCamera" class="flex items-center px-6 py-3 rounded-full transition-all duration-300"
                :class="isCameraOn ? 'bg-lime-500 hover:bg-lime-600 text-white' : 'bg-gray-200 hover:bg-gray-300 text-gray-700'">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                <span>{{ isCameraOn ? 'Cámara ON' : 'Cámara OFF' }}</span>
              </button>
              <button @click="toggleAudio" class="flex items-center px-6 py-3 rounded-full transition-all duration-300"
                :class="isAudioOn ? 'bg-lime-500 hover:bg-lime-600 text-white' : 'bg-gray-200 hover:bg-gray-300 text-gray-700'">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                </svg>
                <span>{{ isAudioOn ? 'Audio ON' : 'Audio OFF' }}</span>
              </button>
            </div>
          </div>

          <button @click="startLiveChat" :disabled="!isConnected"
            class="w-full bg-gradient-to-r from-green-500 via-lime-400 to-lime-300 text-lime-900 px-8 py-4 rounded-full hover:from-green-600 hover:via-lime-500 hover:to-lime-400 transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 font-medium disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Iniciar Live Chat</span>
          </button>
        </div>
      </div>

      <!-- Chat activo -->
      <div v-else-if="isLiveStarted" class="min-h-screen flex flex-col">
        <!-- Video container -->
        <div v-if="showVideo || isChef" class="relative aspect-video bg-black">
          <video :ref="isChef ? 'chefLiveVideo' : 'userVideo'" autoplay :muted="isChef" playsinline
            class="w-full h-full object-cover" :class="{ 'opacity-50': !isStreamActive }">
          </video>

          <div v-if="!isStreamActive && !isChef"
            class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 text-white">
            <p>El chef ha apagado la cámara</p>
          </div>

          <!-- Controles del chef -->
          <div v-if="isChef"
            class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex gap-2 bg-black bg-opacity-50 rounded-full p-2">
            <button @click="toggleCamera"
              class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300"
              :class="isCameraOn ? 'bg-lime-500 hover:bg-lime-600' : 'bg-gray-600 hover:bg-gray-700'">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
              </svg>
            </button>
            <button @click="toggleAudio"
              class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300"
              :class="isAudioOn ? 'bg-lime-500 hover:bg-lime-600' : 'bg-gray-600 hover:bg-gray-700'">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
              </svg>
            </button>
          </div>

          <!-- Controles del viewer -->
          <div v-if="!isChef && isStreamActive" class="absolute bottom-4 right-4">
            <button @click="toggleMute"
              class="w-10 h-10 rounded-full bg-black bg-opacity-50 flex items-center justify-center transition-all duration-300 hover:bg-opacity-70">
              <svg v-if="isMuted" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2" />
              </svg>
              <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
              </svg>
            </button>
          </div>

          <!-- Contador de viewers -->
          <div v-if="isChef"
            class="absolute top-4 right-4 bg-black bg-opacity-50 text-white px-4 py-2 rounded-full flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            {{ activeUsers.length }} espectador(es)
          </div>
        </div>

        <!-- Botones de salida -->
        <div class="p-4 flex justify-end">
          <button v-if="!isChef" @click="leaveLive"
            class="bg-gradient-to-r from-yellow-400 to-yellow-300 text-yellow-900 px-6 py-3 rounded-full transition-all duration-300 hover:shadow-lg hover:brightness-110 hover:scale-105 flex items-center font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            <span>Salir del Live</span>
          </button>
          <button v-if="isChef" @click="endLiveForAll"
            class="bg-gradient-to-r from-red-500 to-red-400 text-white px-6 py-3 rounded-full transition-all duration-300 hover:shadow-lg hover:brightness-110 hover:scale-105 flex items-center font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 9.563C9.309 8.935 9.787 8.4 10.38 8.016C10.973 7.632 11.657 7.414 12.36 7.386C13.064 7.358 13.76 7.52 14.38 7.856C15 8.192 15.52 8.688 15.88 9.288" />
            </svg>
            <span>Finalizar Live</span>
          </button>
        </div>

        <!-- Chat container -->
        <div class="flex-1 flex flex-col bg-white">
          <!-- Chat header -->
          <div class="bg-gradient-to-r from-lime-900 via-lime-700 to-green-800 text-white p-4">
            <h3 class="text-xl font-semibold flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
              </svg>
              Chat en vivo
            </h3>
            <div class="text-sm text-lime-100 mt-1 flex items-center flex-wrap">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-lime-300" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              <span v-for="(user, index) in activeUsers" :key="index">
                {{ user }}{{ index < activeUsers.length - 1 ? ', ' : '' }} </span>
                  <span v-if="activeUsers.length === 0">No hay otros usuarios conectados</span>
            </div>
          </div>

          <!-- Messages container -->
          <div class="flex-1 overflow-y-auto p-4 bg-lime-50" ref="messagesContainer">
            <div v-for="(msg, index) in messages" :key="index" :class="[
              'mb-4 max-w-[80%] rounded-2xl p-3',
              msg.username === username ? 'ml-auto bg-gradient-to-r from-lime-500 to-lime-400 text-white' :
                msg.isSystem ? 'mx-auto bg-lime-100 text-lime-700 text-center' :
                  msg.isChef ? 'bg-white border-l-4 border-lime-500' : 'bg-white'
            ]">
              <div class="flex items-center justify-between mb-1 text-sm">
                <span v-if="msg.isChef && !msg.isSystem" class="mr-2">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                  </svg>
                </span>
                <strong v-if="msg.username !== username && !msg.isSystem">{{ msg.username }}</strong>
                <span class="opacity-75">{{ formatTime(msg.timestamp) }}</span>
              </div>
              <div class="break-words">{{ msg.message }}</div>
            </div>
          </div>

          <!-- Chat input -->
          <div class="p-4 pb-24 md:pb-8 bg-white border-t">
            <form @submit.prevent="sendMessage" class="flex gap-2">
              <input v-model="newMessage" @keyup.enter="sendMessage" placeholder="Escribe tu mensaje..."
                :disabled="!canSendMessages"
                class="flex-1 px-4 py-2 rounded-full border border-lime-300 focus:outline-none focus:border-lime-500 focus:ring-1 focus:ring-lime-500 disabled:bg-lime-50 disabled:cursor-not-allowed">
              <button type="submit" :disabled="!canSendMessages || !newMessage.trim()"
                class="bg-gradient-to-r from-green-500 via-lime-400 to-lime-300 text-lime-900 px-6 py-2 rounded-full transition-all duration-300 hover:shadow-lg hover:brightness-110 hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed flex items-center font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
                <span>Enviar</span>
              </button>
            </form>
          </div>
        </div>
      </div>
    </template>

    <!-- Estado de conexión -->
    <div v-if="!isConnected" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
      <div class="bg-white rounded-2xl p-6 flex items-center">
        <div class="w-6 h-6 border-4 border-lime-400 border-t-transparent rounded-full animate-spin mr-4"></div>
        <span class="text-lime-700">Conectando al chat...</span>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, onUnmounted, nextTick, computed, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { io } from 'socket.io-client';
import { useAuthStore } from '@/stores/authStore';
import communicationManager from '@/services/communicationManager';

export default {
  setup() {
    const route = useRoute();
    const router = useRouter();
    const authStore = useAuthStore();
    const liveId = ref(route.params.liveId);
    const socket = ref(null);
    const username = ref('Usuario');
    const activeUsers = ref([]);
    const waitingUsers = ref([]);
    const messages = ref([]);
    const newMessage = ref('');
    const isConnected = ref(false);
    const isLiveStarted = ref(false);
    const isChef = ref(false);
    const chefName = ref('');
    const messagesContainer = ref(null);
    const loadingUser = ref(true);
    const userError = ref(null);
    const chefPreviewVideo = ref(null);
    const chefLiveVideo = ref(null);
    const userVideo = ref(null);
    const isCameraOn = ref(false);
    const isStreamActive = ref(false);
    const showVideo = ref(true);
    const peerConnections = ref({});
    const localStream = ref(null);
    const videoInitialized = ref(false);
    const isMuted = ref(false);
    const isAudioOn = ref(false);
    const showEndMessage = ref(false);

    const configuration = {
      iceServers: [
        { urls: 'stun:stun.l.google.com:19302' },
        { urls: 'stun:stun1.l.google.com:19302' },
        { urls: 'stun:stun2.l.google.com:19302' },
        { urls: 'stun:stun3.l.google.com:19302' },
        { urls: 'stun:stun4.l.google.com:19302' }
      ]
    };

    const toggleMute = () => {
      isMuted.value = !isMuted.value;
      if (userVideo.value) {
        userVideo.value.muted = isMuted.value;
        console.log(`Usuario ${isMuted.value ? 'muteó' : 'desmuteó'} el audio`);
      }
    };


    /// Alterna el estado del audio del chef
    const toggleAudio = () => {
      if (localStream.value) {
        const audioTracks = localStream.value.getAudioTracks();
        if (audioTracks.length > 0) {
          isAudioOn.value = !audioTracks[0].enabled;
          audioTracks[0].enabled = !audioTracks[0].enabled;

          if (isLiveStarted.value && socket.value) {
            socket.value.emit('chefAudioStatus', {
              liveId: liveId.value,
              status: audioTracks[0].enabled
            });
          }
        }
      }
    };

    const goToLiveList = () => {
      router.push('/live');
    };

    const endLiveForAll = async () => {
      if (!isChef.value || !socket.value) return;

      const confirmEnd = confirm('¿Estás seguro de que deseas finalizar la transmisión para todos?');
      if (!confirmEnd) return;

      try {
        // Primero limpiamos los recursos locales
        if (localStream.value) {
          localStream.value.getTracks().forEach(track => track.stop());
          localStream.value = null;
        }

        closeAllPeerConnections();

        // Actualizamos el estado local
        isLiveStarted.value = false;
        isCameraOn.value = false;
        isAudioOn.value = false;
        isStreamActive.value = false;

        // Emitimos el evento al servidor
        socket.value.emit('chefEndLive', { liveId: liveId.value }, (response) => {
          if (response?.success) {
            console.log('Live finalizado correctamente');
            showEndMessage.value = true;

            // Desconectamos el socket y redirigimos
            socket.value.disconnect();
            isConnected.value = false;
            router.push('/live');
          } else {
            console.error('Error al finalizar live:', response?.message);
            alert('No se pudo finalizar el live. Inténtalo de nuevo.');
          }
        });
      } catch (error) {
        console.error('Error al finalizar live:', error);
        alert('Error al finalizar el live. Inténtalo de nuevo.');
      }
    };
    const leaveLive = () => {
      if (!confirm('¿Estás seguro de que quieres salir del live?')) {
        return;
      }

      if (!socket.value) return;

      // Emitir evento para notificar la salida
      socket.value.emit('leaveRoom', {
        liveId: liveId.value,
        username: username.value
      });

      // Limpiar recursos
      if (userVideo.value && userVideo.value.srcObject) {
        userVideo.value.srcObject.getTracks().forEach(track => track.stop());
        userVideo.value.srcObject = null;
      }

      // Cerrar conexiones peer
      closeAllPeerConnections();

      // Desconectar del socket
      socket.value.disconnect();
      isConnected.value = false;
      isLiveStarted.value = false;

      // Redirigir a /live
      router.push('/live');
    };
    /// Alterna el estado de la cámara del chef
    const toggleCamera = async () => {
      try {
        if (isCameraOn.value) {
          // Apagar cámara y audio
          if (localStream.value) {
            localStream.value.getTracks().forEach(track => track.stop());
            localStream.value = null;
          }
          isCameraOn.value = false;
          isAudioOn.value = false;
          isStreamActive.value = false;

          if (isLiveStarted.value && socket.value) {
            socket.value.emit('chefCameraStatus', {
              liveId: liveId.value,
              status: false
            });
            socket.value.emit('chefAudioStatus', {
              liveId: liveId.value,
              status: false
            });
          }
        } else {
          // Encender cámara y audio
          const constraints = {
            video: {
              width: { ideal: 1280 },
              height: { ideal: 720 },
              facingMode: 'user'
            },
            audio: {
              echoCancellation: true,
              noiseSuppression: true,
              autoGainControl: true
            }
          };

          const stream = await navigator.mediaDevices.getUserMedia(constraints);
          localStream.value = stream;
          isAudioOn.value = true;

          // Configurar el audio para que no esté muteado (solo para el chef)
          if (isChef.value && chefPreviewVideo.value) {
            chefPreviewVideo.value.muted = true;
            chefPreviewVideo.value.srcObject = stream;
          } else if (isChef.value && chefLiveVideo.value) {
            chefLiveVideo.value.muted = true;
            chefLiveVideo.value.srcObject = stream;
          }

          isCameraOn.value = true;
          isStreamActive.value = true;

          if (isLiveStarted.value && socket.value) {
            socket.value.emit('chefCameraStatus', {
              liveId: liveId.value,
              status: true
            });
            initializeConnectionsWithViewers();
          }
        }
      } catch (error) {
        console.error('Error al cambiar estado de la cámara:', error);
        alert('Error al acceder a la cámara/micrófono: ' + error.message);
      }
    };

    /// Cierra todas las conexiones peer-to-peer
    const closeAllPeerConnections = () => {
      Object.keys(peerConnections.value).forEach(socketId => {
        if (peerConnections.value[socketId]) {
          peerConnections.value[socketId].close();
          delete peerConnections.value[socketId];
        }
      });
    };

    /// Inicializa conexiones WebRTC con los espectadores existentes
    const initializeConnectionsWithViewers = () => {
      if (!isChef.value || !localStream.value || !socket.value) return;

      socket.value.emit('requestActiveUsers', { liveId: liveId.value }, (response) => {
        if (response?.success && response.activeUsers) {
          response.activeUsers.forEach(user => {
            if (user.socketId && user.socketId !== socket.value.id) {
              console.log(`Iniciando conexión con: ${user.username} (${user.socketId})`);
              startCall(user.socketId);
            }
          });
        }
      });
    };

    /// Crea una nueva conexión peer-to-peer
    const createPeerConnection = (socketId) => {
      if (peerConnections.value[socketId]) {
        peerConnections.value[socketId].close();
      }

      const pc = new RTCPeerConnection(configuration);
      peerConnections.value[socketId] = pc;

      pc.onicecandidate = (event) => {
        if (event.candidate) {
          socket.value.emit('iceCandidate', {
            liveId: liveId.value,
            target: socketId,
            candidate: event.candidate
          });
        }
      };

      // Solo el chef añade sus tracks
      if (isChef.value && localStream.value) {
        localStream.value.getTracks().forEach(track => {
          pc.addTrack(track, localStream.value);
        });
      }

      // Configuración para los usuarios que reciben el stream
      if (!isChef.value) {
        pc.ontrack = (event) => {
          console.log('Recibiendo tracks:', event.streams);
          if (userVideo.value && event.streams && event.streams[0]) {
            userVideo.value.srcObject = event.streams[0];
            userVideo.value.muted = false;

            const playPromise = userVideo.value.play();
            if (playPromise !== undefined) {
              playPromise
                .then(() => {
                  console.log('Reproducción iniciada');
                })
                .catch(error => {
                  console.warn('Autoplay bloqueado, solicitando interacción del usuario');
                });
            }
          }
        };
      }

      return pc;
    };

    /// Inicia una llamada WebRTC con un espectador específico
    const startCall = async (socketId) => {
      if (!isChef.value || !localStream.value || !socket.value) {
        console.log('No se puede iniciar llamada: condiciones no cumplidas');
        return;
      }

      console.log(`Chef iniciando llamada con: ${socketId}`);
      const pc = createPeerConnection(socketId);

      const senders = pc.getSenders();
      if (senders.length === 0 && localStream.value) {
        console.log('No hay senders, agregando tracks manualmente');
        localStream.value.getTracks().forEach(track => {
          pc.addTrack(track, localStream.value);
        });
      }

      try {
        const offer = await pc.createOffer({
          offerToReceiveAudio: true,
          offerToReceiveVideo: true
        });

        await pc.setLocalDescription(offer);
        console.log('Oferta creada y enviada:', offer.type);

        socket.value.emit('offer', {
          liveId: liveId.value,
          target: socketId,
          offer: pc.localDescription
        });
      } catch (error) {
        console.error('Error al crear oferta:', error);
      }
    };

    /// Maneja una oferta WebRTC entrante
    const handleOffer = async (data) => {
      if (!socket.value) return;

      console.log(`Recibida oferta de: ${data.from}`);
      const pc = createPeerConnection(data.from);

      try {
        await pc.setRemoteDescription(new RTCSessionDescription(data.offer));
        console.log('Descripción remota configurada');

        const answer = await pc.createAnswer();
        await pc.setLocalDescription(answer);
        console.log('Respuesta creada y enviada:', answer.type);

        socket.value.emit('answer', {
          liveId: liveId.value,
          target: data.from,
          answer: pc.localDescription
        });
      } catch (error) {
        console.error('Error al manejar oferta:', error);
      }
    };

    /// Maneja una respuesta WebRTC entrante
    const handleAnswer = async (data) => {
      const pc = peerConnections.value[data.from];
      if (!pc) {
        console.log(`No hay conexión para respuesta de ${data.from}`);
        return;
      }

      try {
        console.log(`Recibida respuesta de: ${data.from}`);
        await pc.setRemoteDescription(new RTCSessionDescription(data.answer));
        console.log('Descripción remota configurada con respuesta');
      } catch (error) {
        console.error('Error al manejar respuesta:', error);
      }
    };

    /// Maneja candidatos ICE para conexiones WebRTC
    const handleIceCandidate = async (data) => {
      const pc = peerConnections.value[data.from];
      if (!pc || !data.candidate) {
        return;
      }

      try {
        console.log(`Recibido candidato ICE de: ${data.from}`);
        await pc.addIceCandidate(new RTCIceCandidate(data.candidate));
      } catch (error) {
        console.error('Error al agregar ICE candidate:', error);
      }
    };

    /// Obtiene los datos del usuario desde el backend
    const fetchUserData = async () => {
      try {
        loadingUser.value = true;
        userError.value = null;

        const token = localStorage.getItem('token');
        if (!token) {
          throw new Error('No estás autenticado');
        }

        const userData = await communicationManager.getUser();

        if (!userData || !userData.id) {
          throw new Error('Datos de usuario incompletos');
        }

        username.value = userData.name || 'Usuario';
        isChef.value = userData.role === 'chef';

        console.log(`Usuario identificado: ${username.value}, Role: ${userData.role}`);
      } catch (error) {
        console.error('Error al obtener datos del usuario:', error);
        userError.value = error.message || 'Error al cargar datos de usuario';
      } finally {
        loadingUser.value = false;
      }
    };

    /// Inicia la transmisión en vivo (solo para chef)
    const startLiveChat = () => {
      if (!isChef.value || !isConnected.value || !socket.value) return;

      console.log('Iniciando live chat...');

      const hasVideo = isCameraOn.value && localStream.value !== null;

      if (hasVideo && !isLiveStarted.value) {
        nextTick(() => {
          if (chefLiveVideo.value && localStream.value) {
            chefLiveVideo.value.srcObject = localStream.value;
          }
        });
      }

      socket.value.emit('chefStartLive', {
        liveId: liveId.value,
        hasVideo: hasVideo
      }, (response) => {
        if (response?.success) {
          isLiveStarted.value = true;
          showVideo.value = hasVideo;
          isStreamActive.value = hasVideo;

          console.log('Live iniciado con éxito!');

          if (hasVideo) {
            nextTick(() => {
              initializeConnectionsWithViewers();
            });
          }
        } else {
          console.error('Error al iniciar el live:', response?.message);
        }
      });
    };

    // Watcher para cambios en el estado de isLiveStarted
    watch(isLiveStarted, (newValue) => {
      if (newValue && isChef.value && isCameraOn.value && localStream.value) {
        nextTick(() => {
          if (chefLiveVideo.value) {
            console.log('Transfiriendo stream al video en vivo');
            chefLiveVideo.value.srcObject = localStream.value;
          }
        });
      }
    });

    /// Envía un mensaje de chat
    const sendMessage = () => {
      if (!newMessage.value.trim() || !isConnected.value || !socket.value) return;

      const messageData = {
        liveId: liveId.value,
        username: username.value,
        message: newMessage.value.trim(),
        isChef: isChef.value
      };

      socket.value.emit('sendChatMessage', messageData);

      messages.value.push({
        username: username.value,
        message: newMessage.value.trim(),
        timestamp: new Date(),
        isChef: isChef.value
      });

      newMessage.value = '';
      scrollToBottom();
    };

    /// Formatea la hora para mostrar en los mensajes
    const formatTime = (timestamp) => {
      if (!timestamp) return '';
      const date = timestamp instanceof Date ? timestamp : new Date(timestamp);
      return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    };

    /// Desplaza el contenedor de mensajes al final
    const scrollToBottom = () => {
      nextTick(() => {
        if (messagesContainer.value) {
          messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
        }
      });
    };

    /// Solicita el stream de video del chef
    const requestChefVideo = () => {
      if (!isChef.value && socket.value && isLiveStarted.value && showVideo.value && !videoInitialized.value) {
        console.log('Usuario solicitando video del chef...');
        socket.value.emit('requestVideoStream', { liveId: liveId.value });
      }
    };

    /// Inicializa la conexión del chat y configura los listeners de socket
    const initializeChat = async () => {
      await fetchUserData();

      if (!userError.value) {
        console.log(`Inicializando chat como ${isChef.value ? 'CHEF' : 'USUARIO'} para sala ${liveId.value}`);

        socket.value = io('http://localhost:4000', {
          path: '/socket.io',
          transports: ['websocket'],
          upgrade: false,
          query: {
            liveId: liveId.value,
            username: username.value,
            isChef: isChef.value,
            userId: authStore.user?.id || null
          }
        });

        socket.value.on('connect', () => {
          console.log('Conectado al servidor:', socket.value.id);
          isConnected.value = true;

          socket.value.emit('joinLiveRoom', {
            liveId: liveId.value,
            username: username.value,
            isChef: isChef.value
          }, (response) => {
            if (response?.success) {
              console.log(`Unido al room correctamente como ${isChef.value ? 'CHEF' : 'USUARIO'}`);

              if (response.isLiveActive) {
                isLiveStarted.value = true;
                showVideo.value = response.hasVideo || false;
                isStreamActive.value = response.hasVideo || false;

                if (response.hasVideo && !isChef.value) {
                  setTimeout(() => {
                    requestChefVideo();
                  }, 1000);
                }
              }
            }
          });
        });

        socket.value.on('liveStarted', (data) => {
          console.log('Live iniciado:', data);
          isLiveStarted.value = true;
          activeUsers.value = data.activeUsers || [];
          showVideo.value = data.hasVideo || false;
          isStreamActive.value = data.hasVideo || false;

          messages.value.push({
            username: 'Sistema',
            message: '¡El chat en vivo ha comenzado!',
            timestamp: new Date(),
            isSystem: true
          });

          if (!isChef.value) {
            if (data.hasVideo) {
              console.log('Solicitando stream de video al iniciar live');
              socket.value.emit('requestVideoStream', { liveId: liveId.value });
            }

            const helloMessage = {
              liveId: liveId.value,
              username: username.value,
              message: 'Hola!',
              isChef: isChef.value
            };
            socket.value.emit('sendChatMessage', helloMessage);

            messages.value.push({
              username: username.value,
              message: 'Hola!',
              timestamp: new Date(),
              isChef: isChef.value
            });
          }

          scrollToBottom();
        });

        socket.value.on('waitingRoomUpdate', (data) => {
          console.log('Actualización sala de espera:', data);
          waitingUsers.value = data.waitingUsers || [];
          activeUsers.value = data.activeUsers || [];
          chefName.value = data.chefName || 'Esperando chef...';

          if (data.hasVideo !== undefined) {
            showVideo.value = data.hasVideo;
          }
        });

        socket.value.on('offer', handleOffer);
        socket.value.on('answer', handleAnswer);
        socket.value.on('iceCandidate', handleIceCandidate);

        socket.value.on('chefCameraStatus', (data) => {
          console.log('Estado de cámara del chef actualizado:', data.status);
          showVideo.value = data.status;
          isStreamActive.value = data.status;

          messages.value.push({
            username: 'Sistema',
            message: data.status ? 'El chef ha encendido la cámara' : 'El chef ha apagado la cámara',
            timestamp: new Date(),
            isSystem: true
          });

          if (data.status && !isChef.value) {
            requestChefVideo();
          } else if (!data.status && !isChef.value && userVideo.value) {
            userVideo.value.srcObject = null;
            videoInitialized.value = false;
          }

          scrollToBottom();
        });

        socket.value.on('newViewer', (socketId) => {
          console.log('Nuevo espectador conectado:', socketId);
          if (isChef.value && isLiveStarted.value && isCameraOn.value && localStream.value) {
            console.log(`Chef iniciando llamada con usuario ${socketId}`);
            setTimeout(() => {
              startCall(socketId);
            }, 500);
          }
        });

        socket.value.on('chefDisconnected', () => {
          if (!isChef.value) {
            messages.value.push({
              username: 'Sistema',
              message: 'El chef se ha desconectado.',
              timestamp: new Date(),
              isSystem: true
            });

            if (userVideo.value) {
              userVideo.value.srcObject = null;
              videoInitialized.value = false;
            }
            isStreamActive.value = false;
            scrollToBottom();
          }
        });

        socket.value.on('newChatMessage', (msg) => {
          if (msg.username !== username.value) {
            messages.value.push(msg);
            scrollToBottom();
          }
        });

        socket.value.on('userJoined', (data) => {
          messages.value.push({
            username: 'Sistema',
            message: `${data.username} se ha unido al chat`,
            timestamp: new Date(),
            isSystem: true
          });
          activeUsers.value = data.users || [];

          if (isChef.value && isCameraOn.value && localStream.value && data.socketId) {
            startCall(data.socketId);
          }

          scrollToBottom();
        });

        socket.value.on('userLeft', (data) => {
          messages.value.push({
            username: 'Sistema',
            message: `${data.username} ha abandonado el chat`,
            timestamp: new Date(),
            isSystem: true
          });
          activeUsers.value = data.users || [];

          if (isChef.value && data.socketId && peerConnections.value[data.socketId]) {
            peerConnections.value[data.socketId].close();
            delete peerConnections.value[data.socketId];
          }

          scrollToBottom();
        });

        socket.value.on('error', (error) => {
          console.error('Error del socket:', error);
          messages.value.push({
            username: 'Sistema',
            message: `Error: ${error.message}`,
            timestamp: new Date(),
            isSystem: true
          });
        });

        socket.value.on('disconnect', () => {
          isConnected.value = false;
          console.log('Desconectado del servidor');
        });

        // Agregar el listener para el evento liveEnded
        socket.value.on('liveEnded', handleLiveEnded);
      }
    };

    // Hook de ciclo de vida: se ejecuta cuando el componente se monta
    onMounted(() => {
      initializeChat();
    });

    // Hook de ciclo de vida: se ejecuta cuando el componente se desmonta
    onUnmounted(() => {
      if (socket.value) {
        socket.value.emit('leaveRoom', {
          liveId: liveId.value,
          username: username.value
        });
        socket.value.disconnect();
      }

      closeAllPeerConnections();

      if (localStream.value) {
        localStream.value.getTracks().forEach(track => track.stop());
      }
    });

    // Computed property para verificar si se pueden enviar mensajes
    const canSendMessages = computed(() => {
      return isConnected.value && (isLiveStarted.value || isChef.value);
    });

    const handleLiveEnded = (data) => {
      console.log('Live finalizado:', data);

      // Limpiar recursos
      if (userVideo.value) {
        userVideo.value.srcObject = null;
      }
      closeAllPeerConnections();

      // Actualizar estado
      isLiveStarted.value = false;
      isStreamActive.value = false;
      showEndMessage.value = true;

      // Agregar mensaje al chat
      messages.value.push({
        username: 'Sistema',
        message: data.message || 'El chef ha finalizado la transmisión en vivo.',
        timestamp: new Date(),
        isSystem: true
      });
    };

    return {
      username, activeUsers, waitingUsers, messages, newMessage,
      isConnected, isLiveStarted, isChef, chefName, messagesContainer,
      canSendMessages, startLiveChat, sendMessage, formatTime,
      loadingUser, userError, chefPreviewVideo, chefLiveVideo,
      userVideo, isCameraOn, isAudioOn,
      toggleAudio, isStreamActive, isMuted,
      toggleMute, showVideo, toggleCamera,
      requestChefVideo, leaveLive, endLiveForAll,
      showEndMessage, goToLiveList
    };
  }
};
</script>

<style>
@keyframes blob {
  0% {
    transform: translate(0px, 0px) scale(1);
  }

  33% {
    transform: translate(30px, -50px) scale(1.1);
  }

  66% {
    transform: translate(-20px, 20px) scale(0.9);
  }

  100% {
    transform: translate(0px, 0px) scale(1);
  }
}

.animate-blob {
  animation: blob 7s infinite;
}

.animation-delay-2000 {
  animation-delay: 2s;
}

.animation-delay-4000 {
  animation-delay: 4s;
}

@keyframes pulse {

  0%,
  100% {
    opacity: 1;
    transform: scale(1);
  }

  50% {
    opacity: 0.5;
    transform: scale(1.1);
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes bounce {

  0%,
  100% {
    transform: translateY(0);
  }

  50% {
    transform: translateY(-25%);
  }
}

.animate-bounce {
  animation: bounce 1s infinite;
}

@keyframes ping {

  75%,
  100% {
    transform: scale(2);
    opacity: 0;
  }
}

.animate-ping {
  animation: ping 1s cubic-bezier(0, 0, 0.2, 1) infinite;
}
</style>