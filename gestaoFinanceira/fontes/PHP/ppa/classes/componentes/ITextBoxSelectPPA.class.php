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
* Arquivo que monta o combo de PPA
* Data de Criação: 26/09/2008

* @author Analista: Heleno Santos
* @author Desenvolvedor: Fellipe Esteves dos Santos
*/

require_once CLA_SELECT;
require_once CAM_GF_PPA_MAPEAMENTO.'TPPA.class.php';

class ITextBoxSelectPPA extends TextBoxSelect
{
    private $_boExibePrograma
    ,   $_boProgramaObrigatorio
    ,   $_obSpnPrograma
    ,   $_boPreenche
    ,   $_inCodPPA
    ,   $_boHomologado
    ,   $_rsPPA;

    public function setCodPPA($inCodPPA)
    {
       $this->_inCodPPA = $inCodPPA;
    }

    public function setHomologado($boValue)
    {
        $this->_boHomologado = $boValue;
    }

    public function setExibirPrograma($_boExibePrograma, $_boProgramaObrigatorio = 0)
    {
        $this->_boExibePrograma = $_boExibePrograma;
        $this->_boProgramaObrigatorio = $_boProgramaObrigatorio;
    }

    public function setPreencheUnico($_boPreenche)
    {
        $this->_boPreenche = $_boPreenche;
    }

    public function setRecordSet($rsPPA)
    {
        $this->_rsPPA = $rsPPA;
    }

    public function getHomologado()
    {
        return $this->_boHomologado;
    }

    public function getCodPPA()
    {
       return $this->_inCodPPA;
    }

    public function getExibirPrograma()
    {
        return $this->_boExibePrograma;
    }

    public function getPreencheUnico()
    {
       return $this->_boPreenche;
    }

    public function getRecordSet()
    {
        return $this->_rsPPA;
    }

    public function __construct($rsPPA = false)
    {
        parent::TextBoxSelect();

        $this->setRotulo('PPA');
        $this->setName('inCodPPA');
        $this->setTitle('Selecione o PPA.');
        $this->setPreencheUnico(false);
        $this->setRecordSet($rsPPA);

        $this->obTextBox->setRotulo('PPA');
        $this->obTextBox->setTitle('Selecione o PPA.');
        $this->obTextBox->setName('inCodPPATxt');
        $this->obTextBox->setId('inCodPPATxt');
        $this->obTextBox->setSize(10);
        $this->obTextBox->setMaxLength(10);
        $this->obTextBox->setInteiro(true);

        $this->obSelect->setRotulo('PPA');
        $this->obSelect->setName('inCodPPA');
        $this->obSelect->setId('inCodPPA');
        $this->obSelect->setCampoID('cod_ppa');
        $this->obSelect->setCampoDesc('periodo');
        $this->obSelect->addOption('', 'Selecione');
        $this->obSelect->setStyle('width: 205px');
    }

    protected function _geraRecordSet()
    {
        $obTPPA      = new TPPA;
        $rsRecordSet = new Recordset;

        if ($this->getHomologado()) {
            $stWhere = "\n WHERE ppa.fn_verifica_homologacao(ppa.cod_ppa) = true";
        }
        $obTPPA->recuperaPPA($rsRecordSet, $stWhere, ' order by ano_inicio');

        return $rsRecordSet;
    }

    private function _verificaRecordSet()
    {
        if ($this->getRecordSet()) {
            $this->obSelect->preencheCombo($this->getRecordSet());
        } else {
            $rsRecordSet = $this->_geraRecordSet();
            $this->setRecordSet($rsRecordSet);
            $this->obSelect->preencheCombo($rsRecordSet);
        }
    }

    public function geraFormulario(&$obFormulario)
    {
        $obFormulario->addComponente($this);

        if ($this->_boExibePrograma) {
            $this->_obSpnPrograma = new Span();
            $this->_obSpnPrograma->setId('spnIPopUpPrograma');

            $obFormulario->addSpan($this->_obSpnPrograma);

            $stOnChange = $this->obTextBox->obEvento->getOnChange();
            $this->obTextBox->obEvento->setOnChange("ajaxJavaScriptSincrono('".CAM_GF_PPA_POPUPS.'ppa/OCProcurarPPA.php?'.Sessao::getId()."&stNomCampoSpan=".$this->_obSpnPrograma->getId()."&stNomCampoCod=".$this->obSelect->getName()."&stIdCampoDesc=".$this->obTextBox->getName()."&boExibePrograma=".(bool) $this->_boExibePrograma."&boProgramaObrigatorio=".$this->_boProgramaObrigatorio."&inCodPPA='+this.value, 'exibePrograma' );" . $stOnChange);

            $stOnChange = $this->obSelect->obEvento->getOnChange();
            $this->obSelect->obEvento->setOnChange("ajaxJavaScriptSincrono('".CAM_GF_PPA_POPUPS.'ppa/OCProcurarPPA.php?'.Sessao::getId()."&stNomCampoSpan=".$this->_obSpnPrograma->getId()."&stNomCampoCod=".$this->obSelect->getName()."&stIdCampoDesc=".$this->obTextBox->getName()."&boExibePrograma=".(bool) $this->_boExibePrograma."&boProgramaObrigatorio=".$this->_boProgramaObrigatorio."&inCodPPA='+this.value, 'exibePrograma' );" . $stOnChange);

            if ($this->_boPreenche) {
                $arOption = $this->obSelect->getOption();
                if (count($arOption) == 2) {
                    $this->obSelect->setValue($arOption[1]->getValor());
                    $this->obTextBox->setValue($arOption[1]->getValor());
                }
            }
        }
    }

    public function montaHTML()
    {
        $this->_verificaRecordSet();

        if ($this->_inCodPPA != '') {
           $this->obTextBox->setValue($this->_inCodPPA);
           $this->obSelect->setValue($this->_inCodPPA);
        }

        if ($this->_boPreenche) {
            $arOption = $this->obSelect->getOption();
            if (count($arOption) == 2) {
                $this->obSelect->setValue($arOption[1]->getValor());
                $this->obTextBox->setValue($arOption[1]->getValor());
            }
        }
        if ($this->_boExibePrograma) {

        }

        parent::montaHTML();
    }
}
