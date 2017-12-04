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
    * Classe de regra de negócio para Gerar Planilha de Lancamentos
    * Data de Criação: 12/08/2008

    * @author Analista: Heleno Menezes dos Santos
    * @author Desenvolvedor: Janilson Mendes P. da Silva

    * @package URBEM
    * @subpackage Regra

    * Casos de uso:

    $Id:$
*/
require_once( CAM_GT_FIS_MAPEAMENTO.'TFISProcessoFiscal.class.php' );
require_once( CAM_GT_FIS_MAPEAMENTO.'TFISProcessoLevantamento.class.php' );
require_once( CAM_GT_FIS_MAPEAMENTO.'TFISLevantamento.class.php' );
require_once( CAM_GT_FIS_MAPEAMENTO.'TFISLevantamentoAcrescimo.class.php' );
require_once( CAM_GT_FIS_MAPEAMENTO.'TFISLevantamentoCorrecao.class.php' );
require_once( CAM_GT_FIS_NEGOCIO.'RFISProcessoFiscal.class.php' );

class RFISGerarPlanilhaLancamentos extends RFISProcessoFiscal
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getRecuperarPlanilhaLancamentos()
    {
        $mapeamento = "TFISInicioFiscalizacao";
        $metodo = "recuperarInicioFiscalizacaoEconomica";

        return parent::CallMap( $mapeamento, $metodo, parent::$this->CriterioSql );
    }

    public function getRecuperaEnderecoProcessoFiscalLevantamentos()
    {
        $mapeamento = "TFISLevantamento";
        $metodo = "recuperaEnderecoProcessoFiscalLevantamentos";

        return parent::CallMap( $mapeamento, $metodo, parent::$this->CriterioSql );
    }

    public function getRecuperaProcessoFiscalTodosLevantamentos()
    {
        $mapeamento = "TFISLevantamento";
        $metodo = "recuperaProcessoFiscalTodosLevantamentos";

        return parent::CallMap( $mapeamento, $metodo, parent::$this->CriterioSql );
    }

    public function getRecuperaProcessoFiscalTotalTodosLevantamentos()
    {
        $mapeamento = "TFISLevantamento";
        $metodo = "recuperaProcessoFiscalTotalTodosLevantamentos";

        return parent::CallMap( $mapeamento, $metodo, parent::$this->CriterioSql );
    }

    public function getRecuperaProcessoFiscalTodosArrecadacao()
    {
        $mapeamento = "TFISLevantamento";
        $metodo = "recuperaProcessoFiscalTodosArrecadacao";

        return parent::CallMap( $mapeamento, $metodo, parent::$this->CriterioSql );
    }

    public function getRecuperaProcessoFiscalTotalTodosArrecadacao()
    {
        $mapeamento = "TFISLevantamento";
        $metodo = "recuperaProcessoFiscalTotalTodosArrecadacao";

        return parent::CallMap( $mapeamento, $metodo, parent::$this->CriterioSql );
    }

    public function getRecuperaFuncao()
    {
        $mapeamento = "TFISLevantamento";
        $metodo = "recuperaFuncao";

        return parent::CallMap( $mapeamento, $metodo, parent::$this->CriterioSql );
    }

    public function getRecuperaSelectFuncao()
    {
        $mapeamento = "TFISLevantamento";
        $metodo = "recuperaSelectFuncao";

        return parent::CallMap( $mapeamento, $metodo, parent::$this->CriterioSql, false );
    }

    public function getRecuperaConfiguracao()
    {
        $mapeamento = "TFISLevantamento";
        $metodo = "recuperaConfiguracao";

        return parent::CallMap( $mapeamento, $metodo, parent::$this->CriterioSql );
    }

    public function getRecuperaVencimentosParcela()
    {
        $mapeamento = "TFISLevantamento";
        $metodo = "recuperaVencimentosParcela";

        return parent::CallMap( $mapeamento, $metodo, parent::$this->CriterioSql );
    }

    public function getRecuperaValorIndicador()
    {
        $mapeamento = "TFISLevantamento";
        $metodo = "recuperaValorIndicador";

        return parent::CallMap( $mapeamento, $metodo, '', false );
    }

    public function getRecuperaIndice()
    {
        $mapeamento = "TFISLevantamento";
        $metodo = "recuperaIndice";

        return parent::CallMap( $mapeamento, $metodo, parent::$this->CriterioSql );
    }

    public function getRecuperaCodProcessoLevantamentos()
    {
        $mapeamento = "TFISLevantamento";
        $metodo = "recuperaCodProcessoLevantamentos";

        return parent::CallMap( $mapeamento, $metodo, parent::$this->CriterioSql );
    }

    public function getRecuperaIndicadorEconomico()
    {
        $mapeamento = "TFISLevantamento";
        $metodo = "recuperaIndicadorEconomico";

        return parent::CallMap( $mapeamento, $metodo, parent::$this->CriterioSql );
    }

    public function recuperarProcessoFiscalGrupo()
    {
        $mapeamento = "TFISProcessoFiscalGrupo";
        $metodo = "recuperaTodos";

        return parent::CallMap( $mapeamento, $metodo, parent::$this->CriterioSql );
    }

    public function cadastrarLevantamentos($arParam)
    {
        $inCodProcesso = $arParam['inCodProcesso'];
        $inInscricaoEconomica = $arParam['inInscricaoEconomica'];
        $flTotalGeral = $arParam['total_geral'];
        $inCount = count( $arParam['competencia'] );

        $pgProg = "PRGerarPlanilhaLancamentos.php?stCtrl=gerarPlanilhaLancamentos";
        $pgProg.= "&inCodProcesso=" . $inCodProcesso;
        $pgProg.= "&inInscricaoEconomica=" . $inInscricaoEconomica;
        $pgProg.= "&flTotalGeral=" . $flTotalGeral;

        $rsRecordSet = new RecordSet();

        $obTFISLevantamento = new TFISLevantamento();
        $obTFISLevantamentoAcrescimo = new TFISLevantamentoAcrescimo();
        $obTFISLevantamentoCorrecao = new TFISLevantamentoCorrecao();
        $obTFISProcessoLevantamento = new TFISProcessoLevantamento();
        $obTransacao = new Transacao();
        $boFlagTransacao = false;
        $boTransacao = "";

        # Inicia nova transação
            $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

        $stCondicao = " WHERE cod_processo = " . $inCodProcesso;
        $obTFISLevantamentoCorrecao->recuperaTodos( $rsRecordSet, $stCondicao );

        if ( !$rsRecordSet->Eof() ) {
            $obTFISLevantamentoCorrecao->setDado( "cod_processo", $inCodProcesso );
            $obTFISLevantamentoCorrecao->setDado( "competencia", $stCompetencia );

            $obErro = $obTFISLevantamentoCorrecao->exclusao( $boTransacao );

            if ( !$obErro->ocorreu() ) {
                $obTFISLevantamentoAcrescimo->setDado( "cod_processo", $inCodProcesso );
                    $obErro = $obTFISLevantamentoAcrescimo->exclusao( $boTransacao );

                if ( $obErro->ocorreu() ) {
                    $return = sistemaLegado::exibeAviso( "Houve um erro ao Gerar Planilha de Lançamentos.(".$inCodProcesso.")", "erro", "erro" );
                    $boTermina = true;
                    break;
                }
            } else {
                $return = sistemaLegado::exibeAviso( "Houve um erro ao Gerar Planilha de Lançamentos.(".$inCodProcesso.")", "erro", "erro" );
                $boTermina = true;
                break;
            }
        }

        unset( $rsRecordSet );

        for ($i = 0; $i < $inCount; $i++) {
            //Levantamento
            $stCompetencia = $arParam['competencia'][$i];
            $flReceitaDeclarada = $arParam['receita_declarado'][$i];
            $flReceitaEfetiva = $arParam['receita_efetivo'][$i];
            $flISSPago = $arParam['issqn_pago'][$i];
            $flISSDevido = $arParam['issqn_devido'][$i];
            $flISSDevolver = $arParam['devolver'][$i];
            $flISSPagar = $arParam['pagar'][$i];
            $flTotalDevolver = $arParam['total_devolver'][$i];
            $flTotalPagar = $arParam['total_pagar'][$i];

            //Levantamento Correcao
            $inCodIndicador = $arParam['inCodIndicador'][$i];
            $inValorCorrigido = $arParam['vl_corrigido'][$i];
            $inIndice = $arParam['indice'][$i];

            //Levantamento Acrescimo - Multa
            $inCodAcrescimoMulta = $arParam['inCodAcrescimoMulta'][$i];
            $inCodTipoMulta = $arParam['inCodTipoMulta'][$i];
            $flValorAcrescimoMulta = $arParam['multa_mora'][$i];

            //Levantamento Acrescimo - Juros
            $inCodAcrescimoJuros = $arParam['inCodAcrescimoJuros'][$i];
            $inCodTipoJuros = $arParam['inCodTipoJuros'][$i];
            $flValorAcrescimoJuros = $arParam['juros_mora'][$i];

            $rsRecordSet = new RecordSet();

            if ( $rsRecordSet->Eof() ) {

                $stCondicao = " WHERE cod_processo = " . $inCodProcesso . " AND competencia = '" . $stCompetencia . "'";
                $obTFISProcessoLevantamento->recuperaTodos( $rsRecordSet, $stCondicao );

                if ( $rsRecordSet->Eof() ) {
                    $obTFISProcessoLevantamento->setDado( "cod_processo", $inCodProcesso );
                    $obTFISProcessoLevantamento->setDado( "competencia", $stCompetencia );
                    $obErro = $obTFISProcessoLevantamento->inclusao( $boTransacao );

                    if ( $obErro->ocorreu() ) {
                        $return = sistemaLegado::exibeAviso( "Houve um erro ao Gerar Planilha de Lançamentos.(".$inCodProcesso.")", "erro", "erro" );
                        $boTermina = true;
                        break;
                    }
                }

                $obTFISLevantamento->recuperaTodos( $rsRecordSet, $stCondicao );

                $obTFISLevantamento->setDado( "cod_processo", $inCodProcesso );
                $obTFISLevantamento->setDado( "competencia", $stCompetencia );
                $obTFISLevantamento->setDado( "receita_declarada", $flReceitaDeclarada );
                $obTFISLevantamento->setDado( "receita_efetiva", $flReceitaEfetiva );
                $obTFISLevantamento->setDado( "iss_pago", $flISSPago );
                $obTFISLevantamento->setDado( "iss_devido", $flISSDevido );
                $obTFISLevantamento->setDado( "iss_devolver", $flISSDevolver );
                $obTFISLevantamento->setDado( "iss_pagar", $flISSPagar );
                $obTFISLevantamento->setDado( "total_devolver", $flTotalDevolver );
                $obTFISLevantamento->setDado( "total_pagar", $flTotalPagar );

                if ( $rsRecordSet->Eof() ) {
                    $obErro = $obTFISLevantamento->inclusao( $boTransacao );
                } else {
                    $obErro = $obTFISLevantamento->alteracao( $boTransacao );
                }

                # Ocorreu erro?
                if ( !$obErro->ocorreu() ) {

                    //Levantamento Correção
                    $obTFISLevantamentoCorrecao->setDado( "cod_processo", $inCodProcesso );
                    $obTFISLevantamentoCorrecao->setDado( "competencia", $stCompetencia );
                    $obTFISLevantamentoCorrecao->setDado( "cod_indicador", $inCodIndicador );
                    $obTFISLevantamentoCorrecao->setDado( "valor", $inValorCorrigido );
                    $obTFISLevantamentoCorrecao->setDado( "indice", $inIndice );

                    $obErro = $obTFISLevantamentoCorrecao->inclusao( $boTransacao );

                } else {
                    $return = sistemaLegado::exibeAviso( "Houve um erro ao Gerar Planilha de Lançamentos.(".$inCodProcesso.")", "erro", "erro" );
                    break;
                }

                # Ocorreu erro?
                if ( !$obErro->ocorreu() ) {
                    if ($flValorAcrescimoMulta  > 0.00) {
                        //Levantamento Acrescimo - Multa
                        $obTFISLevantamentoAcrescimo->setDado( "cod_processo", $inCodProcesso );
                        $obTFISLevantamentoAcrescimo->setDado( "competencia", $stCompetencia );
                        $obTFISLevantamentoAcrescimo->setDado( "cod_acrescimo", $inCodAcrescimoMulta );
                        $obTFISLevantamentoAcrescimo->setDado( "cod_tipo", $inCodTipoMulta );
                        $obTFISLevantamentoAcrescimo->setDado( "valor", $flValorAcrescimoMulta );

                        $obErro = $obTFISLevantamentoAcrescimo->inclusao( $boTransacao );
                    }

                    if ( !$obErro->ocorreu() ) {
                        if ($flValorAcrescimoJuros  > 0.00) {
                            //Levantamento Acrescimo - Juros
                            $obTFISLevantamentoAcrescimo->setDado( "cod_processo", $inCodProcesso );
                            $obTFISLevantamentoAcrescimo->setDado( "competencia", $stCompetencia );
                            $obTFISLevantamentoAcrescimo->setDado( "cod_acrescimo", $inCodAcrescimoJuros );
                            $obTFISLevantamentoAcrescimo->setDado( "cod_tipo", $inCodTipoJuros );
                            $obTFISLevantamentoAcrescimo->setDado( "valor", $flValorAcrescimoJuros );

                            $obErro = $obTFISLevantamentoAcrescimo->inclusao( $boTransacao );
                        }

                        if ( $obErro->ocorreu() ) {
                            $return = sistemaLegado::exibeAviso( "Houve um erro ao Gerar Planilha de Lançamentos.(".$inCodProcesso.")", "erro", "erro" );
                            $boTermina = true;
                            break;
                        }
                    } else {
                        $return = sistemaLegado::exibeAviso( "Houve um erro ao Gerar Planilha de Lançamentos.(".$inCodProcesso.")", "erro", "erro" );
                        $boTermina = true;
                        break;
                    }
                } else {
                    $return = sistemaLegado::exibeAviso( "Houve um erro ao Gerar Planilha de Lançamentos.(".$inCodProcesso.")", "erro", "erro" );
                    $boTermina = true;
                    break;
                }

                $return = sistemaLegado::alertaAviso( $pgProg , $inCodProcesso , "incluir", "aviso", Sessao::getId(), "../");
                $boTermina = true;

            } else {
                    $return = sistemaLegado::exibeAviso( "Houve um erro ao Gerar Planilha de Lançamentos.(".$inCodProcesso.")", "erro", "erro" );
                $boTermina = true;
                break;
            }

        }

        if ($boTermina) {
            # Termina transação
            $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTFISLevantamento );
        }

        return $return;
    }
}
?>
