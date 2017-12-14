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
 * Página que filtra a emissão dos Autos de Infração
 * Data de Criacao: 20/08/2008

 * @author Analista      : Heleno Menezes dos Santos
 * @author Desenvolvedor : Pedro Vaz de Mello de Medeiros

 * @package URBEM
 * @subpackage
 * @ignore

 $Id: FLEmitirAutoInfracao.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso:
 */

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

require_once( CAM_GT_FIS_COMPONENTES . "ITextBoxSelectTipoFiscalizacao.class.php" );
require_once( CAM_GT_FIS_NEGOCIO     . "RFISPenalidade.class.php" );
include_once (CAM_GT_FIS_NEGOCIO."/RFISProcessoFiscal.class.php");
include_once (CAM_GT_FIS_VISAO."/VFISProcessoFiscal.class.php");
$obControllerProcessoFiscal = new RFISProcessoFiscal;
$obVisaoProcessoFiscal = new VFISProcessoFiscal($obControllerProcessoFiscal);

$stAcao = 'emitir';

# Define o nome dos arquivos PHP
$stPrograma = "EmitirAutoInfracao";
$pgFilt     = "FL" . $stPrograma . ".php";
$pgList     = "LS" . $stPrograma . ".php";
$pgForm     = "FM" . $stPrograma . ".php";
$pgProc     = "PR" . $stPrograma . ".php";
$pgOcul     = "OC" . $stPrograma . ".php";
$pgJs       = "JS" . $stPrograma . ".php";

# Define campos hidden
$obHdnAcao = new Hidden();
$obHdnAcao->setName("stAcao");
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden();
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( $_REQUEST['stCtrl'] );

$obHdnCtrl = new Hidden();
$obHdnCtrl->setName ( "numcgm" );
$obHdnCtrl->setValue( substr( $_SESSION['numCgm'], 5, -2 ) );

$obHdnInicio = new Hidden();
$obHdnInicio->setName( "boInicio" );
$obHdnInicio->setValue( true );

# Define o form
$obForm = new Form();
$obForm->setAction( $pgList );
$obForm->setTarget( "telaPrincipal" );

# Define novo formulário
$obFormulario = new Formulario();
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnInicio );
$obFormulario->addTitulo( "Dados para Filtro" );

# Define tipo de fiscalizacao
$obTipoFiscalizacao = new ITextBoxSelectTipoFiscalizacao;
$obTipoFiscalizacao->setNull( true );
$obTipoFiscalizacao->setTitle( "Informe o Tipo de Fiscalização." );
$obTipoFiscalizacao->obTxtTipoFiscalizacao->setId( "txtTipoFiscalizacao" );
$obTipoFiscalizacao->obCmbTipoFiscalizacao->setId( "cmbTipoFiscalizacao" );
$obTipoFiscalizacao->geraFormulario( $obFormulario );

# Define o processo fiscal
$obProcessoFiscal = new TextBox();
$obProcessoFiscal->setName( "inCodProcesso" );
$obProcessoFiscal->setId( "inCodProcesso" );
$obProcessoFiscal->setSize( "10" );
$obProcessoFiscal->setRotulo( "Processo Fiscal" );
$obProcessoFiscal->setTitle( "Informe o Código do Processo Fiscal." );
$obProcessoFiscal->setNull( true );
$obFormulario->addComponente( $obProcessoFiscal );

# Define span que recebe os tipos de inscrição: 1 => inscrição econômica; 2 => inscrição imobiliária;
$obSpanTipoInscricao = new Span();
$obSpanTipoInscricao->setId('spnForm');
$obFormulario->addSpan( $obSpanTipoInscricao );

$obFormulario->ok( true );
if (!$obVisaoProcessoFiscal->getFiscalAtivo()) {
   SistemaLegado::exibeAviso("Fiscal não Habilitado para este tipo de operação.","","erro");
   $obFormulario = new Formulario;
}
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
