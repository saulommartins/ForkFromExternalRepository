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
* Data de Criação: 20/06/2006

* @author Analista: Cleisson Barboza
* @author Desenvolvedor: José Eduardo Porto

* @package URBEM
* @subpackage

$Revision: 30824 $
$Name$
$Author: leandro.zis $
$Date: 2007-07-30 11:21:16 -0300 (Seg, 30 Jul 2007) $

 Casos de uso: uc-02.01.33, uc-02.01.06, uc-03.04.02
*/

/*
$Log$
Revision 1.5  2007/07/30 14:14:43  leandro.zis
Correção para PHP5

Revision 1.4  2007/07/27 14:48:42  luciano
Bug#8887#

Revision 1.3  2007/05/11 14:01:29  cako
Bug #8887#

Revision 1.2  2007/05/09 15:02:02  cako
Ajustes

Revision 1.1  2006/11/20 22:56:54  gelson
Bug #7155#

Revision 1.4  2006/11/08 11:02:19  larocca
Inclusão dos Casos de Uso

Revision 1.3  2006/08/28 11:04:36  jose.eduardo
Ajustes no componente

Revision 1.2  2006/08/25 17:03:05  jose.eduardo
caso de uso

Revision 1.1  2006/08/25 16:14:31  jose.eduardo
Inclusao

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once ( CLA_BUSCAINNER );

class  IPopUpDotacaoFiltro extends BuscaInner
{
var $obCmbEntidades;
var $stTipoBusca;

function setTipoBusca($valor) { $this->stTipoBusca = $valor;   }

function IPopUpDotacaoFiltro($obCmbEntidades = "")
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
    if(!$this->stTipoBusca)
        $this->stTipoBusca = "autorizacaoEmpenho";
}
function montaHTML()
{
    if ($this->obCmbEntidades) {
        if ( !strstr(strtolower(get_class( $this->obCmbEntidades )),"selectmultiplo")) {
            $pgOcul = "'".CAM_GF_ORC_PROCESSAMENTO."OCDotacao.php?".Sessao::getId()."&".$this->obCampoCod->getName()."='+this.value+'&inCodEntidade='+document.frm.".$this->obCmbEntidades->getName().".value+'&stNomCampoCod=".$this->obCampoCod->getName()."&stIdCampoDesc=".$this->getId()."&stUsaEntidade=S'";
            $this->obCampoCod->obEvento->setOnChange ( "if (this.value) { ajaxJavaScript($pgOcul,'buscaPopup'); } else { document.getElementById('".$this->obCampoCod->getId()."').value = ''; document.getElementById('".$this->getId()."').innerHTML = '&nbsp;'; }" );
            $this->setFuncaoBusca(" if(document.frm.".$this->obCmbEntidades->getName().".value) abrePopUp('" . CAM_GF_ORC_POPUPS . "despesa/FLDespesa.php','frm','".$this->obCampoCod->getName()."','".$this->getId()."','?".Sessao::getId()."&tipoBusca=".$this->stTipoBusca."&".$this->obCampoCod->getName()."='+this.value+'&inCodEntidade='+document.frm.".$this->obCmbEntidades->getName().".value+'&stNomCampoCod=".$this->obCampoCod->getName()."&stIdCampoDesc=".$this->getId()."&stUsaEntidade=S&inCodEntidade='+document.frm.".$this->obCmbEntidades->getName().".value,'".Sessao::getId()."','800','550'); else alertaAviso('É necessário informar uma entidade para a conta.','frm','erro','".Sessao::getId()."');");
        } else {
            $pgOcul = "'".CAM_GF_ORC_PROCESSAMENTO."OCDotacao.php?".Sessao::getId()."&".$this->obCampoCod->getName()."='+this.value+'&stNomSelectMultiplo=".$this->obCmbEntidades->getNomeLista2()."&stNomCampoCod=".$this->obCampoCod->getName()."&stIdCampoDesc=".$this->getId()."&stUsaEntidade=S'";
            $this->obCampoCod->obEvento->setOnChange (" if(this.value) ajaxJavaScript($pgOcul+selectMultiploToString(".$this->obCmbEntidades->getNomeLista2()."),'buscaPopup'); else { document.getElementById('".$this->obCampoCod->getId()."').value = ''; document.getElementById('".$this->getId()."').innerHTML = '&nbsp;'; }" );
            $this->setFuncaoBusca("if(document.frm.".$this->obCmbEntidades->getNomeLista2()."[0]) abrePopUp('" . CAM_GF_ORC_POPUPS . "despesa/FLDespesa.php','frm','".$this->obCampoCod->getName()."','".$this->getId()."','?'+selectMultiploToString(".$this->obCmbEntidades->getNomeLista2().")+'&stNomSelectMultiplo=".$this->obCmbEntidades->getNomeLista2()."&tipoBusca=".$this->stTipoBusca."&".$this->obCampoCod->getName()."='+this.value+'&stNomSelectMultiplo=".$this->obCmbEntidades->getNomeLista2()."&stNomCampoCod=".$this->obCampoCod->getName()."&stIdCampoDesc=".$this->getId()."&stUsaEntidade=S','".Sessao::getId()."','800','550'); else alertaAviso('É necessário informar uma entidade para a conta.','frm','erro','".Sessao::getId()."');");
        }
    } else {
        $pgOcul = "'".CAM_GF_ORC_PROCESSAMENTO."OCDotacao.php?".Sessao::getId()."&".$this->obCampoCod->getName()."='+this.value+'&stNomCampoCod=".$this->obCampoCod->getName()."&stIdCampoDesc=".$this->getId()."&stUsaEntidade=N'";
        $this->obCampoCod->obEvento->setOnChange ( "if (this.value) { ajaxJavaScript($pgOcul,'buscaPopup'); } else { document.getElementById('".$this->obCampoCod->getId()."').value = ''; document.getElementById('".$this->getId()."').innerHTML = '&nbsp;'; }" );
        $this->setFuncaoBusca("abrePopUp('" . CAM_GF_ORC_POPUPS . "despesa/FLDespesa.php?".Sessao::getId()."&".$this->obCampoCod->getName()."='+this.value+'&stNomCampoCod=".$this->obCampoCod->getName()."&stIdCampoDesc=".$this->getId()."&stUsaEntidade=N','frm', '".$this->obCampoCod->getName()."','".$this->getId()."','','".Sessao::getId()."','800','550');");
    }

    parent::montaHTML();
}
}
?>
