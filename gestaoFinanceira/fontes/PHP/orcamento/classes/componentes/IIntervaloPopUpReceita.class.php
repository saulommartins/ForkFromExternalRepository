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
    * Data de Criação: 10/05/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    $Id: IIntervaloPopUpReceita.class.php 59612 2014-09-02 12:00:51Z gelson $

    Casos de uso: uc-02.01.06. uc-02.01.34
*/

/*
$Log: IIntervaloPopUpReceita.class.php,v $
Revision 1.1  2007/05/11 02:25:14  diego
Novos componentes adicionados para corrigir o bug:
Bug #9113#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once ( CLA_COMPONENTE );
include_once ( CAM_GF_ORC_COMPONENTES."IPopUpReceita.class.php" );

class IIntervaloPopUpReceita extends Componente
{
/**
    * @access Private
    * @var Object
*/
var $obIPopUpReceitaInicial;
/**
    * @access Private
    * @var Object
*/
var $obLblCodReceita;
/**
    * @access Private
    * @var Object
*/
var $obIPopUpReceitaFinal;

function IIntervaloPopUpReceita($obCmbEntidades = '', $boDedutora = '')
{
    parent::Componente();

    $this->setRotulo("Código Reduzido");
    $this->setTitle ("Informe o código reduzido da ".($boDedutora ? "Dedutora" : "Receita"));

    $this->obIPopUpReceitaInicial = new IPopUpReceita($obCmbEntidades, $boDedutora);
    $this->obIPopUpReceitaInicial->setId                   ( "stDescricaoReceitaInicial" );
    $this->obIPopUpReceitaInicial->obCampoCod->setName     ( "inCodReceitaInicial" );
    $this->obIPopUpReceitaInicial->obCampoCod->setValue    ( "" );
    $this->obIPopUpReceitaInicial->setMostrarDescricao     ( false );
    if($boDedutora)
        $this->obIPopUpReceitaInicial->setTipoBusca        ( 'receitaDedutora' );

    $this->obLblCod = new Label;
    $this->obLblCod->setValue(" até  ");

    $this->obIPopUpReceitaFinal = new IPopUpReceita($obCmbEntidades, $boDedutora);
    $this->obIPopUpReceitaFinal->setId                   ( "stDescricaoReceitaFinal" );
    $this->obIPopUpReceitaFinal->obCampoCod->setName     ( "inCodReceitaFinal" );
    $this->obIPopUpReceitaFinal->obCampoCod->setValue    ( "" );
    $this->obIPopUpReceitaFinal->setMostrarDescricao     ( false );
    if($boDedutora)
        $this->obIPopUpReceitaFinal->setTipoBusca        ( 'receitaDedutora' );

}

function montaHTML()
{
    $this->obIPopUpReceitaInicial->montaHTML();
    $stHTMLInicial = $this->obIPopUpReceitaInicial->getHTML();

    $this->obLblCod->montaHTML();
    $stHTMLLblCod = $this->obLblCod->getHTML();

    $this->obIPopUpReceitaFinal->montaHTML();
    $stHTMLFinal = $this->obIPopUpReceitaFinal->getHTML();

    $obTabela = new Tabela;
    $obTabela->setCellPadding( 0 );
    $obTabela->setCellSpacing( 0 );
    $obTabela->setWidth( 32 );
    $obTabela->setBorder( 0 );
    $obTabela->addLinha();

    $obTabela->ultimaLinha->addCelula();
    $obTabela->ultimaLinha->ultimaCelula->setWidth( "5" );
    $obTabela->ultimaLinha->ultimaCelula->addConteudo( $stHTMLInicial );
    $obTabela->ultimaLinha->commitCelula();

    $obTabela->ultimaLinha->addCelula();
    $obTabela->ultimaLinha->ultimaCelula->setClass( "fieldlabel" );
    $obTabela->ultimaLinha->ultimaCelula->setWidth( "4" );
    $obTabela->ultimaLinha->ultimaCelula->addConteudo( $stHTMLLblCod  );
    $obTabela->ultimaLinha->commitCelula();

    $obTabela->ultimaLinha->addCelula();
    $obTabela->ultimaLinha->ultimaCelula->setWidth( "5" );
    $obTabela->ultimaLinha->ultimaCelula->addConteudo( $stHTMLFinal );
    $obTabela->ultimaLinha->commitCelula();

    $obTabela->commitLinha();
    $obTabela->montaHTML();
    $this->setHTML( $obTabela->getHTML() );
}

}
?>
