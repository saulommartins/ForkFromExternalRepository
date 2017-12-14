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
* Arquivo de popup de busca de Regiao
* Data de Criação: 21/06/2007

* @author Analista: Anelise
* @author Desenvolvedor: Marcio Medeiros

* @package URBEM
* @subpackage

* Casos de uso: uc-02.09.11
*/

/*
$Log$
Revision 1.1  2007/06/21 19:38:03  leandro.zis
popup regiao do ppa
*/

include_once(CLA_BUSCAINNER);

class  IPopUpRegiao extends BuscaInner
{
    /**
        * @access Private
        * @var Object
    */
    public $obForm;

    /**
        * Metodo Construtor
        * @access Public
    */
    public function IPopUpRegiao($obForm)
    {
        parent::BuscaInner();

        $this->obForm = $obForm;

        $this->setRotulo('Região');
        $this->setTitle('Informe a região.');
        $this->setId('stNomRegiao');

        $this->obCampoCod->setName('inCodRegiao');
        $this->obCampoCod->setSize(10);
        $this->obCampoCod->setMaxLength(9);
        $this->obCampoCod->setAlign('left');
    }

    public function montaHTML()
    {
        $sessao = $_SESSION ['sessao'];

        $this->obCampoCod->setId($this->obCampoCod->getName());

        $this->setFuncaoBusca("abrePopUp('" . CAM_GF_PPA_POPUPS . "regiao/FLProcurarRegioes.php','".$this->obForm->getName()."', '". $this->obCampoCod->getName() ."','". $this->getId() . "','','" . Sessao::getId() .
        "','800','550');");

        $this->obCampoCod->obEvento->setOnChange("ajaxJavaScript( '".CAM_GF_PPA_POPUPS.'regiao/OCProcurarRegioes.php?'.Sessao::getId()."&stNomCampoCod=".$this->obCampoCod->getName()."&stIdCampoDesc=".$this->getId()."&stNomForm=".$this->obForm->getName()."&inCodigo='+this.value, 'buscaRegiao' );");

        parent::montaHTML();
    }
}
?>
