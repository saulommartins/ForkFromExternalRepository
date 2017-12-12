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
* Página relatório de registros de evento na complementar por contrato
* Data de Criação   : 10/03/2006

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: André Almeida

* @ignore

$Revision: 30840 $
$Name$
$Author: souzadl $
$Date: 2006-09-26 07:00:14 -0300 (Ter, 26 Set 2006) $

* Casos de uso: uc-04.05.34
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_PDF."RRelatorio.class.php"             );

include_once( CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php"                                    );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF();

$arFiltro = Sessao::read('filtroRelatorio');
//Consulta o CGM a partir do registro
$obRPessoalServidor = new RPessoalServidor;
$obRPessoalServidor->addContratoServidor();
$obRPessoalServidor->roUltimoContratoServidor->setRegistro( $arFiltro['inContrato'] );
$obRPessoalServidor->roUltimoContratoServidor->listarContratosServidorResumido( $rsContratoServidor , $boTransacao );

$stMes = array(1 =>"Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");
if ( substr( $arFiltro["inCodMes"], 0, 1 ) == "0" )
    $inMes = substr( $arFiltro["inCodMes"], 1, 1 );
else
    $inMes = $arFiltro["inCodMes"];

$obPDF->addFiltro( "CGM:                        ", $rsContratoServidor->getCampo("numcgm")." - ".$rsContratoServidor->getCampo("servidor") );
$obPDF->addFiltro( "Matrícula:                   ", $arFiltro['inContrato'] );
$obPDF->addFiltro( "Complementar - Competência: ", $arFiltro['inCodComplementar']." - ".$stMes[$inMes]."/".$arFiltro['inAno'] );

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Folha de Pagamento"         );
$obPDF->setTitulo            ( "Relatório de Eventos na Complementar por Matrícula" );
$obPDF->setSubTitulo         ( ""                           );
$obPDF->setUsuario           ( Sessao::getUsername()            );
$obPDF->setEnderecoPrefeitura( $arConfiguracao              );

$rsRecordSet = Sessao::read("complementarPorContrato");
$arElementos = $rsRecordSet->getElementos();

$arFiltro = $arElementos["filtro"];
$rsFiltro = new RecordSet;
$rsFiltro->preenche( $arFiltro );

$obPDF->addRecordSet( $rsFiltro );
$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho("", 10, 10);
$obPDF->addCabecalho("", 90, 10);

$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo       ("campo" , 8 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo       ("valor"  , 8    );

$rsVazio = new RecordSet;
$rsVazio->setPrimeiroElemento();
$obPDF->addRecordSet($rsVazio);
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho("Eventos Cadastrados", 100, 14);
$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo       ("nome", 8 );

$arEventos = $arElementos["eventos"];
$rsEventos = new RecordSet;
$rsEventos->preenche( $arEventos );

$obPDF->addRecordSet( $rsEventos );
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho("Codigo",          10, 10);
$obPDF->addCabecalho("Descrição",       60, 10);
$obPDF->addCabecalho("Valor",           15, 10);
$obPDF->addCabecalho("Quantidade",      15, 10);

$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo       ("codigo"     , 8 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo       ("descricao"  , 8    );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo       ("valor"      , 8    );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo       ("quantidade" , 8    );

$rsVazio->setPrimeiroElemento();
$obPDF->addRecordSet($rsVazio);
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho("Base de Cálculo", 100, 14);
$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo       ("", 8 );

$arBase = $arElementos["base"];
$rsBase = new RecordSet;
$rsBase->preenche( $arBase );

$obPDF->addRecordSet( $rsBase );
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho("Codigo",          10, 10);
$obPDF->addCabecalho("Descrição",       60, 10);
$obPDF->addCabecalho("Valor",           15, 10);
$obPDF->addCabecalho("Quantidade",      15, 10);

$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo       ("codigo"     , 8 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo       ("descricao"  , 8    );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo       ("valor"      , 8    );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo       ("quantidade" , 8    );

$obPDF->show();
?>
