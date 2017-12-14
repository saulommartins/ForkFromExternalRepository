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
    * Manutneção de usuários
    * Data de Criação: 25/07/2005

    * @author Analista: Cassiano
    * @author Desenvolvedor: Cassiano

    Casos de uso: uc-01.03.93

    $Id: FMManterUsuario.php 59612 2014-09-02 12:00:51Z gelson $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_FW_HTML.'MontaOrgUniDepSet.class.php';
include_once CAM_GA_ADM_NEGOCIO.'RUsuario.class.php';

include_once CAM_GA_ORGAN_COMPONENTES."IMontaOrganograma.class.php";
include_once CAM_GA_ORGAN_MAPEAMENTO."TOrganogramaOrganograma.class.php";

$stAcao = $_REQUEST['stAcao'] ? $_REQUEST['stAcao'] : $stAcao;

//Define o nome dos arquivos PHP
$stPrograma = "ManterUsuario";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obTOrganogramaOrganograma = new TOrganogramaOrganograma;
$obTOrganogramaOrganograma->setDado('ativo', true);
$obTOrganogramaOrganograma->recuperaOrganogramasAtivo($rsOrganogramaAtivo);

# Instancia do novo componente de Organograma
if ($stAcao == 'alterar') {
    $obIMontaOrganograma = new IMontaOrganograma(true);
} else {
    $obIMontaOrganograma = new IMontaOrganograma(false);
}

$obIMontaOrganograma->setNivelObrigatorio(1);

if ($stAcao == 'alterar') {
    $obRUsuario = new RUsuario;
    $obRUsuario->obRCGM->setNumCGM( $_REQUEST['inNumCGM'] );
    $obRUsuario->consultar( $rsLista );

    $stUserName     = $obRUsuario->getUsername ();
    $boStatus       = $obRUsuario->getStatus   ();
    $stDataCadastro = $obRUsuario->getCadastro ();
    $inCodOrgao     = $obRUsuario->getCodOrgao ();

    # Seta o código do Organograma para carregar a edição.
    $obIMontaOrganograma->setCodOrgao($inCodOrgao);
}

$obLblNumCGM = new Label;
$obLblNumCGM->setName   ( "inNumCGMLbl"     );
$obLblNumCGM->setValue  ( $_GET['inNumCGM'] );
$obLblNumCGM->setRotulo ( 'CGM'             );

$obLblNomCGM = new Label;
$obLblNomCGM->setName   ( "stNomCGMLbl"     );
$obLblNomCGM->setValue  ( $_GET['stNomCGM'] );
$obLblNomCGM->setRotulo ( 'Nome CGM'        );

$obLblUserName = new Label;
$obLblUserName->setName   ( "stUsername"     );
$obLblUserName->setValue  ( $stUserName      );
$obLblUserName->setRotulo ( 'Username'       );

$obHdnUserName = new Hidden;
$obHdnUserName->setName   ( "stUserName"     );
$obHdnUserName->setValue  ( $stUserName      );

$obHdnNumCGM = new Hidden;
$obHdnNumCGM->setName  ( 'inNumCGM'        );
$obHdnNumCGM->setValue ( $_GET['inNumCGM'] );

$obHdnNomCGM = new Hidden;
$obHdnNomCGM->setName  ( 'stNomCGM'        );
$obHdnNomCGM->setValue ( $_GET['stNomCGM'] );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( 'stCtrl' );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( 'stAcao' );
$obHdnAcao->setValue( $stAcao );

$obTxtUserName =  new TextBox;
$obTxtUserName->setName      ( 'stUserName' );
$obTxtUserName->setRotulo    ( 'Username'   );
$obTxtUserName->setNull      ( false        );
$obTxtUserName->setSize      ( 15           );
$obTxtUserName->setMaxLength ( 15           );
$obTxtUserName->setValue     ( $stUserName  );

$obPswSenha = new Password;
$obPswSenha->setName      ( 'stSenha' );
$obPswSenha->setRotulo    ( 'Senha'   );
$obPswSenha->setNull      ( false     );
$obPswSenha->setSize      ( 34        );
$obPswSenha->setMaxLength ( 34        );

$obPswConfirmacaoSenha = new Password;
$obPswConfirmacaoSenha->setName      ( 'stConfirmacaoSenha' );
$obPswConfirmacaoSenha->setRotulo    ( 'Confirmação Senha'  );
$obPswConfirmacaoSenha->setNull      ( false     );
$obPswConfirmacaoSenha->setSize      ( 34        );
$obPswConfirmacaoSenha->setMaxLength ( 34        );

$obRadAtivo = new Radio;
$obRadAtivo->setName    ( 'boAtivo' );
$obRadAtivo->setRotulo  ( 'Status'  );
$obRadAtivo->setLabel   ( 'Ativo'   );
$obRadAtivo->setValue   ( 'A'       );
$obRadAtivo->setChecked ( true      );
$obRadAtivo->setNull    ( false     );

$obRadInativo = new Radio;
$obRadInativo->setName    ( 'boAtivo' );
$obRadInativo->setRotulo  ( 'Status'  );
$obRadInativo->setLabel   ( 'Inativo' );
$obRadInativo->setValue   ( 'I'       );
$obRadInativo->setChecked ( false     );
$obRadInativo->setNull    ( false     );

if ($stAcao == 'usuario') {
    $obRadInativo->setChecked ( false );
    $obRadAtivo->setChecked   ( true  );
} elseif ($stAcao == 'alterar') {
    if ($boStatus == 'A') {
        $obRadInativo->setChecked ( false );
        $obRadAtivo->setChecked   ( true  );
    } else {
        $obRadInativo->setChecked ( true  );
        $obRadAtivo->setChecked   ( false );
    }
}

$obLblOrganograma = new Label;
$obLblOrganograma->setName   ( "organogramaAtivo"     );
$obLblOrganograma->setValue  ( $rsOrganogramaAtivo->getCampo('cod_organograma').' - '.$rsOrganogramaAtivo->getCampo('implantacao') );
$obLblOrganograma->setRotulo ( 'Organograma Ativo' );

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( 'oculto' );

$obFormulario = new Formulario;
$obFormulario->setAjuda( "UC-01.03.93" );
$obFormulario->addForm       ( $obForm );
$obFormulario->addTitulo     ( 'Dados para usuário' );
$obFormulario->addHidden     ( $obHdnNumCGM   );
$obFormulario->addHidden     ( $obHdnNomCGM   );
$obFormulario->addHidden     ( $obHdnCtrl     );
$obFormulario->addHidden     ( $obHdnAcao     );
$obFormulario->addComponente ( $obLblNumCGM   );
$obFormulario->addComponente ( $obLblNomCGM   );

if ($stAcao == 'usuario') {
    $obFormulario->addComponente ( $obTxtUserName );
    $obFormulario->addComponente ( $obPswSenha    );
    $obFormulario->addComponente ( $obPswConfirmacaoSenha );
} else {
    $obFormulario->addComponente ( $obLblUserName );
    $obFormulario->addHidden     ( $obHdnUserName );
}

$obFormulario->agrupaComponentes ( array( $obRadAtivo,$obRadInativo ) );

$obFormulario->addComponente ( $obLblOrganograma );

$obIMontaOrganograma->geraFormulario($obFormulario);

# $obMontaOrgUniDepSet->montaFormulario( $obFormulario );

$obFormulario->Cancelar($pgList.'?'.Sessao::getId().'&stAcao='.$stAcao);

$obFormulario->show();

?>
