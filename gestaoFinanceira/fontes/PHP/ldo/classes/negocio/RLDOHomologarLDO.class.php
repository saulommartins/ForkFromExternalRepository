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
    * Classe de Regra de Negócio de Homologar LDO
    * Data de Criação: 27/07/2009

    * @author Analista: Tonismar Bernardo
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage
*/
require_once CAM_GF_PPA_MAPEAMENTO . 'TPPAPPAEncaminhamento.class.php';
require_once CAM_GF_LDO_MAPEAMENTO . 'TLDOHomologacao.class.php';
//require_once CAM_GF_PPA_MAPEAMENTO . 'TPPAPPANorma.class.php';
//require_once CAM_GF_PPA_MAPEAMENTO . 'TPPAPPAPublicacao.class.php';
require_once CAM_GF_PPA_MAPEAMENTO . 'TPPAPeriodicidade.class.php';
require_once CAM_GF_PPA_MAPEAMENTO . 'TPPA.class.php';
require_once CAM_GF_PPA_MAPEAMENTO . 'TPPAReceita.class.php';
require_once CAM_GF_LDO_MAPEAMENTO . 'TLDOAcaoValidada.class.php';
require_once CAM_GF_ORC_MAPEAMENTO . 'TOrcamentoEntidade.class.php';
require_once CAM_GF_LDO_MAPEAMENTO . 'TLDO.class.php';
require_once CAM_GF_LDO_NEGOCIO.'RLDOLDO.class.php';

class RLDOHomologarLDO
{

    private $arMapeamentos;
    private $pgForm;
    public $obRLDO;

    public function __construct()
    {
        $this->pgForm = "FMHomologarLDO.php";
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

        $rsPPA = $this->obRLDO->obRPPAManterPPA->pesquisa('TPPA', 'recuperaTodos', ' cod_ppa = ' . $arParam['inCodPPA'], '', $boTransacao);

        $obTransacao = new Transacao();
        $boFlagTransacao = false;
        $boTransacao = "";

        # Inicia nova transação
        $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        $this->obRLDO->getTimestamp($boTransacao);

        $stTimestamp = $this->obRLDO->stTimestamp;

        $obTLDOHomologacao = new TLDOHomologacao();
        $obTLDOHomologacao->setDado('cod_ppa',$arParam['inCodPPA']);
        $obTLDOHomologacao->setDado('ano',$arParam['inAnoLDO']);
        $obTLDOHomologacao->setDado('timestamp',$stTimestamp);
        $obTLDOHomologacao->setDado('cod_norma',$arParam['inCodNorma']);
        $obTLDOHomologacao->setDado('numcgm_veiculo',$arParam['inCodigoEmpresa']);
        $obTLDOHomologacao->setDado('cod_periodicidade',$arParam['inPeriodicidadeApuracaoMetas']);
        $obTLDOHomologacao->setDado('dt_encaminhamento',$arParam['dtDataLegislativo']);
        $obTLDOHomologacao->setDado('dt_devolucao',$arParam['dtDataDevolucaoExecutivo']);
        $obTLDOHomologacao->setDado('nro_protocolo',$arParam['inProtocolo']);

        $obErro = $obTLDOHomologacao->inclusao($boTransacao);

        # Termina transação
        SistemaLegado::LiberaFrames(true,false);
        $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $obTPPAPPAEncaminhamento);

        return sistemaLegado::alertaAviso($this->pgForm , 'LDO - ' . ($rsPPA->getCampo('ano_inicio') - 1 + $arParam['inAnoLDO']) ,"incluir","aviso", Sessao::getId(), "../");
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

    /**
     * Método para listar os ldos
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     *
     * @return void
     */
    public function listLDO(&$rsLDO, $boTransacao = '')
    {
        $obTLDOAcaoValidada = new TLDOAcaoValidada();
        $obTLDOAcaoValidada->setDado('cod_ppa',$this->obRLDO->obRPPAManterPPA->inCodPPA);
        $obErro = $obTLDOAcaoValidada->listAcaoLDO($rsLDO,'','',$boTransacao);

        return $obErro;
    }

}
?>
