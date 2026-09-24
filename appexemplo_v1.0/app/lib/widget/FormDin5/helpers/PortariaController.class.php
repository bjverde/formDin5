<?php

class PortariaController
{
  public const DATAPORTARIA = 'dataPortaria';
  private const INFO_CACHE_SESSION_KEY = 'PortariaController_info_cache';
  private const INFO_CACHE_TTL = 300;

  private PortariaDAO $dao;
  private PortariaConsultaService $consultaService;
  private PortariaUploadPolicy $uploadPolicy;
  private ?array $infoCache = null;
  private string $diretorioFisicoPortarias;

  public function __construct()
  {
    $this->dao = new PortariaDAO();
    $this->consultaService = new PortariaConsultaService($this->dao);
    $servidor = new ServidorConfig();
    $this->uploadPolicy = new PortariaUploadPolicy($servidor);
    $this->diretorioFisicoPortarias = $servidor->getDiretorioFisicoPortarias();
  }

  public function getExtensoesUploadDisponiveis(): array
  {
    return $this->uploadPolicy->getConfiguredExtensions();
  }

  public function getExtensoesUploadPorData(string $dataPortaria): array
  {
    return $this->uploadPolicy->getAllowedExtensions($dataPortaria);
  }

  private function getRaizFisicaResolvida(): string
  {
    return $this->resolveApplicationPath($this->diretorioFisicoPortarias);
  }

  private function getDiretorioPrivadoPortarias(): string
  {
    return $this->resolveApplicationPath(
      sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'norma-portarias'
    );
  }

  /**
   * Analisa o valor de pesquisa textual bruto e identifica se o usuario
   * solicitou busca por frase exata envolvendo o termo em aspas duplas.
   *
   * @param string $valor Valor bruto vindo do formulario.
   * @return array {
   *   @var string $valorMongo String a enviar para $text.$search (mantem aspas
   *                          quando o termo e uma frase valida; remove aspas
   *                          pendentes caso contrario).
   *   @var bool   $isFrase    true se o termo e uma frase exata entre aspas.
   *   @var string $frase      Conteudo da frase sem aspas externas (vazio se
   *                          nao for frase).
   *   @var array  $tokens     Lista de termos para highlight/match: um unico
   *                          elemento com a frase inteira quando $isFrase e
   *                          true; varios tokens (palavras) caso contrario.
   * }
   */
  public static function analisarPesquisaTextual(string $valor): array
  {
    return PortariaConsultaService::analisarPesquisaTextual($valor);
  }

   public function count($arrayFilter = null)
   {
     return $this->consultaService->count(is_array($arrayFilter) ? $arrayFilter : []);
  }

  /**
   * Executa um consulta no Mongo e retorna o array de resultado
   * @param array $arraySort       - campos de ordenação ordenação
   * @param array $arrayFilter     - campos que serão usados na pesquisa
   * @param number $limit          - Qtd de registro do retorno, valor default 1.000
   * @param number $skip           - Pula para o registro X, valor default é 0
   * @param boolean $resultToFrontEnd - formato do para o front end ou para o banco de dados
   * @return array $result - Array com os dados retornados do Mongo
   */
   public function selectAll($arraySort = null, $arrayFilter = null, $limit = null, $skip = null, $resultFormDin = true)
   {
     $filtros = is_array($arrayFilter) ? $arrayFilter : [];
     $result = $this->consultaService->selectAll(
       $arraySort,
       $filtros,
       $limit,
       $skip,
       $resultFormDin
     );
     if ($resultFormDin) {
       $result = self::trataDadosToFrontEnd($result, $filtros);
     }
     return $result;
   }

   public function selectPage($arraySort = null, $arrayFilter = null, $limit = 15, $skip = 0, $resultFormDin = true)
   {
     $filtros = is_array($arrayFilter) ? $arrayFilter : [];
     $result = $this->consultaService->selectPage(
       $arraySort,
       $filtros,
       (int) $limit,
       (int) $skip
     );

     if ($resultFormDin) {
       $result['data'] = self::trataDadosToFrontEnd($result['data'], $filtros);
     }

    return $result;
  }

  /**
   * Trata os dados depois da consulta
   * @param array $dados - recuperado do mongo
   * @param array $filtroBusca - Filtro de busca do Mongo
   *
   * @return array $dados depois dos dados tratados
   */
  public function trataDadosToFrontEnd($dados, $filtroBusca)
  {
    if (!is_array($dados) || empty($dados)) {
      return [];
    }
     $palavraPesquisa = ArrayHelper::get($filtroBusca, '$text');
     $palavraPesquisa = ArrayHelper::get($palavraPesquisa, '$search');
     if (!is_string($palavraPesquisa) || trim($palavraPesquisa) === '') {
       $palavraPesquisa = ArrayHelper::get($filtroBusca, 'textoPortaria');
     }
     if (!is_string($palavraPesquisa) || trim($palavraPesquisa) === '') {
       $palavraPesquisa = null;
     }
     $infoBusca = $palavraPesquisa !== null
       ? self::analisarPesquisaTextual($palavraPesquisa)
       : null;
     $palavrasParaBuscar = $infoBusca['tokens'] ?? [];
     $fraseExata = ($infoBusca['isFrase'] ?? false) ? $infoBusca['frase'] : null;

    $tipoPortaria = ArrayHelper::get($dados, 'tipoPortaria');
    $arrTipoPortaria = $this->dao->convertMongoElement2String($tipoPortaria);

    foreach ($dados['_id'] as $key => $id) {
      if (isset($arrTipoPortaria[$key]) && is_array($arrTipoPortaria[$key])) {
        $tiposFormatados = [];
        foreach ($arrTipoPortaria[$key] as $arrayInterno) {
          if (is_array($arrayInterno)) {
            $tiposFormatados[] = implode('/', $arrayInterno);
          } else {
            $tiposFormatados[] = $arrayInterno;
          }
        }
        $dados['tipoPortaria'][$key] = implode(' | ', $tiposFormatados);
      } else {
        $dados['tipoPortaria'][$key] = '';
      }
       if ($palavraPesquisa !== null) {
         $texto = $dados['textoIndexado'][$key] ?? $dados['textoPortaria'][$key] ?? '';
         $trecho = $this->extrairTrechoPesquisa($texto, $palavrasParaBuscar, $fraseExata);
         $dados['textoIndexado'][$key] = $this->highlightTexto($trecho, $palavrasParaBuscar);
      } else {
        $texto = $dados['textoIndexado'][$key] ?? $dados['textoPortaria'][$key] ?? null;
        $dados['textoIndexado'][$key] = $texto === null
          ? null
          : htmlspecialchars((string) $texto, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
      }

      if (isset($dados['pathArquivo'][$key])) {
        $path = $dados['pathArquivo'][$key];
        $arquivoLimpo = ucfirst(basename($path, '.pdf'));
        $nomeArquivo = htmlspecialchars(
          str_replace('_', ' ', $arquivoLimpo),
          ENT_QUOTES | ENT_SUBSTITUTE,
          'UTF-8'
        );
        // Alterado para usar UrlController, garantindo que arquivos externos não quebrem o link
        $url = UrlController::getArquivoUrl($path);
        $dados['pathArquivo'][$key] = '<a href="' . $url . '" target="_blank" >' . $nomeArquivo . '</a>';
      }
    }
    return $dados;
  }

  /**
   * Extrai um trecho do texto em torno de uma palavra de pesquisa específica.
   * @param string $texto O texto completo de onde o trecho será extraído.
   * @param string $palavraPesquisa A palavra que está sendo pesquisada no texto.
   * @return string Um trecho do texto que contém a palavra de pesquisa, ou uma mensagem indicando que a palavra não foi encontrada.
   */
  private function extrairTrechoPesquisa($texto,  array $palavrasPesquisa, ?string $fraseExata = null)
  {
    $texto = str_replace('\\', '', $texto);
    $texto = self::normalizarUnicode($texto);

    if ($texto === '') {
      return null;
    }

    if ($fraseExata !== null && $fraseExata !== '') {
      $fraseExata = self::normalizarUnicode($fraseExata);
      $pos = mb_stripos($texto, $fraseExata);
      if ($pos !== false) {
        $inicio = max(0, $pos - 120);
        $trecho = mb_substr($texto, $inicio, 240);
        return '...' . $trecho . '...';
      }

      return '...' . mb_substr($texto, 0, 240) . '...';
    }

    foreach ($palavrasPesquisa as $palavra) {
      $termoBusca = self::normalizarUnicode((string) $palavra);
      if ($termoBusca === '') {
        continue;
      }

      $pos = mb_stripos($texto, $termoBusca);
      if ($pos !== false) {
        $inicio = max(0, $pos - 120);
        $trecho = mb_substr($texto, $inicio, 240);
        return '...' . $trecho . '...';
      }
    }

    return '...' . mb_substr($texto, 0, 240) . '...';
  }

  /**
   * Indica se o trecho retornado para o grid possui conteúdo textual
   * aproveitável (descartando marcadores <mark> e placeholders "...").
   * @param mixed $trecho Conteúdo da célula (pode conter HTML de highlight).
   * @return bool
   */
  public static function trechoTemConteudo($trecho): bool
  {
    if ($trecho === null || $trecho === '') {
      return false;
    }
    $limpo = trim(strip_tags((string) $trecho));
    return $limpo !== '' && $limpo !== '...';
  }

  /**
   * Destaca (realça) uma palavra de pesquisa dentro de um texto.
   * @param string $texto O texto onde a palavra será destacada.
   * @param string $palavraPesquisa A palavra a ser destacada.
   * @return string O texto com a palavra destacada, ou o texto original se não houver o que destacar.
   */
  private function highlightTexto($texto, array $palavrasPesquisa)
  {
    $texto = self::normalizarUnicode((string) $texto);
    $texto = htmlspecialchars($texto, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    if ($texto === '' || empty($palavrasPesquisa)) {
      return $texto;
    }
    $palavrasPesquisa = array_map(function ($p) {
      return htmlspecialchars(self::normalizarUnicode((string) $p), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }, $palavrasPesquisa);
    $quotedWords = array_map(function ($word) {
      return preg_quote($word, '/');
    }, $palavrasPesquisa);
    $pattern = '/(' . implode('|', $quotedWords) . ')/i';
    return preg_replace($pattern, '<mark>$1</mark>', $texto) ?? $texto;
  }
  /**
   * Normaliza uma string para NFC (Unicode Normalization Form C),
   * garantindo que caracteres acentuados compostos (ex.: 'á' U+00E1)
   * e decompostos (ex.: 'a' + '´' U+0061 U+0301) sejam comparáveis
   * com mb_stripos e preg_match. Se a extensão intl não estiver
   * disponível, retorna o texto original sem alterações.
   * @param string $texto
   * @return string
   */
  private static function normalizarUnicode(string $texto): string
  {
    if (!class_exists('Normalizer')) {
      return $texto;
    }
    $normalizado = Normalizer::normalize($texto, Normalizer::FORM_C);
    return $normalizado === false ? $texto : $normalizado;
  }
  /***
   * Formata os Dados para os tipos corretos para gravar no mongo.
   * Transformando as datas do formato DD/MM/YYY para objeto
   * @param PortariaVO $objVo
   * @return PortariaVO
   */
  private function formataDados(PortariaVO $objVo)
  {
    $data = null;
    $dateMongoDateTime = null;
    $data = $objVo->getDataPortaria();
    $dateMongoDateTime = mongoFormDin::date2MongoDateTime($data);
    $objVo->setDataPortaria($dateMongoDateTime); //Campo de Data para facilitar a pesquisa do Front
    return $objVo;
  }

  /***
   * Retorna os campos no formato array para gravar no mongo
   * @param PortariaVO $objVo
   * @return array
   */
  private function getArrayCampos(PortariaVO $vo)
  {
    $arrayCampos = array(
      'tipoPortaria' => $vo->getTipoPortaria(),
      'numeroPortaria' => $vo->getNumeroPortaria(),
      'dataPortaria' => $vo->getDataPortaria(),
      'pathArquivo' => $vo->getPathArquivo(),
      'assunto' => $vo->getAssunto(),
      'categoria' => $vo->getCategoria(),
      'textoPortaria' => $vo->getTextoPortaria(),
        'ano' => $vo->getAno(),
        'numeroSei' => $vo->getNumeroSei(),
    );

    $textoPortaria = $vo->getTextoPortaria();
    $partes = [];
    if ($textoPortaria !== null && $textoPortaria !== '') {
        $partes[] = $textoPortaria;
    }
    $idsParaHash = [];

    foreach ($vo->getAnexos() as $anexo) {
      $conteudo = $anexo['conteudo_indexado'] ?? null;
      if (!empty($conteudo)) {
        $partes[] = $conteudo;
      }
      if (isset($anexo['_id'])) {
        $idsParaHash[] = (string) $anexo['_id'];
      }
    }

    $assunto = $vo->getAssunto();
    if (!empty($assunto)) {
        $partes[] = strip_tags($assunto);
    }

    $arrayCampos['textoIndexado'] = implode("\n\n", $partes);

    sort($idsParaHash, SORT_STRING);
    $hashAnexos = !empty($idsParaHash) ? md5(implode('', $idsParaHash)) : '';

    $arrayCampos['indexacao'] = [
      'status' => 'pendente',
      'atualizadoEm' => new MongoDB\BSON\UTCDateTime(),
      'hashAnexos' => $hashAnexos,
    ];

    $arrayCampos['anexos'] = $vo->getAnexos() ?? [];

    return $arrayCampos;
  }

  /**
   * Move uploaded temp file to destination and extract text via Apache Tika.
   * Reusable for both the main PDF and attachment files.
   * @param string $tempPath  Temporary file path from TFile upload
   * @param string $destPath  Full destination path (directory must exist)
   * @return array [string $absoluteDestPath, ?string $extractedText]
   * @throws Exception on move failure (permission, disk full) or Tika failure
   */
  private function processarArquivo(string $tempPath, string $destPath, array $allowedExtensions): array
  {
      $destFileName = basename($destPath);
      $extension = $this->validateExtension($destFileName, $allowedExtensions);
      $tempPath = $this->validateUploadedTempFile($tempPath, $extension);
      $destDir = dirname($destPath);
      if (!is_dir($destDir)) {
          mkdir($destDir, 0775, true);
      }
      if (!rename($tempPath, $destPath)) {
          throw new Exception('Erro ao mover arquivo para o diretório de destino.');
      }
      $textoExtraido = null;
      try {
          $apacheTika = new ApacheTika();
          $textoExtraido = $apacheTika->getText($destPath);
      } catch (Exception $e) {
          error_log('Erro ao extrair texto via Tika: ' . $e->getMessage());
          if (is_file($destPath)) {
              @unlink($destPath);
          }
          throw new Exception('Falha ao extrair o texto do anexo. Operação cancelada.', 0, $e);
      }
      return [$destPath, $textoExtraido];
  }

  /**
   * Valida a extensão do arquivo contra a lista de extensões permitidas.
   * @param string $storedName Nome do arquivo (do TFile JSON ou destino)
   * @param array  $allowedExtensions Lista de extensões permitidas
   * @param string|null $sourceFileName Nome original do arquivo enviado
   * @return string Extensão validada
   * @throws Exception se a extensão não for permitida
   */
  private function validateExtension(string $storedName, array $allowedExtensions, ?string $sourceFileName = null): string
  {
      $extension = '';

      if (!empty($sourceFileName)) {
          $sourceExtension = strtolower(pathinfo($sourceFileName, PATHINFO_EXTENSION));
          if (in_array($sourceExtension, $allowedExtensions, true)) {
              $extension = $sourceExtension;
          }
      }

      if ($extension === '') {
          $storedExtension = strtolower(pathinfo($storedName, PATHINFO_EXTENSION));
          if (in_array($storedExtension, $allowedExtensions, true)) {
              $extension = $storedExtension;
          }
      }

      if ($extension === '') {
          throw new Exception("Extensão do arquivo inválida. Use: " . implode(', ', $allowedExtensions));
      }

      return $extension;
  }

  private function salvarArquivoPortaria(
    $dadosArquivo,
    $numeroPortaria,
    $siglas,
    $ano,
    ?string $caminhoAnterior,
    ?string $portariaId,
    array $allowedExtensions
  ): array {
    $dadosUpload = $this->validarDadosUpload($dadosArquivo);
    $ano = $this->validarAnoPortaria($ano);
    $numeroArquivo = $this->normalizarNumeroArquivo((string) $numeroPortaria);
    $pastaSiglas = $this->normalizarSiglasArquivo($siglas);
    [$tempFilePath, $extensao] = $this->validarTempFileUpload($dadosUpload, $numeroArquivo, $allowedExtensions);
    $textoExtraido = $this->extrairTextoComTika($tempFilePath);
    $destinoFisico = $this->construirDestinoFisico($ano, $numeroArquivo, $pastaSiglas, $extensao);
    $caminhoRelativo = $this->construirCaminhoRelativo($ano, $pastaSiglas, basename($destinoFisico));
    $lockHandle = $this->acquirePortariaLock(
      'destino:' . $destinoFisico,
      'Outra operação está salvando uma portaria com o mesmo destino. Tente novamente.'
    );

    return $this->moverArquivoComSeguranca(
      $tempFilePath,
      $destinoFisico,
      $caminhoRelativo,
      $caminhoAnterior,
      $portariaId,
      $textoExtraido,
      $lockHandle
    );
  }

  private function validarDadosUpload($dadosArquivo): array
  {
    $dados = json_decode(urldecode((string) $dadosArquivo));
    if (!is_object($dados) || empty($dados->newFile)) {
      throw new Exception('Dados do novo upload da portaria são inválidos.');
    }

    $tempFilePath = (string) $dados->newFile;
    if (!is_file($tempFilePath)) {
      throw new Exception('Arquivo temporário do novo upload não encontrado: ' . basename($tempFilePath));
    }

    $sourceFileName = isset($dados->fileName)
      ? basename(str_replace('\\', '/', (string) $dados->fileName))
      : null;

    return [
      'tempFilePath' => $tempFilePath,
      'sourceFileName' => $sourceFileName,
      'newFile' => $dados->newFile,
    ];
  }

  private function validarAnoPortaria($ano): string
  {
    $ano = trim((string) $ano);
    if (!preg_match('/^\d{4}$/', $ano)) {
      throw new Exception('Ano da portaria inválido.');
    }
    return $ano;
  }

  private function validarTempFileUpload(array $dadosUpload, string $numeroArquivo, array $allowedExtensions): array
  {
    $extensao = $this->validateExtension(
      $dadosUpload['sourceFileName'] ?: $numeroArquivo,
      $allowedExtensions,
      $dadosUpload['sourceFileName']
    );
    $resolvedTempFilePath = $this->validateUploadedTempFile($dadosUpload['tempFilePath'], $extensao);
    return [$resolvedTempFilePath, $extensao];
  }

  private function extrairTextoComTika(string $tempFilePath): ?string
  {
    try {
      $apacheTika = new ApacheTika();
      return $apacheTika->getText($tempFilePath);
    } catch (Throwable $e) {
      error_log('Erro ao extrair texto do PDF: ' . $e->getMessage());
      return null;
    }
  }

  private function construirDestinoFisico(string $ano, string $numeroArquivo, string $pastaSiglas, string $extensao): string
  {
    $baseFisica = $this->getRaizFisicaResolvida();
    $pastaUnidade = 'Portarias_' . $pastaSiglas;
    $diretorioDestino = $baseFisica . DIRECTORY_SEPARATOR . $pastaUnidade . DIRECTORY_SEPARATOR . $ano;

    if (!is_dir($diretorioDestino) && !mkdir($diretorioDestino, 0775, true) && !is_dir($diretorioDestino)) {
      throw new Exception('Não foi possível criar o diretório de destino da portaria.');
    }

    $diretorioDestino = realpath($diretorioDestino);
    $baseFisica = realpath($baseFisica);

    if ($diretorioDestino === false || $baseFisica === false || !$this->pathIsWithin($diretorioDestino, $baseFisica)) {
      throw new Exception('Não foi possível resolver o diretório de destino da portaria.');
    }

    $nomeArquivo = $ano . '_' . $numeroArquivo . '.' . $extensao;
    return $diretorioDestino . DIRECTORY_SEPARATOR . $nomeArquivo;
  }

  private function construirCaminhoRelativo(string $ano, string $pastaSiglas, string $nomeArquivo): string
  {
    return 'Portarias_' . $pastaSiglas . '/' . $ano . '/' . $nomeArquivo;
  }

  private function moverArquivoComSeguranca(
    string $tempFilePath,
    string $destinoFisico,
    string $caminhoRelativo,
    ?string $caminhoAnterior,
    ?string $portariaId,
    ?string $textoExtraido,
    $lockHandle
  ): array {
    $caminhoAnteriorFisico = $this->resolverCaminhoFisico((string) $caminhoAnterior);
    $backupPath = null;
    $arquivoMovido = false;

    try {
      $this->validarDestinoDisponivel($destinoFisico, $caminhoRelativo, $caminhoAnteriorFisico, $portariaId);
      $backupPath = $this->prepararBackupSeNecessario($destinoFisico, $caminhoAnteriorFisico);

      if (!rename($tempFilePath, $destinoFisico)) {
        throw new Exception('Erro ao mover o novo arquivo para o diretório de destino.');
      }
      $arquivoMovido = true;

      return [
        $caminhoRelativo,
        $textoExtraido,
        [
          'destinoFisico' => $destinoFisico,
          'backupPath' => $backupPath,
          'lockHandle' => $lockHandle,
        ],
      ];
    } catch (Throwable $e) {
      $this->reverterMovimentacaoArquivo($arquivoMovido, $destinoFisico, $backupPath);
      $this->releasePortariaLock($lockHandle);
      throw $e;
    }
  }

  private function validarDestinoDisponivel(
    string $destinoFisico,
    string $caminhoRelativo,
    ?string $caminhoAnteriorFisico,
    ?string $portariaId
  ): void {
    if ($this->dao->countPathReferences($this->getEquivalentStoredPaths($caminhoRelativo), $portariaId) > 0) {
      throw new Exception('Já existe outra portaria com o mesmo ano, unidade e número.');
    }

    $destinoExistente = realpath($destinoFisico);
    if ($destinoExistente !== false
      && ($caminhoAnteriorFisico === null || !$this->pathsAreEqual($destinoExistente, $caminhoAnteriorFisico))) {
      throw new Exception('Já existe outra portaria com o mesmo ano, unidade e número.');
    }
  }

  private function prepararBackupSeNecessario(string $destinoFisico, ?string $caminhoAnteriorFisico): ?string
  {
    $destinoExistente = realpath($destinoFisico);
    if ($destinoExistente === false) {
      return null;
    }

    $backupPath = $this->createPrivateBackupPath();
    if (!copy($destinoFisico, $backupPath) || !unlink($destinoFisico)) {
      if (is_file($backupPath)) {
        unlink($backupPath);
      }
      throw new Exception('Não foi possível preparar a substituição do arquivo existente.');
    }
    return $backupPath;
  }

  private function reverterMovimentacaoArquivo(bool $arquivoMovido, string $destinoFisico, ?string $backupPath): void
  {
    if ($arquivoMovido && is_file($destinoFisico)) {
      unlink($destinoFisico);
    }
    if ($backupPath !== null && is_file($backupPath)) {
      if (copy($backupPath, $destinoFisico)) {
        unlink($backupPath);
      } else {
        error_log('PortariaController.salvarArquivoPortaria: não foi possível restaurar o arquivo anterior.');
      }
    }
  }

  private function confirmarArquivoPortaria(array $pendingFile): void
  {
    $backupPath = $pendingFile['backupPath'] ?? null;
    if (is_string($backupPath) && is_file($backupPath) && !unlink($backupPath)) {
      error_log('PortariaController.confirmarArquivoPortaria: não foi possível excluir o backup: ' . $backupPath);
    }

    $this->releasePortariaLock($pendingFile['lockHandle'] ?? null);
  }

  private function reverterArquivoPortaria(array $pendingFile): void
  {
    $destinoFisico = (string) ($pendingFile['destinoFisico'] ?? '');
    $backupPath = $pendingFile['backupPath'] ?? null;
    if ($destinoFisico !== '' && is_file($destinoFisico)) {
      unlink($destinoFisico);
    }
    if (is_string($backupPath) && is_file($backupPath)) {
      if (copy($backupPath, $destinoFisico)) {
        unlink($backupPath);
      } else {
        error_log('PortariaController.reverterArquivoPortaria: não foi possível restaurar o arquivo anterior.');
      }
    }
    $this->releasePortariaLock($pendingFile['lockHandle'] ?? null);
  }


  public function delete($idMongo)
  {
    $lockHandle = $this->acquirePortariaIdLock((string) $idMongo);
    try {
      return $this->executarExclusao($idMongo);
    } finally {
      $this->releasePortariaLock($lockHandle);
    }
  }

  private function executarExclusao($idMongo)
  {
    $portaria = $this->selectById($idMongo);
    if (empty($portaria)) {
      throw new Exception('Registro não encontrado.');
    }
    if (!PermissaoPortaria::podeCadastrarEmQualquerUnidade()
      && !PermissaoPortaria::usuarioTemPermissaoTodasUnidades($this->extrairTipoPortaria($portaria))) {
      throw new Exception('Acesso negado: você não tem permissão para excluir esta portaria.');
    }

    $anexos = $this->extrairAnexosDoDocumento($portaria);

    $result = $this->dao->delete($idMongo);
    if ($result > 0) {
      $this->invalidarInfoCache();
      foreach ($anexos as $anexo) {
        $caminho = $anexo['caminho_fisico'] ?? null;
        if (!empty($caminho)) {
          $this->excluirArquivoAnexo((string) $caminho);
        }
      }
      $this->excluirSubpastaAnexos($portaria);
    }
    return $result;
  }

  private function excluirSubpastaAnexos(array $portaria): void
  {
    $subpasta = $this->extrairSubpastaAnexosDoDocumento($portaria);
    if ($subpasta === null) {
      return;
    }

    $resolved = $this->resolverCaminhoFisico($subpasta);
    if ($resolved === null || !is_dir($resolved)) {
      return;
    }

    if (!@rmdir($resolved)) {
      error_log('PortariaController.excluirSubpastaAnexos: subpasta não vazia, mantida: ' . $resolved);
    }
  }

  public function selectById($idMongo)
  {
    $result = $this->dao->selectById($idMongo);
    $tipoPortariaBsonArray = ArrayHelper::get($result, 'tipoPortaria');
    if (!empty($tipoPortariaBsonArray) && is_array($tipoPortariaBsonArray)) {
      $tipoPortariaArrayFormDin = $this->dao->convertMongoElement2String($tipoPortariaBsonArray);
      $result['tipoPortaria'] = $tipoPortariaArrayFormDin;
    }
    return $result;
  }

  public function editar(PortariaVO $objVo, $id)
  {
    $objVo = self::formataDados($objVo);
    $arrayCampos = self::getArrayCampos($objVo);
    $result = $this->dao->update($arrayCampos, $id);
    if ($result) {
      $this->invalidarInfoCache();
    }
    return $result;
  }

  private function validarPermissaoSalvar($tipoPortaria, $categoria): void
  {
    if (!PermissaoPortaria::podeCadastrarEmQualquerUnidade()
      && !PermissaoPortaria::usuarioTemPermissaoTodasUnidades($tipoPortaria)) {
      throw new Exception('Acesso negado: você não tem permissão para cadastrar/editar esta portaria.');
    }

    if ($categoria === 'conjunta' && !PermissaoPortaria::usuarioPodeCadastrarPortariaConjunta()) {
      throw new Exception('Cadastro de portaria conjunta não permitido. Apenas usuários da Procuradoria-Geral de Justiça podem criar portarias conjuntas.');
    }
  }

  public function save(PortariaVO $objVo, array $uploads = [], ?array $uploadPrincipal = null)
  {
    $idLock = null;
    $pendingFile = null;
    $caminhosMovidos = [];
    $movimentosAnexos = [];
    $movimentosPrincipal = [];
    $substituicoesAnexos = [];
    $renomeacoesAnexos = [];

    try {
      $documento = $this->carregarDocumentoExistente($objVo, $idLock, $updateGuard);
      $this->validarPermissoesSave($objVo, $documento);
      $this->validarUnicidadeIdentificacao($objVo);
      $this->validarExtensoesAntesDaOperacao($objVo, $uploadPrincipal, $uploads);
      $this->moverSubpastaAnexosSeIdentificacaoMudou($objVo, $documento, $movimentosAnexos);
      $pendingFile = $this->processarUploadPrincipal($objVo, $uploadPrincipal);
      $this->validarTextoPortaria($objVo, $documento);
      $this->realocarArquivoPrincipalSeIdentificacaoMudou($objVo, $documento, $uploadPrincipal !== null, $movimentosPrincipal);
      $this->processarAnexos($objVo, $uploads, $caminhosMovidos, $substituicoesAnexos);
      $this->renomearAnexosComNomeAlterado($documento, $objVo, $renomeacoesAnexos);
      $objVo = self::formataDados($objVo);

      $result = $this->persistirPortaria($objVo, $updateGuard);

      $this->descartarBackupsDeSucesso($movimentosPrincipal, $substituicoesAnexos);
      $this->confirmarOperacao($pendingFile);
      $pendingFile = null;
      $caminhosMovidos = [];
      $movimentosAnexos = [];
      $movimentosPrincipal = [];
      $substituicoesAnexos = [];
      $renomeacoesAnexos = [];

      $this->excluirArquivosAnexosRemovidos($documento, $objVo);

      $this->invalidarInfoCache();
      return $result;
    } catch (Throwable $e) {
      $this->reverterOperacao($caminhosMovidos, $pendingFile, $movimentosAnexos, $movimentosPrincipal, $substituicoesAnexos, $renomeacoesAnexos);
      throw $e;
    } finally {
      $this->releasePortariaLock($idLock);
    }
  }

  private function carregarDocumentoExistente(PortariaVO $objVo, &$idLock, &$updateGuard): ?array
  {
    if (!$objVo->getId()) {
      return null;
    }

    $idLock = $this->acquirePortariaIdLock((string) $objVo->getId());
    $updateGuard = $this->dao->getUpdateGuard((string) $objVo->getId());
    $documento = $this->selectById($objVo->getId());

    if ($updateGuard === null || empty($documento)) {
      throw new Exception('Registro não encontrado.');
    }

    return $documento;
  }

  private function validarPermissoesSave(PortariaVO $objVo, ?array $documento): void
  {
    if ($documento !== null) {
      $tipoOriginal = $this->extrairTipoPortaria($documento);
      $categoriaOriginal = ArrayHelper::formDinGetValue($documento, 'categoria', 0);
      $this->validarPermissaoSalvar($tipoOriginal, $categoriaOriginal);
    }

    $this->validarPermissaoSalvar($objVo->getTipoPortaria(), $objVo->getCategoria());
  }

  private function validarExtensoesAntesDaOperacao(
    PortariaVO $objVo,
    ?array $uploadPrincipal,
    array $uploads
  ): void {
    $pathPrincipal = (string) $objVo->getPathArquivo();
    if ($uploadPrincipal !== null) {
      $dadosArquivo = json_decode(urldecode((string) ($uploadPrincipal['dadosArquivo'] ?? '')));
      $pathPrincipal = is_object($dadosArquivo) ? (string) ($dadosArquivo->fileName ?? '') : '';
    }

    $uploadsPorId = [];
    $novosUploads = [];
    foreach ($uploads as $upload) {
      $idAnexo = trim((string) ($upload['idAnexo'] ?? ''));
      if ($idAnexo !== '') {
        $uploadsPorId[$idAnexo] = (string) ($upload['nomeArquivo'] ?? '');
      } else {
        $novosUploads[] = (string) ($upload['nomeArquivo'] ?? '');
      }
    }

    $anexosFinais = [];
    foreach ($objVo->getAnexos() ?? [] as $anexo) {
      $idAnexo = (string) ($anexo['_id'] ?? '');
      $anexo['caminho_fisico'] = $uploadsPorId[$idAnexo]
        ?? ($anexo['caminho_fisico'] ?? '');
      $anexosFinais[] = $anexo;
      unset($uploadsPorId[$idAnexo]);
    }

    foreach (array_merge($novosUploads, array_values($uploadsPorId)) as $nomeArquivo) {
      $anexosFinais[] = ['caminho_fisico' => $nomeArquivo];
    }

    $this->uploadPolicy->validateFinalExtensions(
      (string) $objVo->getDataPortaria(),
      $pathPrincipal,
      $anexosFinais
    );
  }

  private function extrairTipoPortaria(array $documento)
  {
    $tipoOriginal = ArrayHelper::formDinGetValue($documento, 'tipoPortaria', 0);
    if (empty($tipoOriginal) && isset($documento['tipoPortaria'])) {
      $tipoOriginal = $documento['tipoPortaria'];
    }
    return $tipoOriginal;
  }

  private function processarUploadPrincipal(PortariaVO $objVo, ?array $uploadPrincipal): ?array
  {
    if ($uploadPrincipal === null) {
      return null;
    }

    [$caminhoRelativo, $textoExtraido, $pendingFile] = $this->salvarArquivoPortaria(
      $uploadPrincipal['dadosArquivo'] ?? null,
      $objVo->getNumeroPortaria(),
      $objVo->getTipoPortaria(),
      $objVo->getAno(),
      $uploadPrincipal['caminhoAnterior'] ?? null,
      $objVo->getId() ? (string) $objVo->getId() : null,
      $this->uploadPolicy->getAllowedExtensions((string) $objVo->getDataPortaria())
    );

    $objVo->setPathArquivo($caminhoRelativo);
    if ($textoExtraido !== null && $textoExtraido !== '') {
      $objVo->setTextoPortaria($textoExtraido);
    }

    return $pendingFile;
  }

  private function validarTextoPortaria(PortariaVO $objVo, ?array $documento): void
  {
    if (empty($objVo->getId()) && ($objVo->getTextoPortaria() === null || $objVo->getTextoPortaria() === '')) {
      throw new Exception('O texto da portaria é obrigatório.');
    }

    if ($objVo->getId() && ($objVo->getTextoPortaria() === null || $objVo->getTextoPortaria() === '')) {
      $textoExistente = is_array($documento['textoPortaria'] ?? null)
        ? ($documento['textoPortaria'][0] ?? '')
        : ($documento['textoPortaria'] ?? '');
      $objVo->setTextoPortaria((string) $textoExistente);
    }
  }

  private function processarAnexos(PortariaVO $objVo, array $uploads, array &$caminhosMovidos, array &$substituicoesAnexos): void
  {
    if (empty($uploads)) {
      return;
    }

    $anexos = $objVo->getAnexos() ?? [];
    $anexosOriginais = $anexos;
    $baseFisica = $this->getRaizFisicaResolvida();
    $ano = $this->validarAnoPortaria($objVo->getAno());
    $siglas = $this->normalizarSiglasArquivo($objVo->getTipoPortaria());
    $numeroArquivo = $this->normalizarNumeroArquivo((string) $objVo->getNumeroPortaria());
    $allowedExtensions = $this->uploadPolicy->getAllowedExtensions((string) $objVo->getDataPortaria());

    foreach ($uploads as $upload) {
      $idAnexo = trim((string) ($upload['idAnexo'] ?? ''));
      $caminhoAntigo = $idAnexo !== '' ? $this->localizarCaminhoAnexoPorId($anexosOriginais, $idAnexo) : null;
      if ($caminhoAntigo !== null) {
        $this->protegerAnexoSubstituido($caminhoAntigo, $substituicoesAnexos);
      }

      [$anexo, $caminhoFisicoAnexo] = $this->processarAnexoIndividual(
        $upload,
        $baseFisica,
        $ano,
        $siglas,
        $numeroArquivo,
        $allowedExtensions
      );

      if ($idAnexo !== '') {
        $anexos = $this->substituirAnexoPorId($anexos, $idAnexo, $anexo);
        if ($caminhoAntigo !== null && $caminhoAntigo !== $anexo['caminho_fisico']) {
          $this->removerArquivoAnexoAntigo($caminhoAntigo);
        }
      } else {
        $anexos[] = $anexo;
      }

      $caminhosMovidos[] = $caminhoFisicoAnexo;
    }

    $objVo->setAnexos($anexos);
  }

  private function localizarCaminhoAnexoPorId(array $anexos, string $idAnexo): ?string
  {
    foreach ($anexos as $anexo) {
      if ((string) ($anexo['_id'] ?? '') === $idAnexo) {
        $caminho = (string) ($anexo['caminho_fisico'] ?? '');
        return $caminho !== '' ? $caminho : null;
      }
    }
    return null;
  }

  private function substituirAnexoPorId(array $anexos, string $idAnexo, array $novoAnexo): array
  {
    foreach ($anexos as $indice => $anexo) {
      if ((string) ($anexo['_id'] ?? '') === $idAnexo) {
        $novoAnexo['_id'] = (string) $anexo['_id'];
        $anexos[$indice] = $novoAnexo;
        return $anexos;
      }
    }
    $anexos[] = $novoAnexo;
    return $anexos;
  }

  private function protegerAnexoSubstituido(string $caminhoRelativo, array &$substituicoesAnexos): void
  {
    $resolved = $this->resolverCaminhoFisico($caminhoRelativo);
    if ($resolved === null || !is_file($resolved)) {
      return;
    }
    $this->excluirComBackup($resolved, $substituicoesAnexos);
  }

  private function removerArquivoAnexoAntigo(string $caminhoRelativo): void
  {
    $resolved = $this->resolverCaminhoFisico($caminhoRelativo);
    if ($resolved !== null && is_file($resolved)) {
      unlink($resolved);
    }
  }

  private function renomearAnexosComNomeAlterado(?array $documento, PortariaVO $objVo, array &$renomeacoesAnexos): void
  {
    if ($documento === null) {
      return;
    }

    $anexosAntigosPorId = [];
    foreach ($this->extrairAnexosDoDocumento($documento) as $anexoAntigo) {
      $idAntigo = (string) ($anexoAntigo['_id'] ?? '');
      if ($idAntigo !== '') {
        $anexosAntigosPorId[$idAntigo] = $anexoAntigo;
      }
    }
    if (empty($anexosAntigosPorId)) {
      return;
    }

    $anexosAtualizados = $objVo->getAnexos() ?? [];
    foreach ($anexosAtualizados as $indice => $anexo) {
      $id = (string) ($anexo['_id'] ?? '');
      if ($id === '' || !isset($anexosAntigosPorId[$id])) {
        continue;
      }

      $anexoAntigo = $anexosAntigosPorId[$id];
      $caminhoAntigo = (string) ($anexoAntigo['caminho_fisico'] ?? '');
      if ($caminhoAntigo === '' || (string) ($anexo['caminho_fisico'] ?? '') !== $caminhoAntigo) {
        continue;
      }

      $nomeNovo = (string) ($anexo['nome_arquivo'] ?? '');
      if ($nomeNovo === '' || $nomeNovo === (string) ($anexoAntigo['nome_arquivo'] ?? '')) {
        continue;
      }

      $extensaoAntiga = strtolower(pathinfo($caminhoAntigo, PATHINFO_EXTENSION));
      $safeName = preg_replace('/[\\/:*?"<>|]+/', '-', $nomeNovo);
      $extensaoNova = strtolower(pathinfo($safeName, PATHINFO_EXTENSION));
      if ($extensaoNova === '' && $extensaoAntiga !== '') {
        $safeName = rtrim($safeName, '. ') . '.' . $extensaoAntiga;
        $extensaoNova = $extensaoAntiga;
      }
      if ($extensaoAntiga !== '' && $extensaoNova !== $extensaoAntiga) {
        throw new Exception('A extensão do anexo não pode ser alterada sem um novo upload.');
      }
      $allowedExtensions = $this->uploadPolicy->getAllowedExtensions((string) $objVo->getDataPortaria());
      if ($extensaoNova === '' || !in_array($extensaoNova, $allowedExtensions, true)) {
        throw new Exception('Extensão do anexo não permitida. Use: ' . implode(', ', $allowedExtensions) . '.');
      }

      $novoCaminhoRelativo = dirname(str_replace('\\', '/', $caminhoAntigo)) . '/' . $safeName;
      if ($novoCaminhoRelativo === $caminhoAntigo) {
        continue;
      }
      if (UrlController::normalizarCaminhoRelativo($novoCaminhoRelativo) !== $novoCaminhoRelativo) {
        throw new Exception('Nome do anexo inválido.');
      }

      $origemFisica = $this->resolverCaminhoFisico($caminhoAntigo);
      if ($origemFisica === null || !is_file($origemFisica)) {
        error_log('PortariaController.renomearAnexosComNomeAlterado: arquivo não encontrado, apenas metadados atualizados: ' . $caminhoAntigo);
        $anexosAtualizados[$indice]['caminho_fisico'] = $novoCaminhoRelativo;
        continue;
      }

      $raizFisica = $this->getRaizFisicaResolvida();
      $destinoFisico = $raizFisica . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $novoCaminhoRelativo);
      if (file_exists($destinoFisico)) {
        throw new Exception('Já existe um anexo com este nome nesta portaria.');
      }

      if (!rename($origemFisica, $destinoFisico)) {
        throw new Exception('Não foi possível renomear o anexo.');
      }

      $renomeacoesAnexos[] = ['origem' => $origemFisica, 'destino' => $destinoFisico];
      $anexosAtualizados[$indice]['caminho_fisico'] = $novoCaminhoRelativo;
    }

    $objVo->setAnexos($anexosAtualizados);
  }

  private function excluirComBackup(string $caminhoFisico, array &$registro): void
  {
    $backup = $this->createPrivateBackupPath();
    if (!copy($caminhoFisico, $backup)) {
      if (is_file($backup)) {
        @unlink($backup);
      }
      throw new Exception('Não foi possível proteger o arquivo substituído.');
    }
    if (!unlink($caminhoFisico)) {
      @unlink($backup);
      throw new Exception('Não foi possível remover o arquivo substituído.');
    }
    $registro[] = ['tipo' => 'backup', 'origem' => $caminhoFisico, 'backup' => $backup];
  }

  private function processarAnexoIndividual(
    array $upload,
    string $baseFisica,
    string $ano,
    string $siglas,
    string $numeroArquivo,
    array $allowedExtensions
  ): array
  {
    $tempPath = $upload['tempPath'] ?? '';
    $nomeArquivo = $upload['nomeArquivo'] ?? '';
    $subpastaAnexos = $this->construirSubpastaAnexosRelativa($ano, $siglas, $numeroArquivo);
    $diretorioAnexos = $baseFisica . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $subpastaAnexos);
    if (!is_dir($diretorioAnexos) && !mkdir($diretorioAnexos, 0775, true) && !is_dir($diretorioAnexos)) {
      throw new Exception('Não foi possível criar o diretório de anexos da portaria.');
    }
    $safeFileName = preg_replace('/[\\/:*?"<>|]+/', '-', $nomeArquivo);
    $destPath = $diretorioAnexos . DIRECTORY_SEPARATOR . $safeFileName;
    $caminhoRelativo = $subpastaAnexos . '/' . $safeFileName;

    if (file_exists($destPath)) {
      throw new Exception('Já existe um anexo com este nome nesta portaria.');
    }

    [, $textoAnexo] = $this->processarArquivo($tempPath, $destPath, $allowedExtensions);

    $anexo = [
      '_id'               => (string) new MongoDB\BSON\ObjectId(),
      'nome_arquivo'      => $nomeArquivo,
      'caminho_fisico'    => $caminhoRelativo,
      'tamanho'           => filesize($destPath),
      'mimetype'          => mime_content_type($destPath) ?: 'application/octet-stream',
      'data_upload'       => mongoFormDin::date2MongoDateTime(null),
      'conteudo_indexado' => $textoAnexo ?? '',
    ];
    return [$anexo, $destPath];
  }

  private function construirSubpastaAnexosRelativa(string $ano, string $pastaSiglas, string $numeroArquivo): string
  {
    return 'Portarias_' . $pastaSiglas . '/' . $ano . '/anexos/' . $ano . '_' . $numeroArquivo;
  }

  private function extrairSubpastaAnexosDoDocumento(?array $documento): ?string
  {
    if ($documento === null) {
      return null;
    }

    try {
      $siglas = $this->normalizarSiglasArquivo($this->extrairTipoPortaria($documento));
      $ano = $this->validarAnoPortaria(ArrayHelper::formDinGetValue($documento, 'ano', 0));
      $numero = $this->normalizarNumeroArquivo((string) ArrayHelper::formDinGetValue($documento, 'numeroPortaria', 0));
    } catch (Throwable $e) {
      error_log('PortariaController.extrairSubpastaAnexosDoDocumento: identificação inválida: ' . $e->getMessage());
      return null;
    }

    return $this->construirSubpastaAnexosRelativa($ano, $siglas, $numero);
  }

  private function validarUnicidadeIdentificacao(PortariaVO $objVo): void
  {
    $ano = $this->validarAnoPortaria($objVo->getAno());
    $siglas = $this->normalizarSiglasArquivo($objVo->getTipoPortaria());
    $numero = $this->normalizarNumeroArquivo((string) $objVo->getNumeroPortaria());
    $prefixo = 'Portarias_' . $siglas . '/' . $ano . '/' . $ano . '_' . $numero . '.';

    $existe = $this->dao->countByPrefixoPathArquivo(
      $prefixo,
      $objVo->getId() ? (string) $objVo->getId() : null
    );
    if ($existe > 0) {
      throw new Exception('Já existe outra portaria com o mesmo ano, unidade e número.');
    }
  }

  private function moverSubpastaAnexosSeIdentificacaoMudou(PortariaVO $objVo, ?array $documento, array &$movimentosAnexos): void
  {
    $subpastaAntiga = $this->extrairSubpastaAnexosDoDocumento($documento);
    if ($subpastaAntiga === null) {
      return;
    }

    $siglasNovas = $this->normalizarSiglasArquivo($objVo->getTipoPortaria());
    $anoNovo = $this->validarAnoPortaria($objVo->getAno());
    $numeroNovo = $this->normalizarNumeroArquivo((string) $objVo->getNumeroPortaria());
    $subpastaNova = $this->construirSubpastaAnexosRelativa($anoNovo, $siglasNovas, $numeroNovo);

    if ($subpastaAntiga === $subpastaNova) {
      return;
    }

    $raizFisica = $this->getRaizFisicaResolvida();
    $origemFisica = $raizFisica . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $subpastaAntiga);
    $destinoFisico = $raizFisica . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $subpastaNova);

    if (is_dir($origemFisica)) {
      $lockHandle = $this->acquirePortariaLock(
        'destino:' . $destinoFisico,
        'Outra operação está alterando uma portaria com o mesmo destino. Tente novamente.'
      );
      try {
        if (is_dir($destinoFisico)) {
          throw new Exception('Já existe uma pasta de anexos para o novo número da portaria.');
        }

        $destinoPai = dirname($destinoFisico);
        if (!is_dir($destinoPai) && !mkdir($destinoPai, 0775, true) && !is_dir($destinoPai)) {
          throw new Exception('Não foi possível criar o diretório de anexos da portaria.');
        }

        if (!rename($origemFisica, $destinoFisico)) {
          throw new Exception('Não foi possível mover a pasta de anexos para o novo número da portaria.');
        }
      } finally {
        $this->releasePortariaLock($lockHandle);
      }

      $movimentosAnexos[] = ['origem' => $origemFisica, 'destino' => $destinoFisico];
    }

    $this->reescreverCaminhosAnexos($objVo, $subpastaAntiga, $subpastaNova);
  }

  private function reescreverCaminhosAnexos(PortariaVO $objVo, string $subpastaAntiga, string $subpastaNova): void
  {
    $anexos = $objVo->getAnexos() ?? [];
    foreach ($anexos as $indice => $anexo) {
      $caminho = (string) ($anexo['caminho_fisico'] ?? '');
      if ($caminho === '' || !str_starts_with($caminho, $subpastaAntiga . '/')) {
        continue;
      }
      $novoCaminho = $subpastaNova . substr($caminho, strlen($subpastaAntiga));
      if (UrlController::normalizarCaminhoRelativo($novoCaminho) !== $novoCaminho) {
        error_log('PortariaController.reescreverCaminhosAnexos: caminho resultante inválido, ignorado: ' . $caminho);
        continue;
      }
      $anexos[$indice]['caminho_fisico'] = $novoCaminho;
    }
    $objVo->setAnexos($anexos);
  }

  private function realocarArquivoPrincipalSeIdentificacaoMudou(
    PortariaVO $objVo,
    ?array $documento,
    bool $houveUploadPrincipal,
    array &$movimentosPrincipal
  ): void {
    if ($documento === null) {
      return;
    }

    $caminhoAntigo = trim((string) ArrayHelper::formDinGetValue($documento, 'pathArquivo', 0));
    if ($caminhoAntigo === '') {
      return;
    }

    $siglasNovas = $this->normalizarSiglasArquivo($objVo->getTipoPortaria());
    $anoNovo = $this->validarAnoPortaria($objVo->getAno());
    $numeroNovo = $this->normalizarNumeroArquivo((string) $objVo->getNumeroPortaria());

    $extensao = strtolower(pathinfo($caminhoAntigo, PATHINFO_EXTENSION));
    $novoCaminho = 'Portarias_' . $siglasNovas . '/' . $anoNovo . '/' . $anoNovo . '_' . $numeroNovo
      . ($extensao !== '' ? '.' . $extensao : '');

    if ($novoCaminho === $caminhoAntigo) {
      return;
    }

    $origemFisica = $this->resolverCaminhoFisico($caminhoAntigo);

    if ($houveUploadPrincipal) {
      if ($origemFisica !== null && is_file($origemFisica)) {
        $novoFisico = $this->resolverCaminhoFisico((string) $objVo->getPathArquivo());
        if ($novoFisico !== null && !$this->pathsAreEqual($origemFisica, $novoFisico)) {
          $this->excluirComBackup($origemFisica, $movimentosPrincipal);
        }
      }
      return;
    }

    if ($origemFisica === null || !is_file($origemFisica)) {
      error_log('PortariaController.realocarArquivoPrincipal: arquivo principal não encontrado, apenas metadados atualizados: ' . $caminhoAntigo);
      $objVo->setPathArquivo($novoCaminho);
      return;
    }

    $raizFisica = $this->getRaizFisicaResolvida();
    $destinoFisico = $raizFisica . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $novoCaminho);

    $lockHandle = $this->acquirePortariaLock(
      'destino:' . $destinoFisico,
      'Outra operação está alterando uma portaria com o mesmo destino. Tente novamente.'
    );
    try {
      if (file_exists($destinoFisico)) {
        throw new Exception('Já existe um arquivo para o novo ano, unidade e número da portaria.');
      }

      $destinoPai = dirname($destinoFisico);
      if (!is_dir($destinoPai) && !mkdir($destinoPai, 0775, true) && !is_dir($destinoPai)) {
        throw new Exception('Não foi possível criar o diretório de destino da portaria.');
      }

      if (!rename($origemFisica, $destinoFisico)) {
        throw new Exception('Não foi possível mover o arquivo principal para o novo número da portaria.');
      }
    } finally {
      $this->releasePortariaLock($lockHandle);
    }

    $movimentosPrincipal[] = ['tipo' => 'rename', 'origem' => $origemFisica, 'destino' => $destinoFisico];
    $objVo->setPathArquivo($novoCaminho);
  }

  private function excluirArquivosAnexosRemovidos(?array $documento, PortariaVO $objVo): void
  {
    if ($documento === null) {
      return;
    }

    $subpastaAntiga = $this->extrairSubpastaAnexosDoDocumento($documento);
    $subpastaNova = null;
    if ($subpastaAntiga !== null) {
      try {
        $subpastaNova = $this->construirSubpastaAnexosRelativa(
          $this->validarAnoPortaria($objVo->getAno()),
          $this->normalizarSiglasArquivo($objVo->getTipoPortaria()),
          $this->normalizarNumeroArquivo((string) $objVo->getNumeroPortaria())
        );
      } catch (Throwable $e) {
        $subpastaNova = null;
      }
    }

    $idsAtuais = [];
    foreach ($objVo->getAnexos() ?? [] as $anexo) {
      $idsAtuais[(string) ($anexo['_id'] ?? '')] = true;
    }

    foreach ($this->extrairAnexosDoDocumento($documento) as $anexoAntigo) {
      $idAntigo = (string) ($anexoAntigo['_id'] ?? '');
      if ($idAntigo === '' || isset($idsAtuais[$idAntigo])) {
        continue;
      }
      $caminho = (string) ($anexoAntigo['caminho_fisico'] ?? '');
      if ($caminho === '') {
        continue;
      }
      if ($subpastaAntiga !== null && $subpastaNova !== null && $subpastaAntiga !== $subpastaNova
        && str_starts_with($caminho, $subpastaAntiga . '/')) {
        $caminho = $subpastaNova . substr($caminho, strlen($subpastaAntiga));
      }
      $this->excluirArquivoAnexo($caminho);
    }
  }

  private function descartarBackupsDeSucesso(array $movimentosPrincipal, array $substituicoesAnexos): void
  {
    foreach ($movimentosPrincipal as $movimento) {
      if (($movimento['tipo'] ?? '') === 'backup') {
        $this->descartarBackup((string) ($movimento['backup'] ?? ''));
      }
    }
    foreach ($substituicoesAnexos as $substituicao) {
      $this->descartarBackup((string) ($substituicao['backup'] ?? ''));
    }
  }

  private function descartarBackup(string $backup): void
  {
    if ($backup !== '' && is_file($backup)) {
      unlink($backup);
    }
  }

  private function extrairAnexosDoDocumento(array $documento): array
  {
    $anexosRaw = $documento['anexos'] ?? [];
    if (empty($anexosRaw)) {
      return [];
    }
    $anexoData = reset($anexosRaw);
    return self::columnarToRows(is_array($anexoData) ? $anexoData : []);
  }

  private function construirChaveIdentificacao(PortariaVO $objVo): string
  {
    $siglas = $this->normalizarSiglasArquivo($objVo->getTipoPortaria());
    $ano = $this->validarAnoPortaria($objVo->getAno());
    $numero = $this->normalizarNumeroArquivo((string) $objVo->getNumeroPortaria());
    return $siglas . '|' . $ano . '|' . $numero;
  }

  private function persistirPortaria(PortariaVO $objVo, ?array $updateGuard)
  {
    $arrayCampos = self::getArrayCampos($objVo);
    $arrayCampos['chaveIdentificacao'] = $this->construirChaveIdentificacao($objVo);

    try {
      if ($objVo->getId()) {
        $result = $this->dao->update($arrayCampos, $objVo->getId(), $updateGuard);
        if (method_exists($result, 'getMatchedCount') && $result->getMatchedCount() !== 1) {
          throw new Exception('A portaria foi alterada por outra operação. Recarregue o formulário e tente novamente.');
        }
        return $objVo->getId();
      }

      $result = $this->dao->insert($arrayCampos);
      $resultId = !empty($result) ? (string) $result : null;

      if (empty($resultId)) {
        throw new Exception('Erro ao salvar a portaria principal.');
      }

      return $resultId;
    } catch (MongoDB\Driver\Exception\BulkWriteException $e) {
      if ($this->ehViolacaoUnicidade($e)) {
        throw new Exception('Já existe outra portaria com o mesmo ano, unidade e número.');
      }
      throw $e;
    }
  }

  private function ehViolacaoUnicidade(MongoDB\Driver\Exception\BulkWriteException $e): bool
  {
    foreach ($e->getWriteResult()->getWriteErrors() as $erro) {
      if ($erro->getCode() === 11000) {
        return true;
      }
    }
    return $e->getCode() === 11000 || str_contains($e->getMessage(), 'E11000');
  }

  private function confirmarOperacao(?array $pendingFile): void
  {
    if ($pendingFile !== null) {
      $this->confirmarArquivoPortaria($pendingFile);
    }
  }

  private function reverterOperacao(
    array $caminhosMovidos,
    ?array $pendingFile,
    array $movimentosAnexos = [],
    array $movimentosPrincipal = [],
    array $substituicoesAnexos = [],
    array $renomeacoesAnexos = []
  ): void {
    foreach ($caminhosMovidos as $caminho) {
      if (is_file($caminho)) {
        unlink($caminho);
      }
    }
    foreach ($movimentosAnexos as $movimento) {
      $this->reverterRenomePasta(
        (string) ($movimento['destino'] ?? ''),
        (string) ($movimento['origem'] ?? '')
      );
    }
    foreach ($movimentosPrincipal as $movimento) {
      if (($movimento['tipo'] ?? '') === 'backup') {
        $this->restaurarDoBackup((string) ($movimento['origem'] ?? ''), (string) ($movimento['backup'] ?? ''));
      } else {
        $this->reverterRenomePasta(
          (string) ($movimento['destino'] ?? ''),
          (string) ($movimento['origem'] ?? '')
        );
      }
    }
    foreach ($substituicoesAnexos as $substituicao) {
      $this->restaurarDoBackup((string) ($substituicao['origem'] ?? ''), (string) ($substituicao['backup'] ?? ''));
    }
    foreach ($renomeacoesAnexos as $renomeacao) {
      $this->reverterRenomePasta(
        (string) ($renomeacao['destino'] ?? ''),
        (string) ($renomeacao['origem'] ?? '')
      );
    }
    if ($pendingFile !== null) {
      $this->reverterArquivoPortaria($pendingFile);
    }
  }

  private function reverterRenomePasta(string $destino, string $origem): void
  {
    if ($origem === '' || $destino === '' || (!is_dir($destino) && !is_file($destino))) {
      return;
    }
    if (!rename($destino, $origem)) {
      error_log('PortariaController.reverterRenomePasta: não foi possível restaurar: ' . $destino);
    }
  }

  private function restaurarDoBackup(string $origem, string $backup): void
  {
    if ($origem === '' || $backup === '' || !is_file($backup)) {
      return;
    }
    if (copy($backup, $origem)) {
      unlink($backup);
    } else {
      error_log('PortariaController.restaurarDoBackup: não foi possível restaurar: ' . $origem);
    }
  }

  /**
   * Retorna informações estatísticas sobre as portarias cadastradas no sistema.
   *
   * Este método coleta dados dinâmicos sobre a quantidade de portarias por tipo
   * e o total geral de portarias no banco de dados MongoDB.
   * @return array Array associativo contendo:
   *               - Chaves dinâmicas 'QTD_[TIPO]': quantidade de portarias por tipo específico
   *               - Chave 'TOTAL': quantidade total de portarias no sistema
   */
  public function getInfo()
  {
    if (!PermissaoPortaria::isUserAdmin()) {
      throw new Exception('Acesso negado: este dashboard é restrito aos grupos admin e STI.');
    }

    if ($this->infoCache !== null) {
      return $this->infoCache;
    }

    $cacheSessao = TSession::getValue(self::INFO_CACHE_SESSION_KEY);
    if (is_array($cacheSessao)
      && ($cacheSessao['expiresAt'] ?? 0) > time()
      && is_array($cacheSessao['data'] ?? null)) {
      $this->infoCache = $cacheSessao['data'];
      return $this->infoCache;
    }

    $this->infoCache = $this->consultaService->getInfo();
    TSession::setValue(self::INFO_CACHE_SESSION_KEY, [
      'expiresAt' => time() + self::INFO_CACHE_TTL,
      'data' => $this->infoCache,
    ]);
    return $this->infoCache;
  }

  private function invalidarInfoCache(): void
  {
    $this->infoCache = null;
    TSession::setValue(self::INFO_CACHE_SESSION_KEY, null);
  }

  /**
   * Retorna dados de uma portaria formatados e processados para apresentação na View.
   * Centraliza toda lógica de negócio relacionada ao processamento de dados para interface.
   *
   * @param string $idMongo ID MongoDB da portaria
   * @return array Array com dados processados e formatados para apresentação
   */
  public function getPortariaForView($idMongo)
  {
    try {
      // Busca dados brutos do banco
      $documento = $this->selectById($idMongo);

      if (!$documento) {
        return [
          'hasError' => true,
          'errorMessage' => 'Portaria não encontrada'
        ];
      }

      // Processa e formata dados para apresentação
      $dadosFormatados = [
        'hasError' => false,
        'errorMessage' => null,
        'tipoPortaria' => $this->extractAndFormatValue($documento, 'tipoPortaria'),
        'tipoPortariaDescricao' => $this->formatTipoPortariaDescricao($this->extractAndFormatValue($documento, 'tipoPortaria')),
        'numeroPortaria' => $this->extractAndFormatValue($documento, 'numeroPortaria'),
        'dataPortaria' => $this->formatDataPortaria($documento),
        'dataPortariaExtenso' => $this->formatarDataExtenso($documento),
        'categoria' => $this->formatCategoria($documento),
        'assunto' => $this->extractAndFormatValue($documento, 'assunto'),
        'textoPortaria' => $this->extractAndFormatValue($documento, 'textoPortaria'),
        'pathArquivo' => $this->extractAndFormatValue($documento, 'pathArquivo'),
        'ano' => $this->extractAndFormatValue($documento, 'ano'),
        'numeroSei' => $this->extractAndFormatValue($documento, 'numeroSei'),
        'hasPathArquivo' => !empty($this->extractAndFormatValue($documento, 'pathArquivo')),
        'anexos' => $this->getAnexosForView($documento)
      ];

      return $dadosFormatados;
    } catch (Exception $e) {
      return [
        'hasError' => true,
        'errorMessage' => 'Erro ao processar dados da portaria: ' . $e->getMessage()
      ];
    }
  }

  /**
   * Extrai e formata valores de campos do documento MongoDB.
   * Centraliza lógica de processamento de estruturas MongoDB (ObjectId, arrays aninhados).
   *
   * @param array $documento Documento MongoDB
   * @param string $field Nome do campo a extrair
   * @return string|null Valor formatado ou null se não encontrado
   */
  private function extractAndFormatValue($documento, $field)
  {
    if (!isset($documento[$field])) {
      return null;
    }

    $value = $documento[$field];

    // Se for array com elementos, pega o primeiro
    if (is_array($value) && isset($value[0])) {
      $firstValue = $value[0];

      // Se o primeiro elemento também for array (estruturas aninhadas), junta com vírgula
      if (is_array($firstValue)) {
        return implode(', ', $firstValue);
      }

      return $firstValue;
    }

    // Se for array vazio ou valor direto
    if (is_array($value)) {
      return null;
    }

    return $value;
  }

  private function formatTipoPortariaDescricao($tipoPortaria)
  {
    if (empty($tipoPortaria)) {
      return null;
    }

    $mapa = (new TipoPortariaController())->getTipoPortariaForComboAdiantiAll();
    $siglas = preg_split('/\s*(?:,|\|)\s*/', (string) $tipoPortaria, -1, PREG_SPLIT_NO_EMPTY);
    $descricoes = array_map(function ($sigla) use ($mapa) {
      return $mapa[$sigla] ?? $sigla;
    }, $siglas);

    return implode(' | ', $descricoes);
  }

    public static function columnarToRows(array $data): array
    {
        if (empty($data)) {
            return [];
        }
        if (array_is_list($data)) {
            return $data;
        }
        $sample = reset($data);
        if (!is_array($sample)) {
            return [];
        }
        $fieldKeys = array_keys($data);
        $count = count($sample);
        $rows = [];
        for ($i = 0; $i < $count; $i++) {
            $row = [];
            foreach ($fieldKeys as $fk) {
                $row[$fk] = is_array($data[$fk]) && array_key_exists($i, $data[$fk])
                    ? $data[$fk][$i]
                    : null;
            }
            $rows[] = $row;
        }
        return $rows;
    }

    private function getAnexosForView(array $documento): array
    {
        $raw = $documento['anexos'] ?? [];

        if (empty($raw)) {
            return [];
        }

        $anexoData = $raw;
        if (array_is_list($raw) && count($raw) === 1 && is_array($raw[0])) {
            $anexoData = $raw[0];
        }

        $anexos = self::columnarToRows($anexoData);

    $retorno = [];

    foreach ($anexos as $anexo) {
      $caminhoFisico = (string) ($anexo['caminho_fisico'] ?? '');
      if ($caminhoFisico === '') {
        continue;
      }
      $nomeArquivo = (string) ($anexo['nome_arquivo'] ?? basename($caminhoFisico));

      $retorno[] = [
        'id' => (string) ($anexo['_id'] ?? ''),
        'nome_arquivo' => $nomeArquivo,
        'url' => UrlController::getArquivoUrl($caminhoFisico),
        'is_pdf' => strtolower(pathinfo($nomeArquivo, PATHINFO_EXTENSION)) === 'pdf',
      ];
    }

    return $retorno;
  }

  /**
   * Formata data da portaria para apresentação.
   * Converte MongoDB\BSON\UTCDateTime para formato brasileiro.
   *
   * @param array $documento Documento MongoDB
   * @return string|null Data formatada (dd/mm/aaaa) ou null
   */
  private function formatDataPortaria($documento)
  {
    $dataValue = $this->extractAndFormatValue($documento, 'dataPortaria');

    if (!$dataValue) {
      return null;
    }

    // Se for MongoDB\BSON\UTCDateTime, converte para formato brasileiro
    if ($dataValue instanceof MongoDB\BSON\UTCDateTime) {
      $timestamp = $dataValue->toDateTime()->getTimestamp();
      return date('d/m/Y', $timestamp);
    }

    // Se já for string, retorna como está
    if (is_string($dataValue)) {
      return $dataValue;
    }

    return null;
  }

  /**
   * Formata data da portaria por extenso em português.
   * Converte MongoDB\BSON\UTCDateTime para formato extenso (ex: DE 31 DE MARÇO DE 2026).
   *
   * @param array $documento Documento MongoDB
   * @return string|null Data por extenso ou null
   */
  private function formatarDataExtenso($documento)
  {
    $dataValue = $this->extractAndFormatValue($documento, 'dataPortaria');

    if (!$dataValue) {
      return null;
    }

    $dateTime = null;

    if ($dataValue instanceof MongoDB\BSON\UTCDateTime) {
      $dateTime = $dataValue->toDateTime();
    } elseif (is_string($dataValue)) {
      $dateTime = DateTime::createFromFormat('d/m/Y', $dataValue);
      if (!$dateTime) {
        $dateTime = DateTime::createFromFormat('Y-m-d', $dataValue);
      }
    }

    if (!$dateTime) {
      return null;
    }

    $meses = [
      1 => 'JANEIRO', 2 => 'FEVEREIRO', 3 => 'MARÇO', 4 => 'ABRIL',
      5 => 'MAIO', 6 => 'JUNHO', 7 => 'JULHO', 8 => 'AGOSTO',
      9 => 'SETEMBRO', 10 => 'OUTUBRO', 11 => 'NOVEMBRO', 12 => 'DEZEMBRO'
    ];

    $dia = $dateTime->format('j');
    $mes = $meses[(int) $dateTime->format('n')];
    $ano = $dateTime->format('Y');

    return "DE {$dia} DE {$mes} DE {$ano}";
  }

  /**
   * Formata categoria aplicando regras de capitalização e acentuação.
   * Centraliza lógica de formatação de texto seguindo padrões do português brasileiro.
   *
   * @param array $documento Documento MongoDB
   * @return string|null Categoria formatada ou null
   */
  private function formatCategoria($documento)
  {
    $categoria = $this->extractAndFormatValue($documento, 'categoria');

    if (empty($categoria)) {
      return null;
    }

    return $this->formatarTextoPortugues($categoria);
  }

  /**
   * Formata texto com acentuação correta e capitalização adequada.
   * Aplica regras do português brasileiro para apresentação.
   *
   * @param string $texto Texto a ser formatado
   * @return string Texto formatado
   */
  private function formatarTextoPortugues($texto)
  {
    if (empty($texto)) {
      return '';
    }

    // Remove espaços extras e normaliza
    $texto = trim(preg_replace('/\s+/', ' ', $texto));

    // Converte para minúsculas preservando acentos (UTF-8)
    $texto = mb_strtolower($texto, 'UTF-8');

    // Capitaliza usando mb_convert_case para suporte completo a UTF-8
    $textoFormatado = mb_convert_case($texto, MB_CASE_TITLE, 'UTF-8');

    // Array de palavras que devem ficar em minúsculas (exceto no início)
    $palavrasMinusculas = [
      'de',
      'da',
      'do',
      'das',
      'dos',
      'e',
      'em',
      'para',
      'por',
      'com',
      'sem',
      'sobre',
      'sob',
      'entre',
      'contra',
      'desde',
      'até',
      'através',
      'mediante',
      'durante',
      'perante',
      'segundo',
      'conforme',
      'consoante',
      'salvo',
      'exceto',
      'menos',
      'fora',
      'afora',
      'senão',
      'tirante',
      'malgrado',
      'apesar',
      'não',
      'nem',
      'ou',
      'mas',
      'porém',
      'contudo',
      'todavia',
      'entretanto',
      'no',
      'na',
      'nos',
      'nas',
      'ao',
      'à',
      'aos',
      'às'
    ];

    // Usa preg_replace_callback para eficiência - processa apenas palavras que precisam ser alteradas
    $textoFormatado = preg_replace_callback(
      '/\b(' . implode('|', array_map('preg_quote', $palavrasMinusculas)) . ')\b/u',
      function ($matches) use ($textoFormatado) {
        // Não altera se for a primeira palavra da string
        $posicao = mb_strpos($textoFormatado, $matches[0], 0, 'UTF-8');
        if ($posicao === 0) {
          return $matches[0]; // Mantém capitalizada se for primeira palavra
        }
        return mb_strtolower($matches[0], 'UTF-8');
      },
      $textoFormatado
    );

    return $textoFormatado;
  }

  private function normalizarNumeroArquivo(string $numeroPortaria): string
  {
    $numeroSemAno = preg_replace('/\s*\/\s*\d{2,4}(?:\D.*)?$/', '', trim($numeroPortaria));
    $numero = preg_replace('/\D+/', '', (string) $numeroSemAno);
    if ($numero === '') {
      throw new Exception('O número da portaria deve conter ao menos um dígito.');
    }
    return $numero;
  }

  private function normalizarSiglasArquivo($siglas): string
  {
    $siglas = is_array($siglas) ? $siglas : [$siglas];
    $normalizadas = [];

    foreach ($siglas as $sigla) {
      if (is_array($sigla)) {
        $sigla = implode('_', $sigla);
      }
      $sigla = strtoupper(trim((string) $sigla));
      $sigla = trim((string) preg_replace('/[^A-Z0-9_-]+/', '-', $sigla), '-_');
      if ($sigla !== '') {
        $normalizadas[] = $sigla;
      }
    }

    if (empty($normalizadas)) {
      throw new Exception('A unidade da portaria é inválida.');
    }

    $normalizadas = array_values(array_unique($normalizadas));
    sort($normalizadas, SORT_STRING);
    return implode('_', $normalizadas);
  }

  private function getEquivalentStoredPaths(string $caminho): array
  {
    $paths = [$caminho];
    $decoded = json_decode(urldecode($caminho));
    if (is_object($decoded) && !empty($decoded->fileName)) {
      $paths[] = (string) $decoded->fileName;
    }

    return array_values(array_unique(array_filter(array_map('strval', $paths))));
  }

  private function validateUploadedTempFile(string $tempFilePath, string $extension): string
  {
    $resolvedFile = realpath($tempFilePath);
    $tempRoot = realpath($this->resolveApplicationPath('tmp'));
    if ($resolvedFile === false
      || $tempRoot === false
      || is_link($tempFilePath)
      || !$this->pathIsWithin($resolvedFile, $tempRoot)
      || !is_file($resolvedFile)) {
      throw new Exception('O arquivo temporário informado é inválido.');
    }

    $fileSize = filesize($resolvedFile);
    $maximumSize = \Adianti\Service\AdiantiUploaderService::getMaximumFileUploadSize();
    if ($fileSize === false || $fileSize <= 0 || $fileSize > $maximumSize) {
      throw new Exception('O arquivo enviado está vazio ou excede o limite permitido.');
    }

    $allowedMimeTypes = [
      'pdf' => ['application/pdf', 'application/x-pdf'],
      'doc' => ['application/msword', 'application/octet-stream'],
      'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip'],
      'txt' => ['text/plain'],
    ];
    $mimeType = (new finfo(FILEINFO_MIME_TYPE))->file($resolvedFile);
    if (!isset($allowedMimeTypes[$extension]) || !in_array($mimeType, $allowedMimeTypes[$extension], true)) {
      throw new Exception('O conteúdo do arquivo não corresponde à extensão informada.');
    }
    $this->validateOfficeDocumentSignature($resolvedFile, $extension);
    if ($extension === 'pdf') {
      $handle = fopen($resolvedFile, 'rb');
      $signature = is_resource($handle) ? fread($handle, 5) : false;
      if (is_resource($handle)) {
        fclose($handle);
      }
      if ($signature !== '%PDF-') {
        throw new Exception('O arquivo enviado não é um PDF válido.');
      }
    }

    return $resolvedFile;
  }

  private function validateOfficeDocumentSignature(string $filePath, string $extension): void
  {
    if ($extension === 'doc') {
      $handle = fopen($filePath, 'rb');
      $signature = is_resource($handle) ? fread($handle, 8) : false;
      if (is_resource($handle)) {
        fclose($handle);
      }
      if ($signature !== "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1") {
        throw new Exception('O arquivo DOC enviado não possui uma assinatura válida.');
      }
    }

    if ($extension !== 'docx') {
      return;
    }
    if (!class_exists('ZipArchive')) {
      throw new Exception('Não foi possível validar a estrutura do arquivo DOCX.');
    }

    $zip = new ZipArchive();
    if ($zip->open($filePath) !== true
      || $zip->locateName('[Content_Types].xml') === false
      || $zip->locateName('word/document.xml') === false) {
      $zip->close();
      throw new Exception('O arquivo DOCX enviado não possui uma estrutura válida.');
    }
    $zip->close();
  }

  private function createPrivateBackupPath(): string
  {
    $backupDir = $this->getDiretorioPrivadoPortarias()
      . DIRECTORY_SEPARATOR . 'backups';
    if (!is_dir($backupDir) && !mkdir($backupDir, 0770, true) && !is_dir($backupDir)) {
      throw new Exception('Não foi possível criar o diretório privado de backup.');
    }
    $backupPath = tempnam($backupDir, 'portaria-');
    if ($backupPath === false) {
      throw new Exception('Não foi possível reservar o backup da portaria.');
    }
    return $backupPath;
  }

  private function acquirePortariaIdLock(string $portariaId)
  {
    return $this->acquirePortariaLock(
      'id:' . $portariaId,
      'Esta portaria está sendo alterada por outra operação. Tente novamente.'
    );
  }

  private function acquirePortariaLock(string $key, string $errorMessage)
  {
    $lockDir = $this->getDiretorioPrivadoPortarias()
      . DIRECTORY_SEPARATOR . 'locks';
    if (!is_dir($lockDir) && !mkdir($lockDir, 0770, true) && !is_dir($lockDir)) {
      throw new Exception('Não foi possível criar o diretório privado de bloqueios.');
    }

    $lockHandle = fopen($lockDir . DIRECTORY_SEPARATOR . hash('sha256', $key) . '.lock', 'c');
    if ($lockHandle === false || !flock($lockHandle, LOCK_EX | LOCK_NB)) {
      if (is_resource($lockHandle)) {
        fclose($lockHandle);
      }
      throw new Exception($errorMessage);
    }
    return $lockHandle;
  }

  private function releasePortariaLock($lockHandle): void
  {
    if (!is_resource($lockHandle)) {
      return;
    }
    flock($lockHandle, LOCK_UN);
    fclose($lockHandle);
  }

  private function resolveApplicationPath(string $path): string
  {
    $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, trim($path));
    if ($path === '') {
      throw new Exception('A raiz física das portarias não foi configurada.');
    }
    if ($this->isAbsolutePath($path)) {
      $absolutePath = rtrim($path, DIRECTORY_SEPARATOR);
    } else {
      $basePath = defined('PATH') ? PATH : getcwd();
      $absolutePath = rtrim($basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $path;
    }

    $resolvedPath = realpath($absolutePath);
    return $resolvedPath !== false ? $resolvedPath : $absolutePath;
  }

  private function isAbsolutePath(string $path): bool
  {
    return str_starts_with($path, DIRECTORY_SEPARATOR)
      || preg_match('/^[A-Za-z]:[\\\\\/]/', $path) === 1;
  }

  private function resolverCaminhoFisico(string $caminhoRelativo): ?string
  {
    $caminho = UrlController::normalizarCaminhoRelativo($caminhoRelativo);
    if ($caminho === null) {
      return null;
    }

    $raizFisica = $this->getRaizFisicaResolvida();
    $candidate = $raizFisica
      . DIRECTORY_SEPARATOR
      . str_replace('/', DIRECTORY_SEPARATOR, $caminho);

    $resolvedCandidate = realpath($candidate);
    if ($resolvedCandidate === false) {
      return null;
    }

    if (!$this->pathIsWithin($resolvedCandidate, $raizFisica)) {
      error_log('PortariaController.resolverCaminhoFisico: caminho fora da raiz configurada: ' . $caminhoRelativo);
      return null;
    }

    return $resolvedCandidate;
  }

  private function pathsAreEqual(string $firstPath, string $secondPath): bool
  {
    $firstPath = rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $firstPath), DIRECTORY_SEPARATOR);
    $secondPath = rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $secondPath), DIRECTORY_SEPARATOR);
    if (PHP_OS_FAMILY === 'Windows') {
      return strcasecmp($firstPath, $secondPath) === 0;
    }
    return $firstPath === $secondPath;
  }

  private function pathIsWithin(string $path, string $basePath): bool
  {
    if ($this->pathsAreEqual($path, $basePath)) {
      return true;
    }

    $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    $basePath = rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $basePath), DIRECTORY_SEPARATOR)
      . DIRECTORY_SEPARATOR;
    if (PHP_OS_FAMILY === 'Windows') {
      return str_starts_with(strtolower($path), strtolower($basePath));
    }
    return str_starts_with($path, $basePath);
  }

  private function excluirArquivoAnexo(string $caminho): void
  {
    $resolved = $this->resolverCaminhoFisico($caminho);
    if ($resolved === null) {
      error_log('PortariaController.excluirArquivoAnexo: caminho inválido ou fora da raiz configurada: ' . $caminho);
      return;
    }
    if (is_file($resolved)) {
      unlink($resolved);
    } else {
      error_log('PortariaController.excluirArquivoAnexo: arquivo físico não encontrado: ' . $resolved);
    }
  }
}
