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
    * Página de Formulario de Seleção de Impressora para Relatorio
    * Data de Criação   : 25/01/2006

    * @author Analista: Lucas Leusin Oiagen
    * @author Desenvolvedor: Jose Eduardo Porto

    * @ignore

    $Revision: 30835 $
    $Name$
    $Autor:$
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.04.20
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

//Novo Relatorio apartir de 2015 e será feito em MPDF
if (Sessao::getExercicio() >= '2015' ) {
    include_once '../../../../../../config.php';
    include_once CLA_MPDF;
    
    $arDadosBordero = Sessao::read('arDados');
    $arDadosBordero['filtro'] = Sessao::read('filtroRelatorio');
    $obMPDF = new FrameWorkMPDF(2,30,7);
    $obMPDF->setNomeRelatorio("Borderô Pagamento");
    $obMPDF->setFormatoFolha("A4");
    $obMPDF->setConteudo($arDadosBordero);
    $obMPDF->setTipoSaida('I');
    $obMPDF->gerarRelatorio();    

}else{

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php" );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaFormPDF(  );

if (Sessao::read('inCodBordero')) {
    Sessao::write('inNumBordero',Sessao::read('inCodBordero'));
}
// Adicionar logo nos relatorios

if (Sessao::read('arDados') != null) {
    $arFiltro = Sessao::read('arDados');
} else {
    $arFiltro = Sessao::read('filtro');
}

$arFiltroRelatorio = Sessao::read('filtroRelatorio');

if ($arFiltroRelatorio['inCodEntidade'] != "") {
    $obRRelatorio->setCodigoEntidade( $arFiltroRelatorio['inCodEntidade'] );
    $obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );
}

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$arConfiguracao[ "nom_acao" ] = "Borderô de Pagamento";
$obPDF->setModulo            ( "Relatorio" );
$obPDF->setTitulo            ( "Dados para Extrato Bancário" );
$obPDF->setSubTitulo         ( "Borderô Nr. ".str_pad($arFiltroRelatorio['inNumBordero'], 3,"0",STR_PAD_LEFT)."               Exercicio - ".Sessao::getExercicio() );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

if ($arFiltroRelatorio['inSimulacao']) {

    $obPDF->addRecordSet( $arFiltro[12] );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho("",100,10);
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCampo("Simulacao",10, '', '', '','255,255,255');

    $obPDF->addRecordSet( $arFiltro[0] );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho("",100,10);
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo("DadosBordero",8, '', '', 'LTRB','205,206,205');
} else {

    $obPDF->addRecordSet( $arFiltro[0] );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho("",100,10);
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo("DadosBordero",8, '', '', 'LTRB','205,206,205');
}

$obPDF->addRecordSet( $arFiltro[1] );
$obPDF->setQuebraPaginaLista( false );
//$obPDF->setAlturaCabecalho(-7);
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho("", 22, 10);
$obPDF->setAlinhamento ( "R" );
$obPDF->addCabecalho("", 78, 10);
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo("NumBordero", 8, '', '', 'LTRB','255,255,255');
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("DataBordero", 8, '', '', 'LTRB','255,255,255');

$obPDF->addRecordSet( $arFiltro[2] );
$obPDF->setQuebraPaginaLista( false );
//$obPDF->setAlturaCabecalho(-6);
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho("", 100, 10);
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("DadosBoletim", 8 , '', '', 'LTR','205,206,205');

$obPDF->addRecordSet( $arFiltro[3] );
$obPDF->setQuebraPaginaLista( false );
//$obPDF->setAlturaCabecalho(-6);
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho("", 22, 10);
$obPDF->setAlinhamento ( "R" );
$obPDF->addCabecalho("", 78, 10);
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo("NumBoletim", 8, '', '', 'LTRB','255,255,255');
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("DataBoletim", 8, '', '', 'LTRB','255,255,255');

$obPDF->addRecordSet( $arFiltro[4] );
$obPDF->setQuebraPaginaLista( false );
//$obPDF->setAlturaCabecalho(-6);
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho("", 100, 10);
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("AoBanco", 8 , '', '', 'LTR','205,206,205');

$obPDF->addRecordSet( $arFiltro[5] );
$obPDF->setQuebraPaginaLista( false );
//$obPDF->setAlturaCabecalho(-6);
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho("", 15, 10);
$obPDF->setAlinhamento ( "R" );
$obPDF->addCabecalho("", 85, 10);
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo("Agencia", 8, '', '', 'LTRB','255,255,255');
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("NomAgencia", 8, '', '', 'LTRB','255,255,255');

$obPDF->addRecordSet( $arFiltro[6] );
$obPDF->setQuebraPaginaLista( false );
//$obPDF->setAlturaCabecalho(-6);
$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho("", 100, 10);
$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo("Autorizacao", 8, '', '', 'LTRB','255,255,255');

$obPDF->addRecordSet( $arFiltro[7] );
$obPDF->setQuebraPaginaLista( false );
//$obPDF->setAlturaCabecalho(-6);
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho("", 50, 10);
$obPDF->setAlinhamento ( "R" );
$obPDF->addCabecalho("", 25, 10);
$obPDF->setAlinhamento ( "R" );
$obPDF->addCabecalho("", 25, 10);
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("Credor", 8 , '', '', 'LTRB','205,206,205');
$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo("CNPJ",  8 , '', '', 'LTRB','205,206,205');
$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo("BancoAgCC",  8 , '', '', 'LTRB','205,206,205');

$obPDF->addRecordSet( $arFiltro[8] );
$obPDF->setQuebraPaginaLista( false );
//$obPDF->setAlturaCabecalho(-6);
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho("", 35, 10);
$obPDF->setAlinhamento ( "R" );
$obPDF->addCabecalho("", 15, 10);
$obPDF->setAlinhamento ( "R" );
$obPDF->addCabecalho("", 50, 10);
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("Credor", 8 , '', '', 'LTRB','205,206,205');
$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo("CNPJ",  8 , '', '', 'LTRB','205,206,205');
$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo("BancoAgCC",  8 , '', '', 'LTRB','205,206,205');

$obPDF->addRecordSet( $arFiltro[9] );
$obPDF->setQuebraPaginaLista( false );
//$obPDF->setAlturaCabecalho(-6);
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho("", 35, 10);
$obPDF->setAlinhamento ( "R" );
$obPDF->addCabecalho("", 15, 10);
$obPDF->setAlinhamento ( "R" );
$obPDF->addCabecalho("", 25, 10);
$obPDF->setAlinhamento ( "R" );
$obPDF->addCabecalho("", 25, 10);
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("A", 8 , '', '', 'LTRB','255,255,255');
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo("B",  8 , '', '', 'LTRB','255,255,255');
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("C",  8 , '', '', 'LTRB','255,255,255');
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo("D",  8 , '', '', 'LTRB','255,255,255');

$obPDF->addRecordSet( $arFiltro[10] );
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlturaCabecalho(0.9);
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho("", 50, 10);
$obPDF->setAlinhamento ( "R" );
$obPDF->addCabecalho("", 50, 10);
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("Autorizo", 8 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo("Cidade", 8 );

$obPDF->addRecordSet( $arFiltro[11] );
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho("", 33, 10);
$obPDF->setAlinhamento ( "R" );
$obPDF->addCabecalho("", 33, 10);
$obPDF->setAlinhamento ( "R" );
$obPDF->addCabecalho("", 33, 10);
$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo("Assinante_1", 8 );
$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo("Assinante_2", 8 );
$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo("Assinante_3", 8 );

$obPDF->show();

}//Fim do ELSE

?>