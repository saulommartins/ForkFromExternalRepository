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
    * Data de Criação: 28/09/2006

    * @author Analista: Gelson Gonçalves
    * @author Desenvolvedor: Andre Almeida

    * @package URBEM
    * @subpackage

    $Revision: 16284 $
    $Name$
    $Author: andre.almeida $
    $Date: 2006-10-03 07:05:54 -0300 (Ter, 03 Out 2006) $

    * Casos de uso: uc-03.03.06
*/

/*
$Log$
Revision 1.1  2006/10/03 10:05:18  andre.almeida
Bug #6910#

*/

include_once ( CLA_OBJETO );

class  IMontaItemUnidadeTipo extends Objeto
{
    public $obIMontaItemUnidade;
    public $obLabelUnidadeMedida;

    public function IMontaItemUnidadeTipo(&$obForm)
    {
        parent::Objeto();
        include_once( CAM_GP_ALM_COMPONENTES."IMontaItemUnidade.class.php" );

        $this->obIMontaItemUnidade = new IMontaItemUnidade( $obForm );
        $this->obIMontaItemUnidade->obIPopUpCatalogoItem->setExibeTipo( true );
        $this->obIMontaItemUnidade->obIPopUpCatalogoItem->setPreencheTipoNaoInformado( false );

        $this->obLabelTipo = new Label;
        $this->obLabelTipo->setRotulo('Tipo'   );
        $this->obLabelTipo->setId    ('stTipo' );
        $this->obLabelTipo->setValue ('&nbsp;' );

        //A declaração do Hidden está replicada no LSManterItem.php e no OCManterItem.php da popup
        $this->obHiddenCodTipo = new Hidden;
        $this->obHiddenCodTipo->setName( 'inCodTipo' );
        $this->obHiddenCodTipo->setId  ( 'inCodTipo' );

        $this->obHiddenNomTipo = new Hidden;
        $this->obHiddenNomTipo->setName( 'stNomTipo' );
        $this->obHiddenNomTipo->setId  ( 'stNomTipo' );

        $obFormularioSpan = new Formulario;
        $obFormularioSpan->addComponente( $this->obIMontaItemUnidade->obLabelUnidadeMedida     );
        $obFormularioSpan->addHidden    ( $this->obIMontaItemUnidade->obHiddenCodUnidadeMedida );
        $obFormularioSpan->addHidden    ( $this->obIMontaItemUnidade->obHiddenNomUnidadeMedida );
        $obFormularioSpan->addComponente( $this->obLabelTipo );
        $obFormularioSpan->addHidden    ( $this->obHiddenCodTipo );
        $obFormularioSpan->addHidden    ( $this->obHiddenNomTipo );
        $obFormularioSpan->montaInnerHTML();
        $stHtmlSpan = $obFormularioSpan->getHTML();

        $this->obIMontaItemUnidade->obSpnInformacoesItem->setValue( $stHtmlSpan );
    }

    public function geraFormulario(&$obFormulario)
    {
        $this->obIMontaItemUnidade->geraFormulario( $obFormulario );
    }
}

?>
