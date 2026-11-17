enum Prioridade {
  baixa,
  media,
  alta,
}

class Tarefa {
  String titulo;
  bool concluida;
  Prioridade prioridade;
  String observacao;

  Tarefa({
    required this.titulo,
    this.concluida = false,
    this.prioridade = Prioridade.media,
    this.observacao = '',
  });
}

