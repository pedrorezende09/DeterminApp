import 'package:flutter/foundation.dart';
import '../models/tarefa.dart';
import '../services/tarefa_service.dart';

class TarefaProvider extends ChangeNotifier {
  final TarefaService _tarefaService = TarefaService();

  List<Tarefa> _tarefas = [
  Tarefa(titulo: 'Estudar Flutter'),
  Tarefa(titulo: 'Estudar Dart'),
  Tarefa(titulo: 'Fazer exercícios', concluida: true),
  Tarefa(titulo: 'Organizar meus estudos', concluida: true),
  Tarefa(titulo: 'Estudar Programação'),
  Tarefa(titulo: 'Estudar Serverpod'),
];

  List<Tarefa> get tarefas => _tarefas;

  // CARREGAR TAREFAS
  Future<void> carregarTarefas() async {
    final tarefasSalvas = await _tarefaService.carregarTarefas();

    if (tarefasSalvas != null) {
      _tarefas = tarefasSalvas;
    } else {
      await _salvarTarefas();
    }

    notifyListeners();
  }

  // SALVAR TAREFAS
  Future<void> _salvarTarefas() async {
    await _tarefaService.salvarTarefas(_tarefas);
  }

  // ADICIONAR TAREFA
  Future<void> adicionarTarefa(
    String titulo,
    Prioridade prioridade,
    String observacao,
  ) async {
    _tarefas.add(
      Tarefa(
        titulo: titulo,
        prioridade: prioridade,
        observacao: observacao,
      ),
    );

  await _salvarTarefas();
  notifyListeners();
}

  // EDITAR TAREFA
  Future<void> editarTarefa(
  int index,
  String novoTitulo,
  Prioridade novaPrioridade,
  String novaObservacao,
) async {
  _tarefas[index].titulo = novoTitulo;
  _tarefas[index].prioridade = novaPrioridade;
  _tarefas[index].observacao = novaObservacao;

  await _salvarTarefas();
  notifyListeners();
}

  // EXCLUIR TAREFA
  Future<void> excluirTarefa(int index) async {
    _tarefas.removeAt(index);

    await _salvarTarefas();

    notifyListeners();
  }

  // ALTERAR CONCLUSÃO
  Future<void> alterarConclusao(int index, bool concluida) async {
    _tarefas[index].concluida = concluida;

    await _salvarTarefas();

    notifyListeners();
  }
}