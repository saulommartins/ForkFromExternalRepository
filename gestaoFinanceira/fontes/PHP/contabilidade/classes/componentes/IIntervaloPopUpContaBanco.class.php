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

* @author Analista: Gelson
* @author Desenvolvedor: Bruce Cruz de Sena

* @package URBEM
* @subpackage

 Casos de uso: uc-02.02.02

*/

/*
$Log$
Revision 1.2  2007/10/04 21:39:50  hwalves
Ticket#10069#

Revision 1.1  2007/05/30 19:24:18  bruce
Bug #9116#

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once ( CLA_COMPONENTE );
include_once ( CAM_GF_CONT_COMPONENTES."IPopUpContaBanco.class.php" );

class  IIntervaloPopUpContaBanco extends Componente
{
/**
    * @access Private
    * @var Object
*/
var $obIPopUpContaBancoInicial;
/**
    * @access Private
    * @var Object
*/
var $obLblContaBanco;
/**
    * @access Private
    * @var Object
*/
var $obIPopUpContaBancoFinal;

function IIntervaloPopUpContaBanco($obCmbEntidades = "")
{
    parent::Componente();

    $this->setRotulo("Conta Banco");
    $this->setTitle ("Informe o código reduzido da conta");

    $this->obIPopUpContaBancoInicial = new IPopUpContaBanco($obCmbEntidades);
    $this->obIPopUpContaBancoInicial->setId                   ( "stDescricaoContaBancoInicial" );
    $this->obIPopUpContaBancoInicial->obCampoCod->setName     ( "inCodContaBancoInicial" );
    $this->obIPopUpContaBancoInicial->obCampoCod->setValue    ( "" );
    $this->obIPopUpContaBancoInicial->setMostrarDescricao     ( false );

    $this->obLblContaBanco = new Label;
    $this->obLblContaBanco->setValue(" até  ");

    $this->obIPopUpContaBancoFinal = new IPopUpContaBanco($obCmbEntidades);
    $this->obIPopUpContaBancoFinal->setId                   ( "stDescricaoContaBancoFinal" );
    $this->obIPopUpContaBancoFinal->obCampoCod->setName     ( "inCodContaBancoFinal" );
    $this->obIPopUpContaBancoFinal->obCampoCod->setValue    ( "" );
    $this->obIPopUpContaBancoFinal->setMostrarDescricao     ( false );

}

function montaHTML()
{
    $this->obIPopUpContaBancoInicial->montaHTML();
    $stHTMLContaBancoInicial = $this->obIPopUpContaBancoInicial->getHTML();

    $this->obLblContaBanco->montaHTML();
    $stHTMLLblContaBanco = $this->obLblContaBanco->getHTML();

    $this->obIPopUpContaBancoFinal->montaHTML();
    $stHTMLContaBancoFinal = $this->obIPopUpContaBancoFinal->getHTML();

    $obTabela = new Tabela;
    $obTabela->setCellPadding( 0 );
    $obTabela->setCellSpacing( 0 );
    $obTabela->setWidth( 32 );
    $obTabela->setBorder( 0 );
    $obTabela->addLinha();

    $obTabela->ultimaLinha->addCelula();
    $obTabela->ultimaLinha->ultimaCelula->setWidth( "5" );
    $obTabela->ultimaLinha->ultimaCelula->addConteudo( $stHTMLContaBancoInicial );
    $obTabela->ultimaLinha->commitCelula();

    $obTabela->ultimaLinha->addCelula();
    $obTabela->ultimaLinha->ultimaCelula->setClass( "fieldlabel" );
    $obTabela->ultimaLinha->ultimaCelula->setWidth( "4" );
    $obTabela->ultimaLinha->ultimaCelula->addConteudo( $stHTMLLblContaBanco );
    $obTabela->ultimaLinha->commitCelula();

    $obTabela->ultimaLinha->addCelula();
    $obTabela->ultimaLinha->ultimaCelula->setWidth( "5" );
    $obTabela->ultimaLinha->ultimaCelula->addConteudo( $stHTMLContaBancoFinal );
    $obTabela->ultimaLinha->commitCelula();

    $obTabela->commitLinha();
    $obTabela->montaHTML();
    $this->setHTML( $obTabela->getHTML() );
}

}
?>
