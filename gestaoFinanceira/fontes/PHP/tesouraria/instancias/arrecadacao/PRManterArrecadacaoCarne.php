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
    * Página de Processamento para Arrecadacao do módulo Tesouraria
    * Data de Criação   : 18/11/2005

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 24405 $
    $Name$
    $Author: domluc $
    $Date: 2007-07-31 11:03:07 -0300 (Ter, 31 Jul 2007) $

    * Casos de uso: uc-02.04.34
*/

/*
$Log$
Revision 1.9  2007/07/31 14:03:07  domluc
Ajuste de Caso de Uso

Revision 1.8  2007/07/27 00:18:31  domluc
Adicionadas verificações:
  Se carne ja foi pago
  Se carne tem creditos sem receitas/plano vinculado

Revision 1.7  2007/07/25 16:14:18  domluc
Atualizado Arr por Carne

Revision 1.6  2006/07/05 20:38:50  cleisson
Adicionada tag Log aos arquivos

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php" );
include_once(CAM_GF_TES_NEGOCIO."RTesourariaConfiguracao.class.php" );

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterArrecadacaoCarne";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgAutenticacao = "../autenticacao/FMManterAutenticacao.php";

/******************
* MINI LIB
******************/

/* funcao assistiva para passar array para objeto
thanks to :
http://www.phpfreaks.com/quickcode/Array-to-Object/541.php
*/
function array2object($arg_array)
{
  $tmp = new stdClass;
  foreach ($arg_array as $key => $value):
    if (is_array($value)) {
        $tmp->$key = arr2obj($value);
    } else {
      if (is_numeric($key)) {
        die("Cannot turn numeric arrays into objects!");
      }

    $tmp->$key = $value;
    }
  endforeach;

  return $tmp;
}
/**
*  Recebe Objeto de Erro e verifica se ocorreu,
*  se ocorreu envia messagem ao usuario e sai
*/
function VerificaErro($obErro)
{
  if (is_object($obErro)) {
    if ($obErro->ocorreu()) {
      SistemaLegado::exibeAviso(urlencode( $obErro->getDescricao()),"n_erro","erro",Sessao::getId(), "../" );
      exit('ERRO:!<br>' . $obErro->getDescricao());
    }
  }
}

// cria classe temporaria para pagamento;
$Pagamento = new stdClass;

/*****************
* FIM DO MINI LIB
*****************/

switch ($stAcao) {
case 'incluir':
  /**
  * Validações
  */
  if (!$_REQUEST['inCodBoletim']) {
    SistemaLegado::exibeAviso(urlencode(" <i><b>Boletim</b></i> deve ser selecionado! "),"n_alterar","erro");
    exit;
  } else {
    $nuValorRecebido = floatval(str_replace(',','.',str_replace('.','', $_REQUEST['nuVlRecebido'] ) ) );
    $nuValorTotalGeral = floatval(str_replace(',','.',str_replace('.','', $_REQUEST['hdnVlTotalLista'] ) ) );

    if ($nuValorRecebido < $nuValorTotalGeral) {
      SistemaLegado::exibeAviso(urlencode(" <i><b>Valor Recebido</b></i> deve ser maior que o valor total dos carnês! "),"n_alterar","erro");
      exit;
    } else {
      $arItens = Sessao::read('arItens');
      $inCount = count($arItens);
      if ($inCount < 1) {
        exit;
      }
    }
  }

  // seperar boletim
  list($inCodBoletimAberto,$stDtBoletimAberto) = explode ( ':' , $_REQUEST['inCodBoletim']);

    /* carregar arquivos de classes necessarios */
    /* GF */
    require_once( CAM_GF_TES_MAPEAMENTO . "TTesourariaAutenticacao.class.php");
    require_once( CAM_GF_TES_MAPEAMENTO . "TTesourariaArrecadacao.class.php");
    require_once( CAM_GF_TES_MAPEAMENTO . "TTesourariaArrecadacaoReceita.class.php");
    require_once( CAM_GF_TES_MAPEAMENTO . "TTesourariaArrecadacaoCarne.class.php");
    require_once( CAM_GF_TES_MAPEAMENTO . "TTesourariaTransferencia.class.php");
    require_once( CAM_GF_CONT_MAPEAMENTO. "TContabilidadeValorLancamento.class.php" );
    require_once( CAM_GF_CONT_MAPEAMENTO. "TContabilidadePlanoBanco.class.php" );
    require_once( CAM_GF_CONT_MAPEAMENTO. "TContabilidadeLote.class.php" );
    /* GT*/
    require_once( CAM_GT_ARR_NEGOCIO."RARRPagamento.class.php");
    require_once( CAM_GT_ARR_NEGOCIO."RARRCarne.class.php");

  /* inicializar instancias */
    $obErro = new Erro();
    $obTransacao = new Transacao;
    $obTAutenticacao = new TTesourariaAutenticacao();
    $obTArrecadacao = new TTesourariaArrecadacao();
    $obTArrecadacaoReceita = new TTesourariaArrecadacaoReceita();
    $obTArrecadacaoCarne = new TTesourariaArrecadacaoCarne();
    $obTTransferencia =  new TTesourariaTransferencia();
    $obTContabilidadeValorLancamento = new TContabilidadeValorLancamento;
    $obTContabilidadePlanoBanco = new TContabilidadePlanoBanco;
    $obRARRPagamento = new RARRPagamento;
    $obRARRCarne = new RARRCarne;

    // inicializar algumas variaveis do request
    $inCodTerminal = $_REQUEST['inCodTerminal'];
    $stTimestampTerminal = $_REQUEST['stTimestampTerminal'];
    $stTimestampUsuario = $_REQUEST['stTimestampUsuario'];

    // por compatibilidade com as regras de negocio,
    // não podemos tratar a excecao no formato que usa somente mapeamento
    Sessao::setTrataExcecao( false );

    // por isso abrimos a transacao manualmente
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
  VerificaErro($obErro);

  // gerar timestamp de arrecadação pelo boletim
    list( $stDia, $stMes, $stAno ) = explode( '/', $stDtBoletimAberto );
    $stTimestampArrecadacao = $stAno.'-'.$stMes.'-'.$stDia.' '.date('H:i:s.ms');

    // traz itens da sessao
    $arItens = Sessao::read('arItens');

    // looooopp
    foreach($arItens as $Carne):
      // inicializar algumas variaveis
      $obCarne = $rsConvenio = $nuValorPagamento = null;

      // converte array com dados do carne para objeto generico
    $obCarne = array2object($Carne);

    // supri alguns dados para facilitar acesso
    $obCarne->cod_entidade = $_REQUEST['inCodEntidade'];

      /**
      * baixar carne na arrecadação da GT
      */
      $nuValorPagamento = str_replace( ".", "", $obCarne->vl_total );
    $obCarne->vl_total = str_replace( ",", ".", $nuValorPagamento );

    // consultar convenio da numeracao do carne
    $obRARRCarne->setNumeracao($obCarne->cod_carne);
    $obRARRCarne->recuperaConvenio($rsConvenio,$boTransacao);
    $obErro = $obCarne->cod_convenio = $rsConvenio->getCampo("cod_convenio");
    VerificaErro($obErro);

    // consultar banco/agencia
    $obTContabilidadePlanoBanco->setDado("cod_plano",$obCarne->cod_plano);
    $obTContabilidadePlanoBanco->setDado("exercicio",$obCarne->exercicio);
    $obTContabilidadePlanoBanco->setDado("cod_entidade",$obCarne->cod_entidade);
    $obErro = $obTContabilidadePlanoBanco->recuperaContaBanco($rsPlanoBanco,'',$boTransacao);
    VerificaErro($obErro);

    $obCarne->cod_agencia = $rsPlanoBanco->getCampo("cod_agencia");
    $obCarne->cod_banco = $rsPlanoBanco->getCampo("cod_banco");

    // consultar se ja houve pagamento deste carne
    $obRARRPagamento->obTARRPagamento->setDado('numeracao',$obCarne->cod_carne);
    $obRARRPagamento->obTARRPagamento->setDado('cod_convenio',$obCarne->cod_convenio);
    $stFiltroVP = " where numeracao='".$obCarne->cod_carne."' and cod_convenio = " . $obCarne->cod_convenio ." ";
    $obRARRPagamento->obTARRPagamento->recuperaTodos($rsVerificaPagamentos,$stFiltroVP,'',$boTransacao);
    if ( $rsVerificaPagamentos->getNumLinhas() > 0) {
      $obErro->setDescricao('Carnê '.$obCarne->cod_carne.' ja foi pago!');
      VerificaErro($obErro);
    }
//        $obRARRPagamento->obTARRPagamento->debug();
//        99990000000650722
//        exit('minha pica');

    // seta dados para baixa na gt
    $obRARRPagamento->setExercicio                      ( $obCarne->exercicio );
        $obRARRPagamento->setDataLote 											(	date('Y-m-d')			  );
    $obRARRPagamento->setDataPagamento                  ( $stDtBoletimAberto  );
    $obRARRPagamento->setObservacao                     ( $obCarne->observacao );
    $obRARRPagamento->setValorPagamento                 ( $obCarne->vl_total   );
    $obRARRPagamento->obRARRCarne->setNumeracao         ( $obCarne->cod_carne  );
    $obRARRPagamento->obRARRCarne->setExercicio         ( $obCarne->exercicio  );
    $obRARRPagamento->obRARRCarne->obRMONConvenio->setCodigoConvenio( $obCarne->cod_convenio );
    $obRARRPagamento->obRARRTipoPagamento->setCodigoTipo( 20 ); // tipo fixo = Baixa pela Tesouraria
    $obRARRPagamento->obRARRTipoPagamento->setPagamento ( true ); // é pagamento
    $obRARRPagamento->obRMONAgencia->setCodAgencia      ( $obCarne->cod_agencia );
    $obRARRPagamento->obRMONBanco->setCodBanco          ( $obCarne->cod_banco );

    $obErro = $obRARRPagamento->efetuarPagamentoManual( $boTransacao // transacao
                                            , FALSE // não é baixa automatica
                                            , TRUE // fecha a baixa por cada carne
                                            , $nuTotal ); // retornara com o total pago
    VerificaErro($obErro);

    /**
    * Buscar lista de creditos com suas
    * respectivas receitas do carne inserido ai emcima.
    */
    $obTArrecadacaoCarne->setDado("numeracao",$obCarne->cod_carne);
    $obTArrecadacaoCarne->setDado("cod_convenio",$obCarne->cod_convenio);
    $obTArrecadacaoCarne->setDado("exercicio",$obCarne->exercicio);
    $obErro = $obTArrecadacaoCarne->recuperaFuncaoListaPagamentosNumeracao($rsListaPagamentosDaNumeracao,'','',$boTransacao);
    VerificaErro($obErro);

    /**
    * Inserir arrecadações dos pagamentos da GT da tesouraria.
    */
    while(!$rsListaPagamentosDaNumeracao->eof()):
      // preenche classe temporaria de pagamento com dados do pagamento corrente
      $Pagamento->numeracao = $rsListaPagamentosDaNumeracao->getCampo("numeracao");
      $Pagamento->cod_convenio = $rsListaPagamentosDaNumeracao->getCampo("cod_convenio");
      $Pagamento->exercicio = $rsListaPagamentosDaNumeracao->getCampo("exercicio");
      $Pagamento->tipo = $rsListaPagamentosDaNumeracao->getCampo("tipo");
      $Pagamento->codigo = $rsListaPagamentosDaNumeracao->getCampo("codigo");
      $Pagamento->soma = $rsListaPagamentosDaNumeracao->getCampo("soma");

      // busca proximo codigo de autenticacao
        $obErro = $obTAutenticacao->proximoCod($inCodAutenticacao,$boTransacao);
      VerificaErro($obErro);

      // inserir autenticacao da arrecadação
        $obTAutenticacao->setDado("cod_autenticacao" , $inCodAutenticacao );
        $obTAutenticacao->setDado("dt_autenticacao" , $stDtBoletimAberto );
        $obTAutenticacao->setDado("tipo" , "T");
        $obErro = $obTAutenticacao->inclusao($boTransacao);
      VerificaErro($obErro);

      // busca proximo codigo de arrecadação
        $obErro = $obTArrecadacao->proximoCod($inCodArrecadacao,$boTransacao);
      VerificaErro($obErro);

      // inserir arrecadação
        $obTArrecadacao->setDado( "cod_arrecadacao" , $inCodArrecadacao );
        $obTArrecadacao->setDado( "exercicio" , $obCarne->exercicio );
        $obTArrecadacao->setDado( "cod_autenticacao" , $inCodAutenticacao );
        $obTArrecadacao->setDado( "cod_boletim" , $inCodBoletimAberto );
        $obTArrecadacao->setDado( "dt_autenticacao" , $stDtBoletimAberto );
        $obTArrecadacao->setDado( "cod_terminal" , $inCodTerminal );
        $obTArrecadacao->setDado( "timestamp_arrecadacao" , $stTimestampArrecadacao );
        $obTArrecadacao->setDado( "timestamp_terminal" , $stTimestampTerminal );
        $obTArrecadacao->setDado( "cgm_usuario" , Sessao::read('numCgm') );
        $obTArrecadacao->setDado( "timestamp_usuario" , $stTimestampUsuario );
        $obTArrecadacao->setDado( "cod_plano" , $obCarne->cod_plano );
        $obTArrecadacao->setDado( "cod_entidade" , $obCarne->cod_entidade );
        $obTArrecadacao->setDado( "observacao" , $obCarne->observacao );
        $obErro = $obTArrecadacao->inclusao($boTransacao);
        VerificaErro($obErro);

      // verifica se é uma arrecadação extra ou orçamentaria
            switch ($Pagamento->tipo) {
              // caso de um pagamento com arrecadação extra-orçamentaria
                case 'extra':
                    // inserir valores na contabilidade
                    $obTContabilidadeValorLancamento->setDado( "cod_lote",'');
                    $obTContabilidadeValorLancamento->setDado( "tipo",'T');
                    $obTContabilidadeValorLancamento->setDado( "exercicio",$obCarne->exercicio);
                    $obTContabilidadeValorLancamento->setDado( "cod_entidade",$obCarne->cod_entidade);
                    $obTContabilidadeValorLancamento->setDado( "cod_plano_deb" , $Pagamento->codigo );
                    $obTContabilidadeValorLancamento->setDado( "cod_plano_cred", $obCarne->cod_plano ); // cod_plano escolhido na interface
                    $obTContabilidadeValorLancamento->setDado( "cod_historico",907);
                    $obTContabilidadeValorLancamento->setDado( "nom_lote","Arrecadação de Carne pela Tesouraria - CD:".$Pagamento->codigo." | CC:".$obCarne->cod_plano );
                    $obTContabilidadeValorLancamento->setDado( "complemento", "");
                    $obTContabilidadeValorLancamento->setDado( "vl_lancamento",$Pagamento->soma);
                    $obErro = $obTContabilidadeValorLancamento->inclusaoPorPl($rsRecordSet,$boTransacao);
                    VerificaErro($obErro);

                    // buscar dados do lote da contabilidade
                    $obTContabilidadeLote = new TContabilidadeLote();
                    $obTContabilidadeLote->setDado("cod_entidade",$obCarne->cod_entidade);
                    $obTContabilidadeLote->setDado("exercicio",$obCarne->exercicio);
                    $obTContabilidadeLote->setDado("tipo", "T");
                    $obErro = $obTContabilidadeLote->recuperaUltimoLotePorEntidade($rsLoteInserido,'','',$boTransacao);
                    VerificaErro($obErro);

                    $inCodLote = $rsLoteInserido->getCampo("cod_lote");

                    $obTTransferencia->setDado("cod_lote", $inCodLote);
                    $obTTransferencia->setDado("exercicio",$obCarne->exercicio);
                    $obTTransferencia->setDado("cod_entidade",$obCarne->cod_entidade);
                    $obTTransferencia->setDado("tipo",'T');
                    $obTTransferencia->setDado("cod_autenticacao",$inCodAutenticacao);
                    $obTTransferencia->setDado("dt_autenticacao",$stDtBoletimAberto);
                    $obTTransferencia->setDado("cod_boletim",$inCodBoletimAberto);
                    $obTTransferencia->setDado("cod_historico",907);
            $obTTransferencia->setDado( "cod_terminal" , $inCodTerminal );
            $obTTransferencia->setDado( "timestamp_terminal" , $stTimestampTerminal );
            $obTTransferencia->setDado( "timestamp_usuario" , $stTimestampUsuario );
            $obTTransferencia->setDado( "cgm_usuario" , Sessao::read('numCgm' ));
                $obTTransferencia->setDado("observacao","Arrecadação de Carnê pela Tesouraria");
                    $obTTransferencia->setDado("cod_plano_credito",$obCarne->cod_plano);
                    $obTTransferencia->setDado("cod_plano_debito",$Pagamento->codigo);
                    $obTTransferencia->setDado("valor",$Pagamento->soma);
                    $obTTransferencia->setDado("cod_tipo",2);
                    $obErro = $obTTransferencia->inclusao($boTransacao);
                    VerificaErro($obErro);
                break;
              // caso de um pagamento com arrecadação orçamentaria
                case 'orc':
                $obTArrecadacaoReceita->setDado( "cod_arrecadacao",$inCodArrecadacao);
                $obTArrecadacaoReceita->setDado( "cod_receita",$Pagamento->codigo);
                $obTArrecadacaoReceita->setDado( "timestamp_arrecadacao",$stTimestampArrecadacao);
                $obTArrecadacaoReceita->setDado( "exercicio",$obCarne->exercicio);
                $obTArrecadacaoReceita->setDado( "vl_arrecadacao",$Pagamento->soma);
                $obErro = $obTArrecadacaoReceita->inclusao($boTransacao);
                VerificaErro($obErro);

                break;
            } // end switch

            // vincular arrecadação com o carne
            $obTArrecadacaoCarne->setDado("numeracao",$obCarne->cod_carne);
            $obTArrecadacaoCarne->setDado("cod_convenio",$obCarne->cod_convenio);
            $obTArrecadacaoCarne->setDado("cod_arrecadacao",$inCodArrecadacao);
            $obTArrecadacaoCarne->setDado("timestamp_arrecadacao",$stTimestampArrecadacao);
            $obTArrecadacaoCarne->setDado("exercicio",$obCarne->exercicio);
        $obErro = $obTArrecadacaoCarne->inclusao($boTransacao);
            VerificaErro($obErro);

      $rsListaPagamentosDaNumeracao->proximo(); // proximo pagamento, please!

    endwhile;  // fim do loop dos pagamentos por numeracao

    $inNumeroDeArrecadacoes = $rsListaPagamentosDaNumeracao->getNumLinhas();

    endforeach; // fim do loop das numerações

    // fechar transação
  $obErro = $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTArrecadacaoCarne );
  VerificaErro($obErro);

  // avisa usuario do sucesso !
  SistemaLegado::alertaAviso($pgForm."?".Sessao::getId(),$inNumeroDeArrecadacoes.' Arrecadação(ões) incluida(s) no Boletim ' . $stDtBoletimAberto,"incluir","aviso", Sessao::getId(), "../");

break;
case 'estornar':
  SistemaLegado::debugRequest();
break;
}
