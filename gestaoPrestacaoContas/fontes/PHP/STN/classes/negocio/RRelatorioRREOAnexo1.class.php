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
    * Classe de regra de relatório para Evento
    * Data de Criação:26/06/2006

    * @author Analista: Cleissom
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @package URBEM
    * @subpackage Regra de Relatório

    * Casos de uso:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE_RELATORIO                                                            );
include_once ( CAM_GPC_STN_MAPEAMENTO. 'TSTNAnexoI_Receita.class.php' );
include_once ( CAM_GPC_STN_MAPEAMENTO. 'TSTNAnexo1Despesa.class.php'  );

class RRelatorioRREOAnexo1 extends PersistenteRelatorio
{
    public $inBimestre;
    public $inExercicio;
    public $arEntidades;

    public function setBimestre($bimestre) { $this->inBimestre = $bimestre;   }
    public function setExercicio($exercicio) { $this->inExercicio = $exercicio; }

    public function getExercicio() { return $this->inExercicio; }
    public function getBimestre() { return $this->inBimestre;  }

    public function addEntidade($stEntidade) { $this->arEntidades[] = $stEntidade; }
    public function limpaEntidades() { $this->arEntidades = array();       }
    public function getEntidades() { return $this->arEntidades;          }

    public function geraRecordSet(&$arDados)
    {
        $stEntidades = '';

        if (count($this->arEntidades) >0 ) {
            $stEntidades = implode ( ',', $this->arEntidades) ;
        }
        
        SistemaLegado::periodoInicialFinalBimestre($stDtInicial, $stDtFinal, $this->getBimestre(), Sessao::getExercicio());

        $obTReceita = new TSNT_RREO_AnexoI_Receita;
        $obTReceita->setDado( 'exercicio', $this->getExercicio() );        
        $obTReceita->setDado( 'dt_inicial', $stDtInicial         );
        $obTReceita->setDado( 'dt_final'  , $stDtFinal           );
        $obTReceita->setDado( 'entidades', $stEntidades          );
        $obTReceita->recuperaTodos( $rsRecordset );
        $arDados['receita'] = $rsRecordset->arElementos;

        /// calculando totais da receita
        $arTotalReceita = array();
        $arReceita      = array();

        $arTotalCredito = array();
        $arCredito      = array();

        $arTotalReceita[0]['nom_conta']           = 'SUB TOTAL DAS RECEITAS (I)';
        $arTotalReceita[0]['previsao_inicial']    = 0;
        $arTotalReceita[0]['previsao_atualizada'] = 0;
        $arTotalReceita[0]['no_bimestre']         = 0;
        $arTotalReceita[0]['ate_bimestre']        = 0;
        $arTotalReceita[0]['a_realizar']          = 0;

        $arTotalCredito[0]['nom_conta']           = 'SUBTOTAL COM REFIBANCIAMENTO (III)= (I + II)';
        $arTotalCredito[0]['previsao_inicial']    = 0;
        $arTotalCredito[0]['previsao_atualizada'] = 0;
        $arTotalCredito[0]['no_bimestre']         = 0;
        $arTotalCredito[0]['ate_bimestre']        = 0;
        $arTotalCredito[0]['a_realizar']          = 0;

        $i = 0;

        for ($i= 0; $i<count($arDados['receita']);$i++  ) {
            switch ($arDados['receita'][$i]['nivel']) {
                case 3:
                    $arDados['receita'][$i]['nom_conta'] = '   '.$arDados['receita'][$i]['nom_conta'];
                break;
                case 4:
                    $arDados['receita'][$i]['nom_conta'] = '      '.$arDados['receita'][$i]['nom_conta'];
                break;
                case 5:
                    $arDados['receita'][$i]['nom_conta'] = '          '.$arDados['receita'][$i]['nom_conta'];
                break;
            }
            if ($arDados['receita'][$i]['grupo'] != 3) {
                $arReceita[] = $arDados['receita'][$i];
                $arTotalReceita[0]['previsao_inicial']    =  $arTotalReceita[0]['previsao_inicial']   + $arDados['receita'][$i]['previsao_inicial']   ;
                $arTotalReceita[0]['previsao_atualizada'] =  $arTotalReceita[0]['previsao_atualizada']+ $arDados['receita'][$i]['previsao_atualizada'];
                $arTotalReceita[0]['no_bimestre']         =  $arTotalReceita[0]['no_bimestre']        + $arDados['receita'][$i]['no_bimestre']        ;
                $arTotalReceita[0]['ate_bimestre']        =  $arTotalReceita[0]['ate_bimestre']       + $arDados['receita'][$i]['ate_bimestre']       ;
                $arTotalReceita[0]['a_realizar']          =  $arTotalReceita[0]['a_realizar']         + $arDados['receita'][$i]['a_realizar']         ;
            } else {
                $arCredito[] = $arDados['receita'][$i];
                //calculando o total sa seção "OPERAÇÕES DE CRÉDITO / REFINANCIAMENTO (II)"
            }
        }

        $arTotalCredito[0]['previsao_inicial']    = $arTotalReceita[0]['previsao_inicial']    + $arCredito[0]['previsao_inicial']   ;
        $arTotalCredito[0]['previsao_atualizada'] = $arTotalReceita[0]['previsao_atualizada'] + $arCredito[0]['previsao_atualizada'];
        $arTotalCredito[0]['no_bimestre']         = $arTotalReceita[0]['no_bimestre']         + $arCredito[0]['no_bimestre']        ;
        $arTotalCredito[0]['ate_bimestre']        = $arTotalReceita[0]['ate_bimestre']        + $arCredito[0]['ate_bimestre']       ;
        $arTotalCredito[0]['a_realizar']          = $arTotalReceita[0]['a_realizar']          + $arCredito[0]['a_realizar']         ;

        $arDados['receita']      = $arReceita;
        $arDados['totalReceita'] = $arTotalReceita;

        $arCredito[0]['nom_conta'] = 'OPERAÇÕES DE CRÉDITO / REFINANCIAMENTO (II)';
        $arDados['creditos']     = $arCredito;

        $arDados['totalCreditos'] = $arTotalCredito;

        $obTDespesa = new TSNT_RREO_AnexoI_Despesa;
        $obTDespesa->setDado( 'exercicio' , $this->getExercicio() );
        $obTDespesa->setDado( 'dt_inicial', $stDtInicial          );
        $obTDespesa->setDado( 'dt_final'  , $stDtFinal            );
        $obTDespesa->setDado( 'entidades', $stEntidades           );
        $obTDespesa->recuperaTodos( $rsRecordset );
        $arDados['despesas'] = $rsRecordset->arElementos;

        $i = 0;
        $arTotalDespesa = array();
        $arTotalDespesa[0]['descricao']            = 'SUBTOTAL DAS DESPESAS (VI)';
        $arTotalDespesa[0]['dotacao_inicial']      = 0;
        $arTotalDespesa[0]['creditos_adicionais']  = 0;
        $arTotalDespesa[0]['dotacao_atualizada']   = 0;
        $arTotalDespesa[0]['vl_empenhado_bimestre']= 0;
        $arTotalDespesa[0]['vl_empenhado_total']   = 0;
        $arTotalDespesa[0]['vl_liquidado_bimestre']= 0;
        $arTotalDespesa[0]['vl_liquidado_total']   = 0;
        $arTotalDespesa[0]['percentual']           = 0;
        $arTotalDespesa[0]['saldo_liquidar']       = 0;

        $arDespesas    = array();
        $arAmortizacao = array();

        for ( $i = 0 ; $i < count($arDados['despesas']); $i++ ) {
           if ($arDados['despesas'][$i]['grupo'] == 1) {
                switch ($arDados['despesas'][$i]['nivel']) {
                     case 2:
                         $arDados['despesas'][$i]['descricao'] = '   '.$arDados['despesas'][$i]['descricao'];
                     break;
                     case 3:
                         $arDados['despesas'][$i]['descricao'] = '      '.$arDados['despesas'][$i]['descricao'];
                     break;
                     case 4:
                         $arDados['despesas'][$i]['descricao'] = '          '.$arDados['despesas'][$i]['descricao'];
                     break;
                }
                $arTotalDespesa[0]['dotacao_inicial']       = $arTotalDespesa[0]['dotacao_inicial']       + $arDados['despesas'][$i]['dotacao_inicial']      ;
                $arTotalDespesa[0]['creditos_adicionais']   = $arTotalDespesa[0]['creditos_adicionais']   + $arDados['despesas'][$i]['creditos_adicionais']  ;
                $arTotalDespesa[0]['dotacao_atualizada']    = $arTotalDespesa[0]['dotacao_atualizada']    + $arDados['despesas'][$i]['dotacao_atualizada']   ;
                $arTotalDespesa[0]['vl_empenhado_bimestre'] = $arTotalDespesa[0]['vl_empenhado_bimestre'] + $arDados['despesas'][$i]['vl_empenhado_bimestre'];
                $arTotalDespesa[0]['vl_empenhado_total']    = $arTotalDespesa[0]['vl_empenhado_total']    + $arDados['despesas'][$i]['vl_empenhado_total']   ;
                $arTotalDespesa[0]['vl_liquidado_bimestre'] = $arTotalDespesa[0]['vl_liquidado_bimestre'] + $arDados['despesas'][$i]['vl_liquidado_bimestre'];
                $arTotalDespesa[0]['vl_liquidado_total']    = $arTotalDespesa[0]['vl_liquidado_total']    + $arDados['despesas'][$i]['vl_liquidado_total']   ;
                $arTotalDespesa[0]['saldo_liquidar']        = $arTotalDespesa[0]['saldo_liquidar']        + $arDados['despesas'][$i]['saldo_liquidar']       ;
                $arDespesas[] = $arDados['despesas'][$i];
           } else {
                switch ($arDados['despesas'][$i]['nivel']) {
                     case 3:
                         $arDados['despesas'][$i]['descricao'] = '   '.$arDados['despesas'][$i]['descricao'];
                     break;
                     case 5:
                         $arDados['despesas'][$i]['descricao'] = '      '.$arDados['despesas'][$i]['descricao'];
                     break;
                     case 6:
                         $arDados['despesas'][$i]['descricao'] = '          '.$arDados['despesas'][$i]['descricao'];
                     break;
                }

                $arAmortizacao[] = $arDados['despesas'][$i];
           }
        }

        $arAmortizacao[0]['descricao'] = 'AMORTIZAÇÃO DA DIVIDA / REFINANCIAMENTO (VII)';

        $arTotalRefiDesp  = array();
        $arTotalRefiDesp[0]['descricao']             = 'SUBTOTAL COM REFINANCIAMENTO (VIII) = (VI) + (VII) ';
        $arTotalRefiDesp[0]['dotacao_inicial']       = $arTotalDespesa[0]['dotacao_inicial']       + $arAmortizacao [0]['dotacao_inicial']      ;
        $arTotalRefiDesp[0]['creditos_adicionais']   = $arTotalDespesa[0]['creditos_adicionais']   + $arAmortizacao [0]['creditos_adicionais']  ;
        $arTotalRefiDesp[0]['dotacao_atualizada']    = $arTotalDespesa[0]['dotacao_atualizada']    + $arAmortizacao [0]['dotacao_atualizada']   ;
        $arTotalRefiDesp[0]['vl_empenhado_bimestre'] = $arTotalDespesa[0]['vl_empenhado_bimestre'] + $arAmortizacao [0]['vl_empenhado_bimestre'];
        $arTotalRefiDesp[0]['vl_empenhado_total']    = $arTotalDespesa[0]['vl_empenhado_total']    + $arAmortizacao [0]['vl_empenhado_total']   ;
        $arTotalRefiDesp[0]['vl_liquidado_bimestre'] = $arTotalDespesa[0]['vl_liquidado_bimestre'] + $arAmortizacao [0]['vl_liquidado_bimestre'];
        $arTotalRefiDesp[0]['vl_liquidado_total']    = $arTotalDespesa[0]['vl_liquidado_total']    + $arAmortizacao [0]['vl_liquidado_total']   ;
        $arTotalRefiDesp[0]['percentual']            = $arTotalDespesa[0]['percentual']            + $arAmortizacao [0]['percentual']           ;
        $arTotalRefiDesp[0]['saldo_liquidar']        = $arTotalDespesa[0]['saldo_liquidar']        + $arAmortizacao [0]['saldo_liquidar']       ;

        $arDados['despesas']      = $arDespesas;
        $arDados['totalDespesas'] = $arTotalDespesa;

        $arDados['amortizacao']      = $arAmortizacao;
        $arDados['totalAmortizacao'] = $arTotalRefiDesp;

        $arDados['deficit'][0]['nom_conta']           = 'DÉFICIT (IV)';
        $arDados['deficit'][0]['previsao_inicial']    = '-';
        $arDados['deficit'][0]['previsao_atualizada'] = '-';
        $arDados['deficit'][0]['no_bimestre']         = '-';
        $arDados['deficit'][0]['a_realizar']          = '-';

        $arDados['superAvit'][0]['descricao']            = 'SUPERÁVIT (IX)';
        $arDados['superAvit'][0]['dotacao_inicial']      = '-';
        $arDados['superAvit'][0]['creditos_adicionais']  = '-';
        $arDados['superAvit'][0]['dotacao_atualizada']   = '-';
        $arDados['superAvit'][0]['vl_empenhado_bimestre']= '-';
        $arDados['superAvit'][0]['vl_empenhado_total']   = '-';
        $arDados['superAvit'][0]['vl_liquidado_bimestre']= '-';
        $arDados['superAvit'][0]['percentual']           = '-';
        $arDados['superAvit'][0]['saldo_liquidar']       = '-';

        /// calculando super AVIT/DEFICIT

        if ($arTotalCredito[0]['ate_bimestre'] < $arTotalRefiDesp[0]['vl_liquidado_total']) {
            $arDados['deficit'][0]['ate_bimestre']        = ($arTotalCredito[0]['ate_bimestre'] - $arTotalRefiDesp[0]['vl_liquidado_total'] );
            $arDados['superAvit'][0]['vl_liquidado_total']   = '-';
        } else {
            $arDados['deficit'][0]['ate_bimestre']        = '-';
            $arDados['superAvit'][0]['vl_liquidado_total']   = ( $arTotalRefiDesp[0]['vl_liquidado_total'] - $arTotalCredito[0]['ate_bimestre'] );
        }

        $arDados['total'][0]['nom_conta']           = 'TOTAL (V) = (III) + (IV)';
        $arDados['total'][0]['previsao_inicial']    = $arDados['totalCreditos'][0]['previsao_inicial']   ;
        $arDados['total'][0]['previsao_atualizada'] = $arDados['totalCreditos'][0]['previsao_atualizada'];
        $arDados['total'][0]['no_bimestre']         = $arDados['totalCreditos'][0]['no_bimestre']        ;

        if ($arDados['deficit'][0]['ate_bimestre'] != '-') {
            $arDados['total'][0]['ate_bimestre'] = $arDados['totalCreditos'][0]['ate_bimestre'] + $arDados['deficit'][0]['ate_bimestre'] ;
        } else {
            $arDados['total'][0]['ate_bimestre'] = $arDados['totalCreditos'][0]['ate_bimestre'];
        }
        $arDados['total'][0]['a_realizar']          = $arDados['totalCreditos'][0]['a_realizar']         ;
        $arDados['saldo'][0]['nom_conta']           = 'SALDO DE EXERCÍCIOS ANTERIORES';
        $arDados['saldo'][0]['previsao_inicial']    = 0;
        $arDados['saldo'][0]['previsao_atualizada'] = 0;
        $arDados['saldo'][0]['no_bimestre']         = 0;
        $arDados['saldo'][0]['ate_bimestre']        = 0;
        $arDados['saldo'][0]['a_realizar']          = 0;

        $arTotal[0]['descricao']            = 'TOTAL (X) = (VIII) + (IX)';
        $arTotal[0]['dotacao_inicial']      = $arTotalRefiDesp[0]['dotacao_inicial']       ;
        $arTotal[0]['creditos_adicionais']  = $arTotalRefiDesp[0]['creditos_adicionais']   ;
        $arTotal[0]['dotacao_atualizada']   = $arTotalRefiDesp[0]['dotacao_atualizada']    ;
        $arTotal[0]['vl_empenhado_bimestre']= $arTotalRefiDesp[0]['vl_empenhado_bimestre'] ;
        $arTotal[0]['vl_empenhado_total']   = $arTotalRefiDesp[0]['vl_empenhado_total']    ;
        $arTotal[0]['vl_liquidado_bimestre']= $arTotalRefiDesp[0]['vl_liquidado_bimestre'] ;
        if ($arDados['superAvit'][0]['vl_liquidado_total'] == '-') {
            $arTotal[0]['vl_liquidado_total']   = $arTotalRefiDesp[0]['vl_liquidado_total'];
        } else {
            $arTotal[0]['vl_liquidado_total']   = $arTotalRefiDesp[0]['vl_liquidado_total'] +  $arDados['superAvit'][0]['vl_liquidado_total'] ;
        }
        $arTotal[0]['percentual']           = $arTotalRefiDesp[0]['percentual']    ;
        $arTotal[0]['saldo_liquidar']       = $arTotalRefiDesp[0]['saldo_liquidar'];
        $arDados['totalRel']         = $arTotal;
    }
}
