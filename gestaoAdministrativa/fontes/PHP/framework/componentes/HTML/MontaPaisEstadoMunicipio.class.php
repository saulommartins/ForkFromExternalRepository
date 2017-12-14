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
    * Componente monta PaisEstadoMunicipio
    * Data de Criação: 19/08/2008

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: <Alex Cardoso>

    * @ignore

    $Id: MontaPaisEstadoMunicipio.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-01.01.00
*/

include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoPais.class.php");
include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoUF.class.php");
include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoMunicipio.class.php");

/**
    * Gera um Componente composto por combos de País / Estado / Município
    * @author Desenvolvedor: Alex Cardoso

    * @package framework
    * @subpackage componentes
*/

class MontaPaisEstadoMunicipio extends Objeto
{
    /**
        * @access Private
        * @var Integer
    */
    public $inCodPais;

    /**
        * @access Private
        * @var Integer
    */
    public $inCodEstado;

    /**
        * @access Private
        * @var Integer
    */
    public $inCodMunicipio;

    /**
        * @access Private
        * @var Object
    */
    public $obComboPais;

    /**
        * @access Private
        * @var Object
    */
    public $obComboEstado;

    /**
        * @access Private
        * @var Object
    */
    public $obComboMunicipio;

    /**
        * @access Private
        * @var String
    */
    public $stIdComponente;

    /**
        * @access Private
        * @var Boolean
    */
    public $boExibePais;

    /**
        * @access Private
        * @var Boolean
    */
    public $boExibeEstado;

    /**
        * @access Private
        * @var Boolean
    */
    public $boExibeMunicipio;

    /**
        * @access Public
        * @param String $valor
    */
    public function setPais($valor) { $this->inCodPais = $valor; }

    /**
        * @access Public
        * @param String $valor
    */
    public function setEstado($valor) { $this->inCodEstado = $valor; }

    /**
        * @access Public
        * @param String $valor
    */
    public function setMunicipio($valor) { $this->inCodMunicipio = $valor; }

    /**
        * @access Public
        * @param String $valor
    */
    public function setComboPais($valor) { $this->obComboPais = $valor; }

    /**
        * @access Public
        * @param Object $valor
    */
    public function setComboEstado($valor) { $this->obComboEstado = $valor; }

    /**
        * @access Public
        * @param Object $valor
    */
    public function setComboMunicipio($valor) { $this->obComboMunicipio = $valor; }

    /**
        * @access Public
        * @param String $valor
    */
    public function setIdComponente($valor) { $this->stIdComponente = $valor; }

    /**
        * @access Public
        * @param String $valor
    */
    public function setExibePais($valor) { $this->boExibePais = $valor; }

    /**
        * @access Public
        * @param String $valor
    */
    public function setExibeEstado($valor) { $this->boExibeEstado = $valor; }

    /**
        * @access Public
        * @param String $valor
    */
    public function setExibeMunicipio($valor) { $this->boExibeMunicipio = $valor; }

    /**
        * @access Public
        * @return Integer
    */
    public function getPais() { return $this->inCodPais; }

    /**
        * @access Public
        * @return Integer
    */
    public function getEstado() { return $this->inCodEstado; }

    /**
        * @access Public
        * @return Integer
    */
    public function getMunicipio() { return $this->inCodMunicipio; }

    /**
        * @access Public
        * @return String
    */
    public function getIdComponente() { return $this->stIdComponente; }

    /**
        * @access Public
        * @param Object
    */
    public function getComboPais() { return $this->obComboPais; }

    /**
        * @access Public
        * @param Object
    */
    public function getComboEstado() { return $this->obComboEstado; }

    /**
        * @access Public
        * @param Object
    */
    public function getComboMunicipio() { return $this->obComboMunicipio; }

    /**
        * Método Construtor
        * @access Public
    */
    public function MontaPaisEstadoMunicipio()
    {
        $this->obComboPais = new Select();
        $this->obComboPais->setRotulo("País");
        $this->obComboPais->setStyle("width: 250px");
        $this->obComboPais->setName("inCodPais");

        $this->obComboEstado = new Select();
        $this->obComboEstado->setRotulo("Estado");
        $this->obComboEstado->setStyle("width: 250px");
        $this->obComboEstado->setName("inCodEstado");

        $this->obComboMunicipio = new Select();
        $this->obComboMunicipio->setRotulo("Município");
        $this->obComboMunicipio->setStyle("width: 250px");
        $this->obComboMunicipio->setName("inCodMunicipio");

        $this->boExibePais = true;
    }

    /**
        * Monta os componentes
        * @access Public
        * @param  Object $obFormulario Objeto formulario
    */
    public function geraFormulario(&$obFormulario)
    {
        if ($this->boExibePais) {
            if($this->obComboPais->getName() == "")
                $this->obComboPais->setName("inCodPais");

            if($this->obComboPais->getId() == "")
                $this->obComboPais->setId("inCodPais");

            $this->obComboPais->setName($this->obComboPais->getName().$this->getIdComponente());
            $this->obComboPais->setId($this->obComboPais->getId().$this->getIdComponente());
        }

        if ($this->boExibeEstado) {
            if($this->obComboEstado->getName() == "")
                $this->obComboEstado->setName("inCodEstado");

            if($this->obComboEstado->getId() == "")
                $this->obComboEstado->setId("inCodEstado");

            $this->obComboEstado->setName($this->obComboEstado->getName().$this->getIdComponente());
            $this->obComboEstado->setId($this->obComboEstado->getId().$this->getIdComponente());
        }

        if ($this->boExibeMunicipio) {
            if($this->obComboMunicipio->getName() == "")
                $this->obComboMunicipio->setName("inCodMunicipio");

            if($this->obComboMunicipio->getId() == "")
                $this->obComboMunicipio->setId("inCodMunicipio");

            $this->obComboMunicipio->setName($this->obComboMunicipio->getName().$this->getIdComponente());
            $this->obComboMunicipio->setId($this->obComboMunicipio->getId().$this->getIdComponente());
        }

        //adicionado para poder existir mais de uma instância deste componente
        if ($this->boExibePais) {
            $rsPaises = new RecordSet();
            $obTPais  = new TPais;
            $obErro = $obTPais->recuperaTodos( $rsPaises, "", "nom_pais");

            $this->obComboPais->addOption( "", "Selecione" );
            if (!$obErro->ocorreu()) {
                while (!$rsPaises->eof()) {
                    $this->obComboPais->addOption( $rsPaises->getCampo('cod_pais'), $rsPaises->getCampo('nom_pais') );
                    $rsPaises->proximo();
                }
            }

            $this->obComboPais->setValue($this->getPais());

            $obFormulario->addComponente($this->obComboPais);

            if ($this->boExibeEstado) {
                if($this->boExibeMunicipio)
                    $stLimpaComboMunicipio = "limpaSelect( document.getElementById('".$this->obComboMunicipio->getId()."'),1);";

                $this->obComboPais->obEvento->setOnChange( "$stLimpaComboMunicipio ajaxJavaScript('".CAM_FW_INSTANCIAS."processamento/OCMontaPaisEstadoMunicipio.php?".Sessao::getId()."&stPersistente=TUF&stIdCombo=".$this->obComboEstado->getId()."&stCampoId=cod_uf&stCampoDesc=nom_uf&cod_pais='+this.value, 'preencher');" );
            }
        }

        if ($this->boExibeEstado) {
            $this->obComboEstado->addOption( "", "Selecione" );

            if ($this->getPais()) {
                $rsEstados  = new RecordSet();
                $obTEstados = new TUF;
                $stFiltroEstados = " WHERE cod_pais = ".$this->getPais();
                $obErro = $obTEstados->recuperaTodos( $rsEstados, $stFiltroEstados, "nom_uf");

                if (!$obErro->ocorreu()) {
                    while (!$rsEstados->eof()) {
                        $this->obComboEstado->addOption( $rsEstados->getCampo('cod_uf'), $rsEstados->getCampo('nom_uf') );
                        $rsEstados->proximo();
                    }
                }
            }

            $this->obComboEstado->setValue($this->getEstado());

            $obFormulario->addComponente($this->obComboEstado);

            if ($this->boExibeMunicipio) {
                $this->obComboEstado->obEvento->setOnChange( "ajaxJavaScript('".CAM_FW_INSTANCIAS."processamento/OCMontaPaisEstadoMunicipio.php?".Sessao::getId()."&stPersistente=TMunicipio&stIdCombo=".$this->obComboMunicipio->getId()."&stCampoId=cod_municipio&stCampoDesc=nom_municipio&cod_uf='+this.value, 'preencher');" );
            }
        }

        if ($this->boExibeMunicipio) {
            $this->obComboMunicipio->addOption( "", "Selecione" );

            if ($this->getEstado()) {
                $rsMunicipios  = new RecordSet();
                $obTMunicipios = new TMunicipio;
                $stFiltroMunicipios = " WHERE cod_uf = ".$this->getEstado();
                $obErro = $obTMunicipios->recuperaTodos( $rsMunicipios, $stFiltroMunicipios, "nom_municipio");

                if (!$obErro->ocorreu()) {
                    while (!$rsMunicipios->eof()) {
                        $this->obComboMunicipio->addOption( $rsMunicipios->getCampo('cod_municipio'), $rsMunicipios->getCampo('nom_municipio') );
                        $rsMunicipios->proximo();
                    }
                }
            }
            $this->obComboMunicipio->setValue($this->getMunicipio());

            $obFormulario->addComponente($this->obComboMunicipio);
        }
    }

}//end class

?>
