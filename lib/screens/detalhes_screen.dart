
import 'package:flutter/material.dart';

import '../models/tarefa.dart';

class DetalhesScreen extends StatelessWidget {
  final Tarefa tarefa;

  const DetalhesScreen({
    super.key,
    required this.tarefa,
  });

  Future<void> _confirmarExclusao(BuildContext context) async {
    final confirmar = await showDialog<bool>(
      context: context,
      builder: (context) {
        return AlertDialog(
          title: const Text('Excluir tarefa'),
          content: Text(
            'Deseja realmente excluir "${tarefa.titulo}"?',
          ),
          actions: [
            TextButton(
              onPressed: () {
                Navigator.pop(context, false);
              },
              child: const Text('Cancelar'),
            ),
            TextButton(
              onPressed: () {
                Navigator.pop(context, true);
              },
              child: const Text('Excluir'),
            ),
          ],
        );
      },
    );

    if (confirmar == true && context.mounted) {
      Navigator.pop(context, true);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Detalhes da tarefa'),
        centerTitle: true,
      ),
      body: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              tarefa.titulo,
              style: Theme.of(context).textTheme.headlineSmall,
            ),

            const SizedBox(height: 24),

            Text(
              'Situação: ${tarefa.concluida ? 'Concluída' : 'Pendente'}',
            ),

            const SizedBox(height: 16),

            Text(
              'Prioridade: ${tarefa.prioridade.name.toUpperCase()}',
            ),

            const SizedBox(height: 24),

            const Text(
              'Observações',
              style: TextStyle(
                fontWeight: FontWeight.bold,
                fontSize: 16,
              ),
            ),

            const SizedBox(height: 8),

            Text(
              tarefa.observacao.isEmpty
                  ? 'Nenhuma observação adicionada.'
                  : tarefa.observacao,
            ),

            const Spacer(),

            SizedBox(
              width: double.infinity,
              child: ElevatedButton.icon(
                onPressed: () => _confirmarExclusao(context),
                icon: const Icon(Icons.delete),
                label: const Text('Excluir tarefa'),
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.red,
                  foregroundColor: Colors.white,
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
