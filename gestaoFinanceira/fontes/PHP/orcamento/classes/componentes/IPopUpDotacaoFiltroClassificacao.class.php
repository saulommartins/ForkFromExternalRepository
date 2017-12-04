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
* Arquivo de popup de busca de Recurso
* Data de Criação: 30/07/2007

* @author Analista: Anelise
* @author Desenvolvedor: Leandro Zis

* @package URBEM
* @subpackage

 Casos de uso: uc-02.01.06
 Casos de uso: uc-03.03.07
*/

/*
$Log$
Revision 1.5  2007/10/02 18:28:09  leandro.zis
Ticket#9844#

Revision 1.4  2007/08/13 20:23:08  bruce
fiz a pop'up de dotações ter o mesmo filtro da busca via digitação

Revision 1.3  2007/08/01 20:55:23  gelson
Bug#9799#

Revision 1.2  2007/08/01 20:53:00  gelson
Adicionado o caso de uso da GF.

Revision 1.1  2007/07/30 15:09:18  leandro.zis
Bug#9637#



*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once ( CLA_BUSCAINNER );

class  IPopUpDotacaoFiltroClassificacao extends BuscaInner
{
var $obCmbEntidades;

var $stAutorizacao;

var $stTipoBusca;

function setTipoBusca($stBusca) { $this->stTipoBusca = $stBusca; }
function getTipoBusca() { return $this->stTipoBusca; }



function setAutorizacao($valor) { $this->stAutorizacao = $valor; }

function getAutorizacao() { return $this->stAutorizacao ; }

function IPopUpDotacaoFiltroClassificacao($obCmbEntidades = "")
{
    parent::BuscaInner();
if (!(is_object($obCmbEntidades))) {
    $this->obCmbEntidades = unserialize($obCmbEntidades);
} else {
    $this->obCmbEntidades = $obCmbEntidades;
}
    $this->setRotulo ( "Dotação"   );
    $this->setTitle  ( "Informe a Dotação." );
    $this->setNulL   ( true                     );
    $this->setId     ( "stNomDotacao"           );
    $this->setValue  ( ""                       );
    $this->obCampoCod->setName ( "inCodDotacao" );
    $this->obCampoCod->setId   ( "inCodDotacao" );
    $this->setTipoBusca        ( "autorizacaoEmpenho" );
}

function montaHTML()
{
    if ($this->obCmbEntidades) {
        if ( ( strtolower(get_class( $this->obCmbEntidades )) == "select" ) or ( strtolower(get_class( $this->obCmbEntidades )) == "itextboxselectentidadegeral" ) ) {
            $pgOcul = "'".CAM_GF_ORC_PROCESSAMENTO."OCDotacao.php?".Sessao::getId()."&".$this->obCampoCod->getName()."='+this.value+'&inCodEntidade='+document.frm.".$this->obCmbEntidades->getName().".value+'&stNomCampoCod=".$this->obCampoCod->getName()."&stIdCampoDesc=".$this->getId()."&stAutorizacao=".$this->getAutorizacao()."&stUsaEntidade=S'";
            $this->obCampoCod->obEvento->setOnChange ( "ajaxJavaScript($pgOcul,'buscaPopup');" );



            $this->setFuncaoBusca("if(document.frm.".$this->obCmbEntidades->getName().".value)
                                            abrePopUp('" . CAM_GF_ORC_POPUPS . "despesa/FLDespesa2.php','frm', '".
                                            $this->obCampoCod->getName()."','".
                                            $this->getId()."','".$this->stTipoBusca."&inCodEntidade='+document.frm.".
                                            $this->obCmbEntidades->getName().".value,'".Sessao::getId()."','800','550');
                                   else alertaAviso('É necessário informar uma entidade para a conta.','frm','erro','".Sessao::getId()."');");

        } else {
            $pgOcul = "'".CAM_GF_ORC_PROCESSAMENTO."OCDotacao.php?".Sessao::getId()."&".$this->obCampoCod->getName()."='+this.value+'&stNomSelectMultiplo=".$this->obCmbEntidades->getNomeLista2()."&stNomCampoCod=".$this->obCampoCod->getName()."&stIdCampoDesc=".$this->getId()."&stAutorizacao=".$this->getAutorizacao()."&stUsaEntidade=S'";
            $this->obCampoCod->obEvento->setOnChange ( "ajaxJavaScript($pgOcul+selectMultiploToString(".$this->obCmbEntidades->getNomeLista2()."),'buscaPopup');" );
            $this->setFuncaoBusca("if(document.frm.".$this->obCmbEntidades->getNomeLista2()."[0]) abrePopUp('" . CAM_GF_ORC_POPUPS . "despesa/FLDespesa2.php','frm', '".$this->obCampoCod->getName()."','".$this->getId()."',selectMultiploToString(".$this->obCmbEntidades->getNomeLista2().")+'&stNomSelectMultiplo=".$this->obCmbEntidades->getNomeLista2()."','".Sessao::getId()."','800','550');
                                   else alertaAviso('É necessário informar uma entidade para a conta.','frm','erro','".Sessao::getId()."');");
        }
    } else {
        $pgOcul = "'".CAM_GF_ORC_PROCESSAMENTO."OCDotacao.php?".Sessao::getId()."&".$this->obCampoCod->getName()."='+this.value+'&stNomCampoCod=".$this->obCampoCod->getName()."&stIdCampoDesc=".$this->getId()."&stAutorizacao=".$this->getAutorizacao()."&stUsaEntidade=N'";
        $this->obCampoCod->obEvento->setOnChange ( "ajaxJavaScript($pgOcul,'buscaPopup');" );
        $this->setFuncaoBusca("abrePopUp('" . CAM_GF_ORC_POPUPS . "despesa/FLDespesa2.php','frm', '".$this->obCampoCod->getName()."','".$this->getId()."','','".Sessao::getId()."','800','550');");
    }

    parent::montaHTML();
}
}
?>
