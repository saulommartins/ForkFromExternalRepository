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
* Classe de regra de negócio para RFolhaPagamentoCalcularFolhas
* Data de Criação: 20/03/2009

* @author Analista: Dagiane Vieira
* @author Desenvolvedor: Diego Lemos de Souza

* @package URBEM
* @subpackage Regra de Negócio

  $Id:$

* Casos de uso: uc-04.05.09
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class RFolhaPagamentoCalcularFolhas
{
private $stTipoFiltro;
private $stCodigos;
private $rsRecalcular;
private $stTipoFolha;
private $arDesdobramentos;
private $inCodComplementar;

public function setTipoFiltro($stValor) {$this->stTipoFiltro = $stValor;}
public function setCodigos($stValor) {$this->stCodigos = $stValor;}
public function setRecalcular($stValor) {$this->rsRecalcular = $stValor;}
private function setTipoFolha($stValor) {$this->stTipoFolha = $stValor;}
private function setCodComplementar($inValor) {$this->inCodComplementar = $inValor;}

public function getTipoFiltro() {return $this->stTipoFiltro;}
public function getCodigos() {return $this->stCodigos;}
public function getRecalcular() {return $this->rsRecalcular;}
private function getTipoFolha() {return $this->stTipoFolha;}
private function getCodComplementar() {return $this->inCodComplementar;}

function RFolhaPagamentoCalcularFolhas()
{
    $this->setCodComplementar(0);
    $this->addDesdobramento('');
}

public function setCalcularSalario()
{
    $this->setTipoFolha("S");
}
public function setCalcularComplementar($inCodComplementar)
{
    $this->setTipoFolha("C");
    $this->setCodComplementar($inCodComplementar);
}
public function setCalcularFerias()
{
    $this->setTipoFolha("F");
}
public function setCalcularDecimo()
{
    $this->setTipoFolha("D");
    unset($this->arDesdobramentos);
}
public function setCalcularRescisao()
{
    $this->setTipoFolha("R");
}

public function addDesdobramento($stDesdobramento)
{
    $this->arDesdobramentos[] = trim($stDesdobramento);
}
public function getDesdobramentos()
{
    return $this->arDesdobramentos;
}

private function getExcluirCalculados()
{
    if ($this->getTipoFiltro() == "recalcular" AND $this->getTipoFolha() != "F") {
        return false;
    } else {
        return true;
    }
}

//adicionada flag para setar que a função está sendo chamada na recisão do contrato
public function calcularFolha($boRecisaoContrato = false, $boTransacao="")
{
    $obErro = new erro;
    $obTransacao = new Transacao;
    
    $rsContratos = $this->getCodContratosFiltro();
    if ($this->getExcluirCalculados()) {
        $obErro = $this->detetarInformacoesDoCalculo($rsContratos);
        $rsContratos->setPrimeiroElemento();
    }

    if ($rsContratos->getNumLinhas() < 0) {
        $obErro->setDescricao("Não há contratos a serem calculados.");
    }

    $inNumContratos = $rsContratos->getNumLinhas();
    Sessao::write("inContratosCalculados",$inNumContratos);
    $inCalculados   = 0;

    $stLocationCancel = "LS".$this->getNameProgram().".php?".Sessao::getId();
    $stLocationCancel = $this->getNameDiretorio().$stLocationCancel;

    foreach ($this->getDesdobramentos() as $stDesdobramento) {
        while (!$rsContratos->eof() && !$obErro->ocorreu()) {
            $boFlagTransacao = false;
            $boTransacao = "";
            $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $nuPorcentagem  = number_format(($inCalculados*100/$inNumContratos), 2, ',', ' ');
                $stMensagem = "Calculando: ".$rsContratos->getCampo("registro")."-".$rsContratos->getCampo("nom_cgm");
                $this->percentageBar($nuPorcentagem,$stMensagem,$stLocationCancel);

                $inCodContrato = $rsContratos->getCampo("cod_contrato");
                switch ($this->getTipoFolha()) {
                    case "S":
                        include_once(CAM_GRH_FOL_MAPEAMENTO."FFolhaPagamentoCalculaFolha.class.php");
                        $obFFolhaPagamentoCalculaFolha = new FFolhaPagamentoCalculaFolha();
                        $obFFolhaPagamentoCalculaFolha->setDado('cod_contrato',$inCodContrato);
                        $obFFolhaPagamentoCalculaFolha->setDado('boErro',($this->getTipoFiltro()=="recalcular")?'t':'f');
                        $obErro = $obFFolhaPagamentoCalculaFolha->calculaFolha($rsCalcula,$boTransacao);

                        //$rsCalcula->getCampo('retorno') igual a 'f' significa que houve erro no cálculo
                        //Se isso ocorreu deverá ser atualizado a tabela como o erro que ocorreu
                        if ($this->getTipoFiltro() == "recalcular" and $rsCalcula->getNumLinhas() == -1) {
                            $stErro = $obErro->getDescricao();
                            $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );
                            $boFlagTransacao = false;
                            $boTransacao     = "";
                            $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
                            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculo.class.php");
                            $obTFolhaPagamentoLogErroCalculo = new TFolhaPagamentoLogErroCalculo;
                            $stFiltro = " AND registro_evento_periodo.cod_contrato = ".$inCodContrato;
                            $obErro = $obTFolhaPagamentoLogErroCalculo->recuperaLogErroCalculo($rsLogErro,$stFiltro,"",$boTransacao);
                            if ( !$obErro->ocorreu() ) {
                                $obTFolhaPagamentoLogErroCalculo->setDado('cod_evento',$rsLogErro->getCampo('cod_evento'));
                                $obTFolhaPagamentoLogErroCalculo->setDado('cod_registro',$rsLogErro->getCampo('cod_registro'));
                                $obTFolhaPagamentoLogErroCalculo->setDado('cod_configuracao',$rsLogErro->getCampo('cod_configuracao'));
                                $obTFolhaPagamentoLogErroCalculo->setDado('desdobramento',$rsLogErro->getCampo('desdobramento'));
                                $obTFolhaPagamentoLogErroCalculo->setDado('erro',substr($stErro,0,2000));
                                $obErro = $obTFolhaPagamentoLogErroCalculo->alteracao($boTransacao);
                            }
                        }
                        break;
                    case "C":
                        Sessao::write("inCodComplementar",$this->getCodComplementar());
                        include_once(CAM_GRH_FOL_MAPEAMENTO."FFolhaPagamentoCalculaFolhaComplementar.class.php");
                        $obFFolhaPagamentoCalculaFolhaComplementar = new FFolhaPagamentoCalculaFolhaComplementar();
                        $obFFolhaPagamentoCalculaFolhaComplementar->setDado('inCodContrato'       ,$inCodContrato);
                        $obFFolhaPagamentoCalculaFolhaComplementar->setDado('inCodComplementar'   ,$this->getCodComplementar());
                        $obFFolhaPagamentoCalculaFolhaComplementar->setDado('boErro',($this->getTipoFiltro()=="recalcular")?'t':'f');
                        $obErro = $obFFolhaPagamentoCalculaFolhaComplementar->calculaFolhaComplementar($rsCalcula,$boTransacao);

                        if ($this->getTipoFiltro() == "recalcular" and $rsCalcula->getNumLinhas() == -1) {
                            $stErro = $obErro->getDescricao();
                            $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );
                            $boFlagTransacao = false;
                            $boTransacao     = "";
                            $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
                            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculoComplementar.class.php");
                            $obTFolhaPagamentoLogErroCalculoComplementar = new TFolhaPagamentoLogErroCalculoComplementar;
                            $stFiltro  = " AND cod_contrato = ".$inCodContrato;
                            $stFiltro .= " AND cod_complementar = ".$this->getCodComplementar();
                            $obErro = $obTFolhaPagamentoLogErroCalculoComplementar->recuperaLogErroCalculadoComplementar($rsLogErro,$stFiltro,"",$boTransacao);
                            if ( !$obErro->ocorreu() ) {
                                $obTFolhaPagamentoLogErroCalculoComplementar->setDado("cod_registro",$rsLogErro->getCampo("cod_registro"));
                                $obTFolhaPagamentoLogErroCalculoComplementar->setDado("cod_evento",$rsLogErro->getCampo("cod_evento"));
                                $obTFolhaPagamentoLogErroCalculoComplementar->setDado("timestamp",$rsLogErro->getCampo("timestamp"));
                                $obTFolhaPagamentoLogErroCalculoComplementar->setDado("cod_configuracao",$rsLogErro->getCampo("cod_configuracao"));
                                $obTFolhaPagamentoLogErroCalculoComplementar->setDado("erro",substr($stErro,0,2000));
                                $obErro = $obTFolhaPagamentoLogErroCalculoComplementar->alteracao($boTransacao);
                            }
                        }
                        break;
                    case "F":
                        include_once(CAM_GRH_FOL_MAPEAMENTO."FFolhaPagamentoCalculaFolhaFerias.class.php");
                        $obFFolhaPagamentoCalculaFolhaFerias = new FFolhaPagamentoCalculaFolhaFerias();
                        $obFFolhaPagamentoCalculaFolhaFerias->setDado('cod_contrato',$inCodContrato);
                        $obFFolhaPagamentoCalculaFolhaFerias->setDado('boErro',($this->getTipoFiltro()=="recalcular")?'t':'f');
                        $obErro = $obFFolhaPagamentoCalculaFolhaFerias->calculaFolhaFerias($rsCalcula,$boTransacao);
                        break;
                    case "D":
                        include_once(CAM_GRH_FOL_MAPEAMENTO."FFolhaPagamentoCalculaFolhaDecimo.class.php");
                        $obFFolhaPagamentoCalculaFolhaDecimo = new FFolhaPagamentoCalculaFolhaDecimo();
                        $obFFolhaPagamentoCalculaFolhaDecimo->setDado('cod_contrato',$inCodContrato);
                        $obFFolhaPagamentoCalculaFolhaDecimo->setDado('desdobramento',$stDesdobramento);
                        $obFFolhaPagamentoCalculaFolhaDecimo->setDado('boErro',($this->getTipoFiltro()=="recalcular")?'t':'f');
                        $obErro = $obFFolhaPagamentoCalculaFolhaDecimo->calculaFolhaDecimo($rsCalcula,$boTransacao);

                        //$rsCalculaDecimo->getCampo('retorno') igual a 'f' significa que houve erro no cálculo
                        //Se isso ocorreu deverá ser atualizado a tabela como o erro que ocorreu
                        if ($this->getTipoFiltro() == "recalcular" and $rsCalcula->getNumLinhas() == -1) {
                            $stErro = $obErro->getDescricao();
                            $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );
                            $boFlagTransacao = false;
                            $boTransacao     = "";
                            $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
                            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculoDecimo.class.php");
                            $obTFolhaPagamentoLogErroCalculoDecimo = new TFolhaPagamentoLogErroCalculoDecimo;
                            $stFiltro = " AND registro_evento_Decimo.cod_contrato = ".$inCodContrato;
                            $obErro = $obTFolhaPagamentoLogErroCalculoDecimo->recuperaErrosDoContrato($rsLogErro,$stFiltro,"",$boTransacao);
                            if ( !$obErro->ocorreu() ) {
                                $obTFolhaPagamentoLogErroCalculoDecimo->setDado('cod_evento',$rsLogErro->getCampo('cod_evento'));
                                $obTFolhaPagamentoLogErroCalculoDecimo->setDado('cod_registro',$rsLogErro->getCampo('cod_registro'));
                                $obTFolhaPagamentoLogErroCalculoDecimo->setDado('cod_configuracao',$rsLogErro->getCampo('cod_configuracao'));
                                $obTFolhaPagamentoLogErroCalculoDecimo->setDado('desdobramento',$rsLogErro->getCampo('desdobramento'));
                                $obTFolhaPagamentoLogErroCalculoDecimo->setDado('erro',$stErro);
                                $obErro = $obTFolhaPagamentoLogErroCalculoDecimo->alteracao($boTransacao);
                            }
                        }
                        break;
                    case "R":
                        include_once(CAM_GRH_FOL_MAPEAMENTO."FFolhaPagamentoCalculaFolhaRescisao.class.php");
                        $obFFolhaPagamentoCalculaFolhaRescisao = new FFolhaPagamentoCalculaFolhaRescisao();
                        $obFFolhaPagamentoCalculaFolhaRescisao->setDado('cod_contrato',$inCodContrato);
                        $obFFolhaPagamentoCalculaFolhaRescisao->setDado('boErro',($this->getTipoFiltro()=="recalcular")?'t':'f');
                        $obErro = $obFFolhaPagamentoCalculaFolhaRescisao->calculaFolhaRescisao($rsCalcula,$boTransacao);

                        //$rsCalculaRescisao->getCampo('retorno') igual a 'f' significa que houve erro no cálculo
                        //Se isso ocorreu deverá ser atualizado a tabela como o erro que ocorreu
                        if ($this->getTipoFiltro() == "recalcular" and $rsCalcula->getNumLinhas() == -1) {
                            $stErro = $obErro->getDescricao();
                            $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );
                            $boFlagTransacao = false;
                            $boTransacao     = "";
                            $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
                            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculoRescisao.class.php");
                            $obTFolhaPagamentoLogErroCalculoRescisao = new TFolhaPagamentoLogErroCalculoRescisao;
                            $stFiltro = " AND registro_evento_Rescisao.cod_contrato = ".$inCodContrato;
                            $obErro = $obTFolhaPagamentoLogErroCalculoRescisao->recuperaErrosDoContrato($rsLogErro,$stFiltro,"",$boTransacao);
                            if ( !$obErro->ocorreu() ) {
                                $obTFolhaPagamentoLogErroCalculoRescisao->setDado('cod_evento',$rsLogErro->getCampo('cod_evento'));
                                $obTFolhaPagamentoLogErroCalculoRescisao->setDado('cod_registro',$rsLogErro->getCampo('cod_registro'));
                                $obTFolhaPagamentoLogErroCalculoRescisao->setDado('cod_configuracao',$rsLogErro->getCampo('cod_configuracao'));
                                $obTFolhaPagamentoLogErroCalculoRescisao->setDado('desdobramento',$rsLogErro->getCampo('desdobramento'));
                                $obTFolhaPagamentoLogErroCalculoRescisao->setDado('erro',$stErro);
                                $obErro = $obTFolhaPagamentoLogErroCalculoRescisao->alteracao($boTransacao);
                            }
                        }
                        break;
                }

                $inCalculados++;

                if ($inCalculados == $inNumContratos) {
                    $this->percentageBar("100", "Cálculo Finalizado");
                }
            }
            $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );
            $rsContratos->proximo();
        }
    }

    if ( !$obErro->ocorreu() ) {
        if (!$boRecisaoContrato) {
            $pgList = "LS".$this->getNameProgram().".php?".Sessao::getId();
            SistemaLegado::alertaAviso($pgList,"Cálculo concluído","incluir","aviso", Sessao::getId(), "../");
        }else{
            SistemaLegado::LiberaFrames();    
        }
    } else {
        SistemaLegado::LiberaFrames();
        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
    }
}

private function getCodContratosFiltro()
{
    $rsContratos = new RecordSet;

    include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php";
    $obTFolhPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $obTFolhPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);

    include_once CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php";
    $obTPessoalContrato = new TPessoalContrato();
    $obTPessoalContrato->setDado("inCodPeriodoMovimentacao", $rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
    $obTPessoalContrato->setDado("dtFinalCompetencia"      , $rsPeriodoMovimentacao->getCampo("dt_final"));

    switch ($this->getTipoFolha()) {
        case "S":
            $obTPessoalContrato->setDado("boAtivos"      , true);
            $obTPessoalContrato->setDado("boAposentados" , true);
            $obTPessoalContrato->setDado("boPensionistas", true);
            $obTPessoalContrato->setDado("stTipoFolha"   , "S");
            break;
        case "C":
            $obTPessoalContrato->setDado("boAtivos"          , true);
            $obTPessoalContrato->setDado("boAposentados"     , true);
            $obTPessoalContrato->setDado("boRescindidos"     , true);
            $obTPessoalContrato->setDado("boPensionistas"    , true);
            $obTPessoalContrato->setDado("stTipoFolha"       , "C");
            $obTPessoalContrato->setDado("inCodComplementar" , $this->getCodComplementar());
            break;
        case "F":
            $obTPessoalContrato->setDado("boAtivos"          , true);
            $obTPessoalContrato->setDado("stTipoFolha"       , "F");
            break;
        case "D":
            $obTPessoalContrato->setDado("boAtivos"          , true);
            $obTPessoalContrato->setDado("boAposentados"     , true);
            $obTPessoalContrato->setDado("boPensionistas"    , true);
            $obTPessoalContrato->setDado("stTipoFolha"       , "D");
            break;
        case "R":
            $obTPessoalContrato->setDado("boRescindidos"     , true);
            $obTPessoalContrato->setDado("stTipoFolha"       , "R");
            break;
    }

    $stFiltro = "";
    $stOrdem  = " ORDER BY nom_cgm, numcgm";
    switch ($this->getTipoFiltro()) {
        case 'contrato':
        case 'cgm_contrato':
        case 'contrato_rescisao':
        case 'cgm_contrato_rescisao':
        case 'contrato_todos':
        case 'cgm_contrato_todos':
            $rsContratos->preenche($this->getCodigos());
            $rsContratos = $this->adicionarContratoAutomaticos($rsContratos);
            break;
        case 'local':
            $inCodLocal = implode(",",$this->getCodigos());
            $obTPessoalContrato->setDado("inCodLocal", $inCodLocal);
            $obTPessoalContrato->recuperaContratosCalculoFolha($rsContratos, $stFiltro, $stOrdem);
            break;
        case 'lotacao':
            $inCodLotacao = implode(",",$this->getCodigos());
            $obTPessoalContrato->setDado("inCodLotacao", $inCodLotacao);
            $obTPessoalContrato->recuperaContratosCalculoFolha($rsContratos, $stFiltro, $stOrdem);
            break;
        case 'geral':
            $obTPessoalContrato->recuperaContratosCalculoFolha($rsContratos, $stFiltro, $stOrdem);
            break;
        case "recalcular":
            $rsContratos = Sessao::read("rsRecalcular");
            Sessao::remove("rsRecalcular");
            break;
        case "evento":
            $inCodEvento = implode(",",$this->getCodigos());
            $obTPessoalContrato->setDado("inCodEvento", $inCodEvento);
            $obTPessoalContrato->recuperaContratosCalculoFolha($rsContratos, $stFiltro, $stOrdem);
            break;
    }

    Sessao::write("rsContratos",$rsContratos);

    return $rsContratos;
}

private function adicionarContratoAutomaticos($rsContratos)
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);
    $arCGMs = array();
    while (!$rsContratos->eof()) {
        $arCGMs[] = $rsContratos->getCampo("numcgm");
        $rsContratos->proximo();
    }

    $rsContratos->setPrimeiroElemento();
    $arCGMs = array_unique($arCGMs);
    $stCGMs = implode(",",$arCGMs);

    $stOrdem = " nom_cgm,numcgm";
    switch ($this->getTipoFolha()) {
        case "S":
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEvento.class.php");
            $obTFolhaPagamentoRegistroEvento = new TFolhaPagamentoRegistroEvento();
            $obTFolhaPagamentoRegistroEvento->setDado("cod_periodo_movimentacao",$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
            $obTFolhaPagamentoRegistroEvento->setDado("numcgm",$stCGMs);
            $obTFolhaPagamentoRegistroEvento->recuperaContratosAutomaticos($rsContratosAutomaticos,""," ORDER BY ".$stOrdem);
            break;
        case "C":
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoComplementar.class.php");
            $obTFolhaPagamentoRegistroEventoComplementar = new TFolhaPagamentoRegistroEventoComplementar();
            $obTFolhaPagamentoRegistroEventoComplementar->setDado("cod_periodo_movimentacao",$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
            $obTFolhaPagamentoRegistroEventoComplementar->setDado("numcgm",$stCGMs);
            $obTFolhaPagamentoRegistroEventoComplementar->setDado("cod_complementar",$this->getCodComplementar());
            $obTFolhaPagamentoRegistroEventoComplementar->recuperaContratosAutomaticos($rsContratosAutomaticos,""," ORDER BY ".$stOrdem);

            $arContratoAutomaticos = $rsContratosAutomaticos->getElementos();
            $stCodContratos = "";
            while (!$rsContratosAutomaticos->eof()) {
                $stCodContratos .= $rsContratosAutomaticos->getCampo("cod_contrato").",";
                $rsContratosAutomaticos->proximo();
            }
            $stCodContratos = substr($stCodContratos,0,strlen($stCodContratos)-1);

            $arCompetencia = explode("/",$rsPeriodoMovimentacao->getCampo("dt_final"));
            $obTFolhaPagamentoRegistroEventoComplementar->setDado("mes",$arCompetencia[1]);
            $obTFolhaPagamentoRegistroEventoComplementar->setDado("ano",$arCompetencia[2]);
            $stFiltro  = " AND sw_cgm.numcgm IN (".$stCGMs.")";
            if ($stCodContratos != "") {
                $stFiltro .= " AND contrato.cod_contrato NOT IN ($stCodContratos)";
            }
            $obTFolhaPagamentoRegistroEventoComplementar->recuperaContratosAutomaticosFerias($rsContratosAutomaticosFerias,$stFiltro);

            $arContratoAutomaticosFerias = $rsContratosAutomaticosFerias->getElementos();
            $arTemp = array_merge($arContratoAutomaticos,$arContratoAutomaticosFerias);

            $rsContratosAutomaticos = new RecordSet();
            $rsContratosAutomaticos->preenche($arTemp);
            break;
        case "F":
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoFerias.class.php");
            $obTFolhaPagamentoRegistroEventoFerias = new TFolhaPagamentoRegistroEventoFerias();
            $obTFolhaPagamentoRegistroEventoFerias->setDado("cod_periodo_movimentacao",$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
            $obTFolhaPagamentoRegistroEventoFerias->setDado("numcgm",$stCGMs);
            $obTFolhaPagamentoRegistroEventoFerias->setDado("cod_tipo",1);
            $obTFolhaPagamentoRegistroEventoFerias->recuperaContratosAutomaticos($rsContratosAutomaticos,"","Order by ".$stOrdem);
            break;
        case "D":
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoDecimo.class.php");
            $obTFolhaPagamentoRegistroEventoDecimo = new TFolhaPagamentoRegistroEventoDecimo();
            $obTFolhaPagamentoRegistroEventoDecimo->setDado("cod_periodo_movimentacao",$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
            $obTFolhaPagamentoRegistroEventoDecimo->setDado("numcgm",$stCGMs);
            $obTFolhaPagamentoRegistroEventoDecimo->recuperaContratosAutomaticos($rsContratosAutomaticos,"","Order by ".$stOrdem);
            break;
        case "R":
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoRescisao.class.php");
            $obTFolhaPagamentoRegistroEventoRescisao = new TFolhaPagamentoRegistroEventoRescisao();
            $obTFolhaPagamentoRegistroEventoRescisao->setDado("cod_periodo_movimentacao",$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
            $obTFolhaPagamentoRegistroEventoRescisao->setDado("numcgm",$stCGMs);
            $obTFolhaPagamentoRegistroEventoRescisao->recuperaContratosAutomaticos($rsContratosAutomaticos,"","Order by ".$stOrdem);
            break;
    }

    return $rsContratosAutomaticos;
}

private function detetarInformacoesDoCalculo($rsContratos)
{
    //Deleta os eventos calculados de uma única vez
    $obErro = new Erro();
    $stCodContratos = "";
    while (!$rsContratos->eof()) {
        $stCodContratos .= $rsContratos->getCampo("cod_contrato").",";
        $rsContratos->proximo();
    }
    $stCodContratos = substr($stCodContratos,0,strlen($stCodContratos)-1);
    if ( !$obErro->ocorreu() ) {
        include_once(CAM_GRH_FOL_MAPEAMENTO."FFolhaPagamentoDeletarInformacoesCalculo.class.php");
        $obFFolhaPagamentoDeletarInformacoesCalculo = new FFolhaPagamentoDeletarInformacoesCalculo();
        $obFFolhaPagamentoDeletarInformacoesCalculo->setDado("stTipoFolha"          ,$this->getTipoFolha()            );
        $obFFolhaPagamentoDeletarInformacoesCalculo->setDado("inCodComplementar"    ,$this->getCodComplementar()      );
        $obFFolhaPagamentoDeletarInformacoesCalculo->setDado("stCodContratos"       ,$stCodContratos);
        $obErro = $obFFolhaPagamentoDeletarInformacoesCalculo->deletarInformacoesCalculo($rsDeletar);
    }
    //Deleta os eventos calculados de uma única vez
    return $obErro;
}

static function percentageBar($nuPercentual,$stMensagem="",$stLocation="")
{
    $stBarra  = "<div id=\"box\" style=\"width:500px;border:2px solid #D0E4F2;height:17px;text-align:center;\">";
    $stBarra .= $nuPercentual."%";
    $stBarra .= "<div id=\"bar\" style=\"width:".str_replace(',','.',$nuPercentual)."%;background:#4A6491;height:14px;color:#fff;text-align:right;padding:3px 0px 0px 0px;margin-top:-19px\">";
    $stBarra .= "</div>";
    $stBarra .= "</div>";
    $stBarra .= "<div id=\"msgBox\">";
    $stBarra .= $stMensagem;
    $stBarra .= "</div>";

    if (trim($stLocation) != "") {
        $stJsAux  = "window.parent.window.frames[\'oculto\'].location.href=\'index.php\';";
        $stJsAux .= "window.parent.window.frames[\'telaPrincipal\'].location.href=\'".$stLocation."\';";

        $stBarra .= "<div id=\"msgBox\">";
        $stBarra .= "<a onClick=\"".$stJsAux."\" STYLE=\"font-size: 12pt; color: red; cursor: pointer;\">Cancelar</a>";
        $stBarra .= "</div>";
    }

    $stJs = "<script>";
    $stJs .= "jQuery('#loadingModal',parent.frames[2].document).attr('style','visibility:hidden;');";
    $stJs .= "jQuery('#showLoading h5',parent.frames[2].document).html('".$stBarra."');";
    $stJs .= "</script>";
    echo $stJs;
    flush();
}

public function gerarSpanSucessoErro()
{
    $stJs = '';
    $rsContratos  = Sessao::read('rsContratos');
    $stCodContratos = "";
    if ($rsContratos->getNumLinhas() > 0) {
        while (!$rsContratos->eof()) {
            $stCodContratos .= $rsContratos->getCampo("cod_contrato").",";
            $rsContratos->proximo();
        }
        $stCodContratos = substr($stCodContratos,0,strlen($stCodContratos)-1);
    }
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);

    $inErros = 0;
    $rsErros = new recordset;
    if (trim($stCodContratos) != "") {
        switch ($this->getTipoFolha()) {
            case "S":
                include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculoFerias.class.php");
                $obTFolhaPagamentoLogErroCalculoFerias = new TFolhaPagamentoLogErroCalculoFerias();
                $stFiltro  = " AND registro_evento_ferias.cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                $stFiltro .= " AND lancamento_ferias.cod_tipo = 2";
                $stFiltro .= " AND to_char(periodo_movimentacao.dt_final, 'mm/yyyy') = lancamento_ferias.mes_competencia||'/'||lancamento_ferias.ano_competencia";
                $obTFolhaPagamentoLogErroCalculoFerias->recuperaErrosDoContrato($rsErrosFerias,$stFiltro);

                if ($rsErrosFerias->getNumLinhas() == -1) {
                    $inErros = 0;
                    $arErrosFerias = array();
                } else {
                    $inErros = $rsErrosFerias->getNumLinhas();
                    $arErrosFerias = $rsErrosFerias->getElementos();
                    $arTemp = array();
                    foreach ($arErrosFerias as $arErroFerias) {
                        $arErroFerias["erro"] = $arErroFerias["erro"]." (Folha Férias)";
                        $arTemp[] = $arErroFerias;
                    }
                    $arErrosFerias = $arTemp;
                }

                include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculo.class.php");
                $obTFolhaPagamentoLogErroCalculo = new TFolhaPagamentoLogErroCalculo;
                $stFiltro = " AND cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                $obTFolhaPagamentoLogErroCalculo->recuperaErrosDoContrato($rsErros,$stFiltro," nom_cgm,numcgm");
                if ($rsErros->getNumLinhas() == -1) {
                    $inErros += 0;
                } else {
                    $inErros += $rsErros->getNumLinhas();
                }
                $arErros = array_merge($arErrosFerias,$rsErros->getElementos());
                $rsErros = new recordset();
                $rsErros->preenche($arErros);

                include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php");
                $obTFolhaPagamentoEventoCalculado = new TFolhaPagamentoEventoCalculado;
                $stFiltro = "   where cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                $stFiltro.= " AND cod_contrato IN (".$stCodContratos.")";
                $obTFolhaPagamentoEventoCalculado->recuperaContratosCalculados($rsCalculo,$stFiltro," nom_cgm,numcgm");
                break;
            case "C":
                include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoComplementarCalculado.class.php");
                $obTFolhaPagamentoEventoComplementarCalculado = new TFolhaPagamentoEventoComplementarCalculado;
                $obTFolhaPagamentoEventoComplementarCalculado->setDado("cod_periodo_movimentacao",$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
                $obTFolhaPagamentoEventoComplementarCalculado->setDado("cod_complementar",Sessao::read("inCodComplementar"));
                $obTFolhaPagamentoEventoComplementarCalculado->setDado("stCodContratos",$stCodContratos);
                $obTFolhaPagamentoEventoComplementarCalculado->recuperaContratosCalculados($rsCalculo,"","ORDER BY nom_cgm,numcgm");

                include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculoComplementar.class.php");
                $obTFolhaPagamentoLogErroCalculoComplementar = new TFolhaPagamentoLogErroCalculoComplementar();
                $obTFolhaPagamentoLogErroCalculoComplementar->recuperaContratosComErro($rsErros);
                break;
            case "F":
                include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoFeriasCalculado.class.php");
                $obTFolhaPagamentoEventoFeriasCalculado = new TFolhaPagamentoEventoFeriasCalculado;
                $stFiltro  = " WHERE cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                $stFiltro .= "   AND cod_contrato IN (".$stCodContratos.")";
                $obTFolhaPagamentoEventoFeriasCalculado->recuperaContratosCalculados($rsCalculo,$stFiltro," nom_cgm,numcgm");

                include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculoFerias.class.php");
                $obTFolhaPagamentoLogErroCalculoFerias = new TFolhaPagamentoLogErroCalculoFerias();
                $obTFolhaPagamentoLogErroCalculoFerias->recuperaContratosComErro($rsErros);
                break;
            case "D":
                include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoDecimoCalculado.class.php");
                $obTFolhaPagamentoEventoDecimoCalculado = new TFolhaPagamentoEventoDecimoCalculado;
                $stFiltro  = " WHERE cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                $stFiltro .= "   AND cod_contrato IN (".$stCodContratos.")";
                $obTFolhaPagamentoEventoDecimoCalculado->recuperaContratosCalculados($rsCalculo,$stFiltro," nom_cgm,numcgm");

                include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculoDecimo.class.php");
                $obTFolhaPagamentoLogErroCalculoDecimo = new TFolhaPagamentoLogErroCalculoDecimo();
                $obTFolhaPagamentoLogErroCalculoDecimo->recuperaContratosComErro($rsErros);
                break;
            case "R":
                include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoRescisaoCalculado.class.php");
                $obTFolhaPagamentoEventoRescisaoCalculado = new TFolhaPagamentoEventoRescisaoCalculado;
                $stFiltro  = " WHERE cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                $stFiltro .= "   AND cod_contrato IN (".$stCodContratos.")";
                $obTFolhaPagamentoEventoRescisaoCalculado->recuperaContratosCalculados($rsCalculo,$stFiltro," nom_cgm,numcgm");

                include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculoRescisao.class.php");
                $obTFolhaPagamentoLogErroCalculoRescisao = new TFolhaPagamentoLogErroCalculoRescisao();
                $obTFolhaPagamentoLogErroCalculoRescisao->recuperaContratosComErro($rsErros);
                break;
        }
    }

    if ($rsErros->getNumLinhas() == -1) {
        $inErros = 0;
    } else {
        $inErros = $rsErros->getNumLinhas();
    }

    if ($rsCalculo->getNumLinhas() == -1) {
        $inCalculados = 0;
    } else {
        $inCalculados = $rsCalculo->getNumLinhas();
    }

    Sessao::write("rsCalculo",$rsCalculo);
    Sessao::write("rsListaErro",$rsErros);
    $stJs .= "d.getElementById('inQuantContratosSucesso').innerHTML = '".$inCalculados."';    \n";
    $stJs .= "d.getElementById('inQuantContratosErro').innerHTML = '".$inErros."';    \n";
    $stJs .= $this->gerarSpanSucessoCalculo();

    return $stJs;
}

private function getNameProgram()
{
    switch ($this->getTipoFolha()) {
        case "S":
            $stPrograma = "ManterCalculoSalario";
            break;
        case "C":
            $stPrograma = "ManterCalculoFolhaComplementar";
            break;
        case "F":
            $stPrograma = "ManterCalculoFerias";
            break;
        case "R":
            $stPrograma = "ManterCalculoRescisao";
            break;
        case "D":
            $stPrograma = "ManterCalculoDecimo";
            break;
    }

    return $stPrograma;
}

private function getNameDiretorio()
{
    switch ($this->getTipoFolha()) {
        case "S":
            $stDiretorio = CAM_GRH_FOL_INSTANCIAS."movimentacaoFinanceira/";
            break;
        case "C":
            $stDiretorio = CAM_GRH_FOL_INSTANCIAS."folhaComplementar/";
            break;
        case "F":
            $stDiretorio = CAM_GRH_FOL_INSTANCIAS."ferias/";
            break;
        case "R":
            $stDiretorio = CAM_GRH_FOL_INSTANCIAS."folhaRescisao/";
            break;
        case "D":
            $stDiretorio = CAM_GRH_FOL_INSTANCIAS."decimo/";
            break;
    }

    return $stDiretorio;
}

public function gerarSpanSucessoCalculo()
{
    $_SERVER["PHP_SELF"] = str_replace("OC".$this->getNameProgram(),"LS".$this->getNameProgram(),$_SERVER["PHP_SELF"]);
    $link = Sessao::read("link");
    $_GET["pg"] = isset($link["pg"]) ? $link["pg"] : '';
    $_GET["pos"] = isset($link["pos"]) ? $link["pos"] : '';

    $rsCalculo = Sessao::read("rsCalculo");

    $obLista = new Lista;
    $obLista->setRecordSet( $rsCalculo );
    $obLista->setMostraPaginacao( true );
    $obLista->setTitulo("Matrículas Calculadas com Sucesso");

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 2 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Matrícula");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("CGM");
    $obLista->ultimoCabecalho->setWidth( 35 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Ação");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "[inContrato] [registro]" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "[numcgm] - [nom_cgm]" );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "visualizar" );
    $obLista->ultimaAcao->setFuncao("true");
    if ($this->getTipoFolha() == "C") {
        $obLista->ultimaAcao->setLink("javaScript:processarPopUp('".Sessao::read("inCodComplementar")."')");
    } else {
        $obLista->ultimaAcao->setLink("javaScript:processarPopUp()");
    }
    $obLista->ultimaAcao->addCampo( "&inCodContrato"    , "cod_contrato" );
    $obLista->ultimaAcao->addCampo( "&inRegistro"       , "registro" );
    $obLista->ultimaAcao->addCampo( "&numcgm"           , "numcgm" );
    $obLista->ultimaAcao->addCampo( "&nom_cgm"          , "nom_cgm" );
    $obLista->commitAcao();

    if (in_array($this->getTipoFolha(),array('S','F','R','D'))) {
        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "incluir" );
        $obLista->ultimaAcao->setFuncaoAjax("true");
        $obLista->ultimaAcao->setLink("javaScript:executaFuncaoAjax('imprimirFichaFinanceira')");
        $obLista->ultimaAcao->addCampo( "&cod_contrato" , "cod_contrato" );
        $obLista->ultimaAcao->addCampo( "&numcgm" , "numcgm" );
        $obLista->ultimaAcao->addCampo( "&nom_cgm" , "nom_cgm" );
        $obLista->ultimaAcao->addCampo( "&registro" , "registro" );
        $obLista->commitAcao();
    }

    $obLista->montaInnerHtml();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("btnincluir.gif","botao_imprimir.png",$stHtml);
    $stHtml = str_replace("Incluir","Imprimir",$stHtml);
    $stHtml = str_replace("OC".$this->getNameProgram(),"LS".$this->getNameProgram(),$stHtml);
    $stJs = "d.getElementById('spnSpan1').innerHTML = '".$stHtml."';    \n";

    return $stJs;
}

public function gerarSpanErroCalculo()
{
    $_SERVER["PHP_SELF"] = str_replace("OC".$this->getNameProgram(),"LS".$this->getNameProgram(),$_SERVER["PHP_SELF"]);
    $link = Sessao::read("link");
    $_GET["pg"] = isset($link["pg"]) ? $link["pg"] : '';
    $_GET["pos"] = isset($link["pos"]) ? $link["pos"] : '';

    $rsErros = Sessao::read("rsListaErro");
    $obLista = new Lista;
    $obLista->setRecordSet( $rsErros );
    $obLista->setTitulo("Matrículas com Erro no Cálculo");

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 2 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Matrícula");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("CGM");
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Evento");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Erro");
    $obLista->ultimoCabecalho->setWidth( 40 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "[inContrato] [registro]" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "[numcgm] - [nom_cgm]" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "codigo" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "erro" );
    $obLista->commitDado();

    $obLista->montaInnerHtml();
    $stHtml = $obLista->getHTML();

    $obBtnOk = new ok();
    $obBtnOk->setValue                      ( "Imprimir"                );
    $obBtnOk->obEvento->setOnclick("executaFuncaoAjax('imprimirErro');");
    if ($rsErros->getNumLinhas() == -1) {
        $obBtnOk->setDisabled(true);
    }

    $obBtnRecalcular = new ok();
    $obBtnRecalcular->setValue              ( "Recalcular"                );
    $obBtnRecalcular->setStyle("with:300");
    $obBtnRecalcular->obEvento->setOnClick("executaFuncaoAjax('recalcular');");
    if ($rsErros->getNumLinhas() == -1) {
        $obBtnRecalcular->setDisabled(true);
    }

    $obFormulario = new Formulario;
    $obFormulario->addTitulo("Para obter o erro exato do contrato pressione o botão recalcular.");
    $obFormulario->defineBarra              ( array($obBtnOk,$obBtnRecalcular),"",""     );
    $obFormulario->obJavaScript->montaJavaScript();
    $obFormulario->montaInnerHtml();

    $stHtml .= $obFormulario->getHtml();
    $stHtml = str_replace("OC".$this->getNameProgram(),"LS".$this->getNameProgram(),$stHtml);
    $stJs = "d.getElementById('spnSpan1').innerHTML = '".$stHtml."';    \n";

    return $stJs;
}

public function processarRegistroEvento()
{
    include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php"                                );
    include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php"                       );
    include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculadoDependente.class.php"             );
    include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculo.class.php"                        );
    include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoContratoServidorPeriodo.class.php"               );

    Sessao::setTrataExcecao(true);
    $obTPessoalContrato = new TPessoalContrato;
    $stFiltro = " WHERE registro = ".Sessao::read('inContrato');
    $obTPessoalContrato->recuperaTodos($rsContrato,$stFiltro);
    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao;
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsUltimaMovimentacao);

    $obTFolhaPagamentoContratoServidorPeriodo = new TFolhaPagamentoContratoServidorPeriodo;
    $obTFolhaPagamentoRegistroEventoPeriodo   = new TFolhaPagamentoRegistroEventoPeriodo;
    $obTFolhaPagamentoRegistroEventoPeriodo->obTFolhaPagamentoContratoServidorPeriodo = &$obTFolhaPagamentoContratoServidorPeriodo;
    $obTFolhaPagamentoRegistroEvento        = new TFolhaPagamentoRegistroEvento;
    $obTFolhaPagamentoRegistroEvento->obTFolhaPagamentoRegistroEventoPeriodo = &$obTFolhaPagamentoRegistroEventoPeriodo;
    $obTFolhaPagamentoUltimoRegistroEvento  = new TFolhaPagamentoUltimoRegistroEvento;
    $obTFolhaPagamentoUltimoRegistroEvento->obTFolhaPagamentoRegistroEvento = &$obTFolhaPagamentoRegistroEvento;
    $obTFolhaPagamentoRegistroEventoParcela = new TFolhaPagamentoRegistroEventoParcela;
    $obTFolhaPagamentoRegistroEventoParcela->obTFolhaPagamentoUltimoRegistroEvento = &$obTFolhaPagamentoUltimoRegistroEvento;
    $obTFolhaPagamentoLogErroCalculo = new TFolhaPagamentoLogErroCalculo;
    $obTFolhaPagamentoLogErroCalculo->obTFolhaPagamentoUltimoRegistroEvento = &$obTFolhaPagamentoUltimoRegistroEvento;
    $obTFolhaPagamentoEventoCalculado = new TFolhaPagamentoEventoCalculado;
    $obTFolhaPagamentoEventoCalculado->obTFolhaPagamentoUltimoRegistroEvento = &$obTFolhaPagamentoUltimoRegistroEvento;
    $obTFolhaPagamentoEventoCalculadoDependente = new TFolhaPagamentoEventoCalculadoDependente;
    $obTFolhaPagamentoEventoCalculadoDependente->obTFolhaPagamentoEventoCalculado = &$obTFolhaPagamentoEventoCalculado;

    $stFiltro  = "   AND cod_contrato = ".$rsContrato->getCampo("cod_contrato");
    $stFiltro .= "   AND cod_periodo_movimentacao = ".$rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao");
    $obTFolhaPagamentoUltimoRegistroEvento->recuperaRelacionamento($rsRegistroEventoPeriodo,$stFiltro);
    while (!$rsRegistroEventoPeriodo->eof()) {
        $obTFolhaPagamentoUltimoRegistroEvento->setDado("cod_registro",$rsRegistroEventoPeriodo->getCampo("cod_registro"));
        $obTFolhaPagamentoUltimoRegistroEvento->setDado("cod_evento",$rsRegistroEventoPeriodo->getCampo("cod_evento"));
        $obTFolhaPagamentoUltimoRegistroEvento->setDado("desdobramento",$rsRegistroEventoPeriodo->getCampo("desdobramento"));
        $obTFolhaPagamentoUltimoRegistroEvento->setDado("timestamp",$rsRegistroEventoPeriodo->getCampo("timestamp"));
        $obTFolhaPagamentoUltimoRegistroEvento->deletarUltimoRegistroEvento();
        $rsRegistroEventoPeriodo->proximo();
    }
    $obTFolhaPagamentoRegistroEventoPeriodo->setDado("cod_registro","");

    $obTFolhaPagamentoContratoServidorPeriodo->setDado("cod_contrato",$rsContrato->getCampo("cod_contrato"));
    $obTFolhaPagamentoContratoServidorPeriodo->setDado("cod_periodo_movimentacao",$rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao"));
    $obTFolhaPagamentoContratoServidorPeriodo->recuperaPorChave($rsContratoServidorPeriodo);
    if ( $rsContratoServidorPeriodo->getNumLinhas() < 0 ) {
        $obTFolhaPagamentoContratoServidorPeriodo->inclusao();
    }

    //Inclusão de eventos fixos
    $arEventosFixos = Sessao::read("eventosFixos");
    if (is_array($arEventosFixos)) {
        foreach ($arEventosFixos as $arEvento) {
            $stFiltro = " WHERE codigo = '".$arEvento["inCodigo"]."'";
            $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento;
            $obTFolhaPagamentoEvento->recuperaTodos($rsEvento,$stFiltro);

            $nuValor        = ( $arEvento['nuValor']        != "" ) ? $arEvento['nuValor']      : 0;
            $nuQuantidade   = ( $arEvento['nuQuantidade']   != "" ) ? $arEvento['nuQuantidade'] : 0;

            $obTFolhaPagamentoRegistroEvento->setDado("cod_evento"  ,$rsEvento->getCampo("cod_evento"));
            $obTFolhaPagamentoRegistroEvento->setDado("valor"       ,$nuValor);
            $obTFolhaPagamentoRegistroEvento->setDado("quantidade"  ,$nuQuantidade);
            $obTFolhaPagamentoRegistroEvento->setDado("proporcional",false);
            $obTFolhaPagamentoRegistroEventoPeriodo->inclusao();
            $obTFolhaPagamentoRegistroEvento->inclusao();
            $obTFolhaPagamentoUltimoRegistroEvento->inclusao();
            $obTFolhaPagamentoRegistroEventoPeriodo->setDado("cod_registro","");
        }
    }

    //Inclusão de eventos variáveis
    $arEventosVariaveis = Sessao::read("eventosVariaveis");
    if (is_array($arEventosVariaveis)) {
        foreach ($arEventosVariaveis as $arEvento) {
            $stFiltro = " WHERE codigo = '".$arEvento["inCodigo"]."'";
            $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento;
            $obTFolhaPagamentoEvento->recuperaTodos($rsEvento,$stFiltro);
                
            $nuValor        = ( $arEvento['nuValor']        != "" ) ? $arEvento['nuValor']      : 0;
            $nuQuantidade   = ( $arEvento['nuQuantidade']   != "" ) ? $arEvento['nuQuantidade'] : 0;
                
            $obTFolhaPagamentoRegistroEvento->setDado("cod_evento"  ,$rsEvento->getCampo("cod_evento"));
            $obTFolhaPagamentoRegistroEvento->setDado("valor"       ,$nuValor);
            $obTFolhaPagamentoRegistroEvento->setDado("quantidade"  ,$nuQuantidade);
            $obTFolhaPagamentoRegistroEvento->setDado("proporcional",false);
            $obTFolhaPagamentoRegistroEventoPeriodo->inclusao();
            $obTFolhaPagamentoRegistroEvento->inclusao();
            $obTFolhaPagamentoUltimoRegistroEvento->inclusao();
            if ($arEvento['inQuantidadeParc'] != "") {
                $inMesCarencia  = ( $arEvento['inMesCarencia']   != "" ) ? $arEvento['inMesCarencia'] : 0;
                
                $obTFolhaPagamentoRegistroEventoParcela->setDado("parcela"      , $arEvento['inQuantidadeParc']);
                $obTFolhaPagamentoRegistroEventoParcela->setDado("mes_carencia" , $inMesCarencia);
                $obTFolhaPagamentoRegistroEventoParcela->inclusao();
            }
            $obTFolhaPagamentoRegistroEventoPeriodo->setDado("cod_registro","");
        }
    }

    //Inclusão de eventos proporcionais
    $arEventosProporcionais = Sessao::read("eventosProporcionais");
    if (is_array($arEventosProporcionais)) {
        foreach ($arEventosProporcionais as $arEvento) {
            $stFiltro = " WHERE codigo = '".$arEvento["inCodigo"]."'";
            $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento;
            $obTFolhaPagamentoEvento->recuperaTodos($rsEvento,$stFiltro);

            $nuValor        = ( $arEvento['nuValor']        != "" ) ? $arEvento['nuValor']      : 0;
            $nuQuantidade   = ( $arEvento['nuQuantidade']   != "" ) ? $arEvento['nuQuantidade'] : 0;

            $obTFolhaPagamentoRegistroEvento->setDado("cod_evento"  ,$rsEvento->getCampo("cod_evento"));
            $obTFolhaPagamentoRegistroEvento->setDado("valor"       ,$nuValor);
            $obTFolhaPagamentoRegistroEvento->setDado("quantidade"  ,$nuQuantidade);
            $obTFolhaPagamentoRegistroEvento->setDado("proporcional",true);
            $obTFolhaPagamentoRegistroEventoPeriodo->inclusao();
            $obTFolhaPagamentoRegistroEvento->inclusao();
            $obTFolhaPagamentoUltimoRegistroEvento->inclusao();
                
            if ($arEvento['inQuantidadeParc'] != "") {
                $inMesCarencia  = ( $arEvento['inMesCarencia']   != "" ) ? $arEvento['inMesCarencia'] : 0;
                
                $obTFolhaPagamentoRegistroEventoParcela->setDado("parcela"  ,$arEvento['inQuantidadeParc']);
                $obTFolhaPagamentoRegistroEventoParcela->setDado("mes_carencia" , $inMesCarencia);
                $obTFolhaPagamentoRegistroEventoParcela->inclusao();
            }
            $obTFolhaPagamentoRegistroEventoPeriodo->setDado("cod_registro","");
        }
    }

    //Para funcionamento correto dessa PL, foi inserido no registro de evento uma verificação
    //que identifica se o contrato possui registros de eventos, caso não possua, é excluído
    //o dado da tabela folhapagamento.deducao_dependente que identifica a utilização de valor
    //de dedução de dependente.
    if (count($arEventosFixos) == 0 AND count($arEventosVariaveis) == 0 AND count($arEventosProporcionais) == 0) {
        $stFiltro = " AND contrato.registro = ".Sessao::read('inContrato');
        $obTPessoalContrato->recuperaCgmDoRegistro($rsCGM,$stFiltro);

        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoDeducaoDependente.class.php");
        $obTFolhaPagamentoDeducaoDependente = new TFolhaPagamentoDeducaoDependente();
        $obTFolhaPagamentoDeducaoDependente->setDado("numcgm",$rsCGM->getCampo("numcgm"));
        $obTFolhaPagamentoDeducaoDependente->setDado("cod_periodo_movimentacao",$rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao"));
        $obTFolhaPagamentoDeducaoDependente->setDado("cod_tipo",2);
        $obTFolhaPagamentoDeducaoDependente->exclusao();
    }
    
    Sessao::encerraExcecao();

}

public function processarPreviaCalculoSalario($arNumCGM,$stTipoFiltro)
{
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
    $this->setTipoFiltro( $stTipoFiltro );
    $this->setCodigos( $arNumCGM );
    //Verificação de configuração de tabelas.
    //Caso exista uma que não esteja configurada estoura erro.
    //BUSCA COMPETENCIA
    include_once(CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php");
    $obPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
    $obPeriodoMovimentacao->listarUltimaMovimentacao($rsUltimaMovimentacao);
    $stCompetencia = $rsUltimaMovimentacao->getCampo('dt_final');
    //VERIFICA SE EXISTE CÁLCULO DE PENSÃO ALIMENTÍCIA CONFIGURADA
    include_once ( CAM_GRH_FOL_MAPEAMENTO.'TFolhaPagamentoPensaoEvento.class.php' );
    $obTFolhaPagamentoPensaoEvento = new TFolhaPagamentoPensaoEvento;
    $obTFolhaPagamentoPensaoEvento->recuperaTodos($rsPensaoEvento);
    if ($rsPensaoEvento->getNumLinhas() < 0) {
        SistemaLegado::exibeAviso(urlencode("Configuração do Cálculo de Pensão Alimentícia inexistente!"),"n_incluir","erro");
        SistemaLegado::LiberaFrames();
        exit();
    }
    //VERIFICA SE EXISTE CÁLCULO DE FÉRIAS
    include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoFeriasEvento.class.php" );
    $obTFolhaPagamentoFeriasEvento = new TFolhaPagamentoFeriasEvento;
    $obTFolhaPagamentoFeriasEvento->recuperaTodos($rsFeriasEvento);
    if ($rsFeriasEvento->getNumLinhas() < 0) {
        SistemaLegado::exibeAviso(urlencode("Configuração do Cálculo de Férias inexistente!"),"n_incluir","erro");
        SistemaLegado::LiberaFrames();
        exit();
    }
    //VERIFICA SE EXISTE CÁLCULO DE 13º
    include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoDecimoEvento.class.php" );
    $obTFolhaPagamentoDecimoEvento = new TFolhaPagamentoDecimoEvento;
    $obTFolhaPagamentoDecimoEvento->recuperaTodos($rsDecimoEvento);
    if ($rsDecimoEvento->getNumLinhas() < 0) {
        SistemaLegado::exibeAviso(urlencode("Configuração Cálculo de 13º Salário inexistente!"),"n_incluir","erro");
        SistemaLegado::LiberaFrames();
        exit();
    }
    //VERIFICA SE O CÁLCULO PREVIDÊNCIA ESTÁ EM VIGÊNCIA
    include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPrevidenciaPrevidencia.class.php" );
    $obTFolhaPagamentoPrevidenciaPrevidencia = new TFolhaPagamentoPrevidenciaPrevidencia;
    $obTFolhaPagamentoPrevidenciaPrevidencia->recuperaTodos($rsPrevidenciaPrevidencia);
    $rsPrevidenciaPrevidencia->setUltimoElemento();
    if ($rsPrevidenciaPrevidencia->getCampo("vigencia") > $stCompetencia || $rsPrevidenciaPrevidencia->getCampo("vigencia") == "") {
        SistemaLegado::exibeAviso(urlencode("Configuração da Previdência inexistente ou não está em vigor para competência!"),"n_incluir","erro");
        SistemaLegado::LiberaFrames();
        exit();
    }
    //VERIFICA SE O CÁLCULO SALÁRIO FAMÍLIA ESTÁ EM VIGOR
    include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoSalarioFamilia.class.php" );
    $obTFolhaPagamentoSalarioFamilia = new TFolhaPagamentoSalarioFamilia;
    $obTFolhaPagamentoSalarioFamilia->recuperaTodos($rsSalarioFamilia);
    $rsSalarioFamilia->setUltimoElemento();
    if ($rsSalarioFamilia->getCampo("vigencia") > $stCompetencia || $rsSalarioFamilia->getCampo("vigencia") == "") {
        SistemaLegado::exibeAviso(urlencode("Configuração do Salário Família inexistente ou não está em vigor para competência!"),"n_incluir","erro");
        SistemaLegado::LiberaFrames();
        exit();
    }
    //VERIFICA SE O CÁLCULO IRRF ESTÁ EM VIGOR
    include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoTabelaIrrf.class.php" );
    $obTFolhaPagamentoTabelaIRRF = new TFolhaPagamentoTabelaIrrf;
    $obTFolhaPagamentoTabelaIRRF->recuperaUltimaVigencia($rsRecordset);
    if (SistemaLegado::dataToBr($rsRecordset->getCampo("vigencia")) > $stCompetencia || $rsRecordset->getCampo("vigencia") == "") {
        SistemaLegado::exibeAviso(urlencode("Configuração da Tabela IRRF inexistente ou não está em vigor para competência!"),"n_incluir","erro");
        SistemaLegado::LiberaFrames();
        exit();
    }
    //VERIFICA SE O CÁLCULO DO FGTS ESTÁ EM VIGOR
    include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoFgts.class.php" );
    $obTFolhaPagamentoFgts = new TFolhaPagamentoFgts;
    $obTFolhaPagamentoFgts->recuperaTodos($rsRecordSet);
    $rsRecordSet->setUltimoElemento();
    if ($rsRecordSet->getCampo("vigencia") > $stCompetencia || $rsRecordSet->getCampo("vigencia") == "") {
        SistemaLegado::exibeAviso(urlencode("Configuração do FGTS inexistente ou não está em vigor para competência!"),"n_incluir","erro");
        SistemaLegado::LiberaFrames();
        exit();
    }
    $this->setRecalcular(Sessao::read("rsRecalcular"));
    $this->setCalcularSalario();
    //Evitando o redirecionamento da pagina
    $this->calcularFolhaPreviaFolha();
}

public function calcularFolhaPreviaFolha()
{
    $obErro = new erro;
    $obTransacao = new Transacao;
    $rsContratos = $this->getCodContratosFiltro();
    if ($this->getExcluirCalculados()) {
        $obErro = $this->detetarInformacoesDoCalculo($rsContratos);
        $rsContratos->setPrimeiroElemento();
    }

    if ($rsContratos->getNumLinhas() < 0) {
        $obErro->setDescricao("Não há contratos a serem calculados.");
    }
    foreach ($this->getDesdobramentos() as $stDesdobramento) {
        while (!$rsContratos->eof() && !$obErro->ocorreu()) {
            $boFlagTransacao = false;
            $boTransacao = "";
            $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $inCodContrato = $rsContratos->getCampo("cod_contrato");
                
                include_once(CAM_GRH_FOL_MAPEAMENTO."FFolhaPagamentoCalculaFolha.class.php");
                $obFFolhaPagamentoCalculaFolha = new FFolhaPagamentoCalculaFolha();
                $obFFolhaPagamentoCalculaFolha->setDado('cod_contrato',$inCodContrato);
                $obFFolhaPagamentoCalculaFolha->setDado('boErro',($this->getTipoFiltro()=="recalcular")?'t':'f');
                $obErro = $obFFolhaPagamentoCalculaFolha->calculaFolha($rsCalcula,$boTransacao);

                //Se isso ocorreu deverá ser atualizado a tabela como o erro que ocorreu
                if ($this->getTipoFiltro() == "recalcular" and $rsCalcula->getNumLinhas() == -1) {
                    $stErro = $obErro->getDescricao();
                    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );
                    $boFlagTransacao = false;
                    $boTransacao     = "";
                    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
                    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculo.class.php");
                    $obTFolhaPagamentoLogErroCalculo = new TFolhaPagamentoLogErroCalculo;
                    $stFiltro = " AND registro_evento_periodo.cod_contrato = ".$inCodContrato;
                    $obErro = $obTFolhaPagamentoLogErroCalculo->recuperaLogErroCalculo($rsLogErro,$stFiltro,"",$boTransacao);
                    if ( !$obErro->ocorreu() ) {
                        $obTFolhaPagamentoLogErroCalculo->setDado('cod_evento',$rsLogErro->getCampo('cod_evento'));
                        $obTFolhaPagamentoLogErroCalculo->setDado('cod_registro',$rsLogErro->getCampo('cod_registro'));
                        $obTFolhaPagamentoLogErroCalculo->setDado('cod_configuracao',$rsLogErro->getCampo('cod_configuracao'));
                        $obTFolhaPagamentoLogErroCalculo->setDado('desdobramento',$rsLogErro->getCampo('desdobramento'));
                        $obTFolhaPagamentoLogErroCalculo->setDado('erro',substr($stErro,0,2000));
                        $obErro = $obTFolhaPagamentoLogErroCalculo->alteracao($boTransacao);
                    }
                }
            }
            $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );
            $rsContratos->proximo();
        }
    }

    if ( $obErro->ocorreu() ) {
        SistemaLegado::LiberaFrames();
        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
    } else {
        SistemaLegado::LiberaFrames();
    }
}

public function procedimentoCalculo($boTransacao="") {
    //BUSCA COMPETENCIA
    $obPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
    $obPeriodoMovimentacao->listarUltimaMovimentacao($rsUltimaMovimentacao, $boTransacao);
    
    $stCompetencia = $rsUltimaMovimentacao->getCampo('dt_final');
    
    //VERIFICA SE EXISTE CÁLCULO DE PENSÃO ALIMENTÍCIA CONFIGURADA
    $obTFolhaPagamentoPensaoEvento = new TFolhaPagamentoPensaoEvento;
    $obTFolhaPagamentoPensaoEvento->recuperaTodos($rsPensaoEvento, "", "", $boTransacao);
    
    if ($rsPensaoEvento->getNumLinhas() < 0) {
        SistemaLegado::exibeAviso(urlencode("Configuração do Cálculo de Pensão Alimentícia inexistente!"),"n_incluir","erro");
        SistemaLegado::LiberaFrames();
        exit();
    }
    
    //VERIFICA SE EXISTE CÁLCULO DE FÉRIAS
    $obTFolhaPagamentoFeriasEvento = new TFolhaPagamentoFeriasEvento;
    $obTFolhaPagamentoFeriasEvento->recuperaTodos($rsFeriasEvento, "", "", $boTransacao);
    
    if ($rsFeriasEvento->getNumLinhas() < 0) {
        SistemaLegado::exibeAviso(urlencode("Configuração do Cálculo de Férias inexistente!"),"n_incluir","erro");
        SistemaLegado::LiberaFrames();
        exit();
    }
    
    //VERIFICA SE EXISTE CÁLCULO DE 13º
    $obTFolhaPagamentoDecimoEvento = new TFolhaPagamentoDecimoEvento;
    $obTFolhaPagamentoDecimoEvento->recuperaTodos($rsDecimoEvento, "", "", $boTransacao);
    
    if ($rsDecimoEvento->getNumLinhas() < 0) {
        SistemaLegado::exibeAviso(urlencode("Configuração Cálculo de 13º Salário inexistente!"),"n_incluir","erro");
        SistemaLegado::LiberaFrames();
        exit();
    }
    
    //VERIFICA SE O CÁLCULO PREVIDÊNCIA ESTÁ EM VIGÊNCIA
    $obTFolhaPagamentoPrevidenciaPrevidencia = new TFolhaPagamentoPrevidenciaPrevidencia;
    $obTFolhaPagamentoPrevidenciaPrevidencia->recuperaTodos($rsPrevidenciaPrevidencia, "", "", $boTransacao);
    $rsPrevidenciaPrevidencia->setUltimoElemento();
    
    if ($rsPrevidenciaPrevidencia->getCampo("vigencia") > $stCompetencia || $rsPrevidenciaPrevidencia->getCampo("vigencia") == "") {
        SistemaLegado::exibeAviso(urlencode("Configuração da Previdência inexistente ou não está em vigor para competência!"),"n_incluir","erro");
        SistemaLegado::LiberaFrames();
        exit();
    }
    
    //VERIFICA SE O CÁLCULO SALÁRIO FAMÍLIA ESTÁ EM VIGOR
    $obTFolhaPagamentoSalarioFamilia = new TFolhaPagamentoSalarioFamilia;
    $obTFolhaPagamentoSalarioFamilia->recuperaTodos($rsSalarioFamilia, "", "", $boTransacao);
    
    $rsSalarioFamilia->setUltimoElemento();
    if ($rsSalarioFamilia->getCampo("vigencia") > $stCompetencia || $rsSalarioFamilia->getCampo("vigencia") == "") {
        SistemaLegado::exibeAviso(urlencode("Configuração do Salário Família inexistente ou não está em vigor para competência!"),"n_incluir","erro");
        SistemaLegado::LiberaFrames();
        exit();
    }
    
    //VERIFICA SE O CÁLCULO IRRF ESTÁ EM VIGOR
    $obTFolhaPagamentoTabelaIRRF = new TFolhaPagamentoTabelaIrrf;
    $obTFolhaPagamentoTabelaIRRF->recuperaUltimaVigencia($rsRecordset, "", "", $boTransacao);
    
    if (SistemaLegado::dataToBr($rsRecordset->getCampo("vigencia")) > $stCompetencia || $rsRecordset->getCampo("vigencia") == "") {
        SistemaLegado::exibeAviso(urlencode("Configuração da Tabela IRRF inexistente ou não está em vigor para competência!"),"n_incluir","erro");
        SistemaLegado::LiberaFrames();
        exit();
    }
    
    //VERIFICA SE O CÁLCULO DO FGTS ESTÁ EM VIGOR
    $obTFolhaPagamentoFgts = new TFolhaPagamentoFgts;
    $obTFolhaPagamentoFgts->recuperaTodos($rsRecordSet, "", "", $boTransacao);
    
    $rsRecordSet->setUltimoElemento();
    
    if ($rsRecordSet->getCampo("vigencia") > $stCompetencia || $rsRecordSet->getCampo("vigencia") == "") {
        SistemaLegado::exibeAviso(urlencode("Configuração do FGTS inexistente ou não está em vigor para competência!"),"n_incluir","erro");
        SistemaLegado::LiberaFrames();
        exit();
    }
    
    $this->setRecalcular(Sessao::read("rsRecalcular"));
    $this->setCalcularSalario();
    $this->calcularFolha(false, $boTransacao);
}


}//End Of Class
?>