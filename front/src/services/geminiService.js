import { GoogleGenerativeAI } from "@google/generative-ai";

const genAI = new GoogleGenerativeAI(import.meta.env.VITE_GEMINI_API_KEY);
const model = genAI.getGenerativeModel({ model: "gemini-1.5-flash" });

export async function validateFoodImage(imageFile) {
    try {
        // Convertir el archivo a base64
        const imageBase64 = await fileToBase64(imageFile);

        const imagePart = {
            inlineData: {
                data: imageBase64.split(',')[1], // Eliminar el prefijo data:image/...
                mimeType: imageFile.type,
            },
        };

        const prompt = `Eres un experto en análisis de imágenes de comida. Tu tarea es determinar si la imagen proporcionada contiene comida o no. 
    
    Devuelve un JSON con el siguiente formato:
    {
      "isFood": boolean,
      "reason": "string" // Explicación en catalán
    }
    
    Reglas:
    - Si la imagen muestra claramente comida (platos preparados, ingredientes, etc.), marca isFood como true
    - Si la imagen muestra personas pero están comiendo o con comida, marca isFood como true
    - Si la imagen no contiene comida (personas solas, paisajes, objetos, etc.), marca isFood como false
    - Si no puedes determinar con seguridad, marca isFood como false`;

        const result = await model.generateContent([prompt, imagePart]);
        const responseText = result.response.text();

        // Extraer el JSON de la respuesta
        const jsonStart = responseText.indexOf("{");
        const jsonEnd = responseText.lastIndexOf("}") + 1;
        const jsonResponse = responseText.substring(jsonStart, jsonEnd);

        return JSON.parse(jsonResponse);
    } catch (error) {
        console.error("Error al validar la imagen:", error);
        return {
            isFood: false,
            reason: "Error al procesar la imagen. Si us plau, prova amb una altra imatge."
        };
    }
}

function fileToBase64(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = () => resolve(reader.result);
        reader.onerror = error => reject(error);
    });
}


export async function validateFoodVideo(videoFile) {
    try {
      const frames = await extractFramesFromVideo(videoFile, [1, 5, 10, 15, 20]); // segundos
      
      const results = await Promise.all(frames.map(async (base64Frame) => {
        const framePart = {
          inlineData: {
            data: base64Frame.split(",")[1],
            mimeType: "image/jpeg"
          }
        };
  
        const prompt = `Ets un expert en vídeos de cuina. Aquesta imatge és un fotograma extret d'un vídeo. La teva tasca és determinar si mostra aliments o escenes relacionades amb la cuina.
  
  Retorna un JSON amb el format següent:
  {
    "isFood": boolean,
    "reason": "string" // Explicació en català
  }
  
  Recorda:
  - Si el fotograma mostra clarament menjar, ingredients o cuina, és vàlid.
  - Si no sembla rellevant a la cuina, és invàlid.`;
  
        const result = await model.generateContent([prompt, framePart]);
        const text = result.response.text();
  
        const jsonStart = text.indexOf("{");
        const jsonEnd = text.lastIndexOf("}") + 1;
        const jsonResponse = text.substring(jsonStart, jsonEnd);
  
        return JSON.parse(jsonResponse);
      }));
  
      // Si al menos uno de los frames es válido, aceptamos el vídeo
      const isValid = results.some(res => res.isFood);
      
      return {
        isFood: isValid,
        reason: isValid ? "Vídeo vàlid" : "No conté menjar"
      };
    } catch (error) {
      console.error("Error al validar el vídeo:", error);
      return {
        isFood: false,
        reason: "Error al processar el vídeo. Si us plau, prova amb un altre."
      };
    }
}


  function extractFramesFromVideo(videoFile, timesInSeconds) {
  return new Promise((resolve) => {
    const video = document.createElement("video");
    const canvas = document.createElement("canvas");
    const context = canvas.getContext("2d");
    const frames = [];

    video.preload = "auto";
    video.src = URL.createObjectURL(videoFile);
    video.muted = true;
    video.playsInline = true;

    video.onloadedmetadata = async () => {
      for (const time of timesInSeconds) {
        if (time > video.duration) break;

        await new Promise((res) => {
          video.currentTime = time;
          video.onseeked = () => {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            frames.push(canvas.toDataURL("image/jpeg"));
            res();
          };
        });
      }

      resolve(frames);
    };
  });
}
