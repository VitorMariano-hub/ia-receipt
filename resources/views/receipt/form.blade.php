<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload de Comprovante</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-xl">
        <h1 class="text-2xl font-bold mb-6 text-center text-gray-800">Enviar Comprovante</h1>

        <div id="error" class="hidden mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded"></div>

        <form id="uploadForm" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Selecione o arquivo</label>
                <input type="file" name="file" id="file" required
                    class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
            </div>

            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded transition btn-submit-file">
                Enviar
            </button>
        </form>

        <div id="loader" class="hidden flex justify-center mt-12">
            <div class="border-4 border-blue-300 border-t-blue-600 rounded-full w-8 h-8 animate-spin"></div>
        </div>

        <div id="resultado" class="mt-6 hidden">
            <h2 class="text-lg font-semibold text-gray-700 mb-2">Resultado:</h2>
            <div class="bg-gray-50 border border-gray-300 rounded p-4 overflow-auto">
                <pre id="resultadoContent" class="text-sm text-gray-800 whitespace-pre-wrap"></pre>
            </div>
        </div>
    </div>

    <script>
        const form = document.getElementById('uploadForm');
        const loader = document.getElementById('loader');
        const resultado = document.getElementById('resultado');
        const resultadoContent = document.getElementById('resultadoContent');
        const errorDiv = document.getElementById('error');
        const btnSubmitFile = document.querySelector('.btn-submit-file');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const fileInput = document.getElementById('file');
            const file = fileInput.files[0];

            if (!file) return;

            // Resets
            loader.classList.remove('hidden');
            resultado.classList.add('hidden');
            errorDiv.classList.add('hidden');
            resultadoContent.textContent = '';

            btnSubmitFile.textContent = 'Processando...';
            btnSubmitFile.disabled = true;

            const formData = new FormData();
            formData.append('file', file);

            try {
                const response = await fetch('{{ route('process') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                });

                const clonedResponse = response.clone();

                if (!response.ok) {
                    let errorMessage = 'Erro desconhecido ao processar.';

                    try {
                        const errorData = await clonedResponse.json();
                        if (errorData.message) {
                            errorMessage = errorData.message;
                        }
                    } catch {
                        const errorText = await response.text();
                        errorMessage = errorText || errorMessage;
                    }

                    throw new Error(errorMessage);
                }

                const data = await response.json();

                resultadoContent.textContent = JSON.stringify(data.parsed_data, null, 2);
                resultado.classList.remove('hidden');
            } catch (err) {
                errorDiv.textContent = '❌ ' + err.message;
                errorDiv.classList.remove('hidden');
                btnSubmitFile.textContent = 'Enviar';
                btnSubmitFile.disabled = false;
            } finally {
                loader.classList.add('hidden');
                btnSubmitFile.textContent = 'Enviar';
                btnSubmitFile.disabled = false;
            }
        });
    </script>

</body>

</html>
