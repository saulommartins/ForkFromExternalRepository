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

include_once(CLA_BUSCAINNER);

class IPopUpRecurso extends ComponenteBase
{
    /**
        * @access Private
        * @var Object
    */
    public $obForm;
    public $obInnerRecurso;
    public $obLblRecursoValor;
    public $obHdnRecursoValor;
    public $obHdnPPA;

    public $boExibeValor;

    public function getName()
    {
        return 'IPopUpRecurso';
    }

    public function setUtilizaDestinacao($boValor)
    {
        $this->boUtilizaDestinacao = $boValor;
    }

    public function getUtilizaDestinacao()
    {
        return $this->boUtilizaDestinacao;
    }

    public function setExibeValor($boExibeValor)
    {
        $this->boExibeValor = $boExibeValor;
    }

    public function getExibeValor()
    {
        return $this->boExibeValor;
    }

    public function getRotulo()
    {
       return $this->obInnerRecurso->getRotulo();
    }

    public function setCodRecurso($inCodRecurso)
    {
        $this->inCodRecurso = $inCodRecurso;
    }

    /**
    * Metodo Construtor
    * @access Public
    */
    public function IPopUpRecurso($obForm)
    {
        $this->obInnerRecurso = new BuscaInner();

        $this->obForm = $obForm;

        $this->obInnerRecurso->setRotulo('Recurso');
        $this->obInnerRecurso->setTitle('Informe o recurso.');
        $this->obInnerRecurso->setId('stDescricaoRecurso');

        $this->obInnerRecurso->obCampoCod->setId('inCodRecurso');
        $this->obInnerRecurso->obCampoCod->setName('inCodRecurso');
        $this->obInnerRecurso->obCampoCod->setSize(10);
        $this->obInnerRecurso->obCampoCod->setMaxLength(9);
        $this->obInnerRecurso->obCampoCod->setAlign('left');

        $this->obLblRecursoValor = new Label;
        $this->obLblRecursoValor->setRotulo('Valor' );
        $this->obLblRecursoValor->setName('lbRecursoValor');
        $this->obLblRecursoValor->setId('lbRecursoValor');
        $this->obLblRecursoValor->setValue("&nbsp;");

        $this->obHdnRecursoValor = new Hidden;
        $this->obHdnRecursoValor->setName('flRecursoValor');
        $this->obHdnRecursoValor->setId('flRecursoValor');

        $this->obHdnPPA = new Hidden;
        $this->obHdnPPA->setName('inRecursoValor');
        $this->obHdnPPA->setId('inRecursoValor');
    }

    public function geraFormulario(&$obFormulario)
    {
        if ($this->inCodRecurso) {
            $rsRecurso = new RecordSet();

            $this->obHdnRecursoValor->setValue('dsadsa');
            $this->obLblRecursoValor->setValue('dsadsa');

            $obFormulario->addHidden($this->obHdnRecursoValor);
            $obFormulario->addComponente($this->obLblRecursoValor);

            if ($boUtilizaDestinacao == true) {
                $stFiltro = " WHERE recurso.exercicio = ".Sessao::read('exercicio')." AND recurso_destinacao.cod_recurso = ".$inCodRecurso;
                $obTOrcamentoRecursoDestinacao = new TOrcamentoRecursoDestinacao();
                $obTOrcamentoRecursoDestinacao->recuperaRelacionamento($rsRecurso, $stFiltro);
            }

            if ($boUtilizaDestinacao == false) {
                $obTOrcamentoRecursoDireto = new TOrcamentoRecursoDireto();
                $obTOrcamentoRecursoDireto->setDado('cod_recurso', $inCodRecurso);
                   $obTOrcamentoRecursoDireto->setDado('exercicio', Sessao::read('exercicio'));
                $obTOrcamentoRecursoDireto->recuperaPorChave($rsRecurso);
            }

            $this->obInnerRecurso->setValue( $rsRecurso->getCampo('nom_recurso'));
            $this->obInnerRecurso->obCampoCod->setValue($inCodRecurso);
        }

        $this->obInnerRecurso->setFuncaoBusca("abrePopUp('".CAM_GF_PPA_POPUPS."recurso/FLProcurarRecurso.php','".$this->obForm->getName()."','".$this->obInnerRecurso->obCampoCod->getName()."','".$this->obInnerRecurso->getId()."','".(int) $this->boUtilizaDestinacao."','".Sessao::getId()."','800','550');");

        $this->obInnerRecurso->obCampoCod->obEvento->setOnChange("ajaxJavaScript( '".CAM_GF_PPA_POPUPS.'recurso/OCProcurarRecurso.php?'.Sessao::getId()."&stNomCampoCod=".$this->obInnerRecurso->obCampoCod->getName()."&stIdCampoDesc=".$this->obInnerRecurso->getId()."&boUtilizaDestinacao=".(bool) $this->boUtilizaDestinacao."&boExibeValor=".(bool) $this->boExibeValor."&stNomForm=".$this->obForm->getName()."&inCodRecurso='+this.value, 'buscaRecurso' );");

        $obFormulario->addHidden($this->obHdnPPA);

        if ($boExibeValor) {
            $obFormulario->addHidden($this->obHdnRecursoValor);
            $obFormulario->addComponente($this->obLblRecursoValor);
        }

        $obFormulario->addComponente($this->obInnerRecurso);
    }

    public function montaHTML()
    {
        $this->obInnerRecurso->setFuncaoBusca("abrePopUp('" . CAM_GF_PPA_POPUPS . "recurso/FLProcurarRecurso.php','".$this->obForm->getName()."', '". $this->obInnerRecurso->obCampoCod->getName() ."','". $this->obInnerRecurso->getId() . "','".(int) $this->boUtilizaDestinacao."', '".(int) $this->boUtilizaDestinacao."','" . Sessao::getId() .	"','800','550');");

        $this->obInnerRecurso->obCampoCod->obEvento->setOnChange("ajaxJavaScript( '".CAM_GF_PPA_POPUPS.'recurso/OCProcurarRecurso.php?'.Sessao::getId()."&stNomCampoCod=".$this->obInnerRecurso->obCampoCod->getName()."&stIdCampoDesc=".$this->obInnerRecurso->getId()."&boUtilizaDestinacao=".(bool) $this->boUtilizaDestinacao."&boExibeValor=".(bool) $this->boExibeValor."&stNomForm=".$this->obForm->getName()."&inCodRecurso='+this.value, 'buscaRecurso' );");

        $this->obInnerRecurso->montaHTML();
    }

    public function getHTML()
    {
        return $this->obInnerRecurso->getHTML();
    }
}
?>
