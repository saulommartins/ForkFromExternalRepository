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
    * Página de Relatório Demostrativo de Saldos
    * Data de Criação   : 25/08/2006

    * @author Analista:
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    * $Id: OCGeraRelatorioDemonstrativoSaldos.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.04.24
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php");

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF();
$rsVazio      = new RecordSet();

$arFiltro = Sessao::read('filtroRelatorio');

$obRRelatorio->setCodigoEntidade( $arFiltro['inCodEntidade'] );
$obRRelatorio->setExercicioEntidade( Sessao::getExercicio() );

$obRRelatorio->setExercicio( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo( "Relatorio" );

$obPDF->setAcao( "Demonstrativo de Saldos" );

$obPDF->setSubTitulo         ( "Período: ".$arFiltro['stDataInicial']." de ".$arFiltro['stDataFinal']);
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$obPDF->addFiltro( 'Entidade  ' , $arFiltro['inCodEntidade']. " - ". $arFiltro['stEntidade'] );
$obPDF->addFiltro( 'Período   ' , $arFiltro['stDataInicial']." de ".$arFiltro['stDataFinal'] );

if ($arFiltro['inCodPlanoInicial'] && $arFiltro['inCodPlanoFinal']) {
    $obPDF->addFiltro( 'Código Reduzido     ', $arFiltro['inCodPlanoInicial']." até ".$arFiltro['inCodPlanoFinal']);
} elseif ($arFiltro['inCodPlanoInicial']) {
    $obPDF->addFiltro( 'Código Reduzido     ', $arFiltro['inCodPlanoInicial']);
} elseif ($arFiltro['inCodPlanoFinal']) {
    $obPDF->addFiltro( 'Código Reduzido     ', $arFiltro['stDataFinal'] );
}

if ($arFiltro['stCodEstruturalInicial'] && $arFiltro['stCodEstruturalFinal']) {
    $obPDF->addFiltro( 'Código Estrutural   ', $arFiltro['stCodEstruturalInicial']." até ".$arFiltro['stCodEstruturalFinal'] );
} elseif ($arFiltro['stCodEstruturalInicial']) {
    $obPDF->addFiltro( 'Código Estrutural', $arFiltro['stCodEstruturalInicial'] );
} elseif ($arFiltro['stCodEstruturalFinal']) {
    $obPDF->addFiltro( 'Código Estrutural   ', $arFiltro['stCodEstruturalFinal'] );
}

if ($arFiltro['inCodRecurso']) {
    $obPDF->addFiltro( 'Recurso             ', $arFiltro['inCodRecurso'] );
}

if ( isset($arFiltro['inCodUso']) != "" && $arFiltro['inCodDestinacao'] != "" && $arFiltro['inCodEspecificacao'] != "") {
    $stDescricao = SistemaLegado::pegaDado('descricao', 'orcamento.destinacao_recurso', "WHERE exercicio='".Sessao::getExercicio()."' AND cod_destinacao=".$arFiltro['inCodDestinacao']);
    $stDescricaoDestinacao = $arFiltro['inCodUso'].".".$arFiltro['inCodDestinacao'].".".$arFiltro['inCodEspecificacao']." - ".$stDescricao;
    $obPDF->addFiltro('Destinação de Recurso', $stDescricaoDestinacao);
}

$arDados = Sessao::read('arDados');
$arDados->addFormatacao( 'saldo_anterior', 'NUMERIC_BR_NULL' );
$arDados->addFormatacao( 'saldo_atual'   , 'NUMERIC_BR_NULL' );
$arDados->addFormatacao( 'vl_debito'     , 'NUMERIC_BR_NULL' );
$arDados->addFormatacao( 'vl_credito'    , 'NUMERIC_BR_NULL' );

$rsDemostrativo = new RecordSet();

$obPDF->addRecordSet( $rsVazio );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho( "Movimento de Caixa / Bancos", 100, 12 );
$obPDF->addCampo( "", 8 );

$inContadorRecurso=0;
$inContadorMostraIndice=0;
while ( !$arDados->eof() ) {
    if ($arDados->getCampo("cod_recurso")) {
        $arDemostrativo[$inContadorRecurso]["cod_recurso" ] = "";
        $arDemostrativo[$inContadorRecurso]["reduzido"    ] = "";
        $arDemostrativo[$inContadorRecurso]["nom_recurso" ] = "";
        $arDemostrativo[$inContadorRecurso]["sal_anterior"] = "";
        $arDemostrativo[$inContadorRecurso]["debitos"     ] = "";
        $arDemostrativo[$inContadorRecurso]["creditos"    ] = "";
        $arDemostrativo[$inContadorRecurso]["sal_atual"   ] = "";
        $inContadorRecurso++;

        $arDemostrativo[$inContadorRecurso]["cod_recurso" ] = $arDados->getCampo("cod_recurso");
        $arDemostrativo[$inContadorRecurso]["reduzido"    ] = "--------";
        $arDemostrativo[$inContadorRecurso]["nom_recurso" ] = $arDados->getCampo("nom_recurso");
        $arDemostrativo[$inContadorRecurso]["sal_anterior"] = "";
        $arDemostrativo[$inContadorRecurso]["debitos"     ] = "";
        $arDemostrativo[$inContadorRecurso]["creditos"    ] = "";
        $arDemostrativo[$inContadorRecurso]["sal_atual"   ] = "";
        $inContadorRecurso++;

        $arDemostrativo[$inContadorRecurso]["cod_recurso" ] = "Conta";
        $arDemostrativo[$inContadorRecurso]["reduzido"    ] = "Reduz.";
        $arDemostrativo[$inContadorRecurso]["nom_recurso" ] = "Descrição";
        $arDemostrativo[$inContadorRecurso]["sal_anterior"] = "Saldo Anterior";
        $arDemostrativo[$inContadorRecurso]["debitos"     ] = "Débitos";
        $arDemostrativo[$inContadorRecurso]["creditos"    ] = "Créditos";
        $arDemostrativo[$inContadorRecurso]["sal_atual"   ] = "Saldo Atual";
        $inContadorRecurso++;
    } else {
        $arDemostrativo[$inContadorRecurso]["cod_recurso" ] = $arDados->getCampo("cod_estrutural");
        $arDemostrativo[$inContadorRecurso]["reduzido"    ] = $arDados->getCampo("cod_plano"     );
        $arDemostrativo[$inContadorRecurso]["nom_recurso" ] = $arDados->getCampo("nom_conta"     );
        $arDemostrativo[$inContadorRecurso]["sal_anterior"] = $arDados->getCampo("saldo_anterior");
        $arDemostrativo[$inContadorRecurso]["debitos"     ] = $arDados->getCampo("vl_debito"     );
        $arDemostrativo[$inContadorRecurso]["creditos"    ] = $arDados->getCampo("vl_credito"    );
        $arDemostrativo[$inContadorRecurso]["sal_atual"   ] = $arDados->getCampo("saldo_atual"   );
        $inContadorRecurso++;
    }

    $inContadorMostraIndice++;
    $arDados->proximo();

}

$rsDemostrativo->preenche($arDemostrativo);

$obPDF->addRecordSet( $rsDemostrativo );
$obPDF->setQuebraPaginaLista( false );

$obPDF->addCabecalho( "Conta" , 17 );
$obPDF->addCabecalho( "Reduz." ,  6 );
$obPDF->addCabecalho( "Descrição" , 29 );
$obPDF->addCabecalho( "Saldo Anterior" , 12 );
$obPDF->setAlinhamento( "C" );
$obPDF->addCabecalho( "Débitos" , 12 );
$obPDF->addCabecalho( "Créditos" , 12 );
$obPDF->addCabecalho( "Saldo Atual" , 12 );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCampoComLargura( "cod_recurso" , 17,8 );
$obPDF->addCampoComLargura( "reduzido"    ,  6,8 );
$obPDF->addCampoComLargura( "nom_recurso" , 33,8 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampoComLargura( "sal_anterior", 11,8 );
$obPDF->addCampoComLargura( "debitos"     , 11,8 );
$obPDF->addCampoComLargura( "creditos"    , 11,8 );
$obPDF->addCampoComLargura( "sal_atual"   , 11,8 );

$arAssinaturas = Sessao::read('assinaturas');

if ( count($arAssinaturas['selecionadas']) > 0 ) {
    include_once( CAM_FW_PDF."RAssinaturas.class.php" );
    $obRAssinaturas = new RAssinaturas;
    $obRAssinaturas->setArAssinaturas( $arAssinaturas['selecionadas'] );
    $obPDF->setAssinaturasDefinidas( $obRAssinaturas->getArAssinaturas() );
}

$obPDF->show();
