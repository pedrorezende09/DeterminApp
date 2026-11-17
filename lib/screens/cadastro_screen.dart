import 'package:flutter/material.dart';

import '../models/tarefa.dart';

class CadastroScreen extends StatefulWidget {
  final String? initialTitle;
  final Prioridade? initialPriority;
  final String? initialObservacao;

  const CadastroScreen({super.key,
   this.initialTitle, 
   this.initialPriority, 
   this.initialObservacao});

  @override
  State<CadastroScreen> createState() => _CadastroScreenState();
}

class _CadastroScreenState extends State<CadastroScreen> {
  late final TextEditingController _tituloController;
  late final TextEditingController _observacaoController;
  Prioridade _prioridadeSelecionada = Prioridade.media;

  @override
  void initState() {
    super.initState();

    _tituloController = TextEditingController(text: widget.initialTitle ?? '');

    _prioridadeSelecionada = widget.initialPriority ?? Prioridade.media;

    _observacaoController = TextEditingController(text: widget.initialObservacao ?? '',);
  }

  @override
  void dispose() {
    _tituloController.dispose();
    _observacaoController.dispose();
    super.dispose();
  }

  void _salvar() {
    final titulo = _tituloController.text.trim();

    if (titulo.isEmpty) {
      return;
    }

    Navigator.pop(context, (
      titulo: titulo,
      prioridade: _prioridadeSelecionada,
      observacao: _observacaoController.text.trim(),
    ));
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(
          widget.initialTitle == null ? 'Nova Tarefa' : 'Editar Tarefa',
        ),
        centerTitle: true,
      ),
      body: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          children: [
            TextField(
              controller: _tituloController,
              autofocus: true,
              decoration: const InputDecoration(
                labelText: 'Nome da tarefa',
                hintText: 'Digite sua tarefa',
                border: OutlineInputBorder(),
              ),
              onSubmitted: (_) => _salvar(),
            ),
              
              const SizedBox(height: 14),

            DropdownButtonFormField<Prioridade>(
              initialValue: _prioridadeSelecionada,
              decoration: const InputDecoration(
                labelText: 'Prioridade',
                border: OutlineInputBorder(),
              ),
              items: Prioridade.values.map((prioridade) {
                return DropdownMenuItem(
                  value: prioridade,
                  child: Text(prioridade.name.toUpperCase()),
                );
              }).toList(),
              onChanged: (novaPrioridade) {
                if (novaPrioridade == null) return;

                setState(() {
                  _prioridadeSelecionada = novaPrioridade;
                });
              },
            ),
            const SizedBox(height: 16),

            TextField(
                controller: _observacaoController,
                maxLines: 5,
                minLines: 3,
                decoration: const InputDecoration(
                  labelText: 'Observações',
                  hintText: 'Adicione informações, links ou instruções...',
                  border: OutlineInputBorder(),
                  alignLabelWithHint: true,
                ),
              ),
            const SizedBox(height: 16),


            const SizedBox(height: 16),
                ElevatedButton(
                  onPressed: _salvar,
                  child: Text(
                    widget.initialTitle == null
                        ? 'Salvar Tarefa'
                        : 'Salvar alterações',
                  ),
                ),
          ],

          

        ),
      ),
    );
  }
}
