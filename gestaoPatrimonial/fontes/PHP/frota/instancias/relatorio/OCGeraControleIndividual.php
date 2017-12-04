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
  * Data de criação : 14/03/2006

    * @author Analista:
    * @author Programador: Fernando Zank Correa Evangelista

    Caso de uso: uc-03.02.17

    $Id: OCGeraControleIndividual.php 59612 2014-09-02 12:00:51Z gelson $
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php" );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF("L");
$rsVazio      = new RecordSet;
$rsVazio2      = new RecordSet;
$rsVazio3      = new RecordSet;

$arQuebraLinha[0]['vazio'] = "RESUMO TOTAL DE GASTOS";

$rsVazio->preenche($arQuebraLinha);

$filtro = Sessao::read('filtro');

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );

$obPDF->setModulo            ( "Relatorio"   );
$obPDF->setSubTitulo         ( "Periodo: ".$filtro['stDataInicial']." à ".$filtro['stDataFinal']."") ;
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$obPDF->addFiltro ('Código do Veículo' , $filtro["inCodVeiculo"]);
$obPDF->addFiltro ('Prefixo'           , $filtro["stPrefixo"   ]);

if ($filtro["stPlaca"] != null)
    $obPDF->addFiltro ('Placa' , substr($filtro["stPlaca"],0,3)."-".substr($filtro["stPlaca"],-4));

$obPDF->addRecordSet( $rsVazio2);
$obPDF->setAlturaCabecalho(0);
$obPDF->addCabecalho ("",15,10);
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo       ( "vazio"     , 8 );

$obPDF->addRecordSet( Sessao::read('transf6') );
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlturaCabecalho(1);
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "DADOS DO VEÍCULO", 14,9);
$obPDF->addCabecalho   ( "",35,10);
$obPDF->addCabecalho   ( "",15,10);
$obPDF->addCabecalho   ( "",10,10);
$obPDF->addCabecalho   ( "",600,8);

$obPDF->addCampo       ( "titulo1"   , 8 );
$obPDF->addCampo       ( "valor1", 8 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo       ( "titulo2"   , 8 );
$obPDF->addCampo       ( "valor2", 8 );
$obPDF->addCampo       ( "", 8 );
$obPDF->setAlinhamento ( "L" );

$obPDF->addRecordSet( $rsVazio);
$obPDF->setQuebraPaginaLista(false);
$obPDF->setAlturaCabecalho(1);
$obPDF->addCabecalho ("DADOS DA MANUTENÇÃO",40,9);
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo       ( "nada"     , 8 );

$obPDF->addRecordSet( Sessao::read('transf5') );

$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlinhamento ( "L" );
//inclui os cabeçalhos
$obPDF->addCabecalho   ( "DATA", 6,8);
$obPDF->setAlinhamento ( "R" );
$obPDF->addCabecalho   ( "KM",7,8);
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "DESCRIÇÃO", 47,8);
$obPDF->addCabecalho   ( "TIPO", 6,8);
$obPDF->setAlinhamento ( "R" );
$obPDF->addCabecalho   ( "QUANTIDADE",10 ,8);
$obPDF->addCabecalho   ( "VALOR UNITÁRIO",13,8);
$obPDF->addCabecalho   ( "VALOR TOTAL",11,8);

$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo       ( "data"         , 8 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo       ( "quilometragem", 8 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo       ( "descricao"    , 8 );
$obPDF->addCampo       ( "tipo"         , 8 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo       ( "[quantidade]  [unidade_medida]"  , 8 );
$obPDF->addCampo       ( "valor_medio"   , 8 );
$obPDF->addCampo       ( "valor"        , 8 );

$obPDF->addRecordSet( $rsVazio);
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlturaCabecalho(0);
$obPDF->addCabecalho ("",15,10);
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo       ( "vazio"     , 8 );

$obPDF->addRecordSet( Sessao::read('transf7'));
$obPDF->addCabecalho   ( "", 10,10);
$obPDF->addCabecalho   ( "", 15,10);

$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo       ( "[descricao]" , 8 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo       ( "valor", 8 );

$obPDF->show();
