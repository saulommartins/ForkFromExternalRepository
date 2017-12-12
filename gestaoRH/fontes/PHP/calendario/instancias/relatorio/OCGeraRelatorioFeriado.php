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
    * Data de Criação   : 03/09/2004

    * @author Desenvolvedor Eduardo Martins

    * @ignore

    $Revision: 30895 $
    $Name$
    $Author: melo $
    $Date: 2007-06-26 18:09:07 -0300 (Ter, 26 Jun 2007) $

    * Casos de uso :uc-04.02.03

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php" );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF();
$rsVazio      = new RecordSet;

$obRRelatorio->setExercicio        ( Sessao::getExercicio()        );
$obRRelatorio->setCodigoEntidade   ( Sessao::getCodEntidade($boTransacao) );
$obRRelatorio->setExercicioEntidade( Sessao::getExercicio()        );

$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Relatorio" );
$obPDF->setTitulo            ( "Relação de Feriados" );
$obPDF->setSubTitulo         ( "" );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$obPDF->addRecordSet( $rsVazio );

$rsTransf50 = Sessao::read('transf50');
$rsTransf51 = Sessao::read('transf51');
$rsTransf52 = Sessao::read('transf52');
$rsTransf53 = Sessao::read('transf53');

if ( !empty( $rsTransf50 ) ) {

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho("FERIADOS FIXOS", 100, 10);
    $obPDF->addCampo("", -5 );

    $obPDF->addRecordSet( $rsTransf50 );
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho("DATA", 10, 10);
    $obPDF->addCabecalho("TIPO", 20, 10);
    $obPDF->addCabecalho("ABRANGÊNCIA",20, 10);
    $obPDF->addCabecalho("DESCRIÇÃO",50, 10);

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo("dt_feriado", 8 );
    $obPDF->addCampo("tipo", 8 );
    $obPDF->addCampo("tipoferiado", 8 );
    $obPDF->addCampo("descricao", 8 );

    $obPDF->addRecordSet( $rsVazio );
    $obPDF->setQuebraPaginaLista( false );

}

if ( !empty( $rsTransf51 ) ) {
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho("FERIADOS VARIÁVEIS", 100, 10);
    $obPDF->addCampo("", -5 );

    $obPDF->addRecordSet( $rsTransf51 );
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho("DATA", 10, 10);
    $obPDF->addCabecalho("TIPO", 20, 10);
    $obPDF->addCabecalho("ABRANGÊNCIA",20, 10);
    $obPDF->addCabecalho("DESCRIÇÃO",50, 10);

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo("dt_feriado", 8 );
    $obPDF->addCampo("tipo", 8 );
    $obPDF->addCampo("tipoferiado", 8 );
    $obPDF->addCampo("descricao", 8 );

    $obPDF->addRecordSet( $rsVazio );
    $obPDF->setQuebraPaginaLista( false );

}

if ( !empty( $rsTransf52 ) ) {
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho("PONTO FACULTATIVO", 100, 10);
    $obPDF->addCampo("",-5);

    $obPDF->addRecordSet( $rsTransf52 );
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho("DATA", 10, 10);
    $obPDF->addCabecalho("TIPO", 20, 10);
    $obPDF->addCabecalho("DESCRIÇÃO",50, 10);

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo("dt_feriado", 8 );
    $obPDF->addCampo("tipo", 8 );
    $obPDF->addCampo("descricao", 8 );

    $obPDF->addRecordSet( $rsVazio );
    $obPDF->setQuebraPaginaLista( false );

}

if ( !empty( $rsTransf53 ) ) {
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho("DIA COMPENSADO", 100, 10);
    $obPDF->addCampo("", -5 );

    $obPDF->addRecordSet( $rsTransf53 );
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho("DATA", 10, 10);
    $obPDF->addCabecalho("TIPO", 20, 10);
    $obPDF->addCabecalho("DESCRIÇÃO",50, 10);

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo("dt_feriado", 8 );
    $obPDF->addCampo("tipo", 8 );
    $obPDF->addCampo("descricao", 8 );
}

//$obPDF->montaPDF();
//$obPDF->OutPut();
$obPDF->show();
?>
