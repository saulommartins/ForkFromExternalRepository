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
    * Pagina de Lista de Imoveis para Consulta de Arrecadação
    * Data de Criação   : 20/12/2005

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * @ignore

    * $Id: LSConsultaArrecadacao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.19
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_ARR_NEGOCIO."RARRCarne.class.php"                                                  );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCarne.class.php"             );
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ConsultaArrecadacao";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgFormNL = "FM".$stPrograma."NaoLanc.php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";

if ($request->get("inInscricaoEconomica") && $request->get("inCodImovel")) {
    SistemaLegado::exibeAviso("Somente um dos filtros deve ser utilizado (Inscrição Municipal ou Inscrição Econômica) !","n_incluir","erro");
    SistemaLegado::alertaAviso($pgFilt.'?'.Sessao::getId(),"Os filtros Inscrição Municipal e Inscrição Econômica não devem ser preenchidos juntos!", "aviso",Sessao::getId(), "../");
}

include 'JSConsultaArrecadacao.js';

// instancia regra de lancamento
$obRARRCarne = new RARRCarne;
$obTARRCarne = new TARRCarne;
$boTransacao = new Transacao;

$inInscricaoImobiliaria = $_REQUEST['inCodImovel'];
$inInscricaoEconomica	= $_REQUEST['inInscricaoEconomica'];
$inCGM	= $_REQUEST['inCGM'];
$inExercicio = $_REQUEST['stExercicio'];

// constroi filtros
$obRARRCarne->obRARRParcela->roRARRLancamento->obRCIMImovel->setNumeroInscricao($inInscricaoImobiliaria);
$obRARRCarne->obRARRParcela->roRARRLancamento->obRCEMInscricaoEconomica->setInscricaoEconomica ($inInscricaoEconomica);
$obRARRCarne->obRARRParcela->roRARRLancamento->obRCgm->setNumCgm( $inCGM );
$obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->setExercicio( $inExercicio );
$obRARRCarne->setNumeracao( $_REQUEST["stNumeracao"] );

$stFiltro = " WHERE numeracao_consolidacao = '".$_REQUEST["stNumeracao"]."'";
$stOrdem = " ORDER BY numeracao ";
$obRARRCarne->obRARRCarneConsolidacao->obTARRCarneConsolidacao->recuperaTodos(  $rsCarneConsolidacao, $stFiltro, $stOrdem, $boTransacao );

$stCarnes = null;
if ( $rsCarneConsolidacao->getNumLinhas() > 0 ) {
    $obRARRCarne->obRARRCarneConsolidacao->setNumeracaoConsolidacao ($_REQUEST["stNumeracao"]);

    while ( !$rsCarneConsolidacao->eof() ) {
        $stCarnes .= $rsCarneConsolidacao->getCampo ( "numeracao").', ';
        $rsCarneConsolidacao->proximo();
    }

    $stCarnes = substr( $stCarnes, 0, (strlen ($stCarnes) -2 ));
    $obRARRCarne->setNumeracao(  $stCarnes );
}

$obRARRCarne->listarCarneConsulta( $rsLista1 );

$arLista = $rsLista1->arElementos;

include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMTransferenciaAdquirente.class.php");
foreach ($rsLista1->arElementos as $index => $field) {
    if (strpos($field['origem'], 'I.T.I.V.') !== false) {
        $obTransfAdquirente = new TCIMTransferenciaAdquirente;
        $obTransfAdquirente->setCodLancamento($field['cod_lancamento']);
        $obTransfAdquirente->recuperaAdquirentes($rsAdquirentes);
        if ($rsAdquirentes->inNumLinhas > 0) {
            //$arLista[$index]['proprietarios'] = $rsAdquirentes->getCampo('cgm_adquirente');
            while ( !$rsAdquirentes->Eof() ) {
                $stAdquirentes = $rsAdquirentes->getCampo("numcgm_adquirente")." - ".$rsAdquirentes->getCampo("cgm_adquirente" )." / ";
                $rsAdquirentes->proximo();
            }
            $stAdquirentes = substr($stAdquirentes,0,-3);
            $arLista[$index]['proprietarios'] = $stAdquirentes;
        }
    }
}

$rsLista = new RecordSet;
$rsLista->preenche($arLista);

//esquema para retirar recalculos 24_07_07
$arListaLancamentos = $rsLista->getElementos();
$arListaFiltrada = array();
if ( !$rsLista->Eof() ) {
    for ( $inX=0; $inX<count($arListaLancamentos); $inX++ ) {
        $boEncontrou = false;
        if ($arListaLancamentos[$inX]["situacao_lancamento"] == "Recalculo") {
            for ( $inY=$inX+1; $inY<count($arListaLancamentos); $inY++ ) {
                if (
                    ( $arListaLancamentos[$inX]["num_parcelas"] == $arListaLancamentos[$inY]["num_parcelas"] ) &&
                    ( $arListaLancamentos[$inX]["origem"] == $arListaLancamentos[$inY]["origem"] ) &&
                    ( $arListaLancamentos[$inX]["valor_lancamento"] == $arListaLancamentos[$inY]["valor_lancamento"] ) &&
                    ( $arListaLancamentos[$inX]["inscricao"] == $arListaLancamentos[$inY]["inscricao"] )
                ) {
                    $obTARRCarne->ListaCarnesPagosLancamento( $rsListaPagos, $arListaLancamentos[$inX]["cod_lancamento"] );

                    if ( $rsListaPagos->Eof() ) {
                        $boEncontrou = true;
                        break;
                    }
                }
            }
        }

        if (!$boEncontrou) {
            $arListaFiltrada[] = $arListaLancamentos[$inX];
        }
    }
}

$rsLista->preenche( $arListaFiltrada );
//fim do esquema para retirar recalculos 24_07_07

$stAcao = "consultar";

//MANTEM FILTRO E PAGINACAO
$link = Sessao::read( 'link' );
$stLink  = "&stAcao=".$stAcao;
if ( $request->get('pg') and  $request->get('pos') ) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
    $link["pg"]  = $_GET["pg"];
    $link["pos"] = $_GET["pos"];
}

Sessao::write( 'link', $link );
if ( $rsLista->getNumLinhas() > 0 ) {
    $arTmp = $rsLista->arElementos;
    $arNovo = array();
    foreach ($arTmp as $valor) {
        $valor['venal'] = number_format($valor['venal'],2,',','.');
        $valor['valor_lancamento'] = number_format($valor['valor_lancamento'],2,',','.');
        $arNovo[] = $valor;
    }
    $rsLista->preenche($arNovo);
}

#$rsLista->ordena("proprietarios","ASC");

//passa filtro pra sessao
Sessao::write( 'filtro', "&inCodImovel=".$inInscricaoImobiliaria."&inInscricaoEconomica=".$inInscricaoEconomica."&inCGM=".$inCGM."&stExercicio=".$_REQUEST["stExercicio"]."&stNumeracao=".$_REQUEST["stNumeracao"] );

while ( !$rsLista->Eof() ) {
    $rsLista->setCampo( "proprietarios", str_replace( "'", "", $rsLista->getCampo( "proprietarios" ) ) );
    $rsLista->proximo();
}

$rsLista->setPrimeiroElemento();
$table = new Table();
$table->setRecordset( $rsLista );
$table->setSummary('Lançamentos');

//$table->setConditional( true , "#efefef" );

$table->Head->addCabecalho( 'Contribuinte' , 35 );
$table->Head->addCabecalho( 'Origem da Cobrança' , 15 );
$table->Head->addCabecalho( 'Inscrição' , 10 );
$table->Head->addCabecalho( 'Dados Complementares' , 30 );
$table->Head->addCabecalho( 'Situação' , 10 );

$stTitleLanc = "<b>Valor Lançamento:</b><i>R$ [valor_lancamento]</i><br>
                <b>Número de Parcelas: </b><i>[num_parcelas]</i><br>
                <b>Número de Cotas Únicas: </b><i>[num_unicas]</i><br>
                <b>Valor Venal do Imóvel: </b><i>R$ [venal]</i><br>
                [consolidacao]";
#$stTitleLanc .= "<br>CodLancamento: [cod_lancamento]";

$table->Body->addCampo( 'proprietarios', "E", $stTitleLanc );
$table->Body->addCampo( '[cod_grupo] [origem] [origem_consolidacao]', "C", $stTitleLanc );
$table->Body->addCampo( 'inscricao'   , "C", $stTitleLanc );
$table->Body->addCampo( 'dados_complementares', "E", $stTitleLanc );
$table->Body->addCampo( 'situacao_lancamento', "C", $stTitleLanc );

$table->Body->addAcao( 'consultar', 'consultarParcela( %d,%s,%d,%s,%s,%s,%s,%s,%s,%s,%d,%s,%s,%s,%s,%s,%s,%s,%s)' , array( 'cod_lancamento' , 'inscricao' , 'cod_modulo', 'origem', 'numcgm', 'nom_cgm', 'proprietarios', 'cod_grupo', 'numeracao', 'exercicio_origem', 'cod_parcela', 'pagamento', 'database_br', 'vencimento', 'dados_complementares', 'ocorrencia_pagamento','competencia', "tipo_calculo", "tipo_venal" ) );

$table->montaHTML();
echo $table->getHtml();

?>
