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
    * Classe de Visão de Relatório de Regiões
    * Data de Criação: 13/10/2008

    * @author Analista: Heleno Menezes dos Santos
    * @author Desenvolvedor: Janilson Mendes P. da Silva

    * @package URBEM
    * @subpackage

    * Casos de uso: UC-02.09.07
*/
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
//require_once("../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php");

class VPPARelatorioPrograma
{

    private $obController;
    private $arMap;

    public function __construct($obNegocio)
    {
        $this->obController = $obNegocio;
        $this->arMap[0] = "TPPAProgramaOrgaoResponsavel";
        $this->arMap[1] = "TPPAProgramaResponsavel";
        $this->arMap[2] = "TPPAPrograma";
    }

    private function showSQLNomOrgao()
    {
        $stMap = $this->arMap[0];
        $stMetodo = "montaRecuperaNomOrgao";

        return $this->obController->showSQL($stMap, $stMetodo);
    }

    private function showSQLNomCgm()
    {
        $stMap = $this->arMap[1];
        $stMetodo = "montaRecuperaNomCgm";

        return $this->obController->showSQL($stMap, $stMetodo);
    }

    public function pesquisaNomOrgao($stParam = "",$stOrdem = "")
    {
        $stMap = $this->arMap[0];
        $stMetodo = "recuperaNomOrgao";
        $boCriterio = false;

        return $this->obController->pesquisar($stMap, $stMetodo, $stParam, $stOrdem, $boCriterio);
    }

    public function pesquisaNomCgm($stParam = "",$stOrdem = "")
    {
        $stMap = $this->arMap[1];
        $stMetodo = "recuperaNomCgm";
        $boCriterio = false;

        return $this->obController->pesquisar($stMap, $stMetodo, $stParam, $stOrdem, $boCriterio);
    }

    public function pesquisaPrograma($stParam = "",$stOrdem = "")
    {
        $stMap = $this->arMap[2];
        $stMetodo = "recuperaPrograma";
        $boCriterio = false;

        return $this->obController->pesquisar($stMap, $stMetodo, $stParam, $stOrdem, $boCriterio);
    }

    ### Todos os Filtros para SQL  ###
    public function filtrosRelatorioPrograma($arParam)
    {
        if ($arParam['inCodPPA']) {
            $stFiltro[] = " p.cod_ppa = " .$arParam['inCodPPA']. "\n";
        }

        if ($arParam['inCodOrgao']) {
            $stFiltro[] = " por.num_orgao = " .$arParam['inCodOrgao']. "\n";
        }

        if ($arParam['inCGM']) {
            $stFiltro[] = " cgm.numcgm = " .$arParam['inCGM']. "\n";
        }

        if ($arParam['inCodPrograma']) {
            $stFiltro[] = " p.cod_programa = " .$arParam['inCodPrograma']. "\n";
        }

        if ($arParam['boNomOrgao']) {
            $arFiltro['inCodPPA'] = $arParam['inCodPPA'];
            $arFiltro['inCodOrgao'] = $arParam['inCodOrgao'];
            $stFiltroOrgao = $this->filtrosRelatorioPrograma($arFiltro);
            $rsNomOrgao = $this->pesquisaNomOrgao($stFiltroOrgao);
            $stFiltro[] = " ao.nom_orgao ILIKE '%" .$rsNomOrgao->arElementos[0]['nom_orgao']. "%'\n";
        }

        if ($arParam['boNomCgm']) {
            $arFiltro['inCodPPA'] = $arParam['inCodPPA'];
            $arFiltro['inCGM'] = $arParam['inCGM'];
            $stFiltroCgm = $this->filtrosRelatorioPrograma($arFiltro);
            $rsNomCgm = $this->pesquisaNomCgm($stFiltroCgm);
            $stFiltro[] = " cgm.nom_cgm ILIKE '%" .$rsNomCgm->arElementos[0]['nom_cgm']. "%'\n";
        }

        switch ($arParam['stNatureza']) {
            case 0:
                $boNatureza = 1;
                $boNaturezaFiltro = 1;
                $stFiltro[] = " pd.continuo = true \n";
            break;

            case 1:
                $boNatureza = 0;
                $boNaturezaFiltro = 0;
                $stFiltro[] = " pd.continuo = false \n";
            break;

            case 2:
                $boNatureza = 1;
                $boNaturezaFiltro = 0;
                $stFiltro[] = " pd.continuo IN (true, false) \n";
            break;

            default:

            break;
        }

        if ($arParam['inNumProgramaIni']) {
            if (intval($arParam['inNumProgramaIni']) == 0) {
                $stFiltro[] = " p.cod_programa >= 0 \n";
            } else {
                $stFiltro[] = " p.cod_programa >= " . $arParam['inNumProgramaIni'] . " \n";
            }
        }

        if ($arParam['inNumProgramaFim']) {
            if (intval($arParam['inNumProgramaFim']) == 0) {
                $stFiltro[] = " p.cod_programa <= 9999 \n";
            } else {
                $stFiltro[] = " p.cod_programa <= " . $arParam['inNumProgramaFim'] . " \n";
            }
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
     * Recebe os dados postados pelo filtro e monta os parâmetros para
     * o método gerarRelatorioPrograma.
     *
     * @param array $arParam
     */
    public function encaminhaRelatorioPrograma($arParam)
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

        // Filtro por servidor responsável
        if (empty($arParam['inContrato'])) {
            $inNumCGMServidor = '';
        } else {
            $arDadosServidor = explode('-', $arParam['hdnCGM']);
            $inNumCGMServidor = trim($arDadosServidor[0]);
        }

        $pgProg = "PRRelatorioPrograma.php?stAcao=gerarRelatorioPrograma";
        $pgProg.= "&inCodPPA=" . $inCodPPA;
        $pgProg.= "&inNumProgramaIni=" . $arParam['inNumProgramaIni'];
        $pgProg.= "&inNumProgramaFim=" . $arParam['inNumProgramaFim'];
        $pgProg.= "&boAcao=" . $arParam['boAcao'];
        $pgProg.= "&stNatureza=" . $arParam['stNatureza'];
        $pgProg.= "&inCodOrgao=" . $arParam['inCodOrgao'];
        $pgProg.= "&inCGM=" . $arParam['inCGM'];
        $pgProg.= "&inContrato=" . $arParam['inContrato'];
        $pgProg.= "&stAssinatura=" . $arParam['stAssinatura'];
        $pgProg.= "&inCodPPAVigente=" . $inCodPPAVigente;
        $pgProg.= "&stIncluirAssinaturas=" . $arParam['stIncluirAssinaturas'];
        $pgProg.= "&inNumCGMServidor=" . $inNumCGMServidor;
        $return = sistemaLegado::alertaAviso( $pgProg , '' , "incluir", "aviso", Sessao::getId(), "../");

        return $return;
    }

    /**
     * Recebe os parâmetros  montados pelo método
     * encaminhaRelatorioPrograma e monta o preview do Birt
     *
     * @param array $arParam
     */
    public function gerarRelatorioPrograma($arParam)
    {
        ### Filtros para Listar os Programas para montar os Data Set's para o Birt ###
        if (intval($arParam['inNumProgramaIni']) == 0) {
            $inNumProgramaIni = 0;
        } else {
            $inNumProgramaIni = (int) $arParam['inNumProgramaIni'];
        }

        if (intval($arParam['inNumProgramaFim']) == 0) {
            $arFiltro['inCodPPA'] = $arParam['inCodPPA'];
            $arFiltro['stNatureza'] = 3;
            $stFiltroPrograma = $this->filtrosRelatorioPrograma($arFiltro);
            $rsPrograma = $this->pesquisaPrograma($stFiltroPrograma, ' ORDER BY p.num_programa desc ');
            $inNumProgramaFim = (int) $rsPrograma->arElementos[0]['num_programa'];
            unset($arFiltro);
            $boProgramaFiltro = "0";
        } else {
            $inNumProgramaFim = (int) $arParam['inNumProgramaFim'];
            $boProgramaFiltro = "1";
        }

        if ($inNumProgramaFim < $inNumProgramaIni) {
            $numFim = $inNumProgramaIni;
            $inNumProgramaIni = $inNumProgramaFim;
            $inNumProgramaFim = $numFim;
        }

        switch ($arParam['stNatureza']) {
            case 0:
                $boNatureza = 1;
                $boNaturezaFiltro = 1;
                $stNatureza = 1;
            break;

            case 1:
                $boNatureza = 0;
                $boNaturezaFiltro = 0;
                $stNatureza = 1;
            break;

            case 2:
                $boNatureza = 1;
                $boNaturezaFiltro = 0;
                $stNatureza = "";
            break;
        }

        ### Montando Lista dos Orgãos Responsáveis ###
        if (intval($arParam['inCodOrgao']) > 0) {
            $inCodOrgao = $arParam['inCodOrgao'];
            $stCodOrgao = "s";
        } else {
            $inCodOrgao = null;
            $stCodOrgao = "n";
        }

        ### Montando Lista dos Servidores Responsáveis ###
        if (intval($arParam['inNumCGMServidor']) > 0) {
            $inNumCGMServidor = $arParam['inNumCGMServidor'];
            $stNumCGM = "s";
        } else {
            $inNumCGMServidor = null;
            $stNumCGM = "n";
        }

        $obPreview = new PreviewBirt(2, 43, 2);
        $obPreview->setTitulo('Relatório do Birt');
        $obPreview->setVersaoBirt('2.5.0');
        //$obPreview->setExportaExcel(true);
        $obPreview->addParametro("cod_ppa", $arParam['inCodPPA']);
        $obPreview->addParametro("cod_programa_ini", $inNumProgramaIni);
        $obPreview->addParametro("cod_programa_fim", $inNumProgramaFim);
        $obPreview->addParametro("bo_acao", $arParam['boAcao']);
        $obPreview->addParametro("st_natureza", $boNatureza);
        $obPreview->addParametro("st_natureza_filtro", $boNaturezaFiltro);
        $obPreview->addParametro("bo_natureza", $stNatureza);
        $obPreview->addParametro("bo_assinatura", $arParam['stAssinatura']);
        $obPreview->addParametro("cod_contrato", $arParam['inContrato']);
        $obPreview->addParametro("cod_ppa_vigente", $arParam['inCodPPAVigente']);
        $obPreview->addParametro("bo_programa_filtro", $boProgramaFiltro);
        $obPreview->addParametro("numcgm", $inNumCGMServidor);
        $obPreview->addParametro("st_numcgm", $stNumCGM);
        $obPreview->addParametro("cod_orgao", $inCodOrgao);
        $obPreview->addParametro("st_cod_orgao", $stCodOrgao);

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
