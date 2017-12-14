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
* Página relatório de Férias Vencidas
* Data de Criação   : 07/08/2006

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @ignore

$Revision: 30860 $
$Name$
$Author: melo $
$Date: 2007-06-26 16:20:10 -0300 (Ter, 26 Jun 2007) $

* Casos de uso: uc-04.04.46
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/PDF/ListaFormPDFRH.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/PDF/ListaPDFRH.class.php';
include_once( CAM_FW_PDF."RRelatorio.class.php" );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaFormPDFRH();

$obRRelatorio->setExercicio        ( Sessao::getExercicio()   );
$obRRelatorio->setCodigoEntidade   ( Sessao::getCodEntidade($boTransacao) );
$obRRelatorio->setExercicioEntidade( Sessao::getExercicio()   );

$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Local " );
$obPDF->setTitulo            ( "Relatório de Férias Vencidas" );
$obPDF->setSubTitulo         ( "Locais: ".Sessao::getExercicio() );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$rsFeriasVencidas = Sessao::read('FeriasVencidas');
$obPDF->addRecordSet( new RecordSet );

$rsTemp = new RecordSet;
$arTemp = array();
$arTemp[] = array("campo1"=>"Matrícula",
                  "campo2"=>"Nome do Servidor",
                  "campo3"=>"Regime/Função",
                  "campo4"=>"Lotação/Local");
$rsTemp->preenche($arTemp);
$obPDF->addRecordSet($rsTemp);
$obPDF->setQuebraPaginaLista( false );
$obPDF->addCabecalho    ( "",     10, 8);
$obPDF->addCabecalho    ( "",     30, 8);
$obPDF->addCabecalho    ( "",     30, 8);
$obPDF->addCabecalho    ( "",     30, 8);
$obPDF->addCampo        ( "campo1",8 , '', '', 'TL','205,206,205');
$obPDF->addCampo        ( "campo2",8 , '', '', 'T' ,'205,206,205');
$obPDF->addCampo        ( "campo3",8 , '', '', 'T' ,'205,206,205');
$obPDF->addCampo        ( "campo4",8 , '', '', 'TR','205,206,205');

$rsTemp = new RecordSet;
$arTemp = array();
$arTemp[] = array("campo1"=>"",
                  "campo2"=>"Período Aquisitivo",
                  "campo3"=>"Período Concessivo",
                  "campo4"=>"Quantidade de Dias Vencidos");
$rsTemp->preenche($arTemp);
$obPDF->addRecordSet($rsTemp);
$obPDF->setQuebraPaginaLista( false );
$obPDF->addCabecalho    ( "",     10, 8);
$obPDF->addCabecalho    ( "",     30, 8);
$obPDF->addCabecalho    ( "",     30, 8);
$obPDF->addCabecalho    ( "",     30, 8);
$obPDF->addCampo        ( "campo1",8 , '', '', 'BL','205,206,205');
$obPDF->addCampo        ( "campo2",8 , '', '', 'B' ,'205,206,205');
$obPDF->addCampo        ( "campo3",8 , '', '', 'B' ,'205,206,205');
$obPDF->addCampo        ( "campo4",8 , '', '', 'BR','205,206,205');

$rsTemp = new RecordSet;
$arTemp = array();
$arTemp[] = array("campo1"=>"");
$rsTemp->preenche($arTemp);
$obPDF->addRecordSet($rsTemp);
$obPDF->setQuebraPaginaLista( false );
$obPDF->addCabecalho    ( "",     10, 8);
while (!$rsFeriasVencidas->eof()) {
    $rsTemp = new RecordSet;
    $rsTemp->preenche($rsFeriasVencidas->getCampo("contrato"));
    $obPDF->addRecordSet($rsTemp);
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento("R");
    $obPDF->addCabecalho    ( "",     10, 8);
    $obPDF->setAlinhamento("L");
    $obPDF->addCabecalho    ( "",     30, 8);
    $obPDF->addCabecalho    ( "",     30, 8);
    $obPDF->addCabecalho    ( "",     30, 8);
    $obPDF->setAlinhamento("R");
    $obPDF->addCampo        ( "campo1",6 , '', '', 'TL','255,255,255');
    $obPDF->setAlinhamento("L");
    $obPDF->addCampo        ( "campo2",6 , '', '', 'T','255,255,255');
    $obPDF->addCampo        ( "campo3",6, '', '', 'T','255,255,255');
    $obPDF->addCampo        ( "campo4",6 , '', '', 'TR','255,255,255');

    $rsTemp = new RecordSet;
    $rsTemp->preenche($rsFeriasVencidas->getCampo("ferias"));
    $obPDF->addRecordSet($rsTemp);
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento("R");
    $obPDF->addCabecalho    ( "",     10, 8);
    $obPDF->setAlinhamento("L");
    $obPDF->addCabecalho    ( "",     30, 8);
    $obPDF->addCabecalho    ( "",     30, 8);
    $obPDF->addCabecalho    ( "",     30, 8);
    $obPDF->setAlinhamento("R");
    $obPDF->addCampo        ( "campo1",6 , '', '', 'L','255,255,255');
    $obPDF->setAlinhamento("L");
    $obPDF->addCampo        ( "campo2",6 , '', '', '','255,255,255');
    $obPDF->addCampo        ( "campo3",6 , '', '', '','255,255,255');
    $obPDF->addCampo        ( "campo4",6,  '', '', 'R','255,255,255');
    $rsFeriasVencidas->proximo();
}

$rsTemp = new RecordSet;
$arTemp = array();
$arTemp[] = array("campo1"=>"TOTAL DE SERVIDORES:",
                  "campo2"=>Sessao::read('inTotalServidores'));
$rsTemp->preenche($arTemp);
$obPDF->addRecordSet($rsTemp);
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlinhamento("R");
$obPDF->addCabecalho    ( "",     30, 8);
$obPDF->setAlinhamento("L");
$obPDF->addCabecalho    ( "",     70, 8);
$obPDF->setAlinhamento("R");
$obPDF->addCampo        ( "campo1",8 , '', '', 'T','255,255,255');
$obPDF->setAlinhamento("L");
$obPDF->addCampo        ( "campo2",8 , '', '', 'T','255,255,255');
$obPDF->show();
?>
