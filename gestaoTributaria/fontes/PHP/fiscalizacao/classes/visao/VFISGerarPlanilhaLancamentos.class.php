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
    * Classe de Visao do Gerar Planilha de Lancamentos
    * Data de Criação   : 12/08/2008

    * @author Analista      : Heleno Menezes dos Santos
    * @author Desenvolvedor : Janilson Mendes P. da Silva

    * @package URBEM
    * @subpackage Visao

    $Id:$
*/
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/administracao/classes/componentes/ITextBoxSelectDocumento.class.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
require_once ( CAM_GT_FIS_COMPONENTES."IFISTextBoxSelectDocumento.class.php" );
require_once ( CAM_GT_CIM_COMPONENTES."IPopUpImovel.class.php" );
require_once ( CAM_GT_CEM_COMPONENTES."IPopUpEmpresa.class.php" );
require_once ( CAM_GT_FIS_VISAO."VFISIniciarProcessoFiscal.class.php" );
require_once ( CAM_GT_FIS_MAPEAMENTO.'TFISLevantamento.class.php' );
require_once ( CAM_GT_FIS_MAPEAMENTO.'TFISProcessoFiscal.class.php' );
include_once ( CAM_GT_ARR_NEGOCIO."RARRConfiguracao.class.php" );

final class VFISGerarPlanilhaLancamentos
{
    private $controller;
    private $visaoProcessoFiscal;
    private $visaoIniciarProcessoFiscal;
    private $boLevantamento;

    public function __construct($controller)
    {
        $this->controller = $controller;
        $this->visaoProcessoFiscal = new VFISProcessoFiscal($this->controller);
        $this->visaoIniciarProcessoFiscal = new VFISIniciarProcessoFiscal($this->controller);
    }

    public function montaForm($param)
    {
        return $this->visaoIniciarProcessoFiscal->montaForm($param);
    }

    public function recuperarEnderecoPlanilhaLancamentos($stFiltro)
    {
        if ($stFiltro) {
            $this->controller->setCriterio($stFiltro);
        }

        return $this->controller->getRecuperaEnderecoProcessoFiscalLevantamentos();
    }

    public function recuperarCompetencias($stFiltro)
    {
        if ($stFiltro) {
            $this->controller->setCriterio($stFiltro);
        }

        return $this->controller->getRecuperarCompetencias();
    }

    public function recuperarIisCompetencias($stFiltro)
    {
        if ($stFiltro) {
            $this->controller->setCriterio($stFiltro);
        }

        return $this->controller->getRecuperarIisCompetencias();
    }

    public function recuperarCompetenciasRecDeclarada($stFiltro)
    {
        if ($stFiltro) {
            $this->controller->setCriterio($stFiltro);
        }

        return $this->controller->getRecuperarCompetenciasRecDeclarada();
    }

    public function recuperarCompetenciasProcesso($stFiltro)
    {
        if ($stFiltro) {
            $this->controller->setCriterio($stFiltro);
        }

        return $this->controller->getRecuperarCompetenciasProcesso();
    }

    public function recuperarCompetenciasProcessoCalculo($stFiltro)
    {
        if ($stFiltro) {
            $this->controller->setCriterio($stFiltro);
        }

        return $this->controller->getRecuperarCompetenciasProcessoCalculo();
    }

    public function recuperaTodosCodProcessoLevantamentos($stFiltro)
    {
        if ($stFiltro) {
            $this->controller->setCriterio($stFiltro);
        }
        $rsRecordSet = $this->controller->getRecuperaCodProcessoLevantamentos();
        $inCount = count($rsRecordSet->arElementos);

        for ($i = 0; $i < $inCount; $i++) {
            if ($rsRecordSet->arElementos[$i]["nom_cgm"] != "") {
                $rsRecordSet->arElementos[$i]["processo_nome"] = $rsRecordSet->arElementos[$i]["nom_cgm"];
            } else {
                $rsRecordSet->arElementos[$i]["processo_nome"] = $rsRecordSet->arElementos[$i]["cod_processo"]." - Sem Registro";
            }
        }

        return $rsRecordSet;
    }

    private function recuperaLevantamentos($stFiltro)
    {
        if ($stFiltro) {
            $this->controller->setCriterio($stFiltro);
        }

        return $this->controller->getRecuperaProcessoFiscalTodosLevantamentos();
    }

    private function recuperaTotalLevantamentos($stFiltro)
    {
        if ($stFiltro) {
            $this->controller->setCriterio($stFiltro);
        }

        return $this->controller->getRecuperaProcessoFiscalTotalTodosLevantamentos();
    }

    private function recuperaArrecadacao($stFiltro)
    {
        if ($stFiltro) {
            $this->controller->setCriterio($stFiltro);
        }

        return $this->controller->getRecuperaProcessoFiscalTodosArrecadacao();
    }

    private function recuperaTotalArrecadacao($stFiltro)
    {
        if ($stFiltro) {
            $this->controller->setCriterio($stFiltro);
        }

        return $this->controller->getRecuperaProcessoFiscalTotalTodosArrecadacao();
    }

    private function recuperaTipoFuncao($stFiltro)
    {
        if ($stFiltro) {
            $this->controller->setCriterio($stFiltro);
        }

        return $this->controller->getRecuperaFuncao();
    }

    private function recuperaTipoSelectFuncao($stFiltro)
    {
        if ($stFiltro) {
            $this->controller->setCriterio($stFiltro);
        }

        return $this->controller->getRecuperaSelectFuncao();
    }

    private function recuperaTipoConfiguracao($stFiltro)
    {
        if ($stFiltro) {
            $this->controller->setCriterio($stFiltro);
        }

        return $this->controller->getRecuperaConfiguracao();
    }

    private function recuperaTipoVencimentosParcela($stFiltro)
    {
        if ($stFiltro) {
            $this->controller->setCriterio($stFiltro);
        }

        return $this->controller->getRecuperaVencimentosParcela();
    }

    private function recuperaTipoValorIndicador($stFiltro)
    {
        if ($stFiltro) {
            $this->controller->setCriterio($stFiltro);
        }

        return $this->controller->getRecuperaValorIndicador();
    }

    private function recuperaTipoIndice($stFiltro)
    {
        if ($stFiltro) {
            $this->controller->setCriterio($stFiltro);
        }

        return $this->controller->getRecuperaIndice();
    }

    private function recuperaIndicadorEconomico($stFiltro)
    {
        if ($stFiltro) {
            $this->controller->setCriterio($stFiltro);
        }

        return $this->controller->getRecuperaIndicadorEconomico();
    }

    private function recuperarGrupo($stFiltro)
    {
        if ($stFiltro) {
            $this->controller->setCriterio($stFiltro);
        }

        return $this->controller->recuperarProcessoFiscalGrupo();
    }

    private function criaHidden($stName, $stValue)
    {
        //Hidden
        $obHdn = new Hidden();
        $obHdn->setName($stName);
        $obHdn->setValue($stValue);
        $obHdn->montaHtml();

        return $obHdn->getHTML() . "\n";
    }

    public function montaPlanilhaLancamentos($arParam)
    {
        $arNew = explode('-', $arParam['inCodProcessoInscricao']);
        $arParam['inCodProcesso'] = $arNew[0];

        $stFiltro1 = $this->filtrosPlanilhas($arParam);

        $arFiltro['inCodProcesso'] = $arParam['inCodProcesso'];
        $stFiltro2 = $this->filtrosPlanilhas($arFiltro);

        $arFiltro['inInscricao'] = $arNew[1];
        $stFiltro3 = $this->filtrosPlanilhas($arFiltro);

        unset($arFiltro);

        $rsRecordSetLevantamentos = $this->recuperaLevantamentos($stFiltro1);
        $rsRecordSetArrecadacao = $this->recuperaArrecadacao($stFiltro1);

        $rsRecordSetTotalLevantamentos = $this->recuperaTotalLevantamentos($stFiltro2);
        $rsRecordSetTotalArrecadacao = $this->recuperaTotalArrecadacao($stFiltro3);

        if ($rsRecordSetTotalLevantamentos->arElementos[0]['total_issqn_devido'] != "") {
            $stTotalISSQNDevido = sprintf("%01.2f", $rsRecordSetTotalLevantamentos->arElementos[0]['total_issqn_devido']);
        } else {
            $stTotalISSQNDevido = '0.00';
        }

        if ($rsRecordSetTotalLevantamentos->arElementos[0]['total_receita_efetivo'] != "") {
            $stTotalReceitaEfetivo = sprintf("%01.2f", $rsRecordSetTotalLevantamentos->arElementos[0]['total_receita_efetivo']);
        } else {
            $stTotalReceitaEfetivo = '0.00';
        }

        if ($rsRecordSetTotalArrecadacao->arElementos[0]['total_issqn_pago'] != "") {
            $stTotalISSQNPago = sprintf("%01.2f", $rsRecordSetTotalArrecadacao->arElementos[0]['total_issqn_pago']);
        } else {
            $stTotalISSQNPago = '0.00';
        }

        if ($rsRecordSetTotalArrecadacao->arElementos[0]['total_receita_declarado'] != "") {
            $stTotalReceitaDeclarado = sprintf("%01.2f", $rsRecordSetTotalArrecadacao->arElementos[0]['total_receita_declarado']);
        } else {
            $stTotalReceitaDeclarado = '0.00';
        }

        $inCount1 = count($rsRecordSetLevantamentos->arElementos);
        $inCount2 = count($rsRecordSetArrecadacao->arElementos);

        $inDataInicio1 = intval($rsRecordSetLevantamentos->arElementos[0]['competencia']);
        $inDataInicio2 = intval($rsRecordSetArrecadacao->arElementos[0]['competencia']);

        if ($inCount1 == 0 && $inCount2 != 0) {
            $inCount = $inCount2;
            $inCountAux = $inCount1;
            $rsRecordSet = $rsRecordSetArrecadacao;
            $rsRecordSetAux = $rsRecordSetLevantamentos;

            $this->boLevantamento = true;

        } elseif ($inCount1 != 0 && $inCount2 == 0) {
            $inCount = $inCount1;
            $inCountAux = $inCount2;
            $rsRecordSet = $rsRecordSetLevantamentos;
            $rsRecordSetAux = $rsRecordSetArrecadacao;

            $this->boLevantamento = true;
        } elseif ($inCount1 != 0 && $inCount2 != 0) {

            if ($inDataInicio1 > $inDataInicio2) {
                $inCount = $inCount1;
                $inCountAux = $inCount2;
                $rsRecordSet = $rsRecordSetLevantamentos;
                $rsRecordSetAux = $rsRecordSetArrecadacao;

            } else {
                $inCount = $inCount2;
                $inCountAux = $inCount1;
                $rsRecordSet = $rsRecordSetArrecadacao;
                $rsRecordSetAux = $rsRecordSetLevantamentos;
            }
            $this->boLevantamento = true;
        } else {
            $this->boLevantamento = false;
        }

        for ($i = 0; $i < $inCountAux; $i++) {
            foreach ($rsRecordSetAux->arElementos[$i] as $stCampo => $stValor) {
                if ($stCampo == 'competencia') {
                    $arrCompetenciaAux[] = intval($stValor);
                }
                $arAux[$stCampo] = null;
            }
        }

        for ($i = 0; $i < $inCount; $i++) {
            foreach ($rsRecordSet->arElementos[$i] as $stCampo => $stValor) {
                $arrElementos[] = $stCampo;
                $arrValores[$stCampo] = $stValor;

                if ($stCampo == 'competencia') {
                    for ($k = 0; $k < $inCountAux; $k++) {
                        foreach ($rsRecordSetAux->arElementos[$k] as $stCampoArr => $stValorArr) {
                            if ($stCampoArr == 'competencia') {
                                if (intval($stValor) == intval($stValorArr)) {
                                    $rsRecordSet->arElementos[$i] = @array_merge_recursive($rsRecordSet->arElementos[$i], $rsRecordSetAux->arElementos[$k]);
                                } else {
                                    $rsRecordSet->arElementos[$i] = @array_merge_recursive($rsRecordSet->arElementos[$i], $arAux);
                                }
                            }
                        }
                    }
                    $arrCompetencia[] = intval($stValor);
                }

            }

            if (is_array($rsRecordSet->arElementos[$i]['competencia'])) {
                foreach ($rsRecordSet->arElementos[$i]['competencia'] as $vlr) {
                    if ($vlr != '') {
                        $rsRecordSet->arElementos[$i]['competencia'] = $vlr;
                        break;
                    } else {
                        $rsRecordSet->arElementos[$i]['competencia'] = null;
                    }
                }

            }

            if (is_array($rsRecordSet->arElementos[$i]['inscricao_economica'])) {
                foreach ($rsRecordSet->arElementos[$i]['inscricao_economica'] as $vlr) {
                    if ($vlr != '') {
                        $rsRecordSet->arElementos[$i]['inscricao_economica'] = $vlr;
                        break;
                    } else {
                        $rsRecordSet->arElementos[$i]['inscricao_economica'] = null;
                    }
                }
            }

            if (is_array($rsRecordSet->arElementos[$i]['receita_declarado'])) {
                foreach ($rsRecordSet->arElementos[$i]['receita_declarado'] as $vlr) {
                    if ($vlr != '') {
                        $rsRecordSet->arElementos[$i]['receita_declarado'] = sprintf("%01.2f", $vlr);
                        break;
                    } else {
                        $rsRecordSet->arElementos[$i]['receita_declarado'] = null;
                    }
                }
            }

            if (is_array($rsRecordSet->arElementos[$i]['issqn_pago'])) {
                foreach ($rsRecordSet->arElementos[$i]['issqn_pago'] as $vlr) {
                    if ($vlr != '') {
                        $rsRecordSet->arElementos[$i]['issqn_pago'] = sprintf("%01.2f", $vlr);
                        break;
                    } else {
                        $rsRecordSet->arElementos[$i]['issqn_pago'] = null;
                    }
                }
            }

            if (is_array($rsRecordSet->arElementos[$i]['receita_efetivo'])) {
                foreach ($rsRecordSet->arElementos[$i]['receita_efetivo'] as $vlr) {
                    if ($vlr != '') {
                        $rsRecordSet->arElementos[$i]['receita_efetivo'] = sprintf("%01.2f", $vlr);
                        break;
                    } else {
                        $rsRecordSet->arElementos[$i]['receita_efetivo'] = null;
                    }
                }
            }

            if (is_array($rsRecordSet->arElementos[$i]['issqn_devido'])) {
                foreach ($rsRecordSet->arElementos[$i]['issqn_devido'] as $vlr) {
                    if ($vlr != '') {
                        $rsRecordSet->arElementos[$i]['issqn_devido'] = sprintf("%01.2f", $vlr);
                        break;
                    } else {
                        $rsRecordSet->arElementos[$i]['issqn_devido'] = null;
                    }
                }
            }

            $j = $i + 1;
            if (!$rsRecordSet->arElementos[$j]) {

                if (is_array($arrCompetenciaAux) && is_array($arrCompetencia)) {
                    $result = array_diff($arrCompetenciaAux, $arrCompetencia);
                }

                for ($l = 0; $l < $inCountAux; $l++) {
                    foreach ($rsRecordSetAux->arElementos[$l] as $stCampoAux => $stValorAux) {
                        if ($stCampoAux == 'competencia') {
                            foreach ($result as $vlr) {
                                if (intval($stValorAux) == $vlr) {
                                    foreach ($arrElementos as $var) {
                                        switch ($var) {
                                            case 'inscricao_economica':
                                                $arrNew->arElementos[$l][$var] = $arrValores[$var];
                                            break;

                                            case 'competencia':
                                                $arrNew->arElementos[$l][$var] = $stValorAux;
                                            break;

                                            default:
                                                $arrNew->arElementos[$l][$var] = '0.00';
                                            break;
                                        }
                                    }
                                    $rsRecordSetAux->arElementos[$l] = @array_merge_recursive($arrNew->arElementos[$l], $rsRecordSetAux->arElementos[$l]);
                                    if (is_array($rsRecordSetAux->arElementos[$l]['competencia'])) {
                                        $rsRecordSetAux->arElementos[$l]['competencia'] = $rsRecordSetAux->arElementos[$l]['competencia'][0];
                                    }

                                    if (is_array($rsRecordSetAux->arElementos[$l]['inscricao_economica'])) {
                                        $rsRecordSetAux->arElementos[$l]['inscricao_economica'] = $rsRecordSetAux->arElementos[$l]['inscricao_economica'][0];
                                    }
                                    if (is_array($rsRecordSetAux->arElementos[$l]['receita_declarado'])) {
                                        $rsRecordSetAux->arElementos[$l]['receita_declarado'] = sprintf("%01.2f",$rsRecordSetAux->arElementos[$l]['receita_declarado'][0]);
                                    }

                                    if (is_array($rsRecordSetAux->arElementos[$l]['issqn_pago'])) {
                                        $rsRecordSetAux->arElementos[$l]['issqn_pago'] = sprintf("%01.2f",$rsRecordSetAux->arElementos[$l]['issqn_pago'][0]);
                                    }

                                    if (is_array($rsRecordSetAux->arElementos[$l]['receita_efetivo'])) {
                                        $rsRecordSetAux->arElementos[$l]['receita_efetivo'] = sprintf("%01.2f",$rsRecordSetAux->arElementos[$l]['receita_efetivo'][0]);
                                    }

                                    if (is_array($rsRecordSetAux->arElementos[$l]['issqn_devido'])) {
                                        $rsRecordSetAux->arElementos[$l]['issqn_devido'] = sprintf("%01.2f",$rsRecordSetAux->arElementos[$l]['issqn_devido'][0]);
                                    }
                                    array_push($rsRecordSet->arElementos, $rsRecordSetAux->arElementos[$l]);
                                }
                            }
                        }
                    }
                }
            }
        }

        $newCount = count($rsRecordSet->arElementos);
        $rsRecordSet->inNumLinhas = $newCount;
        for ($i = 0; $i < $newCount; $i++) {

            if (trim($rsRecordSet->arElementos[$i]['receita_declarado']) == '') {
                $rsRecordSet->arElementos[$i]['receita_declarado'] = '0.00';
            }

            if (trim($rsRecordSet->arElementos[$i]['receita_efetivo']) == '') {
                $rsRecordSet->arElementos[$i]['receita_efetivo'] = '0.00';
            }

            if (trim($rsRecordSet->arElementos[$i]['issqn_pago']) == '') {
                $rsRecordSet->arElementos[$i]['issqn_pago'] = '0.00';
            }

            if (trim($rsRecordSet->arElementos[$i]['issqn_devido']) == '') {
                $rsRecordSet->arElementos[$i]['issqn_devido'] = '0.00';
            }

            $stPago = str_replace(',', '', $rsRecordSet->arElementos[$i]['issqn_pago']);
            $inPago = str_replace('.', '', $stPago);
            $stDevido = str_replace(',', '', $rsRecordSet->arElementos[$i]['issqn_devido']);
            $inDevido = str_replace('.', '', $stDevido);
            $valor = $inPago - $inDevido;

            if (($valor) >= 0) {
                $valor = "{$valor}";
                $inCent = substr($valor, -2);
                $inInteiro = substr($valor, 0, -2);
                $newValor =  $inInteiro.'.'.$inCent;
                $rsRecordSet->arElementos[$i]['devolver'] = sprintf("%01.2f", $newValor);
                $rsRecordSet->arElementos[$i]['pagar'] = '0.00';
                $inTotalDevolver+= $valor;
            } else {
                $valor = $valor * (-1);
                $valor = "{$valor}";
                $inCent = substr($valor, -2);
                $inInteiro = substr($valor, 0, -2);
                $newValor = $inInteiro.'.'.$inCent;
                $rsRecordSet->arElementos[$i]['devolver'] = '0.00';
                $rsRecordSet->arElementos[$i]['pagar'] = sprintf("%01.2f", $newValor);
                $inTotalPagar+= $valor;
            }

            if ($i == 0) {
                $stFiltroIndicador = " cod_modulo = 34 and parametro = 'fis_indice_correcao' and exercicio = ". Sessao::read('exercicio');
                $rsConfiguracaoIndicador = $this->recuperaTipoConfiguracao($stFiltroIndicador);

                $stFiltroEconomico = " mie.cod_indicador = ".$rsConfiguracaoIndicador->getCampo('valor');
                $rsIndicadorEconomico = $this->recuperaIndicadorEconomico($stFiltroEconomico);

                $obRARRConfiguracao = new RARRConfiguracao;
                $obRARRConfiguracao->consultar();
                $stCodGrupoCreditoEscrituracao = $obRARRConfiguracao->getCodigoGrupoCreditoEscrituracao();
                $arGrupoCreditoEscrituracao = preg_split( "/\//", $stCodGrupoCreditoEscrituracao );
                //$rsRecordSetFuncaoMulta = $this->recuperaTipoFuncao($stCodTipoMulta);
                //$rsRecordSetFuncaoJuros = $this->recuperaTipoFuncao($stCodTipoJuros);

                $stCodTipoJuros = " WHERE mca.cod_tipo = 2 AND acg.cod_grupo = ".$arGrupoCreditoEscrituracao[0]." AND acg.ano_exercicio = ".$arGrupoCreditoEscrituracao[1];
                $stCodTipoMulta = " WHERE mca.cod_tipo = 3 AND acg.cod_grupo = ".$arGrupoCreditoEscrituracao[0]." AND acg.ano_exercicio = ".$arGrupoCreditoEscrituracao[1];

                $obTFISLevantamento = new TFISLevantamento;
                $obTFISLevantamento->recuperaJuroMultaGrupoISS( $rsRecordSetFuncaoJuros, $stCodTipoJuros );
                $obTFISLevantamento->recuperaJuroMultaGrupoISS( $rsRecordSetFuncaoMulta, $stCodTipoMulta );

                $rsRecordSetValorIndicador = $this->recuperaTipoValorIndicador('');

                if ( !$rsRecordSetFuncaoJuros->Eof() ) {
                    $stFuncaoJuros = $rsRecordSetFuncaoJuros->arElementos[0]['nom_funcao'];
                    $inIndicadorJuros = $rsRecordSetFuncaoJuros->arElementos[0]['cod_acrescimo'];
                } else {
                    $stFuncaoJuros = "";
                    $inIndicadorJuros = "";
                }

                if ( !$rsRecordSetFuncaoMulta->Eof() ) {
                    $stFuncaoMulta = $rsRecordSetFuncaoMulta->arElementos[0]['nom_funcao'];
                    $inIndicadorMulta = $rsRecordSetFuncaoMulta->arElementos[0]['cod_acrescimo'];
                } else {
                    $stFuncaoMulta = "";
                    $inIndicadorMulta = "";
                }

                $inIndicadorValor = $rsIndicadorEconomico->arElementos[0]['cod_indicador'];
                $stFuncaoIndice = $rsIndicadorEconomico->arElementos[0]['nom_funcao'];
            }

            $arVarCompetencia = explode('/', $rsRecordSet->arElementos[$i]['competencia']);

            $inExercicio = $arVarCompetencia[1];
            $inParcela = $arVarCompetencia[0];

            $stFiltroGrupo = " cod_processo = ". $arParam['inCodProcesso'];
            $rsRecordSetGrupo = $this->recuperarGrupo($stFiltroGrupo);

            $inGrupoCred = $rsRecordSetGrupo->arElementos[0]['cod_grupo'];

            if ($inGrupoCred != '') {
                $stFiltroVencimento = " cod_grupo = ". $inGrupoCred . " and cod_parcela = " . $inParcela. " and ano_exercicio = ".$inExercicio;
                $rsRecordSetVencimento = $this->recuperaTipoVencimentosParcela($stFiltroVencimento);

                if ($rsRecordSetVencimento->arElementos[0]['data_vencimento']) {
                    $inData = explode('-', $rsRecordSetVencimento->arElementos[0]['data_vencimento']);

                    //Indice
                    $stParamIndice = " date '". $rsRecordSetVencimento->arElementos[0]['data_vencimento'] ."', date '". date('Y-m-d') ."', ". $newValor .", ". $inIndicadorValor;
                    $stFiltroIndice = $stParamIndice . "---" . $stFuncaoIndice;

                    $rsRecordSetIndice = $this->recuperaTipoSelectFuncao($stFiltroIndice);

                    $flIndice = $rsRecordSetIndice->arElementos[0]['funcao'];
                    $newIndice = $flIndice;

                    //Valor Corrigido
                    $newCorrigido = sprintf("%01.2f", $newValor + ($newValor * ($newIndice / 100)));

                    if ($rsRecordSet->arElementos[$i]['pagar'] != '0.00') {

                        //Multa de mora
                        if ($stFuncaoMulta) {
                            $stParamMulta = " date '". $rsRecordSetVencimento->arElementos[0]['data_vencimento'] ."', date '". date('Y-m-d') ."', ". $newCorrigido .", ".$inIndicadorMulta.", 3 ";
                            $stFiltroMulta = $stParamMulta . "---" . $stFuncaoMulta;
                            $rsRecordSetMulta = $this->recuperaTipoSelectFuncao($stFiltroMulta);
                            $flMulta = $rsRecordSetMulta->arElementos[0]['funcao'];
                        } else {
                            $flMulta = '0.00';
                        }

                        //Juros Moratorios
                        if ($stFuncaoJuros) {
                            $stParamJuros = " date '". $rsRecordSetVencimento->arElementos[0]['data_vencimento'] ."', date '". date('Y-m-d') ."', ". $newCorrigido .", ".$inIndicadorJuros.", 2 ";
                            $stFiltroJuros = $stParamJuros . "---" . $stFuncaoJuros;
                            $rsRecordSetJuros = $this->recuperaTipoSelectFuncao($stFiltroJuros);
                            $flJuros = $rsRecordSetJuros->arElementos[0]['funcao'];
                        } else {
                            $flJuros = '0.00';
                        }
                    } else {
                        $flMulta = '0.00';
                        $flJuros = '0.00';
                    }
                } else {
                    unset($rsRecordSet);
                    $rsRecordSet = new RecordSet();
                    $rsRecordSet->inNumLinhas = 1;
                    $rsRecordSet->arElementos[0]['erros'] = '';
                    $boPlanilha = true;
                    $this->boLevantamento = false;

                    return sistemaLegado::exibeAviso("Não há Vencimentos das Parcelas cadastrados para esse processo.(".$arParam['inCodProcesso'].")", "erro", "erro");
                    break;
                }
            } else {
                unset($rsRecordSet);
                $rsRecordSet = new RecordSet();
                $rsRecordSet->inNumLinhas = 1;
                $rsRecordSet->arElementos[0]['erros'] = '';
                $boPlanilha = true;
                $this->boLevantamento = false;

                return sistemaLegado::exibeAviso("Não há Grupo de Crédito Cadastrado para esse processo.(".$arParam['inCodProcesso'].")", "erro", "erro");
                break;
            }

            $inIndice = "{$newIndice}";
            $inIndice = str_replace('.', '', $inIndice);
            $inTotalIndice += $inIndice;

            $inCorrigido = "{$newCorrigido}";
            $inCorrigido = str_replace('.', '', $inCorrigido);
            $inTotalCorrigido += $inCorrigido;

            $inMulta = "{$flMulta}";
            $inMulta = str_replace('.', '', $inMulta);
            $inTotalMulta += $inMulta;

            $inJuros = "{$flJuros}";
            $inJuros = str_replace('.', '', $inJuros);
            $inTotalJuros += $inJuros;

            //Levantamento Correção
            $rsRecordSet->arElementos[$i]['inCodIndicador'] = $inIndicadorValor;
            $rsRecordSet->arElementos[$i]['indice'] = sprintf("%01.2f", $newIndice);
            $rsRecordSet->arElementos[$i]['vl_corrigido'] = sprintf("%01.2f", $newCorrigido);

            //Levantamento Acrescimo -  Multa
            $rsRecordSet->arElementos[$i]['inCodAcrescimoMulta'] = $inIndicadorMulta;
            $rsRecordSet->arElementos[$i]['inCodTipoMulta'] = 3;
            $rsRecordSet->arElementos[$i]['multa_mora'] = sprintf("%01.2f", $flMulta);

            //Levantamento Acrescimo -  Juros
            $rsRecordSet->arElementos[$i]['inCodAcrescimoJuros'] = $inIndicadorJuros;
            $rsRecordSet->arElementos[$i]['inCodTipoJuros'] = 2;
            $rsRecordSet->arElementos[$i]['juros_mora'] = sprintf("%01.2f", $flJuros);

            if ($rsRecordSet->arElementos[$i]['devolver'] != '0.00') {
                $inTotalParcialDevolver = $inCorrigido;
                $inCentParcialDevolver = substr($inTotalParcialDevolver, -2);
                $inInteiroParcialDevolver = substr($inTotalParcialDevolver, 0, -2);

                if ($inInteiroParcialDevolver == "") {
                    $inInteiroParcialDevolver = '0';
                }

                if ($inCentParcialDevolver == "") {
                    $inCentParcialDevolver = '00';
                }

                $stTotalParcialDevolver = sprintf("%01.2f", $inInteiroParcialDevolver.'.'.$inCentParcialDevolver);
                $inTotalGeralDevolver += $inTotalParcialDevolver;

                $rsRecordSet->arElementos[$i]['total_devolver'] = $stTotalParcialDevolver;
                $rsRecordSet->arElementos[$i]['total_pagar'] = '0.00';
            }

            if ($rsRecordSet->arElementos[$i]['pagar'] != '0.00') {
                $inTotalParcialPagar = $inCorrigido + $inMulta + $inJuros;
                $inCentParcialPagar = substr($inTotalParcialPagar, -2);
                $inInteiroParcialPagar = substr($inTotalParcialPagar, 0, -2);

                if ($inInteiroParcialPagar == "") {
                    $inInteiroParcialPagar = '0';
                }

                if ($inCentParcialPagar == "") {
                    $inCentParcialPagar = '00';
                }

                $stTotalParcialPagar = sprintf("%01.2f", $inInteiroParcialPagar.'.'.$inCentParcialPagar);
                $inTotalGeralPagar += $inTotalParcialPagar;

                $rsRecordSet->arElementos[$i]['total_devolver'] = '0.00';
                $rsRecordSet->arElementos[$i]['total_pagar'] = $stTotalParcialPagar;
            }

            foreach ($rsRecordSet->arElementos[$i] as $stCampo => $stValor) {
                if ($stCampo != 'data_competencia') {
                    $stHidden.= $this->criaHidden($stCampo.'['.$i.']', trim($stValor));
                }
            }
        }

        //Total Indice
        $stTotalIndice =  null;

        //Total Corrigido
        if (intval($inTotalCorrigido) != 0) {
            $inCentCorrigido = substr($inTotalCorrigido, -2);
            $inInteiroCorrigido = substr($inTotalCorrigido, 0, -2);
            $stTotalCorrigido = sprintf("%01.2f", $inInteiroCorrigido.'.'.$inCentCorrigido);
        } else {
            $stTotalCorrigido = '0.00';
        }

        //Total Devolver
        if (intval($inTotalDevolver) != 0) {
            $inCentDevolver = substr($inTotalDevolver, -2);
            $inInteiroDevolver = substr($inTotalDevolver, 0, -2);
            $stTotalDevolver = sprintf("%01.2f", $inInteiroDevolver.'.'.$inCentDevolver);
        } else {
            $stTotalDevolver = '0.00';
        }

        //Total Pagar
        if (intval($inTotalPagar) != 0) {
            $inCentPagar = substr($inTotalPagar, -2);
            $inInteiroPagar = substr($inTotalPagar, 0, -2);
            $stTotalPagar = sprintf("%01.2f", $inInteiroPagar.'.'.$inCentPagar);
        } else {
            $stTotalPagar = '0.00';
        }

        //Total Multa de Mora
        if (intval($inTotalJuros) != 0) {
            $inCentMulta = substr($inTotalMulta, -2);
            $inInteiroMulta = substr($inTotalMulta, 0, -2);
            $stTotalMulta = sprintf("%01.2f", $inInteiroMulta.'.'.$inCentMulta);
        } else {
            $stTotalMulta = '0.00';
        }

        //Total Juros Moratórios
        if (intval($inTotalJuros) != 0) {
            $inCentJuros = substr($inTotalJuros, -2);
            $inInteiroJuros = substr($inTotalJuros, 0, -2);
            $stTotalJuros = sprintf("%01.2f", $inInteiroJuros.'.'.$inCentJuros);
        } else {
            $stTotalJuros = '0.00';
        }

        //Total Geral Devolver
        if (intval($inTotalGeralDevolver) != 0) {
            $inCentTotalGeralDevolver = substr($inTotalGeralDevolver, -2);
            $inInteiroTotalGeralDevolver = substr($inTotalGeralDevolver, 0, -2);
            $stTotalGeralDevolver = sprintf("%01.2f", $inInteiroTotalGeralDevolver.'.'.$inCentTotalGeralDevolver);
        } else {
            $stTotalGeralDevolver = '0.00';
        }

        //Total Geral Devolver
        if (intval($inTotalGeralPagar) != 0) {
            $inCentTotalGeralPagar = substr($inTotalGeralPagar, -2);
            $inInteiroTotalGeralPagar = substr($inTotalGeralPagar, 0, -2);
            $stTotalGeralPagar =  sprintf("%01.2f", $inInteiroTotalGeralPagar.'.'.$inCentTotalGeralPagar);
        } else {
            $stTotalGeralPagar = '0.00';
        }

        //Total
        $inTotal = $inTotalGeralPagar - $inTotalGeralDevolver;

        if ($inTotal >= 0) {
            $inCentTotal = substr($inTotal, -2);
            $inInteiroTotal = substr($inTotal, 0, -2);
            if ($inTotal != 0) {
                $stTotal =  sprintf("%01.2f", $inInteiroTotal.'.'.$inCentTotal);
                $inNewTotal = sprintf("%01.2f", $inInteiroTotal.'.'.$inCentTotal);
            } else {
                $stTotal =  '0.00';
                $inNewTotal = '0.00';
            }

            $stTipoTotal = "Pagar";

        } else {
            $inTotal = $inTotal * (-1);
            $inCentTotal = substr($inTotal, -2);
            $inInteiroTotal = substr($inTotal, 0, -2);
            $stTotal =  sprintf("%01.2f", $inInteiroTotal.'.'.$inCentTotal);
            $inNewTotal = sprintf("%01.2f", $inInteiroTotal.'.'.$inCentTotal);
            $stTipoTotal = "Devolver";
        }

        if ($rsRecordSet->inNumLinhas < 0) {
            unset($rsRecordSet);
            $rsRecordSet = new RecordSet();
            $rsRecordSet->inNumLinhas = 1;
            $rsRecordSet->arElementos[0]['erros'] = '';
            $boPlanilha = true;
            $this->boLevantamento = false;

            return sistemaLegado::exibeAviso("Não há Lançamentos cadastrados para esse processo.(".$arParam['inCodProcesso'].")", "erro", "erro");
        }

        $arTMP = $rsRecordSet->getElementos();

        $obTFISProcessoFiscal = new TFISProcessoFiscal;
        $stFiltro = " WHERE cod_processo = ".$arParam['inCodProcesso'];
        $obTFISProcessoFiscal->recuperaTodos( $rsLstProc, $stFiltro );
        if ( !$rsLstProc->Eof() ) {
            $arDataIni = explode( "/", $rsLstProc->getCampo("periodo_inicio") );
            $arDataFim = explode( "/", $rsLstProc->getCampo("periodo_termino") );
            $inAno = $arDataIni[2];
            $inMes = $arDataIni[1];
            while ( ( $inAno < $arDataFim[2] ) || ( $inMes <= $arDataFim[1] ) ) {
                $boNaLista = false;
                for ( $inX=0; $inX<count($arTMP); $inX++ ) {
                    if ( $arTMP[$inX]["competencia"] == sprintf( "%02d/%d", $inMes, $inAno ) ) {
                        $boNaLista = true;
                        break;
                    }
                }

                if (!$boNaLista) {
                    $arTMP[] = array (
                        'competencia' => sprintf( "%02d/%d", $inMes, $inAno ),
                        'receita_declarado' => 0.00,
                        'issqn_pago' => 0.00,
                        'receita_efetivo' => 0.00,
                        'issqn_devido' => 0.00,
                        'devolver' => 0.00,
                        'pagar' => 0.00,
                        'indice' => 0.00,
                        'vl_corrigido' => 0.00,
                        'multa_mora' => 0.00,
                        'juros_mora' => 0.00,
                        'total_devolver' => 0.00,
                        'total_pagar' => 0.00
                    );
                }

                if ($inMes > 11) {
                    $inAno++;
                    $inMes = 1;
                }else
                    $inMes++;
            }
        }

        for ( $inX=0; $inX<count($arTMP); $inX++ ) {
            $arComp = explode( "/", $arTMP[$inX]["competencia"] );
            $arTMP[$inX]["comp"] = $arComp[1].$arComp[0];
        }

        $rsRecordSet->preenche( $arTMP );
        $rsRecordSet->ordena( "comp" );
        unset( $arTMP );
        while ( !$rsRecordSet->Eof() ) {
            $arTMP[] = array (
                'competencia' => $rsRecordSet->getCampo("competencia"),
                'receita_declarado' => $rsRecordSet->getCampo("receita_declarado"),
                'issqn_pago' => $rsRecordSet->getCampo("issqn_pago"),
                'receita_efetivo' => $rsRecordSet->getCampo("receita_efetivo"),
                'issqn_devido' => $rsRecordSet->getCampo("issqn_devido"),
                'devolver' => $rsRecordSet->getCampo("devolver"),
                'pagar' => $rsRecordSet->getCampo("pagar"),
                'indice' => $rsRecordSet->getCampo("indice"),
                'vl_corrigido' => $rsRecordSet->getCampo("vl_corrigido"),
                'multa_mora' => $rsRecordSet->getCampo("multa_mora"),
                'juros_mora' => $rsRecordSet->getCampo("juros_mora"),
                'total_devolver' => $rsRecordSet->getCampo("total_devolver"),
                'total_pagar' => $rsRecordSet->getCampo("total_pagar")
            );

            $rsRecordSet->proximo();
        }

        for ( $inX=0; $inX<count($arTMP); $inX++ ) {
            $arTMP[$inX]["posicao"] = $inX+1;
        }
        //-----------------------------------

        $arTMP[] = array(
            'posicao' => '#',
            'competencia' => 'Total',
            'receita_declarado' => $stTotalReceitaDeclarado,
            'issqn_pago' => $stTotalISSQNPago,
            'receita_efetivo' => $stTotalReceitaEfetivo,
            'issqn_devido' => $stTotalISSQNDevido,
            'devolver' => $stTotalDevolver,
            'pagar' => $stTotalPagar,
            'indice' => $stTotalIndice,
            'vl_corrigido' => $stTotalCorrigido,
            'multa_mora' => $stTotalMulta,
            'juros_mora' => $stTotalJuros,
            'total_devolver' => $stTotalGeralDevolver,
            'total_pagar' => $stTotalGeralPagar
        );

        $rsRecordSet->preenche( $arTMP );

        $rsRecordSet->addFormatacao ( 'receita_declarado', 'NUMERIC_BR' );
        $rsRecordSet->addFormatacao ( 'issqn_pago', 'NUMERIC_BR' );
        $rsRecordSet->addFormatacao ( 'receita_efetivo', 'NUMERIC_BR' );

        $rsRecordSet->addFormatacao ( 'issqn_devido', 'NUMERIC_BR' );
        $rsRecordSet->addFormatacao ( 'devolver', 'NUMERIC_BR' );
        $rsRecordSet->addFormatacao ( 'pagar', 'NUMERIC_BR' );
        $rsRecordSet->addFormatacao ( 'indice', 'NUMERIC_BR' );

        $rsRecordSet->addFormatacao ( 'vl_corrigido', 'NUMERIC_BR' );
        $rsRecordSet->addFormatacao ( 'multa_mora', 'NUMERIC_BR' );
        $rsRecordSet->addFormatacao ( 'juros_mora', 'NUMERIC_BR' );
        $rsRecordSet->addFormatacao ( 'total_devolver', 'NUMERIC_BR' );
        $rsRecordSet->addFormatacao ( 'total_pagar', 'NUMERIC_BR' );

        $obListaLancamento = new Lista();
        $obListaLancamento->setMostraPaginacao(false);
        $obListaLancamento->setNumeracao(false);

        $obListaLancamento->setRecordSet($rsRecordSet);

        $obListaLancamento->addCabecalho();
        $obListaLancamento->ultimoCabecalho->setRowSpan(2);
        $obListaLancamento->ultimoCabecalho->setStyle("vertical-align:middle;");

        $obListaLancamento->ultimoCabecalho->addConteudo("&nbsp;");
        $obListaLancamento->ultimoCabecalho->setWidth("2,5%");
        $obListaLancamento->commitCabecalho();

        $obListaLancamento->addCabecalho();
        $obListaLancamento->ultimoCabecalho->setRowSpan(2);
        $obListaLancamento->ultimoCabecalho->setStyle("vertical-align:middle;");
        $obListaLancamento->ultimoCabecalho->addConteudo("Mês/Ano");
        $obListaLancamento->ultimoCabecalho->setWidth("7,5%");
        $obListaLancamento->commitCabecalho();

        $obListaLancamento->addCabecalho();
        $obListaLancamento->ultimoCabecalho->setColSpan(2);
        $obListaLancamento->ultimoCabecalho->setStyle("vertical-align:middle;");
        $obListaLancamento->ultimoCabecalho->addConteudo("Receita Tributária");
        $obListaLancamento->ultimoCabecalho->setWidth("15%");
        $obListaLancamento->commitCabecalho();

        $obListaLancamento->addCabecalho();
        $obListaLancamento->ultimoCabecalho->setColSpan(2);
        $obListaLancamento->ultimoCabecalho->setStyle("vertical-align:middle;");
        $obListaLancamento->ultimoCabecalho->addConteudo("ISSQN");
        $obListaLancamento->ultimoCabecalho->setWidth("15%");
        $obListaLancamento->commitCabecalho();

        $obListaLancamento->addCabecalho();
        $obListaLancamento->ultimoCabecalho->setColSpan(2);
        $obListaLancamento->ultimoCabecalho->setStyle("vertical-align:middle;");
        $obListaLancamento->ultimoCabecalho->addConteudo("ISSQN");
        $obListaLancamento->ultimoCabecalho->setWidth("15%");
        $obListaLancamento->commitCabecalho();

        $obListaLancamento->addCabecalho();
        $obListaLancamento->ultimoCabecalho->setColSpan(2);
        $obListaLancamento->ultimoCabecalho->setStyle("vertical-align:middle;");
        $obListaLancamento->ultimoCabecalho->addConteudo("Correção Monetária");
        $obListaLancamento->ultimoCabecalho->setWidth("15%");
        $obListaLancamento->commitCabecalho();

        $obListaLancamento->addCabecalho();
        $obListaLancamento->ultimoCabecalho->setRowSpan(2);
        $obListaLancamento->ultimoCabecalho->setStyle("vertical-align:middle;");
        $obListaLancamento->ultimoCabecalho->addConteudo("Multa de Mora (R$)");
        $obListaLancamento->ultimoCabecalho->setWidth("7,5%");
        $obListaLancamento->commitCabecalho();

        $obListaLancamento->addCabecalho();
        $obListaLancamento->ultimoCabecalho->setRowSpan(2);
        $obListaLancamento->ultimoCabecalho->setStyle("vertical-align:middle;");
        $obListaLancamento->ultimoCabecalho->addConteudo("Juros Moratórios (R$)");
        $obListaLancamento->ultimoCabecalho->setWidth("7,5%");
        $obListaLancamento->commitCabecalho();

        $obListaLancamento->addCabecalho();
        $obListaLancamento->ultimoCabecalho->setColSpan(2);
        $obListaLancamento->ultimoCabecalho->setStyle("vertical-align:middle;");
        $obListaLancamento->ultimoCabecalho->addConteudo("Total Parcial");
        $obListaLancamento->ultimoCabecalho->setWidth("15%");
        $obListaLancamento->commitCabecalho();

        $obListaLancamento->addLinha();
        $obListaLancamento->ultimaLinha->addCelula();
        $obListaLancamento->ultimaLinha->ultimaCelula->setClass('labelcenter');
        $obListaLancamento->ultimaLinha->ultimaCelula->setStyle("vertical-align:middle;");
        $obListaLancamento->ultimaLinha->ultimaCelula->addConteudo('Declarada (R$)');
        $obListaLancamento->ultimaLinha->ultimaCelula->setWidth("7,5%");
        $obListaLancamento->ultimaLinha->commitCelula();

        $obListaLancamento->ultimaLinha->addCelula();
        $obListaLancamento->ultimaLinha->ultimaCelula->setClass('labelcenter');
        $obListaLancamento->ultimaLinha->ultimaCelula->setStyle("vertical-align:middle;");
        $obListaLancamento->ultimaLinha->ultimaCelula->addConteudo('Efetiva (R$)');
        $obListaLancamento->ultimaLinha->ultimaCelula->setWidth("7,5%");
        $obListaLancamento->ultimaLinha->commitCelula();

        $obListaLancamento->ultimaLinha->addCelula();
        $obListaLancamento->ultimaLinha->ultimaCelula->setClass('labelcenter');
        $obListaLancamento->ultimaLinha->ultimaCelula->setStyle("vertical-align:middle;");
        $obListaLancamento->ultimaLinha->ultimaCelula->addConteudo('Pago (R$)');
        $obListaLancamento->ultimaLinha->ultimaCelula->setWidth("7,5%");
        $obListaLancamento->ultimaLinha->commitCelula();

        $obListaLancamento->ultimaLinha->addCelula();
        $obListaLancamento->ultimaLinha->ultimaCelula->setClass('labelcenter');
        $obListaLancamento->ultimaLinha->ultimaCelula->setStyle("vertical-align:middle;");
        $obListaLancamento->ultimaLinha->ultimaCelula->addConteudo('Devido (R$)');
        $obListaLancamento->ultimaLinha->ultimaCelula->setWidth("7,5%");
        $obListaLancamento->ultimaLinha->commitCelula();

        $obListaLancamento->ultimaLinha->addCelula();
        $obListaLancamento->ultimaLinha->ultimaCelula->setClass('labelcenter');
        $obListaLancamento->ultimaLinha->ultimaCelula->setStyle("vertical-align:middle;");
        $obListaLancamento->ultimaLinha->ultimaCelula->addConteudo('A Devolver (R$)');
        $obListaLancamento->ultimaLinha->ultimaCelula->setWidth("7,5%");
        $obListaLancamento->ultimaLinha->commitCelula();

        $obListaLancamento->ultimaLinha->addCelula();
        $obListaLancamento->ultimaLinha->ultimaCelula->setClass('labelcenter');
        $obListaLancamento->ultimaLinha->ultimaCelula->setStyle("vertical-align:middle;");
        $obListaLancamento->ultimaLinha->ultimaCelula->addConteudo('A Pagar (R$)');
        $obListaLancamento->ultimaLinha->ultimaCelula->setWidth("7,5%");
        $obListaLancamento->ultimaLinha->commitCelula();

        $obListaLancamento->ultimaLinha->addCelula();
        $obListaLancamento->ultimaLinha->ultimaCelula->setClass('labelcenter');
        $obListaLancamento->ultimaLinha->ultimaCelula->setStyle("vertical-align:middle;");
        $obListaLancamento->ultimaLinha->ultimaCelula->addConteudo('Índice (%)');
        $obListaLancamento->ultimaLinha->ultimaCelula->setWidth("7,5%");
        $obListaLancamento->ultimaLinha->commitCelula();

        $obListaLancamento->ultimaLinha->addCelula();
        $obListaLancamento->ultimaLinha->ultimaCelula->setClass('labelcenter');
        $obListaLancamento->ultimaLinha->ultimaCelula->setStyle("vertical-align:middle;");
        $obListaLancamento->ultimaLinha->ultimaCelula->addConteudo('Valor Corrigido (R$)');
        $obListaLancamento->ultimaLinha->ultimaCelula->setWidth("7,5%");
        $obListaLancamento->ultimaLinha->commitCelula();

        $obListaLancamento->ultimaLinha->addCelula();
        $obListaLancamento->ultimaLinha->ultimaCelula->setClass('labelcenter');
        $obListaLancamento->ultimaLinha->ultimaCelula->setStyle("vertical-align:middle;");
        $obListaLancamento->ultimaLinha->ultimaCelula->addConteudo('A Devolver (R$)');
        $obListaLancamento->ultimaLinha->ultimaCelula->setWidth("7,5%");
        $obListaLancamento->ultimaLinha->commitCelula();

        $obListaLancamento->ultimaLinha->addCelula();
        $obListaLancamento->ultimaLinha->ultimaCelula->setClass('labelcenter');
        $obListaLancamento->ultimaLinha->ultimaCelula->setStyle("vertical-align:middle;");
        $obListaLancamento->ultimaLinha->ultimaCelula->addConteudo('A Pagar (R$)');
        $obListaLancamento->ultimaLinha->ultimaCelula->setWidth("7,5%");
        $obListaLancamento->ultimaLinha->commitCelula();
        $obListaLancamento->commitLinha();

        if ($boPlanilha) {

            $obListaLancamento->addDado();
            $obListaLancamento->ultimoDado->setAlinhamento("CENTER");
            $obListaLancamento->ultimoDado->setColSpan(13);
            $obListaLancamento->ultimoDado->setCampo("erros");
            $obListaLancamento->commitDado();

            $obListaLancamento->montaHTML();

            $stReturn = $obListaLancamento->getHTML();
        } else {
            $obListaLancamento->addDado();
            $obListaLancamento->ultimoDado->setAlinhamento("DIREITA");
            $obListaLancamento->ultimoDado->setCampo("posicao");
            $obListaLancamento->commitDado();

            $obListaLancamento->addDado();
            $obListaLancamento->ultimoDado->setAlinhamento("DIREITA");
            $obListaLancamento->ultimoDado->setCampo("competencia");
            $obListaLancamento->commitDado();

            $obListaLancamento->addDado();
            $obListaLancamento->ultimoDado->setAlinhamento("DIREITA");
            $obListaLancamento->ultimoDado->setCampo("receita_declarado");
            $obListaLancamento->commitDado();

            $obListaLancamento->addDado();
            $obListaLancamento->ultimoDado->setAlinhamento("DIREITA");
            $obListaLancamento->ultimoDado->setCampo("receita_efetivo");
            $obListaLancamento->commitDado();

            $obListaLancamento->addDado();
            $obListaLancamento->ultimoDado->setAlinhamento("DIREITA");
            $obListaLancamento->ultimoDado->setCampo("issqn_pago");
            $obListaLancamento->commitDado();

            $obListaLancamento->addDado();
            $obListaLancamento->ultimoDado->setAlinhamento("DIREITA");
            $obListaLancamento->ultimoDado->setCampo("issqn_devido");
            $obListaLancamento->commitDado();

            $obListaLancamento->addDado();
            $obListaLancamento->ultimoDado->setAlinhamento("DIREITA");
            $obListaLancamento->ultimoDado->setCampo("devolver");
            $obListaLancamento->commitDado();

            $obListaLancamento->addDado();
            $obListaLancamento->ultimoDado->setAlinhamento("DIREITA");
            $obListaLancamento->ultimoDado->setCampo("pagar");
            $obListaLancamento->commitDado();

            $obListaLancamento->addDado();
            $obListaLancamento->ultimoDado->setAlinhamento("DIREITA");
            $obListaLancamento->ultimoDado->setCampo("indice");
            $obListaLancamento->commitDado();

            $obListaLancamento->addDado();
            $obListaLancamento->ultimoDado->setAlinhamento("DIREITA");
            $obListaLancamento->ultimoDado->setCampo("vl_corrigido");
            $obListaLancamento->commitDado();

            $obListaLancamento->addDado();
            $obListaLancamento->ultimoDado->setAlinhamento("DIREITA");
            $obListaLancamento->ultimoDado->setCampo("multa_mora");
            $obListaLancamento->commitDado();

            $obListaLancamento->addDado();
            $obListaLancamento->ultimoDado->setAlinhamento("DIREITA");
            $obListaLancamento->ultimoDado->setCampo("juros_mora");
            $obListaLancamento->commitDado();

            $obListaLancamento->addDado();
            $obListaLancamento->ultimoDado->setAlinhamento("DIREITA");
            $obListaLancamento->ultimoDado->setCampo("total_devolver");
            $obListaLancamento->commitDado();

            $obListaLancamento->addDado();
            $obListaLancamento->ultimoDado->setAlinhamento("DIREITA");
            $obListaLancamento->ultimoDado->setCampo("total_pagar");
            $obListaLancamento->commitDado();

            //Tabela com os Totais de cada campo
            $obListaTotal = new Tabela();
            $obListaTotal->setCellPadding(2);
            $obListaTotal->setCellSpacing(2);
/*
            $obListaTotal->addLinha();
            $obListaTotal->ultimaLinha->addCelula();
            $obListaTotal->ultimaLinha->ultimaCelula->setClass('labelcenter');
            $obListaTotal->ultimaLinha->ultimaCelula->setStyle("vertical-align:middle;");
            $obListaTotal->ultimaLinha->ultimaCelula->addConteudo("&nbsp;");
            $obListaTotal->ultimaLinha->ultimaCelula->setWidth("2,3%");
            $obListaTotal->ultimaLinha->commitCelula();

            $obListaTotal->ultimaLinha->addCelula();
            $obListaTotal->ultimaLinha->ultimaCelula->setClass('labelcenter');
            $obListaTotal->ultimaLinha->ultimaCelula->setStyle("vertical-align:middle;");
            $obListaTotal->ultimaLinha->ultimaCelula->addConteudo('Totais (R$)');
            $obListaTotal->ultimaLinha->ultimaCelula->setWidth("7,9%");
            $obListaTotal->ultimaLinha->commitCelula();

            $obListaTotal->ultimaLinha->addCelula();
            $obListaTotal->ultimaLinha->ultimaCelula->setClass('show_dados_right');
            $obListaTotal->ultimaLinha->ultimaCelula->addConteudo( number_format( $stTotalReceitaDeclarado, 2, ',','.' ) );
            $obListaTotal->ultimaLinha->ultimaCelula->setWidth("8.8%");
            $obListaTotal->ultimaLinha->commitCelula();

            $obListaTotal->ultimaLinha->addCelula();
            $obListaTotal->ultimaLinha->ultimaCelula->setClass('show_dados_right');
            $obListaTotal->ultimaLinha->ultimaCelula->addConteudo( number_format( $stTotalReceitaEfetivo, 2, ',','.' ) );
            $obListaTotal->ultimaLinha->ultimaCelula->setWidth("6%");
            $obListaTotal->ultimaLinha->commitCelula();

            $obListaTotal->ultimaLinha->addCelula();
            $obListaTotal->ultimaLinha->ultimaCelula->setClass('show_dados_right');
            $obListaTotal->ultimaLinha->ultimaCelula->addConteudo(number_format($stTotalISSQNPago, 2, ',','.' ) );
            $obListaTotal->ultimaLinha->ultimaCelula->setWidth("6%");
            $obListaTotal->ultimaLinha->commitCelula();

            $obListaTotal->ultimaLinha->addCelula();
            $obListaTotal->ultimaLinha->ultimaCelula->setClass('show_dados_right');
            $obListaTotal->ultimaLinha->ultimaCelula->addConteudo(number_format($stTotalISSQNDevido, 2, ',','.' ) );
            $obListaTotal->ultimaLinha->ultimaCelula->setWidth("6%");
            $obListaTotal->ultimaLinha->commitCelula();

            $obListaTotal->ultimaLinha->addCelula();
            $obListaTotal->ultimaLinha->ultimaCelula->setClass('show_dados_right');
            $obListaTotal->ultimaLinha->ultimaCelula->addConteudo(number_format($stTotalDevolver, 2, ',','.' ) );
            $obListaTotal->ultimaLinha->ultimaCelula->setWidth("8%");
            $obListaTotal->ultimaLinha->commitCelula();

            $obListaTotal->ultimaLinha->addCelula();
            $obListaTotal->ultimaLinha->ultimaCelula->setClass('show_dados_right');
            $obListaTotal->ultimaLinha->ultimaCelula->addConteudo(number_format($stTotalPagar, 2, ',','.' ) );
            $obListaTotal->ultimaLinha->ultimaCelula->setWidth("7%");
            $obListaTotal->ultimaLinha->commitCelula();

            $obListaTotal->ultimaLinha->addCelula();
            $obListaTotal->ultimaLinha->ultimaCelula->setClass('show_dados_right');
            $obListaTotal->ultimaLinha->ultimaCelula->addConteudo(number_format($stTotalIndice, 2, ',','.' ) );
            $obListaTotal->ultimaLinha->ultimaCelula->setWidth("7%");
            $obListaTotal->ultimaLinha->commitCelula();

            $obListaTotal->ultimaLinha->addCelula();
            $obListaTotal->ultimaLinha->ultimaCelula->setClass('show_dados_right');
            $obListaTotal->ultimaLinha->ultimaCelula->addConteudo(number_format($stTotalCorrigido, 2, ',','.' ) );
            $obListaTotal->ultimaLinha->ultimaCelula->setWidth("8%");
            $obListaTotal->ultimaLinha->commitCelula();

            $obListaTotal->ultimaLinha->addCelula();
            $obListaTotal->ultimaLinha->ultimaCelula->setClass('show_dados_right');
            $obListaTotal->ultimaLinha->ultimaCelula->addConteudo(number_format( $stTotalMulta, 2, ',','.' ) );
            $obListaTotal->ultimaLinha->ultimaCelula->setWidth("7,5%");
            $obListaTotal->ultimaLinha->commitCelula();

            $obListaTotal->ultimaLinha->addCelula();
            $obListaTotal->ultimaLinha->ultimaCelula->setClass('show_dados_right');
            $obListaTotal->ultimaLinha->ultimaCelula->addConteudo(number_format( $stTotalJuros, 2, ',','.' ) );
            $obListaTotal->ultimaLinha->ultimaCelula->setWidth("7,5%");
            $obListaTotal->ultimaLinha->commitCelula();

            $obListaTotal->ultimaLinha->addCelula();
            $obListaTotal->ultimaLinha->ultimaCelula->setClass('show_dados_right');
            $obListaTotal->ultimaLinha->ultimaCelula->addConteudo( number_format( $stTotalGeralDevolver, 2, ',','.' ) );
            $obListaTotal->ultimaLinha->ultimaCelula->setWidth("7,5%");
            $obListaTotal->ultimaLinha->commitCelula();

            $obListaTotal->ultimaLinha->addCelula();
            $obListaTotal->ultimaLinha->ultimaCelula->setClass('show_dados_right');
            $obListaTotal->ultimaLinha->ultimaCelula->addConteudo(number_format( $stTotalGeralPagar, 2, ',','.' ) );
            $obListaTotal->ultimaLinha->ultimaCelula->setWidth("7,5%");
            $obListaTotal->ultimaLinha->commitCelula();

            $obListaTotal->commitLinha();
*/
            $obListaTotal->addLinha();

            $obListaTotal->ultimaLinha->addCelula();
            $obListaTotal->ultimaLinha->ultimaCelula->setClass('');
            $obListaTotal->ultimaLinha->ultimaCelula->setColSpan(11);
            $obListaTotal->ultimaLinha->ultimaCelula->addConteudo("&nbsp;");
            $obListaTotal->ultimaLinha->commitCelula();

            $obListaTotal->ultimaLinha->addCelula();
            $obListaTotal->ultimaLinha->ultimaCelula->setClass('labelcenter');
            $obListaTotal->ultimaLinha->ultimaCelula->setStyle("text-align:left;vertical-align:middle;");
            $obListaTotal->ultimaLinha->ultimaCelula->addConteudo('Total ' . $stTipoTotal . ' (R$)');
            $obListaTotal->ultimaLinha->commitCelula();

            $obListaTotal->ultimaLinha->addCelula();
            $obListaTotal->ultimaLinha->ultimaCelula->setClass('show_dados_right');
            $obListaTotal->ultimaLinha->ultimaCelula->setColSpan(2);
            $obListaTotal->ultimaLinha->ultimaCelula->addConteudo(number_format($stTotal, 2, ',','.' ));
            $obListaTotal->ultimaLinha->commitCelula();

            $obListaTotal->commitLinha();

            $obListaLancamento->montaHTML();
            $obListaTotal->montaHTML();

            $stHidden.= $this->criaHidden('total_geral', trim($inNewTotal));

            $stReturn = $obListaLancamento->getHTML() . $obListaTotal->getHTML(). $stHidden;
        }

        return $stReturn;
    }

    public function getBoLevantamento()
    {
        return $this->boLevantamento;
    }

    public function cadastrarLevantamentosFiscais($param)
    {
        return $this->controller->cadastrarLevantamentos($param);
    }

    public function gerarPlanilhaLancamentos($param)
    {
        $inCodProcesso = $param['inCodProcesso'];
        $inInscricaoEconomica = $param['inInscricaoEconomica'];
        $flTotalGeral = $param['flTotalGeral'];

        $preview = new PreviewBirt(5, 34, 1);
        $preview->setTitulo('Relatório do Birt');
        $preview->setVersaoBirt('2.5.0');
        //$preview->setExportaExcel(true);
        $preview->addParametro("cod_processo", $inCodProcesso);
        $preview->addParametro("inscricao_economica", $inInscricaoEconomica);
        $preview->addParametro("total_geral", $flTotalGeral);

        return $preview->preview();
    }

    public function filtrosPlanilhas($param)
    {
        if ($param['inTipoFiscalizacao'] != "") {
                $stFiltro[] = " pf.cod_tipo = " .$param['inTipoFiscalizacao']. "\n";
        }

        if ($param['inCodProcesso'] != "") {
            $stFiltro[] = " pf.cod_processo = " .$param['inCodProcesso']. "\n";
        }

        if ($param['inInscricaoEconomica'] != "") {
            $stFiltro[] = " pfe.inscricao_economica = " .$param['inInscricaoEconomica']. "\n";
        }

        if ($param['numcgm'] != "") {
            $stFiltro[] = " fc.numcgm = " .$param['numcgm']. "\n";

        }

        if ($param['boInicio']) {
            $stFiltro[] = " pfc.cod_processo is null \n";
        }

        if ($param['inInscricao']) {
            $stFiltro[] = " acef.inscricao_economica = " .$param['inInscricao']. "\n";
        }

        $return = " ";

        if ($stFiltro) {
            foreach ($stFiltro as $chave => $valor) {
                if ($chave == 0) {
                    $return .= $valor;
                } else {
                    if ($valor == " acef.inscricao_economica = " .$param['inInscricao']. "\n") {
                        $return .= "---".$valor;
                    } else {
                        $return .= " AND ".$valor;
                    }
                }
            }
        }

        return $return;
    }
}
?>
