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
 * Data de Criação: 13/09/2007

 * @author Analista: Gelson W. Gonçalves
 * @author Desenvolvedor: Henrique Boaventura

 * @package URBEM
 * @subpackage

 * Casos de uso: uc-03.01.06

 $Id: IPopUpBem.class.php 65343 2016-05-13 17:02:26Z arthur $

 */

include_once CLA_BUSCAINNER;

class IPopUpBem extends BuscaInner
{
    /**
     * @access Private
     * @var Object
    */
    public $obForm;

    /**
     * @access Private
     * @var Object
    */
    public $stTipoBusca;

    /**
      * @access Private
      * @var Object
      */
    public $stValicadao;

    public function setTipoBusca($valor) { $this->stTipoBusca = $valor; }

    # Método criado para sobrepor o valor do setOnBlur() default do componente.
    public function setValidacao($value) { $this->stValicadao = $value; }

    public function getValidacao() { return $this->stValicadao; }
    public function getTipoBusca() { return $this->stTipoBusca; }

    public function IPopUpBem(&$obForm)
    {
        parent::BuscaInner();

        $this->obForm = $obForm;

        $this->setRotulo ( 'Bem' );
        $this->setTitle  ( 'Informe o código do bem.' );
        $this->setId     ( 'stNomBem' );
        $this->setNull   ( false );

        $this->obCampoCod->setName      ( "inCodBem" );
        $this->obCampoCod->setId        ( "inCodBem" );
        $this->obCampoCod->setSize      ( 10 );
        $this->obCampoCod->setMaxLength ( 9 );
        $this->obCampoCod->setAlign     ( "left" );
    }

    public function montaHTML()
    {
        # Monta os parâmetros que possivelmente podem ser enviados ao Oculto.
        if ($this->getTipoBusca() == 'bemNaoBaixado') {
            $stLink = '&boBemBaixado=false';
        } elseif ($this->getTipoBusca() == 'bemBaixado') {
            $stLink = '&boBemBaixado=true';
        }

        if ( $this->getFuncaoBusca() == "")
            $this->setFuncaoBusca("abrePopUp('".CAM_GP_PAT_POPUPS."bem/FLManterBem.php','".$this->obForm->getName()."', '". $this->obCampoCod->stName ."','". $this->stId . "','','" . Sessao::getId().$stLink . "','800','550');");
        
        # Teste necessário para poder usar uma validação independente
        # sem passar pelo OCManterBem do Componente.
        if ($this->getValidacao()) {
            $this->obCampoCod->obEvento->setOnBlur($this->getValidacao());
        } elseif ($this->obCampoCod->obEvento->getOnChange() == "") {
            $this->obCampoCod->obEvento->setOnChange("ajaxJavaScript('".CAM_GP_PAT_POPUPS.'bem/OCManterBem.php?'.Sessao::getId().$stLink."&stNomCampoCod=".$this->obCampoCod->stName."&boMostrarDescricao=".$this->getMostrarDescricao()."&stIdCampoDesc=".$this->stId."&stNomForm=".$this->obForm->getName()."&inCodigo='+this.value, 'buscaPopup');". $this->obCampoCod->obEvento->getOnChange());
        }

        parent::montaHTML();
    }

}

?>
