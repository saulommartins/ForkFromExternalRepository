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
    * Página que Lista dos Documentos para o Levantamento Fiscal
    * Data de Criacao: 21/08/2008

    * @author Analista      : Heleno Menezes dos Santos
    * @author Desenvolvedor : Janilson Mendes P. da Silva

    * @package URBEM
    * @subpackage

    * @ignore

    *Casos de uso:

    $Id:$
*/
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';
require_once( CAM_GT_FIS_NEGOCIO."RFISManterLevantamento.class.php" );
require_once( CAM_GT_FIS_VISAO."VFISManterLevantamento.class.php" );

//Instanciando a Classe de Controle e de Visao
$obController = new RFISManterLevantamento;
$obVisao = new VFISManterLevantamento( $obController );

$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
$inTipoFiscalizacao = $_GET['inTipoFiscalizacao'] ?  $_GET['inTipoFiscalizacao'] : $_POST['inTipoFiscalizacao'];

//Filtros da pesquisa.
$where = $obVisao->filtrosProcessoFiscal( $_REQUEST );

$rsProcessoFiscal = $obVisao->recuperarListaProcessoFiscalEconomicaDocumentos( $where );
$stTipoInscricao = "Inscrição Econômica";

$rsProcessoFiscal = $obVisao->executarCheckBox( $rsProcessoFiscal );

//Form
$obForm = new Form();
$obForm->setAction( $pgProc );

//Monta o formulário
$obFormulario = new Formulario();
$obFormulario->addForm( $obForm );

$tableListaDocumentos = new Table();
$tableListaDocumentos->setRecordset( $rsProcessoFiscal );
$tableListaDocumentos->setSummary("Lista de Documentos");
//$tableListaDocumentos->setConditional( true , "#ddd" );
$tableListaDocumentos->Head->addCabecalho( 'Documento', 100,'');
$tableListaDocumentos->Head->addCabecalho( 'Entregues', 10,'');
$tableListaDocumentos->Body->addCampo( 'nom_documento' , 'E','' );
$tableListaDocumentos->Body->addCampo( 'check', 'C','' );
$tableListaDocumentos->montaHTML();

$obSpanListaDocumentos = new Span;
$obSpanListaDocumentos->setId( 'spamListaCredito' );
$obSpanListaDocumentos->setValue( $tableListaDocumentos->getHtml() );
$obFormulario->addSpan( $obSpanListaDocumentos );
$obFormulario->show();
//Para corrigir o Cache do Navegador
unset( $inTipoFiscalizacao );

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
