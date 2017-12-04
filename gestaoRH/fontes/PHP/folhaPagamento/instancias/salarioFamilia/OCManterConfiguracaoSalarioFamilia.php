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
    * Página Oculta do Gerar Assentamento
    * Data de Criação   : 26/04/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Andre Almeida

    * @ignore
    $Revision: 31475 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $

    * Caso de uso: uc-04.05.44

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

function montaSessaoFaixasConcessoes()
{
    include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFaixaPagamento.class.php"                        );
    include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoSalarioFamilia.class.php"                        );

    $arSessaoFaixasConcessoes = array();
    if ($_POST["stAcao"] == "alterar") {
        $obRFolhaPagamentoFaixaPagamento = new RFolhaPagamentoFaixaPagamento( new RFolhaPagamentoSalarioFamilia );
        $obRFolhaPagamentoFaixaPagamento->obRFolhaPagamentoSalarioFamilia->obRFolhaPagamentoPrevidencia->setCodRegimePrevidencia( $_POST["inCodRegimePrevidencia"] );
        $obRFolhaPagamentoFaixaPagamento->obRFolhaPagamentoSalarioFamilia->setTimestamp( $_POST["stTimestamp"] );
        $obRFolhaPagamentoFaixaPagamento->listar( $rsFaixasPagamento, $boTransacao = "" );
        $rsFaixasPagamento->addFormatacao("vl_inicial"  ,"NUMERIC_BR");
        $rsFaixasPagamento->addFormatacao("vl_final"    ,"NUMERIC_BR");
        $rsFaixasPagamento->addFormatacao("vl_pagamento","NUMERIC_BR");

        for ( $i=1 ; $i<=$rsFaixasPagamento->getNumLinhas() ; $i++ ) {
            $arFaixasConcessoes[ "inId"           ] = $i;
            $arFaixasConcessoes[ "boNovo"         ] = false;
            $arFaixasConcessoes[ "inCodFaixa"     ] = $rsFaixasPagamento->getCampo("cod_faixa");
            $arFaixasConcessoes[ "inValorInicial" ] = $rsFaixasPagamento->getCampo("vl_inicial");
            $arFaixasConcessoes[ "inValorFinal"   ] = $rsFaixasPagamento->getCampo("vl_final");
            $arFaixasConcessoes[ "inValorPagar"   ] = $rsFaixasPagamento->getCampo("vl_pagamento");

            $arSessaoFaixasConcessoes[] = $arFaixasConcessoes;

            $rsFaixasPagamento->proximo();
        }
    }
    Sessao::write("FaixasConcessoes",$arSessaoFaixasConcessoes);
}

function montaSpanFaixasConcessoes()
{
    $rsFaixasConcessoes = new RecordSet;
    $rsFaixasConcessoes->preenche( Sessao::read('FaixasConcessoes') );

    $obLista = new Lista;
    $obLista->setRecordSet  ( $rsFaixasConcessoes );
    $obLista->setTitulo     ("Faixas de Concessões Cadastradas");
    $obLista->setMostraPaginacao( false );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 2 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Valor Inicial do Salário");
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Valor Final do Salário");
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Valor a Pagar");
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Ação");
    $obLista->ultimoCabecalho->setWidth( 2 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "inValorInicial" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "inValorFinal" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "inValorPagar" );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "javascript:modificaDado('excluirFaixasConcessao')" );
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    // preenche a lista com innerHTML
    $js = "d.getElementById('spnConcessoesCadastradas').innerHTML = '".$stHtml."';";

    return $js;

}

function preencheEvento($stCampoCod, $stCampoDesc, $inCodEvento, $stNatureza = 'P')
{
    include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEvento.class.php" );

    $obEvento = new RFolhaPagamentoEvento;
    $obEvento->setCodigo($inCodEvento);
    $obEvento->setNatureza( $stNatureza );
    $obEvento->setEventoSistema( 'true' );
    $obEvento->listarEvento( $rsEvento );
    if ( $rsEvento->getNumLinhas() > 0 ) {
        $js  = " d.getElementById('".$stCampoDesc."').innerHTML = '".$rsEvento->getCampo("descricao")."'; \n";
    } else {
        $js  = " d.getElementById('".$stCampoDesc."').innerHTML = '&nbsp;'; \n";
        $js .= " f.".$stCampoCod.".value = ''; \n";
        $js .= " f.".$stCampoCod.".focus(); \n";

        sistemaLegado::exibeAviso(" Valor inválido. (".$inCodEvento.") "," "," ");
    }

    return $js;
}

function salvarFaixaConcessao($inId, $stAcaoSalvar, $boNovo=true, $inCodFaixa=0)
{
    $arFaixasConcessoes = array();
    $arFaixasConcessoes[ "inId"           ] = $inId;
    $arFaixasConcessoes[ "boNovo"         ] = $boNovo;
    $arFaixasConcessoes[ "inCodFaixa"     ] = $inCodFaixa;
    $arFaixasConcessoes[ "inValorInicial" ] = $_POST[ "inSalarioInicial" ];
    $arFaixasConcessoes[ "inValorFinal"   ] = $_POST[ "inSalarioFinal"   ];
    $arFaixasConcessoes[ "inValorPagar"   ] = $_POST[ "inValorPagar"     ];

    $arSessaoFaixasConcessoes = Sessao::read("FaixasConcessoes");
    if ($stAcaoSalvar == "incluir") {
        $arSessaoFaixasConcessoes[] = $arFaixasConcessoes;
    } else {
        for ($inCount = 0; $inCount < count($arSessaoFaixasConcessoes) ; $inCount++ ) {
            if ($arSessaoFaixasConcessoes[$inCount]["inId"] == $inId) {
                 $arSessaoFaixasConcessoes[$inCount] = $arFaixasConcessoes;
            }
        }
    }
    Sessao::write("FaixasConcessoes",$arSessaoFaixasConcessoes);
}

function incluirFaixaConcessao()
{
    //Para definir o proximo inID
    $rsFaixasConcessoes = new RecordSet;
    $rsFaixasConcessoes->preenche( Sessao::read('FaixasConcessoes') );
    $rsFaixasConcessoes->setUltimoElemento();
    $inUltimoId = $rsFaixasConcessoes->getCampo("inId");
    if (!$inUltimoId) {
        $inProxId = 1;
    } else {
        $inProxId = $inUltimoId + 1;
    }

    $inIdAnterior = $inUltimoId - 1;
    $arFaixasConcessoes = Sessao::read('FaixasConcessoes');
    $inSalInicial = str_replace( array(",", ".") , "" , $_POST[ "inSalarioInicial" ] ) + 0;
    $inSalFinal   = str_replace( array(",", ".") , "" , $arFaixasConcessoes[$inIdAnterior]["inValorFinal"] ) + 0;

    if ($inSalInicial > $inSalFinal) {
        salvarFaixaConcessao( $inProxId, "incluir" );

        $js .= 'f.inSalarioInicial.value = "";';
        $js .= 'f.inSalarioFinal.value   = "";';
        $js .= 'f.inValorPagar.value     = "";';
    } else {
        $js .= 'alertaAviso( "O valor inicial do salário deve ser maior que o valor final da última faixa cadastrada.", "form", "erro", "'.Sessao::getId().'");';
    }

    return $js;
}

function excluirFaixasConcessao($inId)
{
    $arFaixasConcessoes = array();
    $count = 0;
    $arSessaoFaixasConcessoes = Sessao::read('FaixasConcessoes');

    for ( $i=0 ; $i<count($arSessaoFaixasConcessoes) ; $i++ ) {
        if ($arSessaoFaixasConcessoes[$i]["inId"] != $inId) {
            $arFaixasConcessoes[$count] = $arSessaoFaixasConcessoes[$i];
            $count = $count + 1;
        }
    }
    Sessao::write("FaixasConcessoes",$arFaixasConcessoes);
}

switch ($stCtrl) {
    case "montaTela":
        $stJs  = montaSessaoFaixasConcessoes();
        $stJs .= montaSpanFaixasConcessoes();
    break;
    case "preencheEvento":
        $stJs = preencheEvento( $_GET["stCampoCod"], $_GET["stCampoDesc"], $_POST[ $_GET["stCampoCod"] ], $_GET["stNatureza"] );
    break;
    case "incluirFaixaConcessao":
        $stJs  = incluirFaixaConcessao();
        $stJs .= montaSpanFaixasConcessoes();
    break;
    case "excluirFaixasConcessao":
        $stJs  = excluirFaixasConcessao( $_GET["inId"] );
        $stJs .= montaSpanFaixasConcessoes();
    break;
}

if($stJs)
    sistemaLegado::executaFrameOculto($stJs);
?>
