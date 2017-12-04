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
* Arquivo de popup de busca de Produto
* Data de Criação: 21/06/2007

* @author Analista: Anelise
* @author Desenvolvedor: Leandro André Zis

* @package URBEM
* @subpackage

* Casos de uso: uc-02.09.11
*/

/*
$Log$
Revision 1.1  2007/06/21 19:38:03  leandro.zis
popup produto do ppa
*/

include_once(CLA_BUSCAINNER);

class  IPopUpProduto extends BuscaInner
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
    public function IPopUpProduto($obForm)
    {
        parent::BuscaInner();

        $this->obForm = $obForm;

        $this->setRotulo('Produto');
        $this->setTitle('Informe o produto.');
        $this->setId('stNomProduto');

        $this->obCampoCod->setName('inCodProduto');
        $this->obCampoCod->setId('inCodProduto');
        $this->obCampoCod->setSize(10);
        $this->obCampoCod->setMaxLength(9);
        $this->obCampoCod->setAlign('left');
    }

    public function montaHTML()
    {
        $sessao = $_SESSION ['sessao'];

        $this->setFuncaoBusca("abrePopUp('" . CAM_GF_PPA_POPUPS . "produto/FLProcurarProdutos.php','".$this->obForm->getName()."', '". $this->obCampoCod->getName() ."','". $this->getId() . "','','" . Sessao::getId() .
        "','800','550');");

        $this->obCampoCod->obEvento->setOnChange("ajaxJavaScript( '".CAM_GF_PPA_POPUPS.'produto/OCProcurarProdutos.php?'.Sessao::getId()."&stNomCampoCod=".$this->obCampoCod->getName()."&stIdCampoDesc=".$this->getId()."&stNomForm=".$this->obForm->getName()."&inCodigo='+this.value, 'buscaProduto' );");

        parent::montaHTML();
    }
}
?>
