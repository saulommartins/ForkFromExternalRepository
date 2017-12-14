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
* Arquivo de popup de intervalo entre os itens
* Data de Criação: 12/12/2007

* @author Analista: Gelson W. Gonçalves
* @author Desenvolvedor: Henrique Girardi dos Santos

* @package URBEM
* @subpackage

* $Id: IIntervaloPopUpItem.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-03.03.20
*               uc-03.03.24
*               uc-03.03.25

*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once CLA_COMPONENTE;
require_once 'IPopUpItem.class.php';
//require_once CAM_GP_ALM_PROCESSAMENTO."";

class  IIntervaloPopUpItem extends Componente
{

/**
    * @access Private
    * @var Object
*/
var $obIPopUpItemInicial;
/**
    * @access Private
    * @var Object
*/
var $obLblItem;
/**
    * @access Private
    * @var Object
*/
var $obIPopUpItemFinal;

/**
    * @access Private
    * @var Object
*/
var $boItemComposto;

/**
    * @access Private
    * @var Object
*/
var $boTipoNaoInformado;

function setTipoNaoInformado($valor) { $this->boTipoNaoInformado  = $valor; }

function setItemComposto($valor) { $this->boItemComposto = $valor; }

function getTipoNaoInformado() { return $this->boTipoNaoInformado;   }

function IIntervaloPopUpItem(&$obForm)
{

    parent::Componente();

    $stJs = "ajaxJavaScript('".CAM_GP_ALM_PROCESSAMENTO.'OCIntervaloPopUpItem.php?'.Sessao::getId()."&inCodItemInicial='+$('inCodItemInicial').value+'&inCodItemFinal='+$('inCodItemFinal').value+'&inObjId='+this.id, 'verificaDadosItem' );";
    $this->setTipoNaoInformado(false);

    $this->setRotulo("Item");
    $this->setTitle ("Informe o item.");

    $this->obIPopUpItemInicial = new IPopUpItem     ( $obForm );
    $this->obIPopUpItemInicial->setId                ( "stItemInicial" );
    $this->obIPopUpItemInicial->obCampoCod->setId    ( "inCodItemInicial" );
    $this->obIPopUpItemInicial->obCampoCod->setName  ( "inCodItemInicial" );
    $this->obIPopUpItemInicial->obCampoCod->setValue ( "" );
    $this->obIPopUpItemInicial->obCampoCod->obEvento->setOnBlur( $stJs );
    $this->obIPopUpItemInicial->setMostrarDescricao  ( false );

    $this->obLblItem = new Label;
    $this->obLblItem->setValue("&nbsp;&nbsp;até&nbsp;&nbsp;");

    $this->obIPopUpItemFinal = new IPopUpItem     ( $obForm );
    $this->obIPopUpItemFinal->setId                ( "stItemFinal" );
    $this->obIPopUpItemFinal->obCampoCod->setId    ( "inCodItemFinal" );
    $this->obIPopUpItemFinal->obCampoCod->setName  ( "inCodItemFinal" );
    $this->obIPopUpItemFinal->obCampoCod->setValue ( "" );
    $this->obIPopUpItemFinal->obCampoCod->obEvento->setOnBlur( $stJs );
    $this->obIPopUpItemFinal->setMostrarDescricao  ( false );

}

function montaHTML()
{
    // Teste realizado para retirar o validador no evento onChange do campo.
    if ($this->boItemComposto) {
        $this->obIPopUpItemInicial->setItemComposto ( true );
        $this->obIPopUpItemFinal->setItemComposto   ( true );
    }

    $this->obIPopUpItemInicial->setTipoNaoInformado( $this->getTipoNaoInformado() );
    $this->obIPopUpItemInicial->montaHTML();
    $stHTMLItemInicial = $this->obIPopUpItemInicial->getHTML();

    $this->obLblItem->montaHTML();
    $stHTMLLblItem = $this->obLblItem->getHTML();

    $this->obIPopUpItemFinal->setTipoNaoInformado( $this->getTipoNaoInformado() );
    $this->obIPopUpItemFinal->montaHTML();
    $stHTMLItemFinal = $this->obIPopUpItemFinal->getHTML();

    $obTabela = new Tabela;
    $obTabela->setCellPadding( 0 );
    $obTabela->setCellSpacing( 0 );
    $obTabela->setWidth( 32 );
    $obTabela->setBorder( 0 );
    $obTabela->addLinha();

    $obTabela->ultimaLinha->addCelula();
    $obTabela->ultimaLinha->ultimaCelula->setWidth( "5" );
    $obTabela->ultimaLinha->ultimaCelula->addConteudo( $stHTMLItemInicial );
    $obTabela->ultimaLinha->commitCelula();

    $obTabela->ultimaLinha->addCelula();
    $obTabela->ultimaLinha->ultimaCelula->setClass( "fieldlabel" );
    $obTabela->ultimaLinha->ultimaCelula->setWidth( "4" );
    $obTabela->ultimaLinha->ultimaCelula->addConteudo( $stHTMLLblItem );
    $obTabela->ultimaLinha->commitCelula();

    $obTabela->ultimaLinha->addCelula();
    $obTabela->ultimaLinha->ultimaCelula->setWidth( "5" );
    $obTabela->ultimaLinha->ultimaCelula->addConteudo( $stHTMLItemFinal );
    $obTabela->ultimaLinha->commitCelula();

    $obTabela->commitLinha();
    $obTabela->montaHTML();
    $this->setHTML( $obTabela->getHTML() );
}

}
?>
