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
    * Data de Criação   : 19/10/2005

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 30824 $
    $Name$
    $Autor:$
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.08

*/

/*
$Log$
Revision 1.8  2006/07/05 20:43:33  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                             );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaFormPDF();

// Adicionar logo no relatorio
    $rsRecordSet = Sessao::read('rsRelatorio');
    $rsRecordSet->setPrimeiroElemento();
    $rsRecordSet->proximo();
    $stCodEntidade = $rsRecordSet->getCampo("valor");
    $inCodEntidade = $stCodEntidade{0};
    $obRRelatorio->setCodigoEntidade( $inCodEntidade );
    $obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );
    $rsRecordSet->setPrimeiroElemento();

$obRRelatorio->setExercicio     ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );

$arConfiguracao['nom_acao'] = "Relatório de Reserva de Saldos";

$obPDF->setUsuario              ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura   ( $arConfiguracao );
$obPDF->setSubTitulo            ( "Relatório Reserva de Saldos - ".Sessao::read('stDtReserva') );

$obPDF->addRecordSet            ( $rsRecordSet );
//$obPDF->setAlturaCabecalho      ( 5 );
//$obPDF->setQuebraPaginaLista    ( false );
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCabecalho            ( "", 30, 5);
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCabecalho            ( "", 70, 5);
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCampo                ( "descricao", 10);//, '', '', 'TLRB');
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCampo                ( "valor"    , 10);//, '', '', 'TLRB');

$obPDF->show();

?>
