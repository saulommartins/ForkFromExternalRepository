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
* Arquivo de popup de busca de Recurso
* Data de Criação: 06-10-2008

* @author Analista: Heleno Santos
* @author Desenvolvedor: Aldo Jean

* @package URBEM
* @subpackage

$Revision: 30824 $
$Name$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoFuncao.class.php"        );
include_once ( CLA_BUSCAINNER );

class IPopUpFuncao extends BuscaInner
{

    public $obForm;
    public $boNull = false;
    public $inExercicio;
    public $inCodFuncao;
    public $stDescricao;

    public function setNull($valor) { $this->boNull 	  = $valor;   }
    public function getNull() { return $this->boNull; 	    }

    /**
        * @access Public
        * @param Character $Valor
    */
    public function setExercicio($valor) { $this->inExercicio  = $valor; }
    /**
        * @access Public
        * @param Inteter $Valor
    */
    public function setCod_funcao($valor) { $this->inCodFuncao  = $valor; }
    /**
        * @access Public
        * @param Character $Valor
    */
    public function setDescricao($valor) { $this->stDescricao  = $valor; }

    /**
        * @access Public
        * @return Character
    */
    public function getExercicio() { return $this->inExercicio; }
    /**
        * @access Public
        * @return Integer
    */
    public function getCod_funcao() { return $this->inCodFuncao; }
    /**
        * @access Public
        * @return Character
    */
    public function getDescricao() { return $this->stDescricao; }

    public function IPopUpFuncao()
    {
        parent::BuscaInner();

           $this->obForm = $obForm;
           $this->setObrigatorio ( true );
           $this->setRotulo('Função');
           $this->setTitle('Informe a Função.');
           $this->setId('stCodFuncao');
           $this->obCampoCod->setName('inCodFuncao');
           $this->obCampoCod->setSize(10);
           $this->obCampoCod->setMaxLength(9);
           $this->obCampoCod->setAlign('left');
           $this->stTipo = 'geral';
    }

    public function geraFormulario(&$obFormulario)
    {
        $this->obCampoCod->setId($this->obCampoCod->getName());

        if ($this->getCod_funcao()) {
            $obTOrcamentoFuncao = new TOrcamentoFuncao();
            $obTOrcamentoFuncao->setDado('cod_funcao', $this->stDescricao);
            $obTOrcamentoFuncao->recuperaTodos($rsRecordSet);

            $this->obCampoCod->setValue($rsRecordSet->getCampo('cod_funcao'));
            $this->setValue($rsRecordSet->getCampo('descricao'));
        }

        $pgOcul = "'../../../../../../gestaoFinanceira/fontes/PHP/ppa/popups/funcao/OCFuncao.php?".Sessao::getId();
        $pgOcul.= "&".$this->obCampoCod->getName()."='+this.value+'&stNomCampoCod=".$this->obCampoCod->getName();
        $pgOcul.= "&stIdCampoDesc=".$this->getId()."'";

        $this->obCampoCod->obEvento->setOnChange("ajaxJavaScript(".$pgOcul.",'buscaFuncao' );");
        $this->setFuncaoBusca("abrePopUp('".CAM_GF_PPA_POPUPS."funcao/FLFuncao.php','frm', '".$this->obCampoCod->stName ."','". $this->stId . "','". $this->stTipo . "','" . Sessao::getId() ."','800','550');");

        $obFormulario->addComponente($this);
    }
}
?>
