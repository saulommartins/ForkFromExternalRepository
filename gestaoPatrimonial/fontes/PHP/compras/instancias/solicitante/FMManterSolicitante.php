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
    * Arquivo de formulario para alteração/inclusão de Solicitantes
    * Data de Criação: 11/02/2008

    * @author Analista: Gelson W
    * @author Luiz Felipe Prestes Teixeira

    * Casos de uso: uc-03.04.34

    $Id: FMManterSolicitante.php 59612 2014-09-02 12:00:51Z gelson $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GP_COM_MAPEAMENTO."TComprasSolicitante.class.php"       );
include_once ( CLA_IPOPUPCGM);

//Define o nome dos arquivos PHP
$stPrograma = "ManterSolicitante";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');

if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$stLocation = $pgList . "?". Sessao::getId() . "&stAcao=" . $stAcao;

$rsSolicitante = new RecordSet;

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

if ($stAcao == "incluir") {
    $obBscCGMSolicitante = new IPopUpCGM ($obForm);
    $obBscCGMSolicitante->setRotulo('Solicitante');
    $obBscCGMSolicitante->setId('stNomCGMSolicitante');
    $obBscCGMSolicitante->setNull( false);
    $obBscCGMSolicitante->setTitle( 'Informe o CGM do Solicitante.');
    $obBscCGMSolicitante->setValue( $stNomCGMSolicitante);
    $obBscCGMSolicitante->obCampoCod->setValue( $inCGM);
    $obBscCGMSolicitante->obCampoCod->setSize(10);
    $obBscCGMSolicitante->obCampoCod->setName( 'inCodCGMSolicitante' );

} else {

    if ($_REQUEST['boAtivo'] == 'Ativo') {
        $boAtivo = true;
    } else {
        $boAtivo = false;
    }

    $stNomCGMSolicitante = $_REQUEST['stNomCGMSolicitante'];
    $inCodCGMSolicitante = $_REQUEST['inCodCGMSolicitante'];

    $obHdnCGM = new Hidden;
    $obHdnCGM->setName("inCodCGMSolicitante");
    $obHdnCGM->setValue($inCodCGMSolicitante);

    $obLblCodigoCGM= new Label;
    $obLblCodigoCGM->setRotulo( "Solicitante" );
    $obLblCodigoCGM->setValue( $inCodCGMSolicitante." - ".$stNomCGMSolicitante);
}

$obRdbAtivo = new Radio;
$obRdbAtivo->setTitle( "Selecione o status do Solicitante." );
$obRdbAtivo->setName( "boAtivo" );
$obRdbAtivo->setId( "boAtivo" );
$obRdbAtivo->setChecked( $boAtivo);
$obRdbAtivo->setValue( 'true' );
$obRdbAtivo->setRotulo( "Status" );
$obRdbAtivo->setLabel( "Ativo" );
$obRdbAtivo->setNull( false );

$obRdbInativo = new Radio;
$obRdbInativo->setName( "boAtivo" );
$obRdbInativo->setId( "boAtivo" );
$obRdbInativo->setValue( 'false' );
$obRdbInativo->setLabel( "Inativo" );
$obRdbInativo->setChecked( !$boAtivo);

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm);
$obFormulario->setAjuda("UC-03.04.34");
$obFormulario->addHidden( $obHdnAcao);
$obFormulario->addTitulo( "Dados do Solicitante" );

if ($stAcao == "incluir") {
    $obFormulario->addComponente( $obBscCGMSolicitante );
} else {
    $obFormulario->addHidden( $obHdnCGM);
    $obFormulario->addComponente( $obLblCodigoCGM);
    $obFormulario->agrupaComponentes( array( $obRdbAtivo, $obRdbInativo ) );
}

if ($stAcao == "incluir") {
    $obFormulario->OK();
} else {
    $obFormulario->Cancelar( $stLocation );
}

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
