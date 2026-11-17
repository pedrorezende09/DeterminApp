import 'dart:convert';
import 'package:shared_preferences/shared_preferences.dart';
import '../models/tarefa.dart';

class TarefaService {
  static const String _chaveTarefas = 'tarefas';

  final SharedPreferencesAsync _prefs = SharedPreferencesAsync();

  // SALVAR TAREFAS
  Future<void> salvarTarefas(List<Tarefa> tarefas) async {
    final lista = tarefas.map((tarefa) {
      return {
        'titulo': tarefa.titulo,
        'concluida': tarefa.concluida,
        'prioridade': tarefa.prioridade.name,
        'observacao': tarefa.observacao,
      };
    }).toList();

    final dados = jsonEncode(lista);

    await _prefs.setString(_chaveTarefas, dados);
  }

  // CARREGAR TAREFAS
  Future<List<Tarefa>?> carregarTarefas() async {
    final dados = await _prefs.getString(_chaveTarefas);

    if (dados == null) {
      return null;
    }

    final List<dynamic> lista = jsonDecode(dados);

    return lista.map((item) {
    final prioridadeSalva = item['prioridade'] as String?;

    final prioridade = Prioridade.values.firstWhere(
      (p) => p.name == prioridadeSalva,
      orElse: () => Prioridade.media,
    );

    return Tarefa(
      titulo: item['titulo'] as String,
      concluida: item['concluida'] as bool,
      prioridade: prioridade,
      observacao: item['observacao'] as String? ?? '',
    );
      }).toList();
    }
  
  }