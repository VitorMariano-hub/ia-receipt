# Serviço de OCR e Extração de Dados de Comprovantes

Este é um **projeto de estudo**, desenvolvido com Laravel, que permite processar comprovantes bancários enviados em imagens ou PDFs, extraindo dados estruturados através de OCR e interpretação de texto com inteligência artificial.

---

## ✨ Funcionalidades

- ✅ Upload de arquivos nos formatos: **JPEG, PNG, JPG, PDF**.
- ✅ Conversão automática de PDFs em imagens PNG para facilitar o OCR.
- ✅ Extração de texto das imagens utilizando **Tesseract OCR**.
- ✅ Interpretação do texto com IA (**API Ollama**) para extrair dados estruturados em **JSON**.
- ✅ Remoção automática de arquivos temporários após o processamento.
- ✅ Validação dos arquivos enviados e dos dados extraídos.

---

## 🛠️ Tecnologias Utilizadas

- **PHP 8.x**
- **Laravel Framework**
- Sistema de armazenamento com **Laravel Storage**
- **ImageMagick** (para conversão de PDF para imagem)
- **Tesseract OCR** (para reconhecimento de texto em imagem)
- **API Ollama** (para processamento de linguagem natural)
- **JSON** (para comunicação estruturada dos dados)


![Visão Geral do Projeto](https://i.imgur.com/v0YeSiH.png)


