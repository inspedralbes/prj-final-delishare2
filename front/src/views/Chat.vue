<template>
  <div class="h-screen flex flex-col p-4 pt-16 justify-end space-y-4 items-center md:p-20 pb-10">
    <div class="w-full md:w-1/2 max-w-2xl flex flex-col bg-white shadow-lg rounded-lg p-4">
      <!-- Título del chat -->
      <h1 class="text-2xl font-bold text-center mb-4">🍽️ Delishare </h1>

      <div class="flex-1 h-[50vh] overflow-y-auto border rounded-lg p-4 bg-gray-200">
        <!-- Lista de mensajes con estilos condicionales según el rol -->
        <div v-for="(msg, index) in messages" :key="index" :class="[
          'p-2 my-2 rounded-lg max-w-[75%]',
          msg.role === 'user' ? 'bg-blue-500 text-white self-end flex flex-col' : 'bg-gray-300 text-black self-start'
        ]">
          {{ msg.content }}
        </div>
      </div>

      <!-- Área de entrada de mensajes -->
      <div class="flex gap-2 mt-4">
        <input type="text" v-model="input" @keydown.enter="handleSend" placeholder="Ask for a recipe..."
          class="flex-1 p-2 border rounded-lg" />
        <button @click="handleSend" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
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
const groq = new Groq({
  apiKey: import.meta.env.VITE_GROQ_API_KEY,
  dangerouslyAllowBrowser: true
});

export default {
  setup() {
    // Estado reactivo para el input del usuario
    const input = ref("");

    // Estado reactivo para los mensajes del chat
    const messages = ref([
      { role: "system", content: "Hello! I'm Delishare, your recipe expert. Ask me for a recipe or nutrition !" }
    ]);

    /**
     * Maneja el envío de mensajes
     * Valida que el contenido sea sobre comida y obtiene respuesta de la IA
     */
    const handleSend = async () => {
      if (!input.value.trim()) return;

      // Validar que la pregunta sea sobre comida
      const isFoodRelated = await validateFoodContent(input.value);
      if (!isFoodRelated) {
        messages.value.push({
          role: "assistant",
          content: "Ho sento, només puc respondre preguntes sobre cuina i receptes. Pots preguntar-me sobre ingredients, mètodes de cocció o receptes específiques?"
        });
        input.value = "";
        return;
      }

      // Añadir mensaje del usuario al chat
      const newMessages = [...messages.value, { role: "user", content: input.value }];
      messages.value = newMessages;
      input.value = "";

      try {
        // Obtener respuesta de la IA
        const response = await groq.chat.completions.create({
          model: "llama-3.3-70b-versatile",
          messages: newMessages,
        });

        // Añadir respuesta al chat
        if (response.choices && response.choices.length > 0) {
          messages.value.push(response.choices[0].message);
        }
      } catch (error) {
        console.error("Error fetching response:", error);
        messages.value.push({
          role: "assistant",
          content: "Hi ha hagut un error processant la teva petició. Si us plau, torna-ho a intentar."
        });
      }
    };

    return { input, messages, handleSend };
  },
};
</script>