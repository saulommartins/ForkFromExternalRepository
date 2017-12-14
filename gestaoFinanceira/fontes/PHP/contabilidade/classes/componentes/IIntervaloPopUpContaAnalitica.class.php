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

$Revision: 30668 $
$Name$
$Author: hwalves $
$Date: 2007-09-25 18:12:54 -0300 (Ter, 25 Set 2007) $

 Casos de uso: uc-02.02.02
*/

/*
$Log$
Revision 1.3  2007/09/25 21:12:40  hwalves
Ticket#10069#

Revision 1.2  2007/09/03 18:45:30  hboaventura
Ticket#9937#

Revision 1.1  2006/08/15 17:45:53  jose.eduardo
Bug #5192#

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once ( CLA_COMPONENTE );
include_once ( CAM_GF_CONT_COMPONENTES."IPopUpContaAnalitica.class.php" );

class  IIntervaloPopUpContaAnalitica extends Componente
{
/**
    * @access Private
    * @var Object
*/
var $obIPopUpContaAnaliticaInicial;
/**
    * @access Private
    * @var Object
*/
var $obLblContaAnalitica;
/**
    * @access Private
    * @var Object
*/
var $obIPopUpContaAnaliticaFinal;
/**
    * @access Private
    * @var Boolean
*/
var $stTipoBusca;
/*
    * @access Public
    * @param Object $valor
*/
function setTipoBusca($valor) { $this->stTipoBusca = $valor; }
/*
    * @access Public
    * @return Object
*/
function getTipoBusca() { return $this->stTipoBusca; }

function IIntervaloPopUpContaAnalitica($obCmbEntidades = "")
{
    parent::Componente();

    $this->setRotulo("Código Reduzido");
    $this->setTitle ("Informe o código reduzido da conta");

    $this->obIPopUpContaAnaliticaInicial = new IPopUpContaAnalitica($obCmbEntidades);
    $this->obIPopUpContaAnaliticaInicial->setId                   ( "stDescricaoContaAnaliticaInicial" );
    $this->obIPopUpContaAnaliticaInicial->obCampoCod->setName     ( "inCodPlanoInicial" );
    $this->obIPopUpContaAnaliticaInicial->obCampoCod->setId       ( "inCodPlanoInicial" );
    $this->obIPopUpContaAnaliticaInicial->obCampoCod->setValue    ( "" );
    $this->obIPopUpContaAnaliticaInicial->setMostrarDescricao     ( false );

    $this->obLblContaAnalitica = new Label;
    $this->obLblContaAnalitica->setValue( "&nbsp;"."até"."&nbsp;"  );

    $this->obIPopUpContaAnaliticaFinal = new IPopUpContaAnalitica($obCmbEntidades);
    $this->obIPopUpContaAnaliticaFinal->setId                   ( "stDescricaoContaAnaliticaFinal" );
    $this->obIPopUpContaAnaliticaFinal->obCampoCod->setName     ( "inCodPlanoFinal" );
    $this->obIPopUpContaAnaliticaFinal->obCampoCod->setId       ( "inCodPlanoFinal" );
    $this->obIPopUpContaAnaliticaFinal->obCampoCod->setValue    ( "" );
    $this->obIPopUpContaAnaliticaFinal->setMostrarDescricao     ( false );

}

function montaHTML()
{
    if ($this->stTipoBusca) {
        $this->obIPopUpContaAnaliticaInicial->setTipoBusca( $this->getTipoBusca() );
    }
    $this->obIPopUpContaAnaliticaInicial->montaHTML();
    $stHTMLContaAnaliticaInicial = $this->obIPopUpContaAnaliticaInicial->getHTML();

    $this->obLblContaAnalitica->montaHTML();
    $stHTMLLblContaAnalitica = $this->obLblContaAnalitica->getHTML();

    if ($this->stTipoBusca) {
        $this->obIPopUpContaAnaliticaFinal->setTipoBusca( $this->getTipoBusca() );
    }
    $this->obIPopUpContaAnaliticaFinal->montaHTML();
    $stHTMLContaAnaliticaFinal = $this->obIPopUpContaAnaliticaFinal->getHTML();

    $obTabela = new Tabela;
    $obTabela->setCellPadding( 0 );
    $obTabela->setCellSpacing( 0 );
    $obTabela->setWidth( 32 );
    $obTabela->setBorder( 0 );
    $obTabela->addLinha();

    $obTabela->ultimaLinha->addCelula();
    $obTabela->ultimaLinha->ultimaCelula->setWidth( "5" );
    $obTabela->ultimaLinha->ultimaCelula->addConteudo( $stHTMLContaAnaliticaInicial );
    $obTabela->ultimaLinha->commitCelula();

    $obTabela->ultimaLinha->addCelula();
    $obTabela->ultimaLinha->ultimaCelula->setClass( "fieldlabel" );
    $obTabela->ultimaLinha->ultimaCelula->setWidth( "4" );
    $obTabela->ultimaLinha->ultimaCelula->addConteudo( $stHTMLLblContaAnalitica );
    $obTabela->ultimaLinha->commitCelula();

    $obTabela->ultimaLinha->addCelula();
    $obTabela->ultimaLinha->ultimaCelula->setWidth( "5" );
    $obTabela->ultimaLinha->ultimaCelula->addConteudo( $stHTMLContaAnaliticaFinal );
    $obTabela->ultimaLinha->commitCelula();

    $obTabela->commitLinha();
    $obTabela->montaHTML();
    $this->setHTML( $obTabela->getHTML() );
}

}
?>
