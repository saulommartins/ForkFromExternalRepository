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
* Arquivo de popup de busca de SubFunção
* Data de Criação: 06/10/2008

* @author Analista: Heleno Santos
* @author Desenvolvedor: Aldo Jean Soares Silva

* @package URBEM
* @subpackage

$Revision: 30824 $
$Name$
$Author: cako $
$Date: 2008-03-10 12:08:26 $

*/

/*
$Log$
Revision 1.2  2006/07/05 20:41:48  cleisson
Adicionada tag Log aos arquivos
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoSubfuncao.class.php"        );
include_once ( CLA_BUSCAINNER );

class IPopUpSubFuncao extends BuscaInner
{

    public $obForm;
    public $boNull = false;
    public $inExercicio;
    public $InCodSubFuncao;
    public $chDescricaoSubFuncao;

    public function setNull($valor) { $this->boNull 	  = $valor; }
    public function getNull() { return $this->boNull; 	    	    }

    /**
        * @access Public
        * @param Character $Valor
    */
    public function setExercicio($valor) { $this->inExercicio  = $valor; }
    /**
        * @access Public
        * @param Inteter $Valor
    */
    public function setcodSubFuncao($valor) { $this->InCodSubFuncao  = $valor; }
    /**
        * @access Public
        * @param Character $Valor
    */
    public function setdescricaoSubFuncao($valor) { $this->chDescricaoSubFuncao  = $valor; }

    /**
        * @access Public
        * @return Character
    */
    public function getExercicio() { return $this->inExercicio; }
    /**
        * @access Public
        * @return Integer
    */
    public function getcodSubFuncao() { return $this->InCodSubFuncao; }
    /**
        * @access Public
        * @return Character
    */
    public function getdescricaoSubFuncao() { return $this->chDescricaoSubFuncao; }

    public function IPopUpSubFuncao()
    {
        parent::BuscaInner();

           $this->obForm = $obForm;
           $this->setObrigatorio ( true );
           $this->setRotulo('Subfunção');
           $this->setTitle('Informe a Subfunção.');
           $this->setId('stCodSubFuncao');
           $this->obCampoCod->setName('inCodSubFuncao');
           $this->obCampoCod->setSize(10);
           $this->obCampoCod->setMaxLength(9);
           $this->obCampoCod->setAlign('left');
           $this->stTipo = 'geral';
    }

    public function geraFormulario(&$obFormulario)
    {

        $this->obCampoCod->setId($this->obCampoCod->getName());

        if ($this->getcodSubFuncao()) {
            $obTOrcamentoFuncao = new TOrcamentoSubFuncao();
            $obTOrcamentoFuncao->setDado('cod_subfuncao', $this->chDescricaoSubFuncao);
            $obTOrcamentoFuncao->recuperaTodos($rsRecordSet);

            $this->obCampoCod->setValue($rsRecordSet->getCampo('cod_subfuncao'));
            $this->setValue($rsRecordSet->getCampo('descricao'));
        }

        $pgOcul = "'../../../../../../gestaoFinanceira/fontes/PHP/ppa/popups/subfuncao/OCSubFuncao.php?".Sessao::getId();
        $pgOcul.= "&".$this->obCampoCod->getName()."='+this.value+'&stNomCampoCod=".$this->obCampoCod->getName();
        $pgOcul.= "&stIdCampoDesc=".$this->getId()."'";

        $this->obCampoCod->obEvento->setOnChange("ajaxJavaScript(".$pgOcul.",'buscaSubFuncao' );");
        $this->setFuncaoBusca("abrePopUp('" . CAM_GF_PPA_POPUPS . "subfuncao/FLSubFuncao.php','frm', '".$this->obCampoCod->stName ."','". $this->stId . "','". $this->stTipo . "','" . Sessao::getId() ."','800','550');");

        $obFormulario->addComponente($this);
    }
}
?>
