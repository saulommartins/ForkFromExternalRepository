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
    * PÃ¡gina do Frame oculto para Popup de Responsavel Tecnico
    * Data de CriaÃ§Ã£o   : 20/04/2005

    * @author Tonismar Régis Bernardo

    * @ignore

    * $Id: OCProcurarRespTecnico.php 59612 2014-09-02 12:00:51Z gelson $

    Casos de uso: uc-05.02.04
*/

/*
$Log$
Revision 1.8  2007/07/03 21:34:01  bruce
Bug #9552# , Bug #9534#

Revision 1.7  2006/09/15 13:50:33  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

include_once ( CAM_GA_CSE_NEGOCIO."RProfissao.class.php"             );
include_once ( CAM_GA_CSE_NEGOCIO."RConselho.class.php"              );
include_once ( CAM_GA_ADM_NEGOCIO."RAdministracaoUF.class.php"                    );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLoteRural.class.php"          );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLoteUrbano.class.php"         );
include_once ( CAM_GA_CGM_NEGOCIO."RCGMPessoaFisica.class.php"       );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMResponsavelTecnico.class.php" );
include_once ( CAM_FW_URBEM."MontaLocalizacao.class.php"   );

switch ($_GET['stCtrl']) {

    case 'buscaPopup':
        $obRegra = new RCEMResponsavelTecnico();
        if ($_POST[$_GET['stNomCampoCod']]) {
            $obRegra->setNumCGM( $_POST[$_GET['stNomCampoCod']] );
            $obRegra->listarResponsavelTecnico( $rsResponsavelTecnico );
            $stValorRetorno = $rsResponsavelTecnico->getCampo( "nom_cgm" );
            $inCodProfissao = $rsResponsavelTecnico->getCampo('cod_profissao');
        }
        $stJs = "retornaValorBscInner( '".$_GET['stNomCampoCod']."', '".$_GET['stIdCampoDesc']."', '".$_GET['stNomForm']."', '".$stValorRetorno."');";
        if( $inCodProfissao )
                $stJs .= "parent.frames['telaPrincipal'].document.".$_GET['stNomForm'].".inCodProfissao.value=".$rsResponsavelTecnico->getCampo('cod_profissao').";";
        SistemaLegado::executaFrameOculto($stJs);
    break;
}

?>
