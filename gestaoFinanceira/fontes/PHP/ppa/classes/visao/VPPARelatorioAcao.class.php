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
    * Classe de Visão de Relatório de Ação
    * Data de Criação: 19/11/2008

    * @author Analista: Heleno Menezes dos Santos
    * @author Desenvolvedor: Janilson Mendes P. da Silva

    * @package URBEM
    * @subpackage

    * Casos de uso: UC-02.09.09
*/
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
//require_once("../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php");

final class VPPARelatorioAcao
{
    /**
        * Atributo que recebe o objeto da Regra de Negócio
        * @name $obController
    */
    private $obController;

    /**
        * Atributo que recebe um Array contendo os Mapeamentos.
        * @name $arMap
    */
    private $arMap;

    /**
        * Método Construtor da Classe
        * @param object $obNegocio
        * @return void
    */
    public function __construct($obNegocio)
    {
        $this->obController = $obNegocio;
        $this->arMap[0] = "TPPAAcaoRecurso";
        $this->arMap[1] = "TPPAProgramaResponsavel";
        $this->arMap[2] = "TPPAPrograma";
        $this->arMap[3] = "TPPARegiao";
        $this->arMap[4] = "TPPAAcao";
        $this->arMap[5] = "TPessoalContrato";
    }

    /**
        * Método que Retorno de SQL de CGM's
        * @param String $stParam
        * @param String $stOrdem
        * @return object
    */
    private function showSQLNomCgm()
    {
        $stMap = $this->arMap[1];
        $stMetodo = "montaRecuperaNomCgm";

        return $this->obController->showSQL($stMap, $stMetodo);
    }

    /**
        * Método que pesquisa Recursos
        * @param String $stParam
        * @param String $stOrdem
        * @return object
    */
    public function pesquisaRecurso($stParam = "",$stOrdem = "")
    {
        $stMap = $this->arMap[0];
        $stMetodo = "recuperaDados";
        $boCriterio = true;

        return $this->obController->pesquisar($stMap, $stMetodo, $stParam, $stOrdem, $boCriterio);
    }

    /**
        * Método que pesquisa CGM's
        * @param String $stParam
        * @param String $stOrdem
        * @return object
    */
    public function pesquisaNomCgm($stParam = "",$stOrdem = "")
    {
        $stMap = $this->arMap[5];
        $stMetodo = "recuperaCgmDoRegistro";
        $boCriterio = true;

        return $this->obController->pesquisar($stMap, $stMetodo, $stParam, $stOrdem, $boCriterio);
    }

    /**
        * Método que pesquisa Programas
        * @param String $stParam
        * @param String $stOrdem
        * @return object
    */
    public function pesquisaPrograma($stParam = "",$stOrdem = "")
    {
        $stMap = $this->arMap[2];
        $stMetodo = "recuperaPrograma";
        $boCriterio = false;

        return $this->obController->pesquisar($stMap, $stMetodo, $stParam, $stOrdem, $boCriterio);
    }

    /**
        * Método que pesquisa Regiões
        * @param String $stParam
        * @param String $stOrdem
        * @return object
    */
    public function pesquisaRegiao($stParam = "",$stOrdem = "")
    {
        $stMap = $this->arMap[3];
        $stMetodo = "recuperaRegioes";
        $boCriterio = false;

        return $this->obController->pesquisar($stMap, $stMetodo, $stParam, $stOrdem, $boCriterio);
    }

    /**
        * Método que pesquisa Ações
        * @param String $stParam
        * @param String $stOrdem
        * @return object
    */
    public function pesquisaAcao($stParam = "",$stOrdem = "")
    {
        $stMap = $this->arMap[4];
        $stMetodo = "recuperaDados";
        $boCriterio = true;

        return $this->obController->pesquisar($stMap, $stMetodo, $stParam, $stOrdem, $boCriterio);
    }

    /**
        * Método que monta Todos os Filtros para SQL
        * @param array $arParam
        * @return String
    */
    public function filtrosRelatorioAcao($arParam)
    {
        if ($arParam['inCodPPA']) {
            $stFiltro[] = " p.cod_ppa = " .$arParam['inCodPPA']. "\n";
        }

        if ($arParam['inCodPPAPrograma']) {
            $stFiltro[] = " programa.cod_ppa = " .$arParam['inCodPPAPrograma']. "\n";
        }

        if ($arParam['inCGM']) {
            $stFiltro[] = " cgm.numcgm = " .$arParam['inCGM']. "\n";
        }

        if ($arParam['inCodPrograma']) {
            $stFiltro[] = " p.cod_programa = " .$arParam['inCodPrograma']. "\n";
        }

        if ($arParam['inCodRegiao']) {
            $stFiltro[] = " regiao.cod_regiao = " .$arParam['inCodRegiao']. "\n";
        }

        if ($arParam['inCodRecurso']) {
            $stFiltro[] = " acao_recurso.cod_recurso = " .$arParam['inCodRecurso']. "\n";
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

    /**
        * Método que executa o Relatório na Tela Principal
        * @param array $arParam
        * @return void
    */
    public function encaminhaRelatorioAcao($arParam)
    {
        if ($arParam['boAssinaturas'] == 's') {
            $arSessao = Sessao::read("assinaturas");

            $inCount = count($arSessao["selecionadas"]);

            if ($inCount <= 0) {
                return SistemaLegado::exibeAviso('Selecione até três Assinaturas!()', 'form', 'erro');
            } elseif ($inCount > 3) {
                return SistemaLegado::exibeAviso('Selecione apenas três Assinaturas!()', 'form', 'erro');
            }
        }

        if ($arParam['inCodPPA'] != "") {
            $inCodPPA = $arParam['inCodPPA'];
            $inCodPPAVigente = "0";
        } else {
            $inCodPPA = $arParam['inCodPPAVigente'];
            $inCodPPAVigente = "1";
        }

        $arHidden = explode('-', $arParam['hdnCGM']);

        $pgProg = "PRRelatorioAcao.php?stAcao=gerarRelatorioAcao";
        $pgProg.= "&inCodPPA=" . $inCodPPA;
        $pgProg.= "&inNumProgramaIni=" . $arParam['inNumProgramaIni'];
        $pgProg.= "&inNumProgramaFim=" . $arParam['inNumProgramaFim'];
        $pgProg.= "&inCodAcaoIni=" . $arParam['inCodAcaoIni'];
        $pgProg.= "&inCodAcaoFim=" . $arParam['inCodAcaoFim'];
        $pgProg.= "&stUnidadeOrcamentaria=" . $arParam['stUnidadeOrcamentaria'];
        $pgProg.= "&inCGM=" . $arHidden[0];
        $pgProg.= "&inCodRegiao=" . $arParam['inCodRegiao'];
        $pgProg.= "&inCodRecurso=" . $arParam['inCodRecurso'];
        $pgProg.= "&inOrdem=" . $arParam['inOrdem'];
        $pgProg.= "&stAssinatura=" . $arParam['stAssinatura'];
        $pgProg.= "&inCodPPAVigente=" . $inCodPPAVigente;
        $pgProg.= "&stIncluirAssinaturas=" . $arParam['stIncluirAssinaturas'];

        $return = sistemaLegado::alertaAviso( $pgProg , '' , "incluir", "aviso", Sessao::getId(), "../");

        return $return;
    }

    public function gerarRelatorioAcao($arParam)
    {
        $boProgramaFiltro = "1";
        $boAcaoFiltro = "1";

        ### Filtros para Listar os Programas para montar os Data Set's para o Birt ###
        if (empty($arParam['inCGM'])) {
            $arParam['boNomCgm'] = false;
        } else {
            $arParam['boNomCgm'] = true;
        }

        if (intval($arParam['inNumProgramaIni']) == 0) {
            $inNumProgramaIni = 0;
        } else {
            $inNumProgramaIni = (int) $arParam['inNumProgramaIni'];
        }

        if (intval($arParam['inNumProgramaFim']) == 0) {
            $arFiltro['inCodPPA'] = $arParam['inCodPPA'];
            $stFiltroPrograma = $this->filtrosRelatorioAcao($arFiltro);
            $rsPrograma = $this->pesquisaPrograma($stFiltroPrograma, ' ORDER BY p.cod_programa desc ');
            $inNumProgramaFim = (int) $rsPrograma->arElementos[0]['num_programa'];
            unset($arFiltro);
            $boProgramaFiltro = "0";
        } else {
            $inNumProgramaFim = (int) $arParam['inNumProgramaFim'];
        }

        if (intval($arParam['inCodAcaoIni']) == 0) {
            $inCodAcaoIni = 0;
        } else {
            $inCodAcaoIni = (int) $arParam['inCodAcaoIni'];
        }

        if (intval($arParam['inCodAcaoFim']) == 0) {
            $arFiltro['inCodPPAPrograma'] = $arParam['inCodPPA'];
            $stFiltroAcao = $this->filtrosRelatorioAcao($arFiltro);
            $rsAcao = $this->pesquisaAcao($stFiltroAcao, 'acao.cod_acao desc');
            $inCodAcaoFim = (int) $rsAcao->arElementos[0]['cod_acao'];
            unset($arFiltro);
            $boAcaoFiltro = "0";
        } else {
            $inCodAcaoFim = (int) $arParam['inCodAcaoFim'];
        }

        ### Montando Lista dos Servidores Responsáveis ###
        if (intval($arParam['inCGM']) > 0) {
            $inNumCGM = $arParam['inCGM'];
            $stNumCGM = "s";
        } else {
            $inNumCGM = null;
            $stNumCGM = "n";
        }

        ### Montando Lista das Regiões de Abrangência ###
        if (intval($arParam['inCodRegiao']) > 0) {
            $inCodRegiao = $arParam['inCodRegiao'];
            $stCodRegiao = "s";
        } else {
            $inCodRegiao = null;
            $stCodRegiao = "n";
        }

        ### Montando Lista dos Recursos ###
        if (intval($arParam['inCodRecurso']) > 0) {
            $inCodRecurso = $arParam['inCodRecurso'];
            $stCodRecurso = "s";
        } else {
            $inCodRecurso = null;
            $stCodRecurso = "n";
        }

        switch ($arParam['inOrdem']) {
            case 1:
                $stOrdem = 'acao';
            break;

            case 2:
                $stOrdem = 'funcao';
            break;

            case 3:
                $stOrdem = 'regiao';
            break;

            default:
                $stOrdem = 'acao';
            break;
        }

        if (empty($arParam['stUnidadeOrcamentaria'])) {
            $numOrgaoIni = 0;
            $numOrgaoFim = 9999;
            $numUnidadeIni = 0;
            $numUnidadeFim = 9999;

        } else {
            $arOrgaoIni = explode('.',$arParam['stUnidadeOrcamentaria']);
            $numOrgaoIni = (int) $arOrgaoIni[0];
            $numOrgaoFim = (int) $arOrgaoIni[0];
            $numUnidadeIni = (int) $arOrgaoIni[1];
            $numUnidadeFim = (int) $arOrgaoIni[1];
        }
        $inAnoExercicio = Sessao::getExercicio();

        $obPreview = new PreviewBirt(2, 43, 4);
        $obPreview->setTitulo('Relatório do Birt');
        $obPreview->setVersaoBirt('2.5.0');
        //$obPreview->setExportaExcel(true);
        $obPreview->addParametro("cod_ppa", $arParam['inCodPPA']);
        $obPreview->addParametro("cod_programa_ini", $inNumProgramaIni);
        $obPreview->addParametro("cod_programa_fim", $inNumProgramaFim);
        $obPreview->addParametro("cod_acao_ini", $inCodAcaoIni);
        $obPreview->addParametro("cod_acao_fim", $inCodAcaoFim);
        $obPreview->addParametro("num_cgm_servidor", $inNumCGM);
        $obPreview->addParametro("cod_regiao", $inCodRegiao);
        $obPreview->addParametro("cod_recurso", $inCodRecurso);
        $obPreview->addParametro("st_ordem", $stOrdem);
        $obPreview->addParametro("bo_programa_filtro", $boProgramaFiltro);
        $obPreview->addParametro("bo_acao_filtro", $boAcaoFiltro);
        $obPreview->addParametro("num_orgao_ini", $numOrgaoIni);
        $obPreview->addParametro("num_orgao_fim", $numOrgaoFim);
        $obPreview->addParametro("num_unidade_ini", $numUnidadeIni);
        $obPreview->addParametro("num_unidade_fim", $numUnidadeFim);
        $obPreview->addParametro("exercicio_unidade", $inAnoExercicio);
        $obPreview->addParametro("st_num_cgm", $stNumCGM);
        $obPreview->addParametro("st_cod_regiao", $stCodRegiao);
        $obPreview->addParametro("st_cod_recurso", $stCodRecurso);

        ### Assinaturas ###
        $arAssinaturaSelecionada = array();

        if ($arParam['stIncluirAssinaturas'] == "sim") {

            $arAssinatura = Sessao::read('assinaturas');

            if (is_array($arAssinatura) && isset($arAssinatura['selecionadas']) && count($arAssinatura['selecionadas']) > 0) {

                $arAssinaturaSelecionada = $arAssinatura['selecionadas'];
                for ($x = 0; $x < count($arAssinaturaSelecionada); $x++) {
                    $stParametroCgm = "assinatura_" . ($x + 1);
                    $stParametroCargo = "cargo_" . ($x + 1);
                    $obPreview->addParametro($stParametroCgm, $arAssinaturaSelecionada[$x]['stNomCGM']);
                    $obPreview->addParametro($stParametroCargo, $arAssinaturaSelecionada[$x]['stCargo']);
                }
            }
        }

        $arParametros = $obPreview->arParametros;

        if (!isset($arParametros['assinatura_1'])) {
            $obPreview->addParametro('assinatura_1', '');
            $obPreview->addParametro('cargo_1', '');
        }
        if (!isset($arParametros['assinatura_2'])) {
            $obPreview->addParametro('assinatura_2', '');
            $obPreview->addParametro('cargo_2', '');
        }
        if (!isset($arParametros['assinatura_3'])) {
            $obPreview->addParametro('assinatura_3', '');
            $obPreview->addParametro('cargo_3', '');
        }

        return $obPreview->preview();
    }
}

?>
