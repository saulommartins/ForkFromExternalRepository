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
 * Gerar o componente composto com a opcao de busca em POPUP
 * Data de Criação: 08/02/2003

 * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

 * @package framework
 * @subpackage componentes

 Casos de uso: uc-03.03.05

 $Id: IMontaCatalogoClassificacao.class.php 59612 2014-09-02 12:00:51Z gelson $

 */

include_once CAM_GP_ALM_COMPONENTES."IMontaClassificacao.class.php";

/**
 * Classe que monta o HTML da BuscaInner
 * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

 * @package framework
 * @subpackage componentes
 */

class IMontaCatalogoClassificacao extends Objeto
{
    public $obILabelCatalogo;
    public $obITextBoxSelectCatalogo;
    public $obIMontaClassificacao;
    public $obSpnMontaClassificacao;
    public $stHTML;
    public $stRotulo;
    public $stTitle;
    public $boNull;
    public $stName;
    public $stDefinicao;
    public $stValue;
    public $stId;
    public $boReadOnly;

    //SETTERS
    public function setHTML($valor) { $this->stHTML   = $valor; }
    public function setRotulo($valor) { $this->stRotulo = $valor; }
    public function setTitle($valor) { $this->stTitle  = $valor; }
    public function setNull($valor) { $this->boNull   = $valor; }
    public function setName($valor) { $this->stName   = $valor; }

    /**
        * @access Public
        * @param String $valor
    */
    public function setId($valor)
    {
        $this->stId = $valor;
    }

    /**
        * @access Public
        * @param String $valor
    */
    public function setValue($valor)
    {
        $this->stValue = $valor;
    }

    //GETTERS
    public function getHTML() { return $this->stHTML;      }
    public function getRotulo() { return $this->stRotulo;    }
    public function getTitle() { return $this->stTitle;     }
    public function getNull() { return $this->boNull;      }
    public function getName() { return $this->stName;      }
    public function getDefinicao() { return $this->stDefinicao; }
    public function getValue() { return $this->stValue;     }
    public function getId() { return $this->stId;        }

    public function IMontaCatalogoClassificacao()
    {
        include_once CAM_GP_ALM_COMPONENTES."ITextBoxSelectCatalogo.class.php";
        include_once CAM_GP_ALM_COMPONENTES."ILabelCatalogo.class.php";

        $pgOcul  = CAM_GP_ALM_PROCESSAMENTO.'OCIMontaCatalogoClassificacao.php?'.Sessao::getId();

        $this->obHdnValida = new HiddenEval;
        $this->obHdnValida->setName("stValida");

        $this->obILabelCatalogo = new ILabelCatalogo;

        $this->obHdnCatalogo = new Hidden;
        $this->obHdnCatalogo->setName("inCodCatalogo");

        $this->obITextBoxSelectCatalogo = new ITextBoxSelectCatalogo;
        $this->obITextBoxSelectCatalogo->obTextBox->obEvento->setOnChange("ajaxJavaScript('".$pgOcul."&inCodCatalogo='+this.value,'montaClassificacao')");
        $this->obITextBoxSelectCatalogo->obSelect->obEvento->setOnChange("ajaxJavaScript('".$pgOcul."&inCodCatalogo='+this.value,'montaClassificacao')");

        $this->obIMontaClassificacao = new IMontaClassificacao();

        $this->obSpnMontaClassificacao = new Span;
        $this->obSpnMontaClassificacao->setId('spnClassificacao');
        $this->stDefinicao = 'IMONTACATALOGOCLASSIFICACAO';
    }

    public function setCodCatalogo($inCodCatalogo)
    {
        $this->obITextBoxSelectCatalogo->setCodCatalogo($inCodCatalogo);
        $this->obILabelCatalogo->setCodCatalogo($inCodCatalogo);
        $this->obHdnCatalogo->setValue($inCodCatalogo);
    }

    public function setReadOnly($boReadOnly) { $this->boReadOnly = $boReadOnly; }

    public function geraFormulario($obFormulario)
    {
        $this->obIMontaClassificacao->setReadOnly($this->boReadOnly);

        Sessao::write('objMontaClassificacao', $this->obIMontaClassificacao);

        $obFormulario->addHidden ( $this->obHdnValida, true);

        if ($this->boReadOnly) {
           $obFormulario->addComponente( $this->obILabelCatalogo );
           $obFormulario->addHidden( $this->obHdnCatalogo);
        } else {
           $obFormulario->addComponente( $this->obITextBoxSelectCatalogo );
        }

        $obFormulario->addSpan      ( $this->obSpnMontaClassificacao  );
    }
}

?>
