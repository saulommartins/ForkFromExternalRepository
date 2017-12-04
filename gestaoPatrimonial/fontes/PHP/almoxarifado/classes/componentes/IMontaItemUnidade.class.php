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
    * Arquivo de popup de busca de Item do catálogo
    * Data de Criação: 27/02/2003

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage

    $Id: IMontaItemUnidade.class.php 64005 2015-11-17 16:49:06Z michel $

    * Casos de uso: uc-03.03.06
*/

include_once ( CLA_OBJETO );

class  IMontaItemUnidade extends Objeto
{
    public $obIPopUpCatalogoItem;
    public $obILabelUnidade;
    public $obSpnInformacoesItem;
    public $obISelectUnidadeMedida;
    public $obHiddenUnidade;
    public $obHiddenUnidadeValida;
    public $boPreencheUnidadeNaoInformada;
    public $obHiddenCodUnidadeMedida;
    public $obParametroDinamico;

    public function setPreencheUnidadeNaoInformada($valor)
    {
        $this->boPreencheUnidadeNaoInformada = $valor;
        $this->obIPopUpCatalogoItem->setPreencheUnidadeNaoInformada( $valor );
    }

    public function getPreencheUnidadeNaoInformada() { return $this->boPreencheUnidadeNaoInformada;   }

    public function IMontaItemUnidade(&$obForm)
    {
        parent::Objeto();
        include_once( CAM_GP_ALM_COMPONENTES."IPopUpItem.class.php" );

        $this->obIPopUpCatalogoItem = new IPopUpItem($obForm);
        $this->obIPopUpCatalogoItem->setNull           ( true );
        $this->obIPopUpCatalogoItem->setRetornaUnidade ( true );

        $this->obSpnInformacoesItem = new Span;
        $this->obSpnInformacoesItem->setId ( "spnInformacoesItem" );

        //Campo para validar o Combo caso a unidade não tenha sido informada e o PreencheUnidadeNaoInformada estiver como true
        $this->obHiddenUnidadeValida = new HiddenEval;
        $this->obHiddenUnidadeValida->setName( 'hdnUnidadeMedidaValida' );
        $this->obHiddenUnidadeValida->setId  ( 'hdnUnidadeMedidaValida' );

        //A declaração do label está replicada no LSManterItem.php e no OCManterItem.php da popup
        $this->obLabelUnidadeMedida = new Label;
        $this->obLabelUnidadeMedida->setRotulo('Unidade de Medida' );
        $this->obLabelUnidadeMedida->setId    ('stUnidadeMedida'   );
        $this->obLabelUnidadeMedida->setValue ('&nbsp;'            );

        //A declaração do Hidden está replicada no LSManterItem.php e no OCManterItem.php da popup
        $this->obHiddenCodUnidadeMedida = new Hidden;
        $this->obHiddenCodUnidadeMedida->setName( 'inCodUnidadeMedida' );
        $this->obHiddenCodUnidadeMedida->setId  ( 'inCodUnidadeMedida' );

        $this->obHiddenNomUnidadeMedida = new Hidden;
        $this->obHiddenNomUnidadeMedida->setName( 'stNomUnidade' );
        $this->obHiddenNomUnidadeMedida->setId  ( 'stNomUnidade' );

        $obFormularioSpan = new Formulario;
        $obFormularioSpan->addComponente( $this->obLabelUnidadeMedida     );
        $obFormularioSpan->addHidden    ( $this->obHiddenCodUnidadeMedida );
        $obFormularioSpan->addHidden    ( $this->obHiddenNomUnidadeMedida );
        $obFormularioSpan->montaInnerHTML();
        $stHtmlSpan = $obFormularioSpan->getHTML();

        $this->obSpnInformacoesItem->setValue( $stHtmlSpan );

        $this->obIPopUpCatalogoItem->setNomCampoUnidade( $this->obLabelUnidadeMedida->getId() );

        $this->setPreencheUnidadeNaoInformada( false );
    }

    public function geraFormulario(&$obFormulario)
    {
        $obFormulario->addComponente ( $this->obIPopUpCatalogoItem     );
        $obFormulario->addSpan       ( $this->obSpnInformacoesItem     );
        $obFormulario->addHidden     ( $this->obHiddenUnidadeValida    );
    }
}
?>
