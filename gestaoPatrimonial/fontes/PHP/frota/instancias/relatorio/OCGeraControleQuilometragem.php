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
    * Página de geração de relatório
    * Data de criação : 17/03/2006

    * @author Analista:
    * @author Programador: Fernando Zank Correa Evangelista

    Caso de uso: uc-03.02.18

    $Id: OCGeraControleQuilometragem.php 59612 2014-09-02 12:00:51Z gelson $
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php" );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF("L");
$rsVazio      = new RecordSet;

$filtro = Sessao::read('filtro');

$arQuebraLinha[0]['vazio'] =
"------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------";
$arQuebraLinha[1]['vazio'] = "RESUMO TOTAL DE GASTOS";
$rsVazio->preenche($arQuebraLinha);

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );

$obPDF->setModulo            ( "Relatorio"   );
$obPDF->setSubTitulo         ( "Mês de Referência: ".$filtro['stNomeMes']."/".Sessao::getExercicio()."");
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

if ($filtro["stNomeMes"])
    $obPDF->addFiltro ('Mês de Referência' , $filtro["stNomeMes"]);

if ($filtro["inCodVeiculo"])
    $obPDF->addFiltro ('Código do Veículo' , $filtro["inCodVeiculo"]);

if ($filtro["stNomeMarca"])
    $obPDF->addFiltro ('Marca' , $filtro["stNomeMarca"]);

if ($filtro["stNomeModelo"])
    $obPDF->addFiltro ('Modelo' , $filtro["stNomeModelo"]);

if ($filtro["stNomeCombustivel"])
    $obPDF->addFiltro ('Combustivel' , $filtro["stNomeCombustivel"]);

if ($filtro["stNomeTipoVeiculo"])
    $obPDF->addFiltro ('Tipo Veículo' , $filtro["stNomeTipoVeiculo"]);

if ($filtro["stPrefixo"])
    $obPDF->addFiltro ('Prefixo' , $filtro["stPrefixo"]);

if ($filtro["stPlaca"])
    $obPDF->addFiltro ('Placa' , substr($filtro["stPlaca"],0,3)."-".substr($filtro["stPlaca"],-4));

if ($filtro["stNomeCGMResponsavel"])
    $obPDF->addFiltro ('Responsável' , $filtro["stNomeCGMResponsavel"]);

if ($filtro["inCodOrdenacao"]) {
    $stOrdenacao = $filtro["inCodOrdenacao"] == 1 ? "Placa" : "Marca";
    $obPDF->addFiltro ('Ordenação' , $stOrdenacao);
}

if ($filtro["inCodOrigemVeiculo"]) {
    if ($filtro["inCodOrigemVeiculo"] == 1)
        $stOrigemVeiculo = "Todos";
    else
        $stOrigemVeiculo = $filtro["inCodOrigemVeiculo"] == 2 ? "Veículo Próprio":"Veículo de Terceiros";
    $obPDF->addFiltro ('Origem do Veículo'  , $stOrigemVeiculo);
}
if ($filtro["inCodVeiculoBaixado"]) {
    if ($filtro["inCodVeiculoBaixado"] == 1)
        $stVeiculoBaixado = "Todos";
    else
        $stVeiculoBaixado = $filtro["inCodVeiculoBaixado"] == 2 ? "Sim":"Não";
    $obPDF->addFiltro ('Veículos Baixados'  , $stVeiculoBaixado);
}

$obPDF->addRecordSet( Sessao::read('transf5') );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "Código", 6,10);
$obPDF->addCabecalho   ( "Veículo",16,10);
$obPDF->setAlinhamento ( "R" );
$obPDF->addCabecalho   ( "Cilindrada",10,10);
$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho   ( "Combustível",10,10);
$obPDF->setAlinhamento ( "R" );
$obPDF->addCabecalho   ( "Situação",9,10);
$obPDF->addCabecalho   ( "Km Inicial",9,10);
$obPDF->addCabecalho   ( "Km Final",7,10);
$obPDF->addCabecalho   ( "Consumo",8.5,10);
$obPDF->addCabecalho   ( "Qtde.",6.5,10);
$obPDF->addCabecalho   ( "Vlr Médio(R$)",9,10);
$obPDF->addCabecalho   ( "Vlr Total(R$)",9,10);

$obPDF->setAlinhamento ("L");
$obPDF->addCampo       ("codigo",8);
$obPDF->addCampo       ("veiculo",8);
$obPDF->setAlinhamento ("R");
$obPDF->addCampo       ("cilindrada",8);
$obPDF->setAlinhamento ("L");
$obPDF->addCampo       ("combustivel",8);
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo       ("situacao",8);
$obPDF->addCampo       ("km_inicial",8);
$obPDF->addCampo       ("km_final",8);
$obPDF->addCampo       ("[consumo] [unidade_medida]",8);
$obPDF->addCampo       ("[quantidade] [unidade_medida]",8);
$obPDF->addCampo       ("valor_medio",8);
$obPDF->addCampo       ("valor",8);

$obPDF->addRecordSet( $rsVazio);
$obPDF->setQuebraPaginaLista( false );
$obPDF->addCabecalho ("",15,10);
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo       ( "vazio"     , 8 );

$obPDF->addRecordSet( Sessao::read('transf6'));
$obPDF->addCabecalho   ( "",60 ,10);
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "Combustível",12,10);
$obPDF->setAlinhamento ( "R" );
$obPDF->addCabecalho   ( "Qtde.", 8,10);
$obPDF->addCabecalho   ( "Vlr Médio(R$)", 9,10);
$obPDF->addCabecalho   ( "Vlr Total(R$)", 9,10);

$obPDF->setQuebraPaginaLista( false );
$obPDF->addCampo       ( "" , 8 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo       ( "combustivel" , 8 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo       ( "[quantidade] [unidade_medida]", 8 );
$obPDF->addCampo       ( "valor_medio", 8 );
$obPDF->addCampo       ( "valor", 8 );

$obPDF->show();
