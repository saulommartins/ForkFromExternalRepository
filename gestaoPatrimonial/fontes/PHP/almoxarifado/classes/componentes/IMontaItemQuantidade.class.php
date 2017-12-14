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
    * Componente IMontaItemQuantidade
    * Data de Criação: 03/07/2003

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @package URBEM
    * @subpackage

    * Casos de uso: uc-03.03.10

    $Id: IMontaItemQuantidade.class.php 64051 2015-11-24 17:55:39Z franver $
*/

$pgOc = CAM_GP_ALM_PROCESSAMENTO.'OCIMontaItemQuantidade.php?'.Sessao::getId();

include_once( CLA_OBJETO );

class IMontaItemQuantidade extends Objeto
{
    public $obIMontaItemUnidade;
    public $obISelectAlmoxarifado;
    public $obCmbMarca;
    public $obCmbCentroCusto;
    public $obLblSaldo;
    public $obTxtQuantidade;
    public $obHdnMarca;
    public $obHdnCentroCusto;
    public $obHdnSaldo;
    public $boCentroCustoPermissao;
    public $boPerecivel;

    public function setPerecivel($boPerecivel) { $this->boPerecivel            = $boPerecivel;           }

    public function setCentroCustoPermissao($boCentroCustoPermissao) { $this->boCentroCustoPermissao = $boCentroCustoPermissao;}

    public function getCentroCustoPermissao() { return $this->boCentroCustoPermissao;}

    public function IMontaItemQuantidade($obForm, &$obISelectAlmoxarifado, $boPerecivel = false)
    {
        $pgOc = CAM_GP_ALM_PROCESSAMENTO.'OCIMontaItemQuantidade.php?'.Sessao::getId();

        parent::Objeto();
        include_once( CAM_GP_ALM_COMPONENTES."IMontaItemUnidade.class.php"   );
        include_once( CAM_GP_ALM_COMPONENTES."ISelectAlmoxarifadoAlmoxarife.class.php" );

        $this->boPerecivel   = $boPerecivel;
        $obIMontaItemUnidade = new IMontaItemUnidade($obForm);
        $obIMontaItemUnidade->obIPopUpCatalogoItem->setServico( false );
        $obIMontaItemUnidade->obIPopUpCatalogoItem->setComSaldo( true );

        $stCampoCod = $obIMontaItemUnidade->obIPopUpCatalogoItem->obCampoCod->getName();
        $stParam = $obISelectAlmoxarifado->getName()."='+document.frm.".$obISelectAlmoxarifado->getName().".value";
        $obIMontaItemUnidade->obIPopUpCatalogoItem->obCampoCod->obEvento->setOnChange( "ajaxJavaScript('".$pgOc."&".$stParam."+'&".$stCampoCod."='+this.value,'carregaMarca');" );
        $obIMontaItemUnidade->obIPopUpCatalogoItem->obCampoCod->obEvento->setOnBlur( "ajaxJavaScript('".$pgOc."&".$stParam."+'&".$stCampoCod."='+this.value,'carregaMarca');" );

        $obHdnMarca = new Hidden;
        $obHdnMarca->setName  ( "stMarca" );
        $obHdnMarca->setValue ( $stMarca  );

        $obCmbMarca = new Select;
        $obCmbMarca->setRotulo    ( 'Marca' );
        $obCmbMarca->setTitle     ( 'Selecione a marca do item.');
        $obCmbMarca->setName      ( 'inCodMarca' );
        $obCmbMarca->setID        ( 'inCodMarca' );
        $obCmbMarca->setValue     ( $inCodMarca  );
        $obCmbMarca->setCampoID   ( 'cod_marca'  );
        $obCmbMarca->setCampoDesc ( 'descricao'  );
        $obCmbMarca->addOption    ( "", "Selecione" );
        $stParam .= "+'&".$stCampoCod."='+document.frm.".$stCampoCod.".value";
        $obCmbMarca->obEvento->setOnChange( "ajaxJavaScript('".$pgOc."&".$stParam."+'&".$obCmbMarca->getName()."='+this.value,'carregaCentroCusto');" );

        $obHdnCentroCusto = new Hidden;
        $obHdnCentroCusto->setName  ( "stCentroCusto" );
        $obHdnCentroCusto->setValue ( $stCentroCusto  );

        $obCmbCentroCusto = new Select;
        $obCmbCentroCusto->setRotulo    ( 'Centro de Custo' );
        $obCmbCentroCusto->setTitle     ( 'Selecione o centro de custo do item.');
        $obCmbCentroCusto->setName      ( 'inCodCentroCusto' );
        $obCmbCentroCusto->setID        ( 'inCodCentroCusto' );
        $obCmbCentroCusto->setValue     ( $inCodCentroCusto  );
        $obCmbCentroCusto->setCampoID   ( 'cod_centro'  );
        $obCmbCentroCusto->setCampoDesc ( 'descricao'  );
        $obCmbCentroCusto->addOption    ( "", "Selecione" );
        $stParam .= "+'&".$obCmbMarca->getName()."='+document.frm.".$obCmbMarca->getName().".value";
        $obCmbCentroCusto->obEvento->setOnChange( "ajaxJavaScript('".$pgOc."&".$stParam."+'&".$obCmbCentroCusto->getName()."='+this.value,'mostraSaldo'); ajaxJavaScript('".$pgOc."&".$stParam."+'&".$obCmbCentroCusto->getName()."='+this.value,'mostraSaldoAtributo');".
( $this->boPerecivel ? "ajaxJavaScript('".$pgOc."&".$stParam."+'&".$obCmbCentroCusto->getName()."='+this.value,'mostraSaldoPereciveis');" : "" )
 );

        $obHdnSaldo = new Hidden;
        $obHdnSaldo->setName  ( "stSaldo" );
        $obHdnSaldo->setId    ( "stSaldo" );
        $obHdnSaldo->setValue ( $stSaldo  );

        $obSpnAtributos = new Span;
        $obSpnAtributos->setId('spnAtributos');

        $obSpnListaLotes = new Span;
        $obSpnListaLotes->setId('spnListaLotes');

        $obLblSaldo = new Label;
        $obLblSaldo->setRotulo( 'Saldo em Estoque' );
        $obLblSaldo->setId    ( 'inSaldo' );

        $obTxtQuantidade = new Quantidade;
        $obTxtQuantidade->setRotulo  ( "Quantidade" );
        $obTxtQuantidade->setValue   ( $inQuantidade  ); 
        $obTxtQuantidade->setInteiro ( false );
        $obTxtQuantidade->setFloat   ( true  );
        $obTxtQuantidade->setSize (14);
        $obTxtQuantidade->setMaxLength(13);
        $obTxtQuantidade->setDefinicao('NUMERIC');
        
        $this->obIMontaItemUnidade = & $obIMontaItemUnidade;
        $this->obCmbMarca = & $obCmbMarca;
        $this->obCmbCentroCusto = & $obCmbCentroCusto;
        $this->obLblSaldo = $obLblSaldo;

        $this->obSpnAtributos = $obSpnAtributos;
        $this->obSpnListaLotes = $obSpnListaLotes;

        $this->obTxtQuantidade = $obTxtQuantidade;
        $this->obHdnMarca = $obHdnMarca;
        $this->obHdnCentroCusto = $obHdnCentroCusto;
        $this->obHdnSaldo = $obHdnSaldo;
        $this->obISelectAlmoxarifado = $obISelectAlmoxarifado;
        $this->stOculto = $pgOc;
        $this->obISelectAlmoxarifado = &$obISelectAlmoxarifado;
        $this->setCentroCustoPermissao(false);
        $this->obISelectAlmoxarifado->obEvento->setOnChange( "ajaxJavaScript('".$pgOc."','limpaCampos');" );
    }

    public function geraFormulario(&$obFormulario)
    {
        $obFormulario->addHidden    ( $this->obHdnMarca );
        $obFormulario->addHidden    ( $this->obHdnCentroCusto );
        $obFormulario->addHidden    ( $this->obHdnSaldo );
        $this->obIMontaItemUnidade->geraFormulario( $obFormulario );
        $obFormulario->addComponente( $this->obCmbMarca );
        $obFormulario->addComponente( $this->obCmbCentroCusto );
        $obFormulario->addTitulo    ( "Quantidade" );
        $obFormulario->addComponente( $this->obLblSaldo );

        $obFormulario->addSpan( $this->obSpnAtributos);
        $obFormulario->addSpan( $this->obSpnListaLotes);

        $obFormulario->addComponente( $this->obTxtQuantidade );

        $obIMontaItemQuantidade = $this;
        Sessao::write("obIMontaItemQuantidade", $obIMontaItemQuantidade);

    }

}
