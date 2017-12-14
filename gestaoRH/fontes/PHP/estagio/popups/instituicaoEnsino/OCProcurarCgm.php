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
    * Arquivo de filtro de busca de CGM de Instituição/Entidade
    * Data de Criação: 03/10/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2006-10-03 08:08:21 -0300 (Ter, 03 Out 2006) $

    * Casos de uso: uc-04.07.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GA_CGM_NEGOCIO."RCGMPessoaFisica.class.php"               );
include_once( CAM_GA_CGM_NEGOCIO."RCGMPessoaJuridica.class.php"             );

switch ($_GET['stCtrl']) {

    case 'buscaPopup':
        if ($_POST[ $_GET['stNomCampoCod'] ] != "") {
            if ($_REQUEST["stTipoBusca"]=="fisica") {
                $obRegra = new RCGMPessoaFisica();
            } elseif ($_REQUEST["stTipoBusca"]=="juridica") {
                $obRegra = new RCGMPessoaJuridica();
            } else {
                $obRegra = new RCGM();
            }
            $obRegra->setNumCGM( $_POST[$_GET['stNomCampoCod']] );
            $obRegra->consultarCGM($rsCGM);
            $stNomCGM = addslashes($obRegra->getNomCGM());
        }

        if ($stNomCGM == '' && $_REQUEST["stTipoBusca"]) {
            $stJs .= "alertaAviso('@Número do CGM (". $_POST[ $_GET['stNomCampoCod'] ] .") não encontrado no cadastro de Pessoa ".$_REQUEST["stTipoBusca"]."', 'form','erro','".Sessao::getId()."');";
            sistemaLegado::executaFrameOculto( $stJs );
        }
            sistemaLegado::executaFrameOculto("retornaValorBscInner( '".$_GET['stNomCampoCod']."', '".$_GET['stIdCampoDesc']."', '".$_GET['stNomForm']."', '".$stNomCGM."')");

    break;

}

?>
