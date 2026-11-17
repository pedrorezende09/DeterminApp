
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../providers/tarefa_provider.dart';
import '../screens/cadastro_screen.dart';
import '../models/tarefa.dart';
import 'detalhes_screen.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  String _filtroSelecionado = 'todas';
  String _ordemSelecionada = 'cadastro';

  Color _corPrioridade(Prioridade prioridade) {
    switch (prioridade) {
      case Prioridade.baixa:
        return Colors.green;

      case Prioridade.media:
        return Colors.orange;

      case Prioridade.alta:
        return Colors.red;
    }
  }

  // EDITAR TAREFA
  Future<void> _editarTarefa(int index) async {
    final provider = context.read<TarefaProvider>();
    final tarefas = provider.tarefas;

    final resultado =
        await Navigator.push<
          ({String titulo, Prioridade prioridade, String observacao})
        >(
          context,
          MaterialPageRoute(
            builder: (context) => CadastroScreen(
              initialTitle: tarefas[index].titulo,
              initialPriority: tarefas[index].prioridade,
              initialObservacao: tarefas[index].observacao,
            ),
          ),
        );

    if (resultado != null && resultado.titulo.isNotEmpty) {
      await provider.editarTarefa(
        index,
        resultado.titulo,
        resultado.prioridade,
        resultado.observacao,
      );
    }
  }

  // ABRIR DETALHES
  Future<void> _abrirDetalhes(int index) async {
    final provider = context.read<TarefaProvider>();
    final tarefa = provider.tarefas[index];

    final excluir = await Navigator.push<bool>(
      context,
      MaterialPageRoute(
        builder: (context) => DetalhesScreen(tarefa: tarefa),
      ),
    );

    if (excluir == true && mounted) {
      await provider.excluirTarefa(index);
    }
  }

  // EXCLUIR TAREFA
  Future<void> _excluirTarefa(int index) async {
    final provider = context.read<TarefaProvider>();
    final tarefas = provider.tarefas;

    final bool? confirmar = await showDialog<bool>(
      context: context,
      builder: (context) {
        return AlertDialog(
          title: const Text('Excluir tarefa'),
          content: Text(
            'Deseja realmente excluir "${tarefas[index].titulo}"?',
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

    if (confirmar == true) {
      await provider.excluirTarefa(index);
    }
  }

  // CONSTRUIR INTERFACE
  @override
  Widget build(BuildContext context) {
    final tarefaProvider = context.watch<TarefaProvider>();
    final todasTarefas = tarefaProvider.tarefas;

    // FILTRAR TAREFAS
    final tarefas = todasTarefas.asMap().entries.where((entrada) {
      final tarefa = entrada.value;

      if (_filtroSelecionado == 'pendentes') {
        return !tarefa.concluida;
      }

      if (_filtroSelecionado == 'concluidas') {
        return tarefa.concluida;
      }

      return true;
    }).toList();

    // ORDENAR TAREFAS
    if (_ordemSelecionada == 'prioridade') {
      tarefas.sort((a, b) {
        final comparacao = b.value.prioridade.index.compareTo(
          a.value.prioridade.index,
        );

        if (comparacao != 0) {
          return comparacao;
        }

        return a.key.compareTo(b.key);
      });
    }

    return Scaffold(
      appBar: AppBar(
        title: const Text('Minhas Tarefas'),
        centerTitle: true,
      ),

      body: Column(
        children: [
          // FILTROS
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 12),
            child: SegmentedButton<String>(
              segments: const [
                ButtonSegment(
                  value: 'todas',
                  label: Text('Todas'),
                ),
                ButtonSegment(
                  value: 'pendentes',
                  label: Text('Pendentes'),
                ),
                ButtonSegment(
                  value: 'concluidas',
                  label: Text('Concluídas'),
                ),
              ],
              selected: {_filtroSelecionado},
              onSelectionChanged: (Set<String> selecao) {
                setState(() {
                  _filtroSelecionado = selecao.first;
                });
              },
            ),
          ),

          // ORDENAÇÃO
          Padding(
            padding: const EdgeInsets.symmetric(
              horizontal: 12,
              vertical: 8,
            ),
            child: SegmentedButton<String>(
              segments: const [
                ButtonSegment(
                  value: 'cadastro',
                  label: Text('Cadastro'),
                  icon: Icon(Icons.access_time),
                ),
                ButtonSegment(
                  value: 'prioridade',
                  label: Text('Prioridade'),
                  icon: Icon(Icons.flag),
                ),
              ],
              selected: {_ordemSelecionada},
              onSelectionChanged: (Set<String> selecao) {
                setState(() {
                  _ordemSelecionada = selecao.first;
                });
              },
            ),
          ),

          // LISTA DE TAREFAS
          Expanded(
            child: ListView.builder(
              itemCount: tarefas.length,
              itemBuilder: (context, index) {
                final tarefa = tarefas[index].value;
                final indiceOriginal = tarefas[index].key;

                return Dismissible(
                  key: ValueKey(tarefa),
                  direction: DismissDirection.endToStart,

                  // FUNDO EXIBIDO DURANTE O DESLIZE
                  background: Container(
                    color: Colors.red,
                    alignment: Alignment.centerRight,
                    padding: const EdgeInsets.only(right: 20),
                    child: const Icon(
                      Icons.delete,
                      color: Colors.white,
                    ),
                  ),

                  // CONFIRMAR ANTES DE EXCLUIR
                  confirmDismiss: (direction) async {
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

                    return confirmar ?? false;
                  },

                  // EXECUTAR EXCLUSÃO
                  onDismissed: (direction) async {
                    await context.read<TarefaProvider>().excluirTarefa(
                      indiceOriginal,
                    );
                  },

                  // CONTEÚDO DA TAREFA
                  child: ListTile(
                    leading: Checkbox(
                      value: tarefa.concluida,
                      onChanged: (valor) async {
                        final provider = context.read<TarefaProvider>();

                        await provider.alterarConclusao(
                          indiceOriginal,
                          valor ?? false,
                        );
                      },
                    ),

                    title: GestureDetector(
                      onTap: () => _abrirDetalhes(indiceOriginal),
                      child: Text(tarefa.titulo),
                    ),

                    subtitle: Text(
                      'Prioridade: ${tarefa.prioridade.name.toUpperCase()}',
                      style: TextStyle(
                        color: _corPrioridade(tarefa.prioridade),
                        fontWeight: FontWeight.bold,
                      ),
                    ),

                    trailing: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        IconButton(
                          icon: const Icon(Icons.edit),
                          tooltip: 'Editar tarefa',
                          onPressed: () => _editarTarefa(indiceOriginal),
                        ),

                        IconButton(
                          icon: const Icon(
                            Icons.delete,
                            color: Colors.red,
                          ),
                          tooltip: 'Excluir tarefa',
                          onPressed: () => _excluirTarefa(indiceOriginal),
                        ),
                      ],
                    ),
                  ),
                );
              },
            ),
          ),
        ],
      ),

      // BOTÃO DE CADASTRO
      floatingActionButton: FloatingActionButton(
        onPressed: () async {
          final provider = context.read<TarefaProvider>();

          final resultado =
              await Navigator.push<
                ({String titulo, Prioridade prioridade, String observacao})
              >(
                context,
                MaterialPageRoute(
                  builder: (context) => const CadastroScreen(),
                ),
              );

          if (resultado != null && resultado.titulo.isNotEmpty) {
            await provider.adicionarTarefa(
              resultado.titulo,
              resultado.prioridade,
              resultado.observacao,
            );
          }
        },
        child: const Icon(Icons.add),
      ),
    );
  }
}
