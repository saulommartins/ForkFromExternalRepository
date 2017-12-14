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
/*
 * Classe do IPopUpCalendario
 * Data de Criação   : 13/10/2008

 * @author Analista      Dagiane Vieira
 * @author Desenvolvedor Diego Lemos de Souza

 * @package URBEM
 * @subpackage

 * @ignore

 $Id:$
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';

class  IPopUpCalendario extends BuscaInner
{
var $stFuncaoCalendario;
var $obImagemCalendario;

function setImagemCalendario() {$this->obImagemCalendario = new Img();}
function setFuncaoCalendario($stValor) {$this->stFuncaoCalendario = $stValor;}

function getImagemCalendario() {return $this->obImagemCalendario;}
function getFuncaoCalendario() {return $this->stFuncaoCalendario;}

/**
    * Método construtor
    * @access Private
*/
function IPopUpCalendario()
{
    parent::BuscaInner();

    $pgOcul = "'".CAM_GRH_CAL_PROCESSAMENTO."OCIPopUpCalendario.php?".Sessao::getId()."&'+this.name+'='+this.value";

    $this->setRotulo                         ( "Calendário"                          );
    $this->setTitle                          ( "Selecione o calendário."             );
    $this->setId                             ( "stCalendario"                        );
    $this->obCampoCod->setName               ( "inCodCalendario"                     );
    $this->obCampoCod->setId                 ( "inCodCalendario"                     );
    $this->obCampoCod->setSize               ( 10                                    );
    $this->obCampoCod->obEvento->setOnChange ( "ajaxJavaScript($pgOcul,'preencherCalendario');"          );
    $this->setFuncaoBusca                    ( "abrePopUp('".CAM_GRH_CAL_POPUPS."calendario/FLProcurarCalendario.php','frm','inCodCalendario','stCalendario','','".Sessao::getId()."','800','550')" );

    $this->setImagemCalendario();
    $this->getImagemCalendario()->setCaminho   ( CAM_FW_IMAGENS."calendario.gif");
    $this->getImagemCalendario()->setAlign     ( "absmiddle" );
    $this->getImagemCalendario()->setId("stImgConsultarCalendario");
}

/**
    * Monta o HTML do Objeto Label
    * @access Private
*/
function montaHTML()
{
    if ( $this->getNomeBusca() ) {
        $stNomeBusca = $this->getNomeBusca();
        $this->obCampoCod->setName  ( "inCod".$stNomeBusca );
    }
    if ( is_array( $this->arValoresBusca ) ) {
       $this->obCampoCod->obEvento->setOnChange( $this->obCampoCod->obEvento->getOnChange()."buscaValorBscInner( '".$this->arValoresBusca['stPaginaBusca']."', '".$this->arValoresBusca['stNomForm']."', '".$this->obCampoCod->getName()."','".$this->stId."', '".$this->arValoresBusca['stTipoBusca']."' );");
    }

    $this->obCampoCod->montaHTML();
    $this->obImagem->montaHTML();
    $this->obImagemCalendario->montaHTML();

    $stTitleImagem = strtolower( preg_replace("/\*/","",$this->stRotulo));
    if ( $this->getMonitorarCampoCod() ) {
        $stTrocaFlag = "boObserver".$this->obCampoCod->getId()."=true;";
        $stLink  = "&nbsp;<a href=\"JavaScript: ".$stTrocaFlag.$this->getFuncaoBusca().";\" title='Buscar ".$stTitleImagem."'>";
    } else {
        $stLink  = "&nbsp;<a href=\"JavaScript: ".$this->getFuncaoBusca().";\" title='Buscar ".$stTitleImagem."'>";
    }
    $stLink .= $this->obImagem->getHTML();
    $stLink .= "</a>";

    $stLink .= "&nbsp;<a href=\"JavaScript: ".$this->getFuncaoCalendario().";\" id='linkConsultarCalendario' title='Consultar ".$stTitleImagem."'>";
    $stLink .= $this->obImagemCalendario->getHTML();
    $stLink .= "</a>";

    $obTabela = new Tabela;
    $obTabela->setCellPadding( 0 );
    $obTabela->setCellSpacing( 0 );
    $obTabela->setWidth( 100 );
    $obTabela->addLinha();
    if ( $this->getLabel() ) {
        $obTabela->setStyle( 'display:none;' );
    }
    if ( $this->obCampoCod->getMinLength() > 0 ) {
        $obTabela->ultimaLinha->addCelula();
        $obTabela->ultimaLinha->ultimaCelula->setClass( "field" );
        $obTabela->ultimaLinha->ultimaCelula->setWidth( "13" );
        $obTabela->ultimaLinha->ultimaCelula->addConteudo( $this->obCampoCod->getHTML() );
        $obTabela->ultimaLinha->commitCelula();
    }
    if ( $this->getId() ) {
        $obTabela->ultimaLinha->addCelula();
        $obTabela->ultimaLinha->ultimaCelula->setClass( "fakefield" );
        $obTabela->ultimaLinha->ultimaCelula->setWidth( "60" );
        $obTabela->ultimaLinha->ultimaCelula->setHeight( "20" );
        $obTabela->ultimaLinha->ultimaCelula->setId( $this->getId() );
        if ( !$this->getMostrarDescricao() ) {
            $obTabela->ultimaLinha->ultimaCelula->setStyle( "display:none;" );
        }
        $obTabela->ultimaLinha->ultimaCelula->addConteudo( ( $this->getValue() ) ? $this->getValue() : '&nbsp;' );
        $obTabela->ultimaLinha->commitCelula();
    }
    if ( $this->obCampoCod->getMinLength() > 0 || $this->obCampoCod->getMaxLength() == 0 ) {

        if ( trim($this->stValue) ) {
            $stValue = "<script>document.getElementById('".$this->stId."').innerHTML = '".$this->stValue."';</script>";
            $stLink .= $stValue;
        }
        $obTabela->ultimaLinha->addCelula();
        //$obTabela->ultimaLinha->ultimaCelula->setWidth( "25" );
        $obTabela->ultimaLinha->ultimaCelula->setClass( "fieldleft" );
        $obTabela->ultimaLinha->ultimaCelula->setValign( "top" );

        $this->obCampoCodHidden->setName ( 'Hdn'.$this->obCampoCod->getName() );
        $this->obCampoCodHidden->montaHtml();
        $this->obCampoDescrHidden->montaHtml();

        $obTabela->ultimaLinha->ultimaCelula->addConteudo( $stLink.$this->obCampoCodHidden->getHtml().$this->obCampoDescrHidden->getHtml() );
        $obTabela->ultimaLinha->commitCelula();
    }

    $obTabela->commitLinha();
    $obTabela->montaHTML();
    $stHTML = $obTabela->getHTML();
    if ( $this->getLabel() ) {
        $stHTML .= "<span id=\"".$this->getId()."_label\" >";
        $stHTML .= $this->obCampoCod->getValue();
        if ( $this->getValue() != '' ) {
            $stHTML .= ' - '.$this->getValue();
        }
        $stHTML .= "</span>";
    }
    $this->setHTML( $stHTML );
}

}
?>
