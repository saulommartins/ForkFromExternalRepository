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
    * Classe interface para Filtro de Contrato de Servidores
    * não Aposentados e não Pensionistas

    * Data de Criação: 19/12/2008

    * @author Analista      : Heleno Menezes dos Santos
    * @author Desenvolvedor : Janilson Mendes P. da Silva

*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once(CAM_GRH_PES_COMPONENTES."IContratoDigitoVerificador.class.php");

class IFiltroContratoAposentadoPensionista extends Objeto
{

    public $obLblCGM;
    public $obHdnCGM;
    public $obIContratoDigitoVerificador;
    public $obLblInformacoesFuncao;
    public $obTituloFormulario;
    private $stSituacao;
    private $boInformacoesFuncao;

    public function setInformacoesFuncao($valor) { $this->boInformacoesFuncao = $valor; }

    public function setTituloFormulario($valor) { $this->obTituloFormulario = $valor; }

    public function setSituacao($valor) { $this->stSituacao = $valor; }

    public function getInformacoesFuncao() { return $this->boInformacoesFuncao; }

    public function getTituloFormulario() { return $this->obTituloFormulario; }

    public function getSituacao() { return $this->stSituacao; }

    public function __construct($stSituacao = false)
    {
        switch ($stSituacao) {
            case true:
                $this->setSituacao('rescindidos');
                break;
            case false:
                $this->setSituacao('ativos');
                break;
            case "todos":
                $this->setSituacao('todos');
                break;
        }

        $this->obLblCGM = new Label();
        $this->obLblCGM->setRotulo("CGM");
        $this->obLblCGM->setName("inNomCGM");
        $this->obLblCGM->setId("inNomCGM");

        $this->obHdnCGM = new Hidden();
        $this->obHdnCGM->setName("hdnCGM");
        $this->obHdnCGM->setValue("");

        $this->obIContratoDigitoVerificador = new IContratoDigitoVerificador("",$stSituacao);
        $this->obIContratoDigitoVerificador->setPagFiltro(true);

        $this->setInformacoesFuncao(false);
        $this->setTituloFormulario("Filtro por Matrícula");

    }

    public function geraFormulario(&$obFormulario)
    {
        $stOnChange = $this->obIContratoDigitoVerificador->obTxtRegistroContrato->obEvento->getOnChange();
        $stOnBlur = $this->obIContratoDigitoVerificador->obTxtRegistroContrato->obEvento->getOnBlur();

        if ($this->getInformacoesFuncao()) {
            $this->obLblInformacoesFuncao = new Label();
            $this->obLblInformacoesFuncao->setId("stInformacoesFuncao");
            $this->obLblInformacoesFuncao->setRotulo("Informações da Função");
            $this->obLblInformacoesFuncao->setValue("");
            $boFuncao = true;
        } else {
            $boFuncao = false;
        }

        $this->obIContratoDigitoVerificador->obTxtRegistroContrato->obEvento->setOnChange("ajaxJavaScriptSincrono( '../../../../../../gestaoTributaria/fontes/PHP/fiscalizacao/instancias/fiscal/OCFiltroContratoAposentadoPensionista.php?".Sessao::getId()."&".$this->obIContratoDigitoVerificador->obTxtRegistroContrato->getName()."='+this.value+'&stAcao='+document.frm.stAcao.value+'&boFuncao=".$boFuncao."', 'validaRegistroContrato' );".$stOnChange);
        $this->obIContratoDigitoVerificador->obTxtRegistroContrato->obEvento->setOnBlur("ajaxJavaScriptSincrono( '../../../../../../gestaoTributaria/fontes/PHP/fiscalizacao/instancias/fiscal/OCFiltroContratoAposentadoPensionista.php?".Sessao::getId()."&".$this->obIContratoDigitoVerificador->obTxtRegistroContrato->getName()."='+this.value+'&stAcao='+document.frm.stAcao.value+'&boFuncao=".$boFuncao."', 'validaRegistroContrato' );".$stOnBlur);

        if ($this->getTituloFormulario()!='') {
            $obFormulario->addTitulo($this->getTituloFormulario());
        }

        $obFormulario->addHidden($this->obHdnCGM);
        $obFormulario->addComponente($this->obLblCGM);
        $this->obIContratoDigitoVerificador->geraFormulario($obFormulario);

        if ($this->getInformacoesFuncao()) {
            $obFormulario->addComponente($this->obLblInformacoesFuncao);
        }
    }
}
