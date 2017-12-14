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
    * Página de processamento para o Lançamento do Imposto de Transferência
    * Data de Criação   : 04/10/2006

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: PRLancarTransferencia.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.21
*/

/*
$Log$
Revision 1.3  2007/04/16 18:06:30  cercato
Bug #9132#

Revision 1.2  2006/11/27 10:24:33  cercato
setando cgm do contribuinte no lancamento de transferencia.

Revision 1.1  2006/10/10 15:17:57  cercato
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMTransferencia.class.php"  );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php"   );
include_once ( CAM_GT_ARR_NEGOCIO."RARRLancamentoTransferencia.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRCalculo.class.php");

$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma = "LancarTransferencia";
$pgFilt = "FL".$stPrograma.".php?stAcao=$stAcao";
$pgList = "LS".$stPrograma.".php?stAcao=$stAcao";
$pgForm = "FM".$stPrograma.".php?stAcao=$stAcao";

$obRCIMTransferencia = new RCIMTransferencia;
$obRARRLancamentoTransferencia = new RARRLancamentoTransferencia;
$obRARRCalculo = new RARRCalculo;
$obErro = new Erro;

$obRCIMConfiguracao  = new RCIMConfiguracao;
$obRCIMConfiguracao->consultarConfiguracao();
$stMascaraInscricao = $obRCIMConfiguracao->getMascaraIM();
$inInscricaoMascara = str_pad($_REQUEST["inInscricaoImobiliaria"],strlen($stMascaraInscricao),"0",STR_PAD_LEFT);
$arDocumentos = Sessao::read( "Documentos" );
$inTotArray = count( $arDocumentos ) - 1;
for ($inCount = 0; $inCount <= $inTotArray; $inCount ++) {
     if ( isset( $_REQUEST['boEntregue_'.($inCount+1)] ) ) {
         $arDocumentos[$inCount]['entregue'] = 't';
     }
}

Sessao::write( "Documentos", $arDocumentos );

function VerificaDocumentacaoEntregue($listaDocumentos)
{
    $contEntregue = 0;
    $cont = 0;
    $tam = count ( $listaDocumentos );

    while ($cont < $tam) {
        if ($listaDocumentos[$cont]['entregue'] == 'f' && $listaDocumentos[$cont]['obrigatorio'] != 'Não') {
            return false;
        }

        $cont++;
    }

    return true;
}

switch ($stAcao) {
    case "incluir":
        if ( ($_REQUEST['boEmissaoCarne'] == "sim")  && (!$_REQUEST['stArquivo']) ) {
            SistemaLegado::exibeAviso( "Campo Modelo de Carnê inválido.", "n_incluir", "erro");
            exit;
        }
//----------------------------------------------------------------------------

// dados da parcela
        $arParcelas = array();
        $arParcelas[0] = array (   'stTipoParcela' => '1',
                                    'dtVencimento'  => $_REQUEST['dtDataVencimento']
        );

        Sessao::write( "parcelas", $arParcelas );
        $arProprietario = Sessao::read( "proprietario" );

        $arAdquirentes = Sessao::read("Adquirentes");

        //$_REQUEST['dtVencimento'] //usado assim mesmo no calculo
        $obRARRCalculo->obRCIMImovel->inNumeroInscricao = $_REQUEST["inInscricaoImobiliaria"];
        $obRARRCalculo->obRCIMImovel->addProprietario();
        $obRARRCalculo->obRCIMImovel->roUltimoProprietario->inNumeroCGMInicial = $arProprietario["cgm"];
        $obRARRCalculo->obRCIMImovel->roUltimoProprietario->inNumeroCGMFinal = $arProprietario["cgm"];

        $obRARRCalculo->setParametros($boInformado);
        $obRARRCalculo->setExercicio( Sessao::getExercicio() );
        $obRARRCalculo->obRCGM->setNumCGM ( $arProprietario["cgm"] );
        $obRARRCalculo->obRModulo->setCodModulo( Sessao::read('modulo') );
        $obRARRCalculo->obRMONCredito->setDescricao('I.T.B.I.');

//-----------------------------------------------------------------------

        //dados para imovel_valor_venal
        $obAtributos = new MontaAtributos;
        $obAtributos->setName( "Atributo_" );
        $obAtributos->recuperaVetor( $arChave );

        foreach ($arChave as $key=>$value) {
            $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
            $inCodAtributo = $arChaves[0];
            if ( is_array($value) ) {
                $value = implode(",",$value);
            }

            $obRARRLancamentoTransferencia->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo, $value );
        }

//deve colocar o cgm do contribuinte e nao o do usuario do siam.
        if ($arAdquirentes != '') {
          $obRARRLancamentoTransferencia->setInNumCGM ( $arAdquirentes );
        } else {
          $obRARRLancamentoTransferencia->setInNumCGM ( $arProprietario["cgm"] );
        }
        $obRARRLancamentoTransferencia->obRCIMImovel->setNumeroInscricao( $_REQUEST["inInscricaoImobiliaria"] );
        $obRARRLancamentoTransferencia->setflVenalTerritorialDeclarado( $_REQUEST["flTerritorialDeclarado"] );
        $obRARRLancamentoTransferencia->setflVenalPredialDeclarado( $_REQUEST["flPredialDeclarado"] );
        $obRARRLancamentoTransferencia->setflVenalTotalDeclarado( $_REQUEST["flTotalDeclarado"] );

        $obRARRLancamentoTransferencia->setflVenalTerritorialAvaliado( $_REQUEST["flTerritorialAvaliado"] );
        $obRARRLancamentoTransferencia->setflVenalPredialAvaliado( $_REQUEST["flPredialAvaliado"] );
        $obRARRLancamentoTransferencia->setflVenalTotalAvaliado( $_REQUEST["flTotalAvaliado"] );
        $obRARRLancamentoTransferencia->setExercicio( Sessao::getExercicio() );
        if ($_REQUEST["boFinanciamento"] == "sim") {
            $obRARRLancamentoTransferencia->setflValorFinanciado ( $_REQUEST["flValorFinanciado"] );
            $obRARRLancamentoTransferencia->setflAliquotaValorFinanciado ( $_REQUEST["flFinanciadoAliquota"] );
        }

        $obRARRLancamentoTransferencia->setflAliquotaValorAvaliado ( $_REQUEST["flTotalAliquota"] );

        $obRARRLancamentoTransferencia->obCalculo = $obRARRCalculo;
//------------------------------------------

        list($inProcesso, $inExercicio) = explode("/", $_POST['inProcesso'] );

        $obRCIMTransferencia->setEfetivacao         ( 'c' );//'f' );
        $obRCIMTransferencia->setInscricaoMunicipal ( $_REQUEST["inInscricaoImobiliaria"] );
        $obRCIMTransferencia->setCodigoNatureza     ( $_REQUEST["inCodigoNatureza"      ] );
        $obRCIMTransferencia->setProcesso           ( $inProcesso                         );
        $obRCIMTransferencia->setExercicioProcesso  ( $inExercicio                        );
        $obRCIMTransferencia->obRCIMCorretagem->setRegistroCreci( $_REQUEST["stCreci"   ] );
        $obRCIMTransferencia->setDocumentos         ( Sessao::read( 'Documentos' ) );
        $obRCIMTransferencia->setAdquirentes        ( Sessao::read( 'Adquirentes' ) );

        $obRCIMTransferencia->listarTransferencia( $rsRecordSet );

        $boIncluirTransferencia = true;
        if ( $rsRecordSet->getNumLinhas() > 0 && $rsRecordSet->getCampo("dt_efetivacao") == "" && $rsRecordSet->getCampo("dt_cancelamento") == "" ) {
            $boIncluirTransferencia = false;
        }

//ativar transacao
        $obTransacao = new Transacao;
        $boFlagTransacao = true;
        $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
            if ( !VerificaDocumentacaoEntregue( Sessao::read( 'Documentos' ) ) )
                $obErro->setDescricao ('Documentos obrigatórios não foram entregues!');

            if( $boIncluirTransferencia && !$obErro->ocorreu() )
                $obErro = $obRCIMTransferencia->cadastrarTransferencia( $boTransacao );

            if ( !$obErro->ocorreu() )
                $obErro = $obRARRLancamentoTransferencia->lancarImovel( $boTransacao );
        $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );
//fim transacao

        if ( $_REQUEST['boEmissaoCarne'] == "sim" && !$obErro->ocorreu() ) {
            include_once ( CAM_GT_ARR_NEGOCIO."RARRCarne.class.php");
            $obRARRCarne = new RARRCarne;
            $obRARRCarne->inInscricaoImobiliariaInicial = $_REQUEST["inInscricaoImobiliaria"];
            $obRARRCarne->setExercicio ( Sessao::getExercicio() );
            $obRARRCarne->obRARRParcela->roRARRLancamento->inCodLancamento = $obRARRLancamentoTransferencia->obCalculo->obRARRLancamento->inCodLancamento;
            include_once 'PREmitirCarne.php';
            exit;
        }

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgFilt,"Inscrição Imobiliária: ".$inInscricaoMascara,"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }

        break;
}
?>
