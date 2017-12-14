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
$Author: eduardoschitz $
$Date: 2008-02-06 14:24:19 -0200 (Qua, 06 Fev 2008) $

 Casos de uso: uc-02.01.33, uc-02.01.06, uc-03.04.02
*/

/*
$Log$
Revision 1.11  2007/10/02 18:28:09  leandro.zis
Ticket#9844#

Revision 1.10  2007/07/27 22:39:39  tonismar
Bug#9112#

Revision 1.9  2007/07/27 20:38:49  tonismar
alteração no nome do arquivo na chamada da popup Despesa2 -> Despesa

Revision 1.8  2007/07/27 18:21:55  tonismar
alteração no nome do arquivo na chamada da popup Despesa -> Despesa2 após commit do arquivo esperado.

Revision 1.7  2007/07/27 13:59:26  tonismar
alteração no nome do arquivo na chamada da popup Despesa2 -> Despesa

Revision 1.6  2007/07/24 19:54:19  leandro.zis
Bug#9637#

Revision 1.5  2007/03/15 17:58:04  vitor
#8632#

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

class  IPopUpDotacao extends BuscaInner
{
var $obCmbEntidades;

var $stAutorizacao;

function setAutorizacao($valor) { $this->stAutorizacao = $valor; }

function getAutorizacao() { return $this->stAutorizacao ; }

function IPopUpDotacao($obCmbEntidades = "")
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
}

function montaHTML()
{
    if ($this->obCmbEntidades) {
        if ( strtolower(get_class( $this->obCmbEntidades )) == "select" ) {
            $pgOcul = "'".CAM_GF_ORC_PROCESSAMENTO."OCDotacao.php?".Sessao::getId()."&".$this->obCampoCod->getName()."='+this.value+'&inCodEntidade='+document.frm.".$this->obCmbEntidades->getName().".value+'&stNomCampoCod=".$this->obCampoCod->getName()."&stIdCampoDesc=".$this->getId()."&stAutorizacao=".$this->getAutorizacao()."&stUsaEntidade=S'";
            $this->obCampoCod->obEvento->setOnChange ( "ajaxJavaScript($pgOcul,'buscaPopup');" );
            $this->setFuncaoBusca("if(document.frm.".$this->obCmbEntidades->getName().".value) abrePopUp('" . CAM_GF_ORC_POPUPS . "despesa/FLDespesa.php','frm', '".$this->obCampoCod->getName()."','".$this->getId()."','&inCodEntidade='+document.frm.".$this->obCmbEntidades->getName().".value,'".Sessao::getId()."','800','550'); else alertaAviso('É necessário informar uma entidade para a conta.','frm','erro','".Sessao::getId()."');");
        } else {
            $pgOcul = "'".CAM_GF_ORC_PROCESSAMENTO."OCDotacao.php?".Sessao::getId()."&".$this->obCampoCod->getName()."='+this.value+'&stNomSelectMultiplo=".$this->obCmbEntidades->getNomeLista2()."&stNomCampoCod=".$this->obCampoCod->getName()."&stIdCampoDesc=".$this->getId()."&stAutorizacao=".$this->getAutorizacao()."&stUsaEntidade=S'";
            $this->setFuncaoBusca("if(document.frm.".$this->obCmbEntidades->getNomeLista2()."[0]) abrePopUp('" . CAM_GF_ORC_POPUPS . "despesa/FLDespesa.php','frm', '".$this->obCampoCod->getName()."','".$this->getId()."',selectMultiploToString(".$this->obCmbEntidades->getNomeLista2().")+'&stNomSelectMultiplo=".$this->obCmbEntidades->getNomeLista2()."','".Sessao::getId()."','800','550'); else alertaAviso('É necessário informar uma entidade para a conta.','frm','erro','".Sessao::getId()."');");
        }
    } else {
        $pgOcul = "'".CAM_GF_ORC_PROCESSAMENTO."OCDotacao.php?".Sessao::getId()."&".$this->obCampoCod->getName()."='+this.value+'&stNomCampoCod=".$this->obCampoCod->getName()."&stIdCampoDesc=".$this->getId()."&stAutorizacao=".$this->getAutorizacao()."&stUsaEntidade=N'";
        $this->obCampoCod->obEvento->setOnChange ( "ajaxJavaScript($pgOcul,'buscaPopup');" );
        $this->setFuncaoBusca("abrePopUp('" . CAM_GF_ORC_POPUPS . "despesa/FLDespesa.php','frm', '".$this->obCampoCod->getName()."','".$this->getId()."','','".Sessao::getId()."','800','550');");
    }

    parent::montaHTML();
}
}
?>
