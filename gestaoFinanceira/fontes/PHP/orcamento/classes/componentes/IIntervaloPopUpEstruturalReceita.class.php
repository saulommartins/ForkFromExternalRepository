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
* Data de Criação: 01/09/2006

* @author Analista: Cleisson Barboza
* @author Desenvolvedor: José Eduardo Porto

* @package URBEM
* @subpackage

$Revision: 30668 $
$Name$
$Author: eduardoschitz $
$Date: 2008-03-04 09:28:44 -0300 (Ter, 04 Mar 2008) $

 Casos de uso: uc-02.01.06. uc-02.01.34
*/

/*
$Log$
Revision 1.2  2006/09/25 12:10:44  cleisson
Bug #7032#

Revision 1.1  2006/09/01 15:05:50  jose.eduardo
Inclusão de componente
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once ( CLA_COMPONENTE );
include_once ( CAM_GF_ORC_COMPONENTES."IPopUpEstruturalReceita.class.php" );

class IIntervaloPopUpEstruturalReceita extends Componente
{
/**
    * @access Private
    * @var Object
*/
var $obIPopUpEstruturalReceitaInicial;
/**
    * @access Private
    * @var Object
*/
var $obLblCodEstrutural;
/**
    * @access Private
    * @var Object
*/
var $obIPopUpEstruturalReceitaFinal;

function IIntervaloPopUpEstruturalReceita($boDedutora = "")
{
    parent::Componente();

    if ($boDedutora) {
        $this->setRotulo("Classificação da Dedutora");
        $this->setTitle ("Informe o código de Classificação da Dedutora");

        $this->obIPopUpEstruturalReceitaInicial = new IPopUpEstruturalReceita ( $boDedutora = true );
    } else {
        $this->setRotulo("Classificação da Receita");
        $this->setTitle ("Informe o código de Classificação da Receita");

        $this->obIPopUpEstruturalReceitaInicial = new IPopUpEstruturalReceita;
    }

    $this->obIPopUpEstruturalReceitaInicial->setId                   ( "stDescricaoClassificacaoInicial" );
    $this->obIPopUpEstruturalReceitaInicial->obCampoCod->setName     ( "stCodEstruturalInicial" );
    $this->obIPopUpEstruturalReceitaInicial->obCampoCod->setValue    ( "" );
    $this->obIPopUpEstruturalReceitaInicial->setMostrarDescricao     ( false );

    $this->obLblCodEstrutural = new Label;
    $this->obLblCodEstrutural->setValue(" até  ");

    if ($boDedutora) {
        $this->obIPopUpEstruturalReceitaFinal = new IPopUpEstruturalReceita ( $boDedutora = true );
    } else {
        $this->obIPopUpEstruturalReceitaFinal = new IPopUpEstruturalReceita;
    }

    $this->obIPopUpEstruturalReceitaFinal->setId                   ( "stDescricaoClassificacaoFinal" );
    $this->obIPopUpEstruturalReceitaFinal->obCampoCod->setName     ( "stCodEstruturalFinal" );
    $this->obIPopUpEstruturalReceitaFinal->obCampoCod->setValue    ( "" );
    $this->obIPopUpEstruturalReceitaFinal->setMostrarDescricao     ( false );

}

function montaHTML()
{
    $this->obIPopUpEstruturalReceitaInicial->montaHTML();
    $stHTMLEstruturalInicial = $this->obIPopUpEstruturalReceitaInicial->getHTML();

    $this->obLblCodEstrutural->montaHTML();
    $stHTMLLblCodEstrutural = $this->obLblCodEstrutural->getHTML();

    $this->obIPopUpEstruturalReceitaFinal->montaHTML();
    $stHTMLEstruturalFinal = $this->obIPopUpEstruturalReceitaFinal->getHTML();

    $obTabela = new Tabela;
    $obTabela->setCellPadding( 0 );
    $obTabela->setCellSpacing( 0 );
    $obTabela->setWidth( 52 );
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
