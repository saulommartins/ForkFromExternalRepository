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
 * Página de formulário para processar Penalidade
 * Data de Criacao: 25/07/2008

 * @author Analista      : Heleno Menezes dos Santos
 * @author Desenvolvedor : Pedro Vaz de Mello de Medeiros
 * @ignore

 $Id: FLManterPenalidade.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso:
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once( CAM_GA_NORMAS_CLASSES . "componentes/IPopUpNorma.class.php" );
include_once( CAM_GT_FIS_COMPONENTES . "ITextBoxSelectTipoPenalidade.class.php" );
include_once( CAM_GT_FIS_NEGOCIO . "RFISPenalidade.class.php" );

$stAcao = $request->get('stAcao');

Sessao::write( 'link', "" );
Sessao::write( 'arValores', array() );

if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

# Define o nome dos arquivos PHP
$stPrograma = "ManterPenalidade";
$pgFilt     = "FL" . $stPrograma . ".php";
$pgList     = "LS" . $stPrograma . ".php";
$pgForm     = "FM" . $stPrograma . ".php";
$pgProc     = "PR" . $stPrograma . ".php";
$pgOcul     = "OC" . $stPrograma . ".php";
$pgJs       = "JS" . $stPrograma . ".js";

include_once( $pgJs );

# Define form
$obForm = new Form();
$obForm->setAction( $pgList );
$obForm->setTarget( "telaPrincipal" );

# Define campos escondidos
$obHdnAcao = new Hidden();
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden();
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

# Define descrição da penalidade
$obDescricao = new TextBox();
$obDescricao->setName( "stNomPenalidade" );
$obDescricao->setRotulo( "Descrição da Penalidade" );
$obDescricao->setTitle( "Descrição da penalidade." );
$obDescricao->setSize( 50 );
$obDescricao->setMaxLength( 80 );

# Define o formulário e acrescenta todos os componentes
$obFormulario = new Formulario();
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo( "Dados para Filtro" );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );

# Define componente tipo de penalidade
$obITextBoxSelectTipoPenalidade = new ITextBoxSelectTipoPenalidade();
$obITextBoxSelectTipoPenalidade->setTitle( "Tipo de penalidade." );
$obITextBoxSelectTipoPenalidade->setName( "inCodTipoPenalidade" );
$obITextBoxSelectTipoPenalidade->geraFormulario( $obFormulario );

$obFormulario->addComponente( $obDescricao );
$obFormulario->ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
