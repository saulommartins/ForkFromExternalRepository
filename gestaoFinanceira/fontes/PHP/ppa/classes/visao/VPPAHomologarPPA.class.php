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
 * Classe de Visão de Homologar PPA
 * Data de Criação: 29/09/2008

 * @author Analista: Heleno Menezes dos Santos
 * @author Desenvolvedor: Janilson Mendes P. da Silva

 * @package URBEM
 * @subpackage

 * Casos de uso: UC-02.09.12
 */
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';

final class VPPAHomologarPPA
{
    private $obController;
    private $arMap;

    public function __construct($obNegocio)
    {
        $this->obController = $obNegocio;
        $this->arMap[0] = "TPPAPPAEncaminhamento";
        $this->arMap[1] = "TPPAPPANorma";
        $this->arMap[2] = "TPPAPPAPublicacao";
        $this->arMap[3] = "TPPAPeriodicidade";
        $this->arMap[4] = "TOrcamentoEntidade";
        $this->arMap[5] = "TPPA";
    }

    public function cadastrarHomologacao($arParam)
    {
        return $this->obController->cadastrar($arParam);
    }

    public function pesquisaPPAEncaminhamento($stParam = "")
    {
        $stMap = $this->arMap[0];
        $stMetodo = "recuperaPPAEncaminhamento";

        return $this->obController->pesquisar($stMap, $stMetodo, $stParam, $stOrdem);
    }

    public function pesquisaDadosPPA($stParam = '')
    {
        $stMap = $this->arMap[5];
        $stMetodo = 'recuperaDadosPPA';
        $stOrdem  = ' ORDER BY ano_inicio ASC ';

        return $this->obController->pesquisar($stMap, $stMetodo, $stParam, $stOrdem);
    }

    public function pesquisaPPAHomologacao($stParam = '')
    {
        $stMap = $this->arMap[5];
        $stMetodo = 'recuperaPPAHomologacao';
        $stOrdem .= "   GROUP BY ppa.cod_ppa       \n";
        $stOrdem .= "          , ppa.ano_inicio    \n";
        $stOrdem .= "          , ppa.ano_final     \n";
        $stOrdem .= ' ORDER BY ano_inicio ASC ';

        return $this->obController->pesquisar($stMap, $stMetodo, $stParam, $stOrdem);
    }

    public function pesquisaPPANorma($stParam = "")
    {
        $stMap = $this->arMap[1];
        $stMetodo = "recuperaPPANorma";
        $stOrdem = ' ORDER BY ano_inicio ASC ';

        return $this->obController->pesquisar($stMap, $stMetodo, $stParam, $stOrdem);
    }

    public function pesquisaPeriodicidade($stParam = "")
    {
        $stMap = $this->arMap[3];
        $stMetodo = "recuperaPeriodicidade";

        return $this->obController->pesquisar($stMap, $stMetodo, $stParam, $stOrdem);
    }

    private function pesquisaEntidade($stParam = "")
    {
        $stMap = $this->arMap[4];
        $stMetodo = "recuperaEntidadeGeral";

        return $this->obController->pesquisar($stMap, $stMetodo, $stParam, $stOrdem);
    }

    public function filtrosHomologarPPA($arParam)
    {
        if ($arParam['inCodPPA'] != "") {
            $stFiltro[] = " ppa.cod_ppa = " .$arParam['inCodPPA']. "\n";
        }

        $return = " ";

        if ($stFiltro) {
            foreach ($stFiltro as $chave => $valor) {
                if ($chave == 0) {
                    $return .= $valor;
                } else {
                    $return .= " AND ".$valor;
                }
            }
        }

        return $return;
    }

    public function montaSpanHomologacao($arParam)
    {
        if ($arParam['inCodPPA'] == '') {
            $arParam['inCodPPA'] = $arParam['inCodPPASelecionado'];
        }

        if ($arParam['inCodPPA'] != '') {
            $stMap = $this->arMap[5];
            $stMetodo = "recuperaPPA";
            $stParam = " ppa.cod_ppa = ".$arParam['inCodPPA'];

            $rsPPA = $this->obController->pesquisar($stMap, $stMetodo, $stParam, $stOrdem);

            $obRsPeriodicidade = $this->pesquisaPeriodicidade('');

            //Define o formulario
            $obFormulario = new Formulario;

            $obEncaminhamento = new Hidden;
            $obEncaminhamento->setName('boEncaminhamento');
            $obEncaminhamento->setValue(true);

            ### Data de Encaminhamento Legislativo ###
            $obDataLegislativo = new Data;
            $obDataLegislativo->setName("dtDataLegislativo");
            $obDataLegislativo->setId("dtDataLegislativo");
            $obDataLegislativo->setSize("8");
            $obDataLegislativo->setStyle("width:90px;");
            $obDataLegislativo->setRotulo("Data de Encaminhamento Legislativo");
            $obDataLegislativo->setTitle("Informe a Data de Encaminhamento Legislativo.");
            $obDataLegislativo->setNull(false);
            $obDataLegislativo->obEvento->setOnChange('validaDataEncaminhamento();');

            ### Número do Protocolo ###
            $obTxtNumeroProtocolo = new TextBox();
            $obTxtNumeroProtocolo->setName('inProtocolo');
            $obTxtNumeroProtocolo->setId('inProtocolo');
            $obTxtNumeroProtocolo->setRotulo('Número do Protocolo');
            $obTxtNumeroProtocolo->setStyle("width:90px;");
            $obTxtNumeroProtocolo->setTitle('Informe o Número do Protocolo.');
            $obTxtNumeroProtocolo->obEvento->setOnKeyUp("mascaraProtocolo(this,verificaProtocolo);");
            $obTxtNumeroProtocolo->setMaxLength(9);
            $obTxtNumeroProtocolo->setNull(false);

            ### Data Devolução ao Executivo ###
            $obDataDevolucaoExecutivo = new Data;
            $obDataDevolucaoExecutivo->setName("dtDataDevolucaoExecutivo");
            $obDataDevolucaoExecutivo->setId("dtDataDevolucaoExecutivo");
            $obDataDevolucaoExecutivo->setSize("8");
            $obDataDevolucaoExecutivo->setStyle("width:90px;");
            $obDataDevolucaoExecutivo->setRotulo("Data de Devolução ao Executivo");
            $obDataDevolucaoExecutivo->setTitle("Informe a Data de Devolução ao Executivo.");
            $obDataDevolucaoExecutivo->setNull(false);
            $obDataDevolucaoExecutivo->obEvento->setOnChange('validaDataDevolucao();');

            ### Periodicidade Apuração das Metas ###
            $obPeriodicidadeApuracaoMetas = new Select;
            $obPeriodicidadeApuracaoMetas->setName("inPeriodicidadeApuracaoMetas");
            $obPeriodicidadeApuracaoMetas->setId("inPeriodicidadeApuracaoMetas");
            $obPeriodicidadeApuracaoMetas->setMultiple(false);
            $obPeriodicidadeApuracaoMetas->setStyle("width:90px;");
            $obPeriodicidadeApuracaoMetas->setRotulo("Periodicidade Apuração das Metas");
            $obPeriodicidadeApuracaoMetas->setTitle("Informe a Periodicidade Apuração das Metas.");
            $obPeriodicidadeApuracaoMetas->setNull(false);
            $obPeriodicidadeApuracaoMetas->setCampoId("cod_periodicidade");
            $obPeriodicidadeApuracaoMetas->setCampoDesc("nom_periodicidade");
            $obPeriodicidadeApuracaoMetas->addOption("", "Selecione");
            $obPeriodicidadeApuracaoMetas->preencheCombo($obRsPeriodicidade);

            ### Monta Veículo de Publicação ###
            $obSpanVeiculo = new Span();
            $obSpanVeiculo->setID("veiculoPublicacao");
            $obSpanVeiculo->setValue("");

            ### Dados da Primeira Homologação do PPA ###
            $obFormulario->addTitulo('Dados da Primeira Homologação do PPA');
            $obFormulario->addHidden($obEncaminhamento);
            $obFormulario->addComponente($obDataLegislativo);
            $obFormulario->addComponente($obTxtNumeroProtocolo);
            $obFormulario->addComponente($obDataDevolucaoExecutivo);
            $obFormulario->addSpan($obSpanVeiculo);
            $obFormulario->addComponente($obPeriodicidadeApuracaoMetas);
            $obFormulario->montaInnerHTML();
            $stJs  = "$('inCodPPATxt').value = '".$arParam['inCodPPA']."';";
            $stJs .= "$('inCodPPA').value = '".$arParam['inCodPPA']."';";
            $stJs .= "jq('#stAnoInicio').val(".$rsPPA->getCampo('ano_inicio').");";
            $stJs .= "jq('#stAnoFinal').val(".$rsPPA->getCampo('ano_final').");";
            $stJs .= "$('spnHomologacaoPPA').innerHTML = '".$obFormulario->getHTML()."';";
            $stJs .="buscaValor('montaVeiculo');";
        } else {
            $stJs .= "$('spnHomologacaoPPA').innerHTML = '';";
        }

        return $stJs;
    }

    public function montaVeiculo()
    {
        require_once CAM_GF_PPA_COMPONENTES.'MontaVeiculoPublicitario.class.php';

        $obFormulario = new Formulario;
        $obVeiculoPublicitario = new MontaVeiculoPublicitario;
        $obVeiculoPublicitario->obTxtCodTpVeiculoPublicitario->setRotulo("Tipo do Veículo de Publicação");
        $obVeiculoPublicitario->obTxtCodTpVeiculoPublicitario->setTitle("Tipo do Veículo de Publicação");
        $obVeiculoPublicitario->obTxtCodEmpresa->setRotulo("Nome do Veículo de Publicação");
        $obVeiculoPublicitario->obTxtCodEmpresa->setTitle("Nome do Veículo de Publicação");
        $obVeiculoPublicitario->geraFormulario($obFormulario);
        $obFormulario->montaInnerHTML();
        $stJs = "d.getElementById('veiculoPublicacao').innerHTML = '". $obFormulario->getHTML(). "';\n";

        sistemaLegado::executaFrameOculto($stJs);
    }

    public function preencheEmpresas()
    {
        require_once '../../../../../../gestaoFinanceira/fontes/PHP/ppa/classes/visao/VPPAMontaVeiculoPublicitario.class.php';

        $obVPPAMontaVeiculoPublicitario = new VPPAMontaVeiculoPublicitario();

        return $obVPPAMontaVeiculoPublicitario->preencheEmpresas();
    }

    /**
     * Verifica se existe diferença entre o valor da Receita e o da Despesa e
     * pede confirmação para o usuário se deseja continuar a homologação do PPA.
     *
     * @param array $arParametros
     */
    public function calcularDiferencaReceitaDespesa($arParametros)
    {
        $inCodPPA = (int) $arParametros['inCodPPA'];
        if ($inCodPPA > 0) {
            if ($this->obController->calcularDiferencaReceitaDespesa($inCodPPA)) {
                $_REQUEST["inCodPPA"] = $inCodPPA;
                $stMsg = 'Existe diferença entre o valor da Receita e o da Despesa.\nDeseja continuar?';
                $stJs  = "$('spnHomologacaoPPA').innerHTML = '';";
                $stJs .= "$('inCodPPASelecionado').value = '$inCodPPA';";
                $stJs .= "confirmPopUp('Homologar PPA', '$stMsg', 'montaParametrosGET(\'montaSpanHomologacao\');');";
                $stJs .= 'limparCampoPPA();';
            } else {
                $stJs = "montaParametrosGET('montaSpanHomologacao');";
            }
        } else {
            $stJs = 'limparSpanPPA();';
        }

        return $stJs;
    }

    public function montarEntidades()
    {
        $rsRecordSet = $this->pesquisaEntidade();
        $inCount = count($rsRecordSet->arElementos);

        $arEntidades = array();

        for ($i = 0; $i < $inCount; $i++) {
            $arCampos = $rsRecordSet->arElementos[$i];
            $arEntidades[$i] = $arCampos['cod_entidade'];
        }

        asort($arEntidades);

        return implode(',', $arEntidades);
    }
}

?>
