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
$Author: tonismar $
$Date: 2007-07-27 19:40:00 -0300 (Sex, 27 Jul 2007) $

 Casos de uso: uc-02.01.33, uc-02.01.06
*/

/*
$Log$
Revision 1.4  2007/07/27 22:39:39  tonismar
Bug#9112#

Revision 1.3  2006/08/28 11:12:53  jose.eduardo
Ajustes no componente

Revision 1.2  2006/08/25 17:03:05  jose.eduardo
caso de uso

Revision 1.1  2006/08/25 16:14:31  jose.eduardo
Inclusao

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once ( CLA_COMPONENTE );
include_once ( CAM_GF_ORC_COMPONENTES."IPopUpDotacao.class.php" );

class  IIntervaloPopUpDotacao extends Componente
{
/**
    * @access Private
    * @var Object
*/
var $obIPopUpDotacaoInicial;
/**
    * @access Private
    * @var Object
*/
var $obLblDotacao;
/**
    * @access Private
    * @var Object
*/
var $obIPopUpDotacaoFinal;

function setAutorizacao($valor) { $this->stAutorizacao = $valor; }

function getAutorizacao() { return $this->stAutorizacao ; }

function IIntervaloPopUpDotacao($obCmbEntidades = "")
{
    parent::Componente();

    $this->setRotulo("Dotação");
    $this->setTitle ("Informe a Dotação.");

    $this->obIPopUpDotacaoInicial = new IPopUpDotacao( $obCmbEntidades );
    $this->obIPopUpDotacaoInicial->setId                   ( "stDescricaoDotacaoInicial" );
    $this->obIPopUpDotacaoInicial->obCampoCod->setName     ( "inCodDotacaoInicial" );
    $this->obIPopUpDotacaoInicial->obCampoCod->setValue    ( "" );
    $this->obIPopUpDotacaoInicial->setMostrarDescricao     ( false );
    if ( $this->getAutorizacao() == 'autorizacaoOrcamento') {
        $this->obIPopUpDotacaoInicial->setAutorizacao( 'autorizacaoOrcamento');
    }

    $this->obLblDotacao = new Label;
    $this->obLblDotacao->setValue(" até  ");

    $this->obIPopUpDotacaoFinal = new IPopUpDotacao( $obCmbEntidades );
    $this->obIPopUpDotacaoFinal->setId                   ( "stDescricaoDotacaoFinal" );
    $this->obIPopUpDotacaoFinal->obCampoCod->setName     ( "inCodDotacaoFinal" );
    $this->obIPopUpDotacaoFinal->obCampoCod->setValue    ( "" );
    $this->obIPopUpDotacaoFinal->setMostrarDescricao     ( false );
    if ( $this->getAutorizacao() == 'autorizacaoOrcamento') {
        $this->obIPopUpDotacaoFinal->setAutorizacao( 'autorizacaoOrcamento');
    }

}

function montaHTML()
{
    $this->obIPopUpDotacaoInicial->montaHTML();
    $stHTMLDotacaoInicial = $this->obIPopUpDotacaoInicial->getHTML();

    $this->obLblDotacao->montaHTML();
    $stHTMLLblDotacao = $this->obLblDotacao->getHTML();

    $this->obIPopUpDotacaoFinal->montaHTML();
    $stHTMLDotacaoFinal = $this->obIPopUpDotacaoFinal->getHTML();

    $obTabela = new Tabela;
    $obTabela->setCellPadding( 0 );
    $obTabela->setCellSpacing( 0 );
    $obTabela->setWidth( 32 );
    $obTabela->setBorder( 0 );
    $obTabela->addLinha();

    $obTabela->ultimaLinha->addCelula();
    $obTabela->ultimaLinha->ultimaCelula->setWidth( "5" );
    $obTabela->ultimaLinha->ultimaCelula->addConteudo( $stHTMLDotacaoInicial );
    $obTabela->ultimaLinha->commitCelula();

    $obTabela->ultimaLinha->addCelula();
    $obTabela->ultimaLinha->ultimaCelula->setClass( "fieldlabel" );
    $obTabela->ultimaLinha->ultimaCelula->setWidth( "4" );
    $obTabela->ultimaLinha->ultimaCelula->addConteudo( $stHTMLLblDotacao );
    $obTabela->ultimaLinha->commitCelula();

    $obTabela->ultimaLinha->addCelula();
    $obTabela->ultimaLinha->ultimaCelula->setWidth( "5" );
    $obTabela->ultimaLinha->ultimaCelula->addConteudo( $stHTMLDotacaoFinal );
    $obTabela->ultimaLinha->commitCelula();

    $obTabela->commitLinha();
    $obTabela->montaHTML();
    $this->setHTML( $obTabela->getHTML() );
}

}
?>
