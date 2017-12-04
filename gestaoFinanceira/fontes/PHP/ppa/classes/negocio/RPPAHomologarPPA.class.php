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
    * Classe de Regra de Negócio de Homologar PPA
    * Data de Criação: 29/09/2008

    * @author Analista: Heleno Menezes dos Santos
    * @author Desenvolvedor: Janilson Mendes P. da Silva

    * @package URBEM
    * @subpackage

    * Casos de uso: UC-02.09.12
*/
require_once '../../../../../../gestaoFinanceira/fontes/PHP/ppa/classes/mapeamento/TPPAPPAEncaminhamento.class.php';
require_once '../../../../../../gestaoFinanceira/fontes/PHP/ppa/classes/mapeamento/TPPAPPANorma.class.php';
require_once '../../../../../../gestaoFinanceira/fontes/PHP/ppa/classes/mapeamento/TPPAPPAPublicacao.class.php';
require_once '../../../../../../gestaoFinanceira/fontes/PHP/ppa/classes/mapeamento/TPPAPeriodicidade.class.php';
require_once CAM_GF_PPA_MAPEAMENTO . 'TPPA.class.php';
require_once CAM_GF_PPA_MAPEAMENTO . 'TPPAReceita.class.php';
require_once CAM_GF_ORC_MAPEAMENTO . 'TOrcamentoEntidade.class.php';
require_once CAM_GF_LDO_MAPEAMENTO . 'TLDO.class.php';
require_once CAM_GF_LDO_NEGOCIO.'RLDOLDO.class.php';

class RPPAHomologarPPA
{
    private $arMapeamentos;
    private $pgForm;
    public $obRLDO;

    public function __construct()
    {
        $this->pgForm = "FMHomologarPPA.php";
        $this->obRLDO = new RLDOLDO;
    }

    protected function callMapeamento($stMapeamento, $stMetodo, $stCriterio = "", $stOrdem = "",$boTransacao = "")
    {
        if ($stCriterio) {
            $stCriterio = ' WHERE ' . $stCriterio;
        }

        $obMapeamento = new $stMapeamento();

        if ($stMapeamento == "TOrcamentoEntidade") {
            $obMapeamento->setDado("exercicio", Sessao::read('exercicio'));
        }

        $obMapeamento->$stMetodo($obRecordSet, $stCriterio, $stOrdem,$boTransacao);

        return $obRecordSet;
    }

    public function pesquisar($stMap, $stMetodo, $stParam, $stOrdem,$boTransacao = "")
    {
        return $this->callMapeamento($stMap, $stMetodo, $stParam, $stOrdem, $boTransacao);
    }

    public function cadastrar($arParam)
    {
        if ($arParam["boEncaminhamento"]) {

            if ($arParam["dtDataLegislativo"] == "") {
                SistemaLegado::LiberaFrames(true,false);
                $erros[] = sistemaLegado::exibeAviso("Informe a Data de Encaminhamento Legislativo!","alerta","alerta");
            }

            if ($arParam["inProtocolo"] == "") {
                SistemaLegado::LiberaFrames(true,false);
                $erros[] = sistemaLegado::exibeAviso("Informe o Número de Protocolo!","alerta","alerta");
            }

            if ($arParam["dtDataDevolucaoExecutivo"] == "") {
                SistemaLegado::LiberaFrames(true,false);
                $erros[] = sistemaLegado::exibeAviso("Informe a Data de Devolução ao Executivo!","alerta","alerta");
            }

            if ($arParam["inCodigoTipoVeiculoPublicitario"] == "") {
                SistemaLegado::LiberaFrames(true,false);
                $erros[] = sistemaLegado::exibeAviso("Selecione um Tipo do Veículo de Publicação!","alerta","alerta");
            }

            if ($arParam["inCodigoEmpresa"] == "") {
                SistemaLegado::LiberaFrames(true,false);
                $erros[] = sistemaLegado::exibeAviso("Selecione um Nome do Veículo de Publicação!","alerta","alerta");
            }

            if ($arParam["inPeriodicidadeApuracaoMetas"] == "") {
                SistemaLegado::LiberaFrames(true,false);
                $erros[] = sistemaLegado::exibeAviso("Informe a Periodicidade de Apuracao das Metas!","alerta","alerta");
            }

            if ($arParam["dtDataLegislativo"] != "" && $arParam["dtDataDevolucaoExecutivo"] != "") {
                $dtNew = explode("/",$arParam["dtDataLegislativo"]);
                $dtLegislativo = strtotime($dtNew[2]."-".$dtNew[1]."-".$dtNew[0]);

                $dtOld = explode("/",$arParam["dtDataDevolucaoExecutivo"]);
                $dtExecutivo = strtotime($dtOld[2]."-".$dtOld[1]."-".$dtOld[0]);

                if ($dtLegislativo > $dtExecutivo) {
                    SistemaLegado::LiberaFrames(true,false);
                    $erros[] = sistemaLegado::exibeAviso("Data de Devolução ao Executivo menor Data de Encaminhamento Legislativo!","alerta","alerta");
                }
            }
            if (is_array($erros)) {
                print_r($erros);
                foreach ($erros as $vlr) {
                    $return.= $vlr;
                }

                return $return;
            }
        }

        $inCodPPA = $arParam["inCodPPA"];
        $inCodNorma = $arParam["inCodNorma"];
        $dtDataLegislativo = $arParam["dtDataLegislativo"];
        $inProtocolo = $arParam["inProtocolo"];
        $dtDataDevolucaoExecutivo = $arParam["dtDataDevolucaoExecutivo"];
        $inCodigoTipoVeiculoPublicitario = $arParam["inCodigoTipoVeiculoPublicitario"];
        $inCodigoEmpresa = $arParam["inCodigoEmpresa"];
        $inPeriodicidadeApuracaoMetas = $arParam["inPeriodicidadeApuracaoMetas"];

        $obTPPAPPAEncaminhamento = new TPPAPPAEncaminhamento();
        $obTPPAPPAPublicacao = new TPPAPPAPublicacao();

        $obTransacao = new Transacao();
        $boFlagTransacao = false;
        $boTransacao = "";

        $stCampo = 'tablename';
        $stTabela = 'pg_tables';
        $stFiltro = " WHERE schemaname = 'ldo' AND tablename = 'ldo'";
        $boCampoExiste = SistemaLegado::pegaDado($stCampo, $stTabela, $stFiltro);
        $boLDO = false;

        // Inicia nova transação
        $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        $stTimestamp = date('Y-m-d H:i:s');
        if ($arParam["boEncaminhamento"]) {
            $obTPPAPPAPublicacao->setDado("cod_ppa"       , $inCodPPA);
            $obTPPAPPAPublicacao->setDado("numcgm_veiculo", $inCodigoEmpresa);
            $obTPPAPPAPublicacao->setDado("cod_norma"     , $inCodNorma);
            $obTPPAPPAPublicacao->setDado("timestamp"     , $stTimestamp);

            $obErro = $obTPPAPPAPublicacao->inclusao($boTransacao);

            // Termina transação erro $obTPPAPPAPublicacao
            if ($obErro->ocorreu()) {
                SistemaLegado::LiberaFrames(true,false);

                return sistemaLegado::exibeAviso("Houve erro ao incluir a data de publicação deste PPA(".$inCodPPA.")","erro","erro");
            }

            $obTPPAPPAEncaminhamento->setDado("cod_ppa", $inCodPPA);
            $obTPPAPPAEncaminhamento->setDado("cod_periodicidade", $inPeriodicidadeApuracaoMetas);
            $obTPPAPPAEncaminhamento->setDado("dt_encaminhamento", $dtDataLegislativo);
            $obTPPAPPAEncaminhamento->setDado("dt_devolucao", $dtDataDevolucaoExecutivo);
            $obTPPAPPAEncaminhamento->setDado("nro_protocolo", $inProtocolo);
            $obTPPAPPAEncaminhamento->setDado("timestamp", $stTimestamp);

            $obErro = $obTPPAPPAEncaminhamento->inclusao($boTransacao);

            if (!$obErro->ocorreu()) {

                if ($boCampoExiste) {
                    // Verifica quantos anos tem entre o início e fim do PPA e adiciona mais
                    // O resultado é o tempo de vigência do PPA
                    $inQtdAnos = ($arParam['stAnoFinal'] - $arParam['stAnoInicio']) + 1;

                    $this->obRLDO->obRPPAManterPPA->inCodPPA = $inCodPPA;
                    $this->obRLDO->stTimestamp = $stTimestamp;
                    $this->obRLDO->listar($rsLDO, $boTransacao);
                    if ($rsLDO->getNumLinhas() < 1) {
                        for ($inCount = 1; $inCount <= $inQtdAnos; $inCount++) {
                            $this->obRLDO->inAno = $inCount;
                            $boLDO = true;
                            $obErro = $this->obRLDO->incluir($boTransacao);
                        }
                    }
                }
            }

            # Termina transação erro $obTPPAPPAEncaminhamento
            if ($obErro->ocorreu()) {
                SistemaLegado::LiberaFrames(true,false);

                return sistemaLegado::exibeAviso("Houve erro ao incluir o encaminhamento deste PPA (".$inCodPPA.")","erro","erro");
            }
        }

        # Termina transação
        SistemaLegado::LiberaFrames(true,false);
        if ($boLDO) {
            $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $obTLDO);
        } else {
            $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $obTPPAPPAEncaminhamento);
        }

        return sistemaLegado::alertaAviso($this->pgForm , $inCodPPA ,"incluir","aviso", Sessao::getId(), "../");
    }

    /**
     * Após homologar o PPA verificar se há diferença de valores entre receita e
     * despesa. No momento da homologação o sistema deve fazer a consistência
     * entre os valores de receita e despesa,
     * informando se há diferenças entre os valores totais de Receita e Despesa.
     *
     * @param  int  $inCodPPA
     * @return bool
     */
    public function calcularDiferencaReceitaDespesa($inCodPPA)
    {
        $obTPPAReceita = new TPPAReceita();
        $obTPPAReceita->recuperaValoresReceitaDespesa($rsRecordSet, $inCodPPA);
        if (count($rsRecordSet->arElementos) > 0) {
            $stTotalDespesa = $rsRecordSet->arElementos[0]['total_despesa'];
            $stTotalReceita = $rsRecordSet->arElementos[0]['total_receita'];
            $flSaldoReceita = $stTotalReceita - $stTotalDespesa;
            if ($flSaldoReceita > 0) {
                return true;
            } else {
                return false;
            }
        }
    }

}
?>
