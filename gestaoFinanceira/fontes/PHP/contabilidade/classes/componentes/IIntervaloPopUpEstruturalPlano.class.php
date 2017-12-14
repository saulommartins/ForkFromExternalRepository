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
$Author: jose.eduardo $
$Date: 2006-08-15 14:47:07 -0300 (Ter, 15 Ago 2006) $

 Casos de uso: uc-02.02.02
*/

/*
$Log$
Revision 1.1  2006/08/15 17:45:53  jose.eduardo
Bug #5192#

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once ( CLA_COMPONENTE );
include_once ( CAM_GF_CONT_COMPONENTES."IPopUpEstruturalPlano.class.php" );

class IIntervaloPopUpEstruturalPlano extends Componente
{
/**
    * @access Private
    * @var Object
*/
var $obIPopUpEstruturalPlanoInicial;
/**
    * @access Private
    * @var Object
*/
var $obLblCodEstrutural;
/**
    * @access Private
    * @var Object
*/
var $obIPopUpEstruturalPlanoFinal;

function IIntervaloPopUpEstruturalPlano()
{
    parent::Componente();

    $this->setRotulo("Código Estrutural");
    $this->setTitle ("Informe o código de classificação da conta");

    $this->obIPopUpEstruturalPlanoInicial = new IPopUpEstruturalPlano;
    $this->obIPopUpEstruturalPlanoInicial->setId                   ( "stDescricaoClassificacaoInicial" );
    $this->obIPopUpEstruturalPlanoInicial->obCampoCod->setName     ( "stCodEstruturalInicial" );
    $this->obIPopUpEstruturalPlanoInicial->obCampoCod->setValue    ( "" );
    $this->obIPopUpEstruturalPlanoInicial->setMostrarDescricao     ( false );

    $this->obLblCodEstrutural = new Label;
    $this->obLblCodEstrutural->setValue( "&nbsp;" ."até". "&nbsp;" );

    $this->obIPopUpEstruturalPlanoFinal = new IPopUpEstruturalPlano;
    $this->obIPopUpEstruturalPlanoFinal->setId                   ( "stDescricaoClassificacaoFinal" );
    $this->obIPopUpEstruturalPlanoFinal->obCampoCod->setName     ( "stCodEstruturalFinal" );
    $this->obIPopUpEstruturalPlanoFinal->obCampoCod->setValue    ( "" );
    $this->obIPopUpEstruturalPlanoFinal->setMostrarDescricao     ( false );

}

function montaHTML()
{
    $this->obIPopUpEstruturalPlanoInicial->montaHTML();
    $stHTMLEstruturalInicial = $this->obIPopUpEstruturalPlanoInicial->getHTML();

    $this->obLblCodEstrutural->montaHTML();
    $stHTMLLblCodEstrutural = $this->obLblCodEstrutural->getHTML();

    $this->obIPopUpEstruturalPlanoFinal->montaHTML();
    $stHTMLEstruturalFinal = $this->obIPopUpEstruturalPlanoFinal->getHTML();

    $obTabela = new Tabela;
    $obTabela->setCellPadding( 0 );
    $obTabela->setCellSpacing( 0 );
    $obTabela->setWidth( 56 );
    $obTabela->setBorder( 0 );
    $obTabela->addLinha();

    $obTabela->ultimaLinha->addCelula();
    $obTabela->ultimaLinha->ultimaCelula->setWidth( "5" );
    $obTabela->ultimaLinha->ultimaCelula->addConteudo( $stHTMLEstruturalInicial );
    $obTabela->ultimaLinha->commitCelula();

    $obTabela->ultimaLinha->addCelula();
    $obTabela->ultimaLinha->ultimaCelula->setClass( "fieldlabel" );
    $obTabela->ultimaLinha->ultimaCelula->setWidth( "4" );
    $obTabela->ultimaLinha->ultimaCelula->addConteudo( $stHTMLLblCodEstrutural  );
    $obTabela->ultimaLinha->commitCelula();

    $obTabela->ultimaLinha->addCelula();
    $obTabela->ultimaLinha->ultimaCelula->setWidth( "5" );
    $obTabela->ultimaLinha->ultimaCelula->addConteudo( $stHTMLEstruturalFinal );
    $obTabela->ultimaLinha->commitCelula();

    $obTabela->commitLinha();
    $obTabela->montaHTML();
    $this->setHTML( $obTabela->getHTML() );
}

}
?>
