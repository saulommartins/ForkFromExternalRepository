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
    * Arquivo de popup de busca de Receita
    * Data de Criação: 09/05/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    $Id: IPopUpReceita.class.php 59612 2014-09-02 12:00:51Z gelson $

     Casos de uso: uc-02.01.06
*/

/*
$Log: IPopUpReceita.class.php,v $
Revision 1.4  2007/07/30 15:09:36  leandro.zis
Correção para PHP5

Revision 1.3  2007/06/12 21:18:47  cako
Bug #9349#

Revision 1.2  2007/06/04 22:17:42  cako
Bug #9349#

Revision 1.1  2007/05/11 02:25:14  diego
Novos componentes adicionados para corrigir o bug:
Bug #9113#

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once ( CLA_BUSCAINNER );

class  IPopUpReceita extends BuscaInner
{
var $usaFiltro;
var $stTipoBusca;
var $obCmbEntidades;
var $boDedutora;
var $stEvento;

function setUsaFiltro($valor) { $this->usaFiltro = $valor; }
function setTipoBusca($valor) { $this->stTipoBusca = $valor; }
function setDedutora($valor) { $this->boDedutora = $valor; }
function setEvento($valor) { $this->stEvento = $valor; }

function getUsaFiltro() { return $this->usaFiltro;   }
function getTipoBusca() { return $this->stTipoBusca; }
function getDedutora() { return $this->boDedutora; }
function getEvento() { return $this->stEvento; }

function IPopUpReceita($obCmbEntidades = "", $boDedutora = "")
{
    Sessao::remove('linkPopUp');
    //sessao->linkPopUp = null;

    parent::BuscaInner();

    if (!(is_object($obCmbEntidades))) {
        $this->obCmbEntidades = unserialize($obCmbEntidades);
    } else {
        $this->obCmbEntidades = $obCmbEntidades;
    }

    $this->setRotulo               ( "Receita" );
    $this->setTitle                ( "Informe a ".($boDedutora ? "dedutora." : "receita.") );
    $this->setNull                 ( true );
    $this->setId                   ( "stDescricaoCodReceita" );
    $this->obCampoCod->setName     ( "inCodReceita" );
    $this->obCampoCod->setId       ( "inCodReceita" );
    $this->obCampoCod->setValue    ( "" );
    $this->obCampoCod->setAlign    ("left");
    $this->obCampoCod->setInteiro  ( true );
    $this->setUsaFiltro            ( false );
    $this->setTipoBusca            ( '' );
    $this->setDedutora             ( $boDedutora );

}

function montaHTML()
{
    if($this->usaFiltro)
         $stPopUp = "FLReceita.php";
    else $stPopUp = "LSReceita.php";

    if ($this->obCmbEntidades) {
        if ( !strstr(strtolower(get_class( $this->obCmbEntidades )),"selectmultiplo")) {
            $pgOcul = "'".CAM_GF_ORC_PROCESSAMENTO."OCReceita.php?".Sessao::getId()."&".$this->obCampoCod->getName()."='+this.value+'&inCodEntidade='+document.frm.".$this->obCmbEntidades->getName().".value+'&stNomCampoCod=".$this->obCampoCod->getName()."&stIdCampoDesc=".$this->getId()."&stUsaEntidade=S&tipoBusca=".$this->getTipoBusca()."'";
            $this->obCampoCod->obEvento->setOnChange ( " if (this.value) { ajaxJavaScript($pgOcul,'buscaReceitaCod');} else { document.getElementById('".$this->obCampoCod->getId()."').value = ''; document.getElementById('".$this->getId()."').innerHTML = '&nbsp;'; }".$this->getEvento().$this->obCampoCod->obEvento->getOnChange() );
            $this->setFuncaoBusca(" if (document.frm.".$this->obCmbEntidades->getName().".value) { abrePopUp('" . CAM_GF_ORC_POPUPS . "receita/".$stPopUp."','frm','".$this->obCampoCod->getName()."','".$this->getId()."', '?".Sessao::getId()."&".$this->obCampoCod->getName()."='+this.value+'&inCodEntidade='+document.frm.".$this->obCmbEntidades->getName().".value+'&stNomCampoCod=".$this->obCampoCod->getName()."&stIdCampoDesc=".$this->getId()."&stUsaEntidade=S&inCodEntidade='+document.frm.".$this->obCmbEntidades->getName().".value+'&tipoBusca=".$this->getTipoBusca()."','".Sessao::getId()."','800','550');} else { alertaAviso('É necessário informar uma entidade para a receita.','frm','erro','".Sessao::getId()."');}");
        } else {
            $pgOcul = "'".CAM_GF_ORC_PROCESSAMENTO."OCReceita.php?".Sessao::getId()."&".$this->obCampoCod->getName()."='+this.value+'&stNomSelectMultiplo=".$this->obCmbEntidades->getNomeLista2()."&stNomCampoCod=".$this->obCampoCod->getName()."&stIdCampoDesc=".$this->getId()."&stUsaEntidade=S&tipoBusca=".$this->getTipoBusca()."'";
            $this->obCampoCod->obEvento->setOnChange (" if (this.value) { ajaxJavaScript($pgOcul+selectMultiploToString(".$this->obCmbEntidades->getNomeLista2()."),'buscaReceitaCod'); ".$this->gettEvento()."} else { document.getElementById('".$this->obCampoCod->getId()."').value = ''; document.getElementById('".$this->getId()."').innerHTML = '&nbsp;'; }".$this->obCampoCod->obEvento->getOnChange() );
            $this->setFuncaoBusca(" if(document.frm.".$this->obCmbEntidades->getNomeLista2()."[0]) abrePopUp('" . CAM_GF_ORC_POPUPS . "receita/".$stPopUp."','frm','".$this->obCampoCod->getName()."','".$this->getId()."','?'+selectMultiploToString(".$this->obCmbEntidades->getNomeLista2().")+'&stNomSelectMultiplo=".$this->obCmbEntidades->getNomeLista2()."&".$this->obCampoCod->getName()."='+this.value+'&stNomSelectMultiplo=".$this->obCmbEntidades->getNomeLista2()."&stNomCampoCod=".$this->obCampoCod->getName()."&stIdCampoDesc=".$this->getId()."&stUsaEntidade=S&tipoBusca=".$this->getTipoBusca()."&buscaReceitaCod','".Sessao::getId()."','800','550'); else alertaAviso('É necessário informar uma entidade para a receita.','frm','erro','".Sessao::getId()."');");
        }
    } else {
        $pgOcul = "'".CAM_GF_ORC_PROCESSAMENTO."OCReceita.php?".Sessao::getId()."&".$this->obCampoCod->getName()."='+this.value+'&stNomCampoCod=".$this->obCampoCod->getName()."&stIdCampoDesc=".$this->getId()."&stUsaEntidade=N&tipoBusca=".$this->getTipoBusca()."'";
        $this->obCampoCod->obEvento->setOnChange ( "if (this.value) { ajaxJavaScript($pgOcul,'buscaReceitaCod'); } else { document.getElementById('".$this->obCampoCod->getId()."').value = ''; document.getElementById('".$this->getId()."').innerHTML = '&nbsp;'; }".$this->getEvento().$this->obCampoCod->obEvento->getOnChange() );
        $this->setFuncaoBusca( "abrePopUp('" . CAM_GF_ORC_POPUPS . "receita/".$stPopUp."? ".Sessao::getId()."&".$this->obCampoCod->getName()."='+this.value+'&stNomCampoCod=".$this->obCampoCod->getName()."&stIdCampoDesc=".$this->getId()."&stUsaEntidade=N','frm','".$this->obCampoCod->getName()."','".$this->getId()."','buscaReceitaCod&tipoBusca=".$this->getTipoBusca()."','".Sessao::getId()."','800','550');");
    }

   parent::montaHTML();
}
}
?>
