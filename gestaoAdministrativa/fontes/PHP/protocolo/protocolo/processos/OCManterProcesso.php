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
    * Arquivo de instância para manutenção dos processos
    * Data de Criação: 16/10/2006

    * @author Analista: Cassiano de Vasconcellos Ferreira
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    Casos de uso: uc-01.06.98

    $Id: OCManterProcesso.php 61966 2015-03-18 21:54:54Z jean $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once( CAM_FW_HTML.'MontaOrgUniDepSet.class.php' );

$stJs = "";

$obMontaOrgUniDepSet = new MontaOrgUniDepSet;
switch ($_REQUEST['stCtrl']) {
    case 'documento':
        ob_start();
        include_once( CAM_GA_PROT_COMPONENTES."IChkDocumentoProcesso.class.php" );
        ob_clean();
        $arChave = preg_split("/[^a-zA-Z0-9]/",$_GET['codClassifAssunto']);
        $inCodigoClassificacao = $arChave[0] < 1 ? 0 : $arChave[0];
        $inCodigoAssunto = $arChave[1] < 1 ? 0 : $arChave[1];
        echo "document.getElementById('obCmpDocumento').style.display = 'none';";
        $obIChkDocumentoProcesso = new IChkDocumentoProcesso();
        $obIChkDocumentoProcesso->setCodigoClassificacao($inCodigoClassificacao);
        $obIChkDocumentoProcesso->setCodigoAssunto($inCodigoAssunto);
        $obFormulario = new Formulario();
        $obIChkDocumentoProcesso->geraFormulario($obFormulario);
        $obFormulario->montaInnerHTML();
        echo "document.getElementById('obCmpDocumento').innerHTML ='".$obFormulario->getHTML()."';";
        echo "document.getElementById('obCmpDocumento').style.display = 'block';";
        //echo "alert('".$obFormulario->getHTML()."');";
        $stJs = ob_get_contents();
        ob_end_clean();
        echo $stJs;
    break;
    case 'unidade':
        $arChaveOrgao = explode( '-', $_POST['stChaveOrgao'] );
        $obMontaOrgUniDepSet->obRSetor->obDepartamento->obUnidade->obOrgao->setCodOrgao( $arChaveOrgao[0] );
        $obMontaOrgUniDepSet->obRSetor->obDepartamento->obUnidade->obOrgao->setExercicio( $arChaveOrgao[1] );
        $obMontaOrgUniDepSet->obRSetor->setNomSetor( $_POST['stChaveNomSetor'] );
        $obErro = $obMontaOrgUniDepSet->montarUnidade();
    break;
    case 'departamento':
        $arChaveOrgao = explode( '-', $_POST['stChaveOrgao'] );
        $obMontaOrgUniDepSet->obRSetor->obDepartamento->obUnidade->obOrgao->setCodOrgao( $arChaveOrgao[0] );
        $obMontaOrgUniDepSet->obRSetor->obDepartamento->obUnidade->obOrgao->setExercicio( $arChaveOrgao[1] );
        $obMontaOrgUniDepSet->obRSetor->obDepartamento->obUnidade->setCodUnidade( $_POST['stChaveUnidade'] );
        $obMontaOrgUniDepSet->obRSetor->setNomSetor( $_POST['stChaveNomSetor'] );
        $obErro = $obMontaOrgUniDepSet->montarDepartamento();
    break;
    case 'setor':
        $arChaveOrgao = explode( '-', $_POST['stChaveOrgao'] );
        $obMontaOrgUniDepSet->obRSetor->obDepartamento->obUnidade->obOrgao->setCodOrgao( $arChaveOrgao[0] );
        $obMontaOrgUniDepSet->obRSetor->obDepartamento->obUnidade->obOrgao->setExercicio( $arChaveOrgao[1] );
        $obMontaOrgUniDepSet->obRSetor->obDepartamento->obUnidade->setCodUnidade( $_POST['stChaveUnidade'] );
        $obMontaOrgUniDepSet->obRSetor->obDepartamento->setCodDepartamento( $_POST['stChaveDepartamento'] );
        $obMontaOrgUniDepSet->obRSetor->setNomSetor( $_POST['stChaveNomSetor'] );
        $obErro = $obMontaOrgUniDepSet->montarSetor();
    break;
    case 'chaveSetor':
        $arChaveOrgao = explode( '-', $_POST['stChaveOrgao'] );
        $obMontaOrgUniDepSet->obRSetor->obDepartamento->obUnidade->obOrgao->setCodOrgao( $arChaveOrgao[0] );
        $obMontaOrgUniDepSet->obRSetor->obDepartamento->obUnidade->obOrgao->setExercicio( $arChaveOrgao[1] );
        $obMontaOrgUniDepSet->obRSetor->obDepartamento->obUnidade->setCodUnidade( $_POST['stChaveUnidade'] );
        $obMontaOrgUniDepSet->obRSetor->obDepartamento->setCodDepartamento( $_POST['stChaveDepartamento'] );
        $obMontaOrgUniDepSet->obRSetor->setCodSetor( $_POST['stChaveSetor'] );
        $obMontaOrgUniDepSet->obRSetor->setNomSetor( $_POST['stChaveNomSetor'] );
        $obMontaOrgUniDepSet->montarChaveSetor();
    break;
    case 'montarPorChave':
        $obMontaOrgUniDepSet->setChaveSetor( $_POST['stChaveSetorTxt'] );
        $obMontaOrgUniDepSet->obRSetor->setNomSetor( $_POST['stChaveNomSetor'] );
        $obMontaOrgUniDepSet->montarPorChave();
    break;
    case 'montaEntidade':
        if ($_REQUEST['stIncluirAssinaturas'] == 'sim') {
            include_once CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeUsuario.class.php";
            $obISelectEntidadeUsuario = new ITextBoxSelectEntidadeUsuario();
            $obISelectEntidadeUsuario->setNull(false);
            $obISelectEntidadeUsuario->obSelect->obEvento->setOnChange('getIMontaAssinaturas();');

            $obFormulario = new Formulario();
            $obFormulario->addComponente($obISelectEntidadeUsuario);
            $obFormulario->montaInnerHTML();
            $stHTML = $obFormulario->getHTML();
            $stJs .= "jQuery('#spnEntidade').html('".$stHTML."');\n";
            echo $stJs;
        } else {
            $stJs .= "jQuery('#spnEntidade').html('');\n";
            echo $stJs;
        }
    break;
}
?>
