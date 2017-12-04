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
 * Pagina de PopUp tipo do uc-02.10.04
 * Data de Criação: 17/02/209
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author <analista> Bruno Ferreira Santos <bruno.ferreira>
 * @author <desenvolvedor> Jânio Eduardo Vasconcellos de Magalhães <janio.magalhaes>
 * @package GF
 * @subpackage ldo
 * @uc uc-02.10.04
 */

include_once(CLA_BUSCAINNER);

class  IPopUpRecurso extends ComponenteBase
{
    /**
        * @access Private
        * @var Object
    */
    public $obInnerRecurso;
    public $inCodReceita;

    public function getName()
    {
        return 'IPopUpRecurso';
    }

    public function setCodReceita($inCodReceita)
    {
        $this->inCodReceita = $inCodReceita;
    }

    public function getCodReceita()
    {
        return $this->inCodReceita;
    }

    public function getExibePPA()
    {
        return $this->boExibePPA;
    }

    public function getRotulo()
    {
       return $this->obInnerRecurso->getRotulo();
    }

    public function setNumRecurso($inNumRecurso)
    {
        $this->inNumRecurso = $inNumRecurso;
    }

    public function setNull($boNull)
    {
        $this->obInnerRecurso->setNull($boNull);
    }

    /**
    * Metodo Construtor
    * @access Public
    */
    public function IPopUpRecurso()
    {
        $this->obInnerRecurso = new BuscaInner();

        $this->obInnerRecurso->setRotulo('Recurso');
        $this->obInnerRecurso->setTitle('Informe o Recurso.');
        $this->obInnerRecurso->setId('stNomRecurso');

        $this->obInnerRecurso->obCampoCod->setId('inNumRecurso');
        $this->obInnerRecurso->obCampoCod->setName('inNumRecurso');
        $this->obInnerRecurso->obCampoCod->setSize(10);
        $this->obInnerRecurso->obCampoCod->setMaxLength(9);
        $this->obInnerRecurso->obCampoCod->setAlign('left');
    }

    public function geraFormulario(&$obFormulario)
    {
        $this->obInnerRecurso->setFuncaoBusca("abrePopUp('".CAM_GF_LDO_POPUPS."recurso/FLProcurarRecurso.php','frm','".$this->obInnerRecurso->obCampoCod->getName()."','".$this->obInnerRecurso->getId()."','".$this->inCodReceita."','".Sessao::getId()."','800','550');");

        $this->obInnerRecurso->obCampoCod->obEvento->setOnChange("ajaxJavaScript( '".CAM_GF_LDO_POPUPS.'recurso/OCProcurarRecurso.php?'.Sessao::getId()."&stNomCampoCod=".$this->obInnerRecurso->obCampoCod->getName()."&stIdCampoDesc=".$this->obInnerRecurso->getId()."&inCodReceita=".$this->inCodReceita."&stNomForm=frm&inNumRecurso='+this.value, 'listaRecurso' );");
        $obFormulario->addComponente($this->obInnerRecurso);
    }

    public function montaHTML()
    {
        $this->obInnerRecurso->setFuncaoBusca("abrePopUp('" . CAM_GF_LDO_POPUPS . "recurso/FLProcurarRecurso.php','frm', '". $this->obInnerRecurso->obCampoCod->getName() ."','". $this->obInnerRecurso->getId() . "','','" . Sessao::getId() .	"','800','550');");

        $this->obInnerRecurso->obCampoCod->obEvento->setOnChange("ajaxJavaScript( '".CAM_GF_LDO_POPUPS.'recurso/OCProcurarRecurso.php?'.Sessao::getId()."&stNomCampoCod=".$this->obInnerRecurso->obCampoCod->getName()."&stIdCampoDesc=".$this->obInnerRecurso->getId()."&stNomForm=frm&inNumRecurso='+this.value, 'listaRecurso' );");

        $this->obInnerRecurso->montaHTML();
    }

    public function getHTML()
    {
        return $this->obInnerRecurso->getHTML();
    }
}
