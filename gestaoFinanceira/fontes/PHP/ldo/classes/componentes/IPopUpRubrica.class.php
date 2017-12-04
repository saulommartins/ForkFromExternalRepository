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
 * Componente IPopUpRubrica
 * Data de Criação: 07/09/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Janilson Mendes Pereira da Silva <janilson.mendes>
 * @package GF
 * @subpackage LDO
 * @uc uc-02.10.03
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once CLA_BUSCAINNER;
include_once CAM_GA_ADM_MAPEAMENTO   . 'TAdministracaoConfiguracao.class.php';
include_once CAM_GA_ADM_MAPEAMENTO   . 'TAdministracaoConfiguracao.class.php';
include_once CAM_GF_ORC_COMPONENTES  . 'IPopUpEstruturalReceita.class.php';
include_once CAM_GF_ORC_NEGOCIO      . 'ROrcamentoReceita.class.php';

class IPopUpRubrica extends BuscaInner
{
    /**
     * Atributo para verificar se no Buscar Inner vai para a LS ou FL do componente
     * @
     **/
    private $usaFiltro;
    private $boDedutora;
    private $stTipoDedutora;
    public $obHiddenCodConta;

    public function setUsaFiltro($valor)
    {
        $this->usaFiltro = $valor;
    }

    public function setDedutora($boDedutora)
    {
        $this->boDedutora = $boDedutora;
    }

    public function setTipoDedutora($stTipoDedutora)
    {
        $this->stTipoDedutora = $stTipoDedutora;
    }

    public function getUsaFiltro()
    {
        return $this->usaFiltro;
    }

    public function getDedutora()
    {
        return $this->boDedutora;
    }

    public function getTipoDedutora()
    {
        return $this->stTipoDedutora;
    }

    public function __construct()
    {
        Sessao::remove('linkPopUp');
        parent::BuscaInner();

        $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao;

        $obTAdministracaoConfiguracao->setDado("cod_modulo", 8);
        $obTAdministracaoConfiguracao->setDado("exercicio", Sessao::getExercicio());

        if ($this->getTipoDedutora() == 'receita') {
            $obTAdministracaoConfiguracao->pegaConfiguracao($stMascara, "masc_class_receita_dedutora");
            $this->setTitle("Informe a Rubrica(Código Estrutural da Dedutora).");
        } elseif ($this->getTipoDedutora() ==  'despesa') {
            $obROrcamentoDespesa = new ROrcamentoDespesa;
            $stMascara = $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->recuperaMascara();
            $this->setTitle("Informe a Rubrica(Código Estrutural da Despesa).");
        } else {
            $obROrcamentoReceita = new ROrcamentoReceita;
            $stMascara = $obROrcamentoReceita->obROrcamentoClassificacaoReceita->recuperaMascara();
            $this->setTitle("Informe a Rubrica(Código Estrutural da Receita).");
        }

        $this->setRotulo("Rubrica");
        $this->setNull(true);
        $this->setId("stDescricaoReceita");
        $this->obCampoCod->setName("stCodReceita");
        $this->obCampoCod->setId("stCodReceita");
        $this->obCampoCod->setValue("");
        $this->obCampoCod->setAlign("left");
        $this->obCampoCod->setMascara($stMascara);
        $this->obCampoCod->setPreencheComZeros('D');
        $this->obCampoCod->setSize(strlen($stMascara));
        $this->obCampoCod->setMaxLength(strlen($stMascara));
        $this->obCampoCod->obEvento->setOnFocus("selecionaValorCampo( this );");
        $this->obCampoCod->obEvento->setOnKeyUp("mascaraDinamico('".$stMascara."', this, event);");
        $this->setUsaFiltro(true);

        $this->obHiddenCodConta = new Hidden();
        $this->obHiddenCodConta->setName("inCodConta");
        $this->obHiddenCodConta->setId("inCodConta");
        $this->obHiddenCodConta->setValue("");
    }

    public function geraFormulario(&$obFormulario)
    {
        $stTipoBusca = $this->getTipoDedutora() ? $this->getTipoDedutora() : null;
        $boDedutora  = $this->getDedutora() ? $this->getDedutora() : false;

        $pgOcul = "'".CAM_GF_LDO_POPUPS."rubrica/OCProcurarRubrica.php?".Sessao::getId()."&boDedutora=".(int) $boDedutora."&".$this->obCampoCod->getName()."='+this.value+'&stNomCampoCod=".$this->obCampoCod->getName()."&stIdCampoDesc=".$this->getId()."&stIdCodConta=".$this->obHiddenCodConta->getId()."'";

        if ($this->getTipoDedutora() == 'receita') {
            $this->obCampoCod->obEvento->setOnChange("ajaxJavaScript($pgOcul,'receita');");
        } elseif ($this->getTipoDedutora() ==  'despesa') {
            $this->obCampoCod->obEvento->setOnChange("ajaxJavaScript($pgOcul,'despesa');");
        } else {
            $this->obCampoCod->obEvento->setOnChange("ajaxJavaScript($pgOcul,'default');");
        }

        if ($this->getUsaFiltro()) {
            $this->setFuncaoBusca("abrePopUp('".CAM_GF_LDO_POPUPS."rubrica/FLProcurarRubrica.php?boDedutora=".(int) $boDedutora."&stIdCodConta=".$this->obHiddenCodConta->getId()."','frm','".$this->obCampoCod->getName()."','".$this->getId()."','".$stTipoBusca."','".Sessao::getId()."','800','550');");
        } else {
            $this->setFuncaoBusca("abrePopUp('".CAM_GF_LDO_POPUPS."rubrica/LSProcurarRubrica.php?boDedutora=".(int) $boDedutora."&stIdCodConta=".$this->obHiddenCodConta->getId()."','frm','".$this->obCampoCod->getName()."','".$this->getId()."','".$stTipoBusca."','".Sessao::getId()."','800','550');");
        }

        $obFormulario->addComponente($this);
        $obFormulario->addHidden($this->obHiddenCodConta);
    }
}
