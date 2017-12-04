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
    *
    * Arquivo de formulário para importação de arquivo da leitora
    *
    *
    * @date 09/08/2010
    * @author Analista: Gelson
    * @author Desenvol: Tonismar
    *
    * @ignore
**/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stAcao = $_POST['stAcao'] ? $_POST['stAcao'] : $_GET['stAcao'];
if ( empty($stAcao) ) {
    $stAcao = 'importar';
}

Sessao::write("link", "");

$form = new Form();
$form->setAction( 'PRImportarColetora.php' );
$form->setTarget( 'oculto' );
$form->setEncType( 'multipart/form-data' );

$acao = new Hidden();
$acao->setName( 'stAcao' );
$acao->setValue( $stAcao );

$ctrl = new Hidden();
$ctrl->setName( 'stCtrl' );
$ctrl->setValue( '' );

$arquivo = new FileBox();
$arquivo->setRotulo( 'Arquivo' );
$arquivo->setName( 'arquivoColetora' );
$arquivo->setSize( 50 );
$arquivo->setTitle( 'Arquivo de coletora' );
$arquivo->setNull( false );
$arquivo->setMaxLength( 100 );
$arquivo->setId( 'arquivoColetora' );

$formulario = new Formulario();
$formulario->addForm( $form );
$formulario->addHidden( $acao );
$formulario->addHidden( $ctrl );
$formulario->addTitulo( 'Dados do Arquivo' );
$formulario->addComponente( $arquivo );
$formulario->setFormFocus( $arquivo->getId() );
$formulario->Ok();
$formulario->show();

include_once 'JSImportarColetora.js';
