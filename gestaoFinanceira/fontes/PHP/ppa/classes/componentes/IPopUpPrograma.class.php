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
* Arquivo de popup de busca de Programas
* Data de Criação: 21/06/2007

* @author Analista: Heleno Santos
* @author Desenvolvedor: Fellipe Esteves dos Santos
*/

include_once (CAM_GF_PPA_NEGOCIO . "RPPAManterPrograma.class.php");

include_once CLA_BUSCAINNER;

class  IPopUpPrograma extends ComponenteBase
{
    /**
     * @access Public
     */
    public $obInnerPrograma;
    public $obForm;

    public $inNumPrograma;
    public $inCodPPA;

    public function getName()
    {
        return 'IPopUpPrograma';
    }

    public function getRotulo()
    {
       return $this->obInnerPrograma->getRotulo();
    }

    public function setNumPrograma($inNumPrograma)
    {
        $this->inNumPrograma = $inNumPrograma;
    }

    public function setCodPPA($inCodPPA)
    {
        $this->inCodPPA = $inCodPPA;
    }

    /**
    * Metodo Construtor
    * @access Public
    */
    public function IPopUpPrograma($obForm)
    {
        $this->obInnerPrograma = new BuscaInner();

        $this->obForm = $obForm;

        $this->obInnerPrograma->setRotulo('Programa');
        $this->obInnerPrograma->setTitle('Informe o programa.');
        $this->obInnerPrograma->setId('stNomPrograma');

        $this->obInnerPrograma->obCampoCod->setId('inNumPrograma');
        $this->obInnerPrograma->obCampoCod->setName('inNumPrograma');
        $this->obInnerPrograma->obCampoCod->setSize(10);
        $this->obInnerPrograma->obCampoCod->setMaxLength(9);
        $this->obInnerPrograma->obCampoCod->setAlign('left');
    }

    public function geraFormulario(&$obFormulario)
    {
        $obRPPAManterPrograma  = new RPPAManterPrograma();
        $obVPPAManterPrograma  = new VPPAManterPrograma($obRPPAManterPrograma);

        $this->obInnerPrograma->setFuncaoBusca("abrePopUp('".CAM_GF_PPA_POPUPS."programa/FLProcurarPrograma.php','".$this->obForm->getName()."','".$this->obInnerPrograma->obCampoCod->getName()."','".$this->obInnerPrograma->getId()."','".$this->inCodPPA."','".Sessao::getId()."','800','550');");

        $stAcaoChange = $this->obInnerPrograma->obCampoCod->obEvento->getOnChange();
        $this->obInnerPrograma->obCampoCod->obEvento->setOnChange("ajaxJavaScriptSincrono( '".CAM_GF_PPA_POPUPS.'programa/OCProcurarPrograma.php?'.Sessao::getId()."&stNomCampoCod=".$this->obInnerPrograma->obCampoCod->getName()."&stIdCampoDesc=".$this->obInnerPrograma->getId()."&stNomForm=".$this->obForm->getName()."&inCodPPA=".$this->inCodPPA."&inNumPrograma='+this.value, 'buscaPrograma' );" . $stAcaoChange);

        $obFormulario->addComponente($this->obInnerPrograma);
    }

    public function montaHTML()
    {
        $sessao = $_SESSION['sessao'];

        $this->obInnerPrograma->setFuncaoBusca("abrePopUp('" . CAM_GF_PPA_POPUPS . "programa/FLProcurarPrograma.php','".$this->obForm->getName()."', '". $this->obInnerPrograma->obCampoCod->getName() ."','". $this->obInnerPrograma->getId() . "','','" . Sessao::getId() .	"','800','550');");

        $stAcaoChange = $this->obInnerPrograma->obCampoCod->obEvento->getOnChange();
        $this->obInnerPrograma->obCampoCod->obEvento->setOnChange("ajaxJavaScriptSincrono( '".CAM_GF_PPA_POPUPS.'programa/OCProcurarPrograma.php?'.Sessao::getId()."&stNomCampoCod=".$this->obInnerPrograma->obCampoCod->getName()."&stIdCampoDesc=".$this->obInnerPrograma->getId()."&stNomForm=".$this->obForm->getName()."&inCodPPA=".$this->inCodPPA."&inNumPrograma='+this.value, 'buscaPrograma' );" . $stAcaoChange);

        $this->obInnerPrograma->montaHTML();
    }

    public function getHTML()
    {
        return $this->obInnerPrograma->getHTML();
    }
}
?>
