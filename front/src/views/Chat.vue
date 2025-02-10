<template>
    <div class="h-screen flex flex-col p-4 pt-16 justify-end space-y-4 items-center md:p-20 pb-10">
      <div class="w-full md:w-1/2 max-w-2xl flex flex-col bg-white shadow-lg rounded-lg p-4">
        <h1 class="text-2xl font-bold text-center mb-4">🍽️ Delishare </h1>
        
        <div class="flex-1 h-[50vh] overflow-y-auto border rounded-lg p-4 bg-gray-200">
          <div 
            v-for="(msg, index) in messages" 
            :key="index"
            :class="[
              'p-2 my-2 rounded-lg max-w-[75%]',
              msg.role === 'user' ? 'bg-blue-500 text-white self-end flex flex-col' : 'bg-gray-300 text-black self-start'
            ]"
          >
            {{ msg.content }}
          </div>
        </div>
  
        <div class="flex gap-2 mt-4">
          <input
            type="text"
            v-model="input"
            @keydown.enter="handleSend"
            placeholder="Ask for a recipe..."
            class="flex-1 p-2 border rounded-lg"
          />
          <button
            @click="handleSend"
            class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600"
          >
            Send
          </button>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import { ref } from "vue";
  import Groq from "groq-sdk";
  //console.log(import.meta.env.VITE_GROQ_API_KEY);
  const groq = new Groq({ apiKey: import.meta.env.VITE_GROQ_API_KEY,
  dangerouslyAllowBrowser: true });
  
  export default {
    setup() {
      const input = ref("");
      const messages = ref([
        { role: "system", content: "Hello! I'm Delishare, your recipe expert. Ask me for a recipe or nutrition !" }
      ]);
  
      const handleSend = async () => {
        if (!input.value.trim()) return;
  
        const newMessages = [...messages.value, { role: "user", content: input.value }];
        messages.value = newMessages;
        input.value = "";
  
        try {
          const response = await groq.chat.completions.create({
            model: "llama-3.3-70b-versatile",
            messages: newMessages,
          });
  
          if (response.choices && response.choices.length > 0) {
            messages.value.push(response.choices[0].message);
          }
        } catch (error) {
          console.error("Error fetching response:", error);
        }
      };
  
      return { input, messages, handleSend };
    },
  };
  </script>