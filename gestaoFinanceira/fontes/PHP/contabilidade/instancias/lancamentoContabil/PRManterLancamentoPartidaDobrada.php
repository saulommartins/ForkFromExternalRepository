<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
    * Página de Processamento de Inclusao/Alteracao de Lancamento Partida Dobrada
    * Data de Criação   : 05/04/2007

    * @author Analista: Gelson Gonçalves
    * @author Desenvolvedor: Rodrigo S. Rodrigues

    * @ignore

    * $Id: PRManterLancamentoPartidaDobrada.php 63831 2015-10-22 12:51:00Z franver $

    * Casos de uso: uc-02.02.33
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamento.class.php"                             );
include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadeValorLancamento.class.php"                        );

//Define o nome dos arquivos
$stPrograma = "ManterLancamentoPartidaDobrada";
$pgForm    = "FM".$stPrograma.".php";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";
$pgJs      = "JS".$stPrograma.".js";

$arValoresDebito  = Sessao::read('arValoresDebito');
$arValoresCredito = Sessao::read('arValoresCredito');

$rsValoresDebito  = new RecordSet;
$rsValoresCredito = new RecordSet;

$vlTotalDebito  = 0.00;
$vlTotalCredito = 0.00;

if (count($arValoresDebito) > 0) {
    $boDebito = true;
    $rsValoresDebito->preenche($arValoresDebito);
    while (!$rsValoresDebito->eof()) {
        $vlTotalDebito = $vlTotalDebito + $rsValoresDebito->getCampo('nuVlDebito');
        $rsValoresDebito->proximo();
    }
    $rsValoresDebito->setPrimeiroElemento();
}
if (count($arValoresCredito) > 0) {
    $boCredito = true;
    $rsValoresCredito->preenche($arValoresCredito);
    while (!$rsValoresCredito->eof()) {
        $vlTotalCredito = $vlTotalCredito + $rsValoresCredito->getCampo('nuVlCredito');
        $rsValoresCredito->proximo();
    }
    $rsValoresCredito->setPrimeiroElemento();
}
if (trim($vlTotalDebito) != trim($vlTotalCredito)) {
    $vlDiferenca = true;
}

//valida a utilização da rotina de encerramento do mês contábil
$boUtilizarEncerramentoMes = SistemaLegado::pegaConfiguracao('utilizar_encerramento_mes', 9);
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeEncerramentoMes.class.php";
$obTContabilidadeEncerramentoMes = new TContabilidadeEncerramentoMes;
$obTContabilidadeEncerramentoMes->setDado('exercicio', Sessao::getExercicio());
$obTContabilidadeEncerramentoMes->setDado('situacao', 'F');
$obTContabilidadeEncerramentoMes->recuperaEncerramentoMes($rsUltimoMesEncerrado, '', ' ORDER BY mes DESC LIMIT 1 ');

$arDtAutorizacao = explode('/', $_POST['stDtLote']);
if ($boUtilizarEncerramentoMes == 'true' AND $rsUltimoMesEncerrado->getCampo('mes') >= $arDtAutorizacao[1]) {
    SistemaLegado::LiberaFrames(true,False);
    SistemaLegado::exibeAviso(urlencode("Mês do Lançamento encerrado!"),"n_incluir","erro");
    exit;
}

switch ($_REQUEST['stAcao']) {
    case "incluir":

        $boExecuta = true;

        if ($boDebito == true and $boCredito == true) {
            if ($vlDiferenca == true) {
                $boExecuta = false;
                sistemaLegado::exibeAviso( 'Existe diferença de débito e crédito.',"n_incluir","erro" );
            }
        }
        if (!$_REQUEST['inCodLote'] and !$_REQUEST['stNomLote']) {
            $boExecuta = false;
            sistemaLegado::exibeAviso( 'Informe o número e o nome do lote.',"n_incluir","erro" );
        }
        if ($_REQUEST['inCodLote'] and !$_REQUEST['stNomLote']) {
            $boExecuta = false;
            sistemaLegado::exibeAviso( 'Informe o nome do lote.',"n_incluir","erro" );
        }
        if (!$_REQUEST['inCodLote'] and $_REQUEST['stNomLote']) {
            $boExecuta = false;
            sistemaLegado::exibeAviso( 'Informe o número do lote.',"n_incluir","erro" );
        }
        if (!$_REQUEST['stDtLote']) {
            $boExecuta = false;
            sistemaLegado::exibeAviso( 'Informe a data do lote.',"n_incluir","erro" );
        }

        if ($boExecuta) {
            if ($rsValoresDebito->getNumLinhas() > 0 or $rsValoresCredito->getNumLinhas() > 0 ) {

                include_once(CAM_GF_CONT_MAPEAMENTO."TContabilidadeLote.class.php");
                $obTContabilidadeLote = new TContabilidadeLote;
                $stFiltro  = " WHERE exercicio = '".Sessao::getExercicio()."'";
                $stFiltro .= "   AND cod_entidade = ".$_REQUEST['inCodEntidade'];
                $stFiltro .= "   AND tipo = 'M'";
                $stFiltro .= "   AND cod_lote = ".$_REQUEST['inCodLote'];
                $obTContabilidadeLote->recuperaTodos($rsRecordSet, $stFiltro);
                if ($rsRecordSet->getNumLinhas() == -1) {
                    $obTContabilidadeLote->setDado( 'cod_lote'     , $_REQUEST['inCodLote']     );
                    $obTContabilidadeLote->setDado( 'exercicio'    , Sessao::getExercicio()     );
                    $obTContabilidadeLote->setDado( 'tipo'         , 'M'                        );
                    $obTContabilidadeLote->setDado( 'cod_entidade' , $_REQUEST['inCodEntidade'] );
                    $obTContabilidadeLote->setDado( 'nom_lote'     , $_REQUEST['stNomLote']     );
                    $obTContabilidadeLote->setDado( 'dt_lote'      , $_REQUEST['stDtLote']      );
                    $obTContabilidadeLote->inclusao();
                }

                /* LANÇA OS DÉBITOS  */
                if ($rsValoresDebito->getNumLinhas() > 0) {
                    $obErro = new Erro;
                    include_once(CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamento.class.php");
                    include_once(CAM_GF_CONT_MAPEAMENTO."TContabilidadeContaDebito.class.php");
                    $obTContabilidadeLancamento = new TContabilidadeLancamento;
                    $obTContabilidadeValorLancamento = new TContabilidadeValorLancamento;
                    $obTContabilidadeContaDebito = new TContabilidadeContaDebito;

                    while (!$rsValoresDebito->eof()) {
                        $obTContabilidadeLancamento->recuperaProximaSequencia($rsRecordSet);
                        $obTContabilidadeLancamento->setDado( 'sequencia'     , $rsRecordSet->getCampo('prox_seq')                        );
                        $obTContabilidadeLancamento->setDado( 'cod_lote'      , $_REQUEST['inCodLote']                                    );
                        $obTContabilidadeLancamento->setDado( 'tipo'          , 'M'                                                       );
                        $obTContabilidadeLancamento->setDado( 'exercicio'     , Sessao::getExercicio()                                    );
                        $obTContabilidadeLancamento->setDado( 'cod_entidade'  , $_REQUEST['inCodEntidade']                                );
                        $obTContabilidadeLancamento->setDado( 'cod_historico' , $rsValoresDebito->getCampo('inCodHistoricoDebito')        );
                        $obTContabilidadeLancamento->setDado( 'complemento'   , stripslashes($rsValoresDebito->getCampo('stComplementoDebito')) );
                        $obErro = $obTContabilidadeLancamento->inclusao();

                        if (!$obErro->ocorreu()) {
                            $obTContabilidadeValorLancamento->setDado( 'cod_lote'      , $_REQUEST['inCodLote']                   );
                            $obTContabilidadeValorLancamento->setDado( 'tipo'          , 'M'                                      );
                            $obTContabilidadeValorLancamento->setDado( 'sequencia'     , $rsRecordSet->getCampo('prox_seq')       );
                            $obTContabilidadeValorLancamento->setDado( 'exercicio'     , Sessao::getExercicio()                   );
                            $obTContabilidadeValorLancamento->setDado( 'tipo_valor'    , 'D'                                      );
                            $obTContabilidadeValorLancamento->setDado( 'cod_entidade'  , $_REQUEST['inCodEntidade']               );
                            $obTContabilidadeValorLancamento->setDado( 'vl_lancamento' , $rsValoresDebito->getCampo('nuVlDebito') );
                            $obErro = $obTContabilidadeValorLancamento->inclusao();

                            if (!$obErro->ocorreu()) {
                                $obTContabilidadeContaDebito->setDado( 'cod_lote'     , $_REQUEST['inCodLote']                          );
                                $obTContabilidadeContaDebito->setDado( 'tipo'         , 'M'                                             );
                                $obTContabilidadeContaDebito->setDado( 'sequencia'    , $rsRecordSet->getCampo('prox_seq')              );
                                $obTContabilidadeContaDebito->setDado( 'exercicio'    , Sessao::getExercicio()                          );
                                $obTContabilidadeContaDebito->setDado( 'tipo_valor'   , 'D'                                             );
                                $obTContabilidadeContaDebito->setDado( 'cod_entidade' , $_REQUEST['inCodEntidade']                      );
                                $obTContabilidadeContaDebito->setDado( 'cod_plano'    , $rsValoresDebito->getCampo('inCodContaDebito')  );
                                $obErro = $obTContabilidadeContaDebito->inclusao();
                            }
                        }
                        $rsValoresDebito->proximo();
                    }
                }

                /* LANÇA OS CRÉDITOS */
                if ($rsValoresCredito->getNumLinhas() > 0) {
                    $obErro = new Erro;
                    include_once(CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamento.class.php");
                    include_once(CAM_GF_CONT_MAPEAMENTO."TContabilidadeContaCredito.class.php");
                    $obTContabilidadeLancamento = new TContabilidadeLancamento;
                    $obTContabilidadeValorLancamento = new TContabilidadeValorLancamento;
                    $obTContabilidadeContaCredito = new TContabilidadeContaCredito;

                    while (!$rsValoresCredito->eof()) {
                        $obTContabilidadeLancamento->recuperaProximaSequencia($rsRecordSet);
                        $obTContabilidadeLancamento->setDado( 'sequencia'     , $rsRecordSet->getCampo('prox_seq')                          );
                        $obTContabilidadeLancamento->setDado( 'cod_lote'      , $_REQUEST['inCodLote']                                      );
                        $obTContabilidadeLancamento->setDado( 'tipo'          , 'M'                                                         );
                        $obTContabilidadeLancamento->setDado( 'exercicio'     , Sessao::getExercicio()                                      );
                        $obTContabilidadeLancamento->setDado( 'cod_entidade'  , $_REQUEST['inCodEntidade']                                  );
                        $obTContabilidadeLancamento->setDado( 'cod_historico' , $rsValoresCredito->getCampo('inCodHistoricoCredito')        );
                        $obTContabilidadeLancamento->setDado( 'complemento'   , stripslashes($rsValoresCredito->getCampo('stComplementoCredito')));
                        $obErro = $obTContabilidadeLancamento->inclusao();

                        if (!$obErro->ocorreu()) {
                            $obTContabilidadeValorLancamento->setDado( 'cod_lote'      , $_REQUEST['inCodLote']                         );
                            $obTContabilidadeValorLancamento->setDado( 'tipo'          , 'M'                                            );
                            $obTContabilidadeValorLancamento->setDado( 'sequencia'     , $rsRecordSet->getCampo('prox_seq')             );
                            $obTContabilidadeValorLancamento->setDado( 'exercicio'     , Sessao::getExercicio()                         );
                            $obTContabilidadeValorLancamento->setDado( 'tipo_valor'    , 'C'                                            );
                            $obTContabilidadeValorLancamento->setDado( 'cod_entidade'  , $_REQUEST['inCodEntidade']                     );
                            $obTContabilidadeValorLancamento->setDado( 'vl_lancamento' , '-'.$rsValoresCredito->getCampo('nuVlCredito') );
                            $obErro = $obTContabilidadeValorLancamento->inclusao();

                            if (!$obErro->ocorreu()) {
                                $obTContabilidadeContaCredito->setDado( 'cod_lote'     , $_REQUEST['inCodLote']                           );
                                $obTContabilidadeContaCredito->setDado( 'tipo'         , 'M'                                              );
                                $obTContabilidadeContaCredito->setDado( 'sequencia'    , $rsRecordSet->getCampo('prox_seq')               );
                                $obTContabilidadeContaCredito->setDado( 'exercicio'    , Sessao::getExercicio()                           );
                                $obTContabilidadeContaCredito->setDado( 'tipo_valor'   , 'C'                                              );
                                $obTContabilidadeContaCredito->setDado( 'cod_entidade' , $_REQUEST['inCodEntidade']                       );
                                $obTContabilidadeContaCredito->setDado( 'cod_plano'    , $rsValoresCredito->getCampo('inCodContaCredito') );
                                $obErro = $obTContabilidadeContaCredito->inclusao();
                            }
                        }
                        $rsValoresCredito->proximo();
                    }
                }

                if ($_REQUEST['boEmitirNota']) {
                    include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php");
                    $obREntidade = new ROrcamentoEntidade();
                    $rsEntidades = new RecordSet();
                    $obREntidade->setCodigoEntidade($_REQUEST['inCodEntidade']);
                    $obREntidade->listar( $rsEntidade , " ORDER BY cod_entidade" );

                    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
                    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
                    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

                    $preview = new PreviewBirt(2,9,9);
                    $preview->setTitulo('Nota de lançamento');
                    $preview->setVersaoBirt('2.5.0');
                    $preview->setFormato('pdf');

                    $preview->addParametro('entidade_codigo'    , $_REQUEST['inCodEntidade']);
                    $preview->addParametro('entidade_nome'      , $rsEntidade->getCampo('nom_cgm'));
                    $preview->addParametro('lote_nome'          , $_REQUEST['stNomLote']);
                    $preview->addParametro('lote_numero'        , $_REQUEST['inCodLote']);
                    $preview->addParametro('lote_data'          , $_REQUEST['stDtLote']);

                    // Gera código da nota de lançamento
                    include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php";
                    $rsConfiguracao  = new RecordSet();
                    $obTConfiguracao = new TAdministracaoConfiguracao();
                    $obTConfiguracao->setDado( "cod_modulo", 9 );
                    $obTConfiguracao->setDado( "parametro" ,"nota_lancamento" );
                    $obTConfiguracao->setDado( "exercicio" , Sessao::getExercicio() );
                    $obTConfiguracao->recuperaPorChave( $rsConfiguracao );

                    $notaLancamento = unserialize($rsConfiguracao->getCampo('valor'));
                    $notaLancamento[$_REQUEST['inCodEntidade']] = $notaLancamento[$_REQUEST['inCodEntidade']] + 1;
                    $codigoNota     = str_pad($notaLancamento[$_REQUEST['inCodEntidade']], 4, "0", STR_PAD_LEFT).'/'.Sessao::getExercicio();

                    $obTConfiguracao->setDado( "valor" , serialize($notaLancamento) );
                    $obTConfiguracao->alteracao();

                    $preview->addParametro('nome_relatorio'     , 'Nota de lançamento                                                              '.$codigoNota);

                    $stIncluirAssinaturas = $_REQUEST['stIncluirAssinaturas'];
                    if ($stIncluirAssinaturas == 'nao') {
                        $stIncluirAssinaturas = 'não';
                    } else {
                        $stIncluirAssinaturas = 'sim';
                    }

                    $preview->addAssinaturas(Sessao::read('assinaturas'));
                    $preview->preview();
                }

                if ($obErro->ocorreu()) {
                    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
                } else {
                    Sessao::remove('arValoresDebito');
                    Sessao::remove('arValoresCredito');
                    sistemaLegado::alertaAviso($pgForm."?stAcao=".$_REQUEST['stAcao'], $_REQUEST['inCodLote'] .'/'. Sessao::getExercicio() ,"incluir","aviso", Sessao::getId(), "../");
                }
             } else {
                sistemaLegado::exibeAviso( 'Informe os lançamentos para inclusão.',"n_incluir","erro" );
            }
        }
    break;

    case "alterar":

        Sessao::setTrataExcecao(true);
        $obErro = new Erro;

        include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadeValorLancamento.class.php" );
        include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamento.class.php"      );
        include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadeContaDebito.class.php"     );
        include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadeContaCredito.class.php"    );
        $obTContabilidadeValorLancamento = new TContabilidadeValorLancamento;
        $obTContabilidadeLancamento = new TContabilidadeLancamento;
        $obTContabilidadeContaDebito = new TContabilidadeContaDebito;
        $obTContabilidadeContaCredito = new TContabilidadeContaCredito;

        $arCreditosSalvos = Sessao::read('arCreditosSalvos');
        $arDebitosSalvos  = Sessao::read('arDebitosSalvos');

        $boExecuta = true;
        if ($boDebito == true and $boCredito == true) {
            if ($vlDiferenca == true) {
                $boExecuta = false;
                sistemaLegado::exibeAviso( 'Existe diferença de débito e crédito.',"n_incluir","erro" );
            }
        }

        if ($boExecuta) {
            /* A SEQUENCIA É UNICA POR LOTE */
            $arElementos = array_merge($arCreditosSalvos, $arDebitosSalvos);

            /* VERIFICA SE HÁ LANÇAMENTOS */
            if ($rsValoresDebito->getNumLinhas() > 0 or $rsValoresCredito->getNumLinhas() > 0 ) {

               /* EXCLUSAO DOS LANCAMENTOS */
               if (count($arElementos) > 0) {
                   $rsElementos = new RecordSet;
                   $rsElementos->preenche($arElementos);
                   /* EXCLUIU CONTAS DEBITO E CREDITO */
                   while (!$rsElementos->eof()) {
                       if ($rsElementos->getCampo('sequenciaCredito')) {
                          $stFiltro  = " WHERE exercicio    = '".Sessao::getExercicio()."' ";
                          $stFiltro .= "   AND cod_entidade = ".$_REQUEST['cod_entidade'];
                          $stFiltro .= "   AND tipo         = 'M'";
                          $stFiltro .= "   AND cod_lote     = ".$_REQUEST['cod_lote'];
                          $stFiltro .= "   AND sequencia    = ".$rsElementos->getCampo('sequenciaCredito');
                          $stFiltro .= "   AND tipo_valor   = 'C'";
                          $obTContabilidadeContaCredito->recuperaTodos($rsRecordSet, $stFiltro);
                          if ($rsRecordSet->getNumLinhas() > 0) {
                              $obTContabilidadeContaCredito->setDado( 'cod_lote'     , $_REQUEST['cod_lote']                              );
                              $obTContabilidadeContaCredito->setDado( 'tipo'         , 'M'                                                );
                              $obTContabilidadeContaCredito->setDado( 'sequencia'    , $rsElementos->getCampo('sequenciaCredito')         );
                              $obTContabilidadeContaCredito->setDado( 'exercicio'    , Sessao::getExercicio()                             );
                              $obTContabilidadeContaCredito->setDado( 'tipo_valor'   , 'C'                                                );
                              $obTContabilidadeContaCredito->setDado( 'cod_entidade' , $_REQUEST['cod_entidade']                          );
                              $obErro = $obTContabilidadeContaCredito->exclusao();
                          }
                      }
                      if ($rsElementos->getCampo('sequenciaDebito')) {
                          $stFiltro  = " WHERE exercicio    = '".Sessao::getExercicio()."'";
                          $stFiltro .= "   AND cod_entidade = ".$_REQUEST['cod_entidade'];
                          $stFiltro .= "   AND tipo         = 'M'";
                          $stFiltro .= "   AND cod_lote     = ".$_REQUEST['cod_lote'];
                          $stFiltro .= "   AND sequencia    = ".$rsElementos->getCampo('sequenciaDebito');
                          $stFiltro .= "   AND tipo_valor   = 'D'";
                          $obTContabilidadeContaDebito->recuperaTodos($rsRecordSet, $stFiltro);
                          if ($rsRecordSet->getNumLinhas() > 0) {
                              $obTContabilidadeContaDebito->setDado( 'cod_lote'     , $_REQUEST['cod_lote']                              );
                              $obTContabilidadeContaDebito->setDado( 'tipo'         , 'M'                                                );
                              $obTContabilidadeContaDebito->setDado( 'sequencia'    , $rsElementos->getCampo('sequenciaDebito')          );
                              $obTContabilidadeContaDebito->setDado( 'exercicio'    , Sessao::getExercicio()                             );
                              $obTContabilidadeContaDebito->setDado( 'tipo_valor'   , 'D'                                                );
                              $obTContabilidadeContaDebito->setDado( 'cod_entidade' , $_REQUEST['cod_entidade']                          );
                              $obErro = $obTContabilidadeContaDebito->exclusao();
                          }
                      }
                      $rsElementos->proximo();
                   }
                   /* EXCLUI OS VALORES DOS LANCAMENTOS */
                   $rsElementos->setPrimeiroElemento();
                   while (!$rsElementos->eof()) {
                      if ($rsElementos->getCampo('sequenciaCredito')) {
                          $obTContabilidadeValorLancamento->setDado( 'cod_lote'      , $_REQUEST['cod_lote']                     );
                          $obTContabilidadeValorLancamento->setDado( 'tipo'          , 'M'                                       );
                          $obTContabilidadeValorLancamento->setDado( 'sequencia'     , $rsElementos->getCampo('sequenciaCredito'));
                          $obTContabilidadeValorLancamento->setDado( 'exercicio'     , Sessao::getExercicio()                    );
                          $obTContabilidadeValorLancamento->setDado( 'tipo_valor'    , 'C'                                       );
                          $obTContabilidadeValorLancamento->setDado( 'cod_entidade'  , $_REQUEST['cod_entidade']                 );
                          $obErro = $obTContabilidadeValorLancamento->exclusao();
                      }
                      if ($rsElementos->getCampo('sequenciaDebito')) {
                          $obTContabilidadeValorLancamento->setDado( 'cod_lote'      , $_REQUEST['cod_lote']                     );
                          $obTContabilidadeValorLancamento->setDado( 'tipo'          , 'M'                                       );
                          $obTContabilidadeValorLancamento->setDado( 'sequencia'     , $rsElementos->getCampo('sequenciaDebito') );
                          $obTContabilidadeValorLancamento->setDado( 'exercicio'     , Sessao::getExercicio()                    );
                          $obTContabilidadeValorLancamento->setDado( 'tipo_valor'    , 'D'                                       );
                          $obTContabilidadeValorLancamento->setDado( 'cod_entidade'  , $_REQUEST['cod_entidade']                 );
                          $obErro = $obTContabilidadeValorLancamento->exclusao();
                      }
                      $rsElementos->proximo();
                   }
                   /* EXCLUI OS LANCAMENTOS */
                   if (!$obErro->ocorreu()) {
                       $rsElementos->setPrimeiroElemento();
                       while (!$rsElementos->eof()) {
                          if ($rsElementos->getCampo('sequenciaCredito')) {
                              $obTContabilidadeLancamento->setDado( 'exercicio'     , Sessao::getExercicio()                                    );
                              $obTContabilidadeLancamento->setDado( 'sequencia'     , $rsElementos->getCampo('sequenciaCredito')                );
                              $obTContabilidadeLancamento->setDado( 'cod_lote'      , $_REQUEST['cod_lote']                                     );
                              $obTContabilidadeLancamento->setDado( 'tipo'          , 'M'                                                       );
                              $obTContabilidadeLancamento->setDado( 'cod_entidade'  , $_REQUEST['cod_entidade']                                 );
                              $obErro = $obTContabilidadeLancamento->exclusao();
                          }
                          if ($rsElementos->getCampo('sequenciaDebito')) {
                              $obTContabilidadeLancamento->setDado( 'exercicio'     , Sessao::getExercicio()                                    );
                              $obTContabilidadeLancamento->setDado( 'sequencia'     , $rsElementos->getCampo('sequenciaCredito')                );
                              $obTContabilidadeLancamento->setDado( 'cod_lote'      , $_REQUEST['cod_lote']                                     );
                              $obTContabilidadeLancamento->setDado( 'tipo'          , 'M'                                                       );
                              $obTContabilidadeLancamento->setDado( 'cod_entidade'  , $_REQUEST['cod_entidade']                                 );
                              $obErro = $obTContabilidadeLancamento->exclusao();
                          }
                          $rsElementos->proximo();
                       }
                   }
               }
               /* INCLUSAO DOS LANÇAMENTOS */
               /* LANÇA OS DÉBITOS  */
               if ($rsValoresDebito->getNumLinhas() > 0) {
                  while (!$rsValoresDebito->eof()) {
                      if ($rsValoresDebito->getCampo('sequenciaDebito')) {
                          $inNumSequencia = $rsValoresDebito->getCampo('sequenciaDebito');
                      } else {
                          $obTContabilidadeLancamento->recuperaProximaSequencia($rsRecordSet);
                          $inNumSequencia = $rsRecordSet->getCampo('prox_seq');
                      }
                      $stFiltro  = " WHERE exercicio = '".Sessao::getExercicio()."'";
                      $stFiltro .= "   AND cod_entidade = ".$_REQUEST['cod_entidade'];
                      $stFiltro .= "   AND tipo = 'M'";
                      $stFiltro .= "   AND cod_lote = ".$_REQUEST['cod_lote'];
                      $stFiltro .= "   AND sequencia = ".$inNumSequencia;
                      $obTContabilidadeLancamento->recuperaTodos($rsRecordSet, $stFiltro);
                      if ($rsRecordSet->getNumLinhas() < 0) {

                          $obTContabilidadeLancamento->setDado( 'sequencia'     , $inNumSequencia                                           );
                          $obTContabilidadeLancamento->setDado( 'cod_lote'      , $_REQUEST['cod_lote']                                     );
                          $obTContabilidadeLancamento->setDado( 'tipo'          , 'M'                                                       );
                          $obTContabilidadeLancamento->setDado( 'exercicio'     , Sessao::getExercicio()                                    );
                          $obTContabilidadeLancamento->setDado( 'cod_entidade'  , $_REQUEST['cod_entidade']                                 );
                          $obTContabilidadeLancamento->setDado( 'cod_historico' , $rsValoresDebito->getCampo('inCodHistoricoDebito')        );
                          $obTContabilidadeLancamento->setDado( 'complemento'   , stripslashes($rsValoresDebito->getCampo('stComplementoDebito')));
                          $obErro = $obTContabilidadeLancamento->inclusao();
                      }
                      if (!$obErro->ocorreu()) {
                          $obTContabilidadeValorLancamento->setDado( 'cod_lote'      , $_REQUEST['cod_lote']                    );
                          $obTContabilidadeValorLancamento->setDado( 'tipo'          , 'M'                                      );
                          $obTContabilidadeValorLancamento->setDado( 'sequencia'     , $inNumSequencia                          );
                          $obTContabilidadeValorLancamento->setDado( 'exercicio'     , Sessao::getExercicio()                   );
                          $obTContabilidadeValorLancamento->setDado( 'tipo_valor'    , 'D'                                      );
                          $obTContabilidadeValorLancamento->setDado( 'cod_entidade'  , $_REQUEST['cod_entidade']                );
                          $obTContabilidadeValorLancamento->setDado( 'vl_lancamento' , $rsValoresDebito->getCampo('nuVlDebito') );
                          $obErro = $obTContabilidadeValorLancamento->inclusao();
                          if (!$obErro->ocorreu()) {
                              $obTContabilidadeContaDebito->setDado( 'cod_lote'     , $_REQUEST['cod_lote']                           );
                              $obTContabilidadeContaDebito->setDado( 'tipo'         , 'M'                                             );
                              $obTContabilidadeContaDebito->setDado( 'sequencia'    , $inNumSequencia                                 );
                              $obTContabilidadeContaDebito->setDado( 'exercicio'    , Sessao::getExercicio()                          );
                              $obTContabilidadeContaDebito->setDado( 'tipo_valor'   , 'D'                                             );
                              $obTContabilidadeContaDebito->setDado( 'cod_entidade' , $_REQUEST['cod_entidade']                       );
                              $obTContabilidadeContaDebito->setDado( 'cod_plano'    , $rsValoresDebito->getCampo('inCodContaDebito')  );
                              $obErro = $obTContabilidadeContaDebito->inclusao();
                              $rsValoresDebito->proximo();
                          }
                      }
                  }
               }
               /* LANÇA OS CRÉDITOS  */
               if ($rsValoresCredito->getNumLinhas() > 0) {

                  while (!$rsValoresCredito->eof()) {
                      if ($rsValoresCredito->getCampo('sequenciaCredito')) {
                          $inNumSequencia = $rsValoresCredito->getCampo('sequenciaCredito');
                      } else {
                          $obTContabilidadeLancamento->recuperaProximaSequencia($rsRecordSet);
                          $inNumSequencia = $rsRecordSet->getCampo('prox_seq');
                      }
                      $stFiltro  = " WHERE exercicio = '".Sessao::getExercicio()."'";
                      $stFiltro .= "   AND cod_entidade = ".$_REQUEST['cod_entidade'];
                      $stFiltro .= "   AND tipo = 'M'";
                      $stFiltro .= "   AND cod_lote = ".$_REQUEST['cod_lote'];
                      $stFiltro .= "   AND sequencia = ".$inNumSequencia;
                      $obTContabilidadeLancamento->recuperaTodos($rsRecordSet, $stFiltro);
                      if ($rsRecordSet->getNumLinhas() <  0) {
                          $obTContabilidadeLancamento->setDado( 'sequencia'     , $inNumSequencia                                            );
                          $obTContabilidadeLancamento->setDado( 'cod_lote'      , $_REQUEST['cod_lote']                                      );
                          $obTContabilidadeLancamento->setDado( 'tipo'          , 'M'                                                        );
                          $obTContabilidadeLancamento->setDado( 'exercicio'     , Sessao::getExercicio()                                     );
                          $obTContabilidadeLancamento->setDado( 'cod_entidade'  , $_REQUEST['cod_entidade']                                  );
                          $obTContabilidadeLancamento->setDado( 'cod_historico' , $rsValoresCredito->getCampo('inCodHistoricoCredito')       );

  $obTContabilidadeLancamento->setDado( 'complemento'   , stripslashes($rsValoresCredito->getCampo('stComplementoCredito')));
                          $obErro = $obTContabilidadeLancamento->inclusao();
                      }
                      $obTContabilidadeValorLancamento->setDado( 'cod_lote'      , $_REQUEST['cod_lote']                     );
                      $obTContabilidadeValorLancamento->setDado( 'tipo'          , 'M'                                       );
                      $obTContabilidadeValorLancamento->setDado( 'sequencia'     , $inNumSequencia                           );
                      $obTContabilidadeValorLancamento->setDado( 'exercicio'     , Sessao::getExercicio()                    );
                      $obTContabilidadeValorLancamento->setDado( 'tipo_valor'    , 'C'                                       );
                      $obTContabilidadeValorLancamento->setDado( 'cod_entidade'  , $_REQUEST['cod_entidade']                 );
                      $obTContabilidadeValorLancamento->setDado( 'vl_lancamento' , '-'.$rsValoresCredito->getCampo('nuVlCredito'));
                      $obErro = $obTContabilidadeValorLancamento->inclusao();

                      $obTContabilidadeContaCredito->setDado( 'cod_lote'     , $_REQUEST['cod_lote']                           );
                      $obTContabilidadeContaCredito->setDado( 'tipo'         , 'M'                                             );
                      $obTContabilidadeContaCredito->setDado( 'sequencia'    , $inNumSequencia                                 );
                      $obTContabilidadeContaCredito->setDado( 'exercicio'    , Sessao::getExercicio()                          );
                      $obTContabilidadeContaCredito->setDado( 'tipo_valor'   , 'C'                                             );
                      $obTContabilidadeContaCredito->setDado( 'cod_entidade' , $_REQUEST['cod_entidade']                       );
                      $obTContabilidadeContaCredito->setDado( 'cod_plano'    , $rsValoresCredito->getCampo('inCodContaCredito'));

                      $obErro = $obTContabilidadeContaCredito->inclusao();

                      $rsValoresCredito->proximo();
                  }
               }
               if ($obErro->ocorreu()) {
                   SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
               } else {
                   Sessao::remove('arValoresDebito');
                   Sessao::remove('arValoresCredito');
                   sistemaLegado::alertaAviso($pgList."?stAcao=".$_REQUEST['stAcao'], $_REQUEST['cod_lote'] .'/'. Sessao::getExercicio() ,"alterar","aviso", Sessao::getId(), "../");
               }
            } else {
               sistemaLegado::exibeAviso( 'Informe os lançamentos para inclusão.',"n_incluir","erro" );
            }
        }
        Sessao::encerraExcecao();
    break;

}

?>
