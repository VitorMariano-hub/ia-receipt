# Serviço de OCR e Extração de Dados de Comprovantes

Este serviço, desenvolvido com Laravel, processa comprovantes bancários enviados em imagens ou PDFs, extraindo dados estruturados por meio de OCR e interpretação de texto com inteligência artificial.

---

## Funcionalidades

- Aceita upload de arquivos nos formatos: jpeg, png, jpg, pdf.
- Converte arquivos PDF em imagens PNG para processamento OCR.
- Extrai texto das imagens usando Tesseract OCR.
- Interpreta o texto extraído com IA (API Ollama) para retornar dados estruturados em JSON.
- Remove arquivos temporários após o processamento.
- Valida arquivos enviados e dados extraídos.

---

## Tecnologias Utilizadas

- PHP 8.x
- Laravel Framework
- Sistema de armazenamento do Laravel (Storage)
- ImageMagick para conversão de PDF para imagem
- Tesseract OCR para reconhecimento de texto
- API Ollama para interpretação do texto via inteligência artificial
- JSON para troca de dados

---

