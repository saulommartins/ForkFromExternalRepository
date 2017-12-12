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
    * Regra de negocio para anexo 15
    * Data de Criaçãoo   : 06/05/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Regra

    $Revision: 31751 $
    $Name$
    $Autor:$
    $Date: 2007-10-16 10:30:31 -0200 (Ter, 16 Out 2007) $

    * Casos de uso: uc-02.02.12
*/

/*
$Log$
Revision 1.18  2007/10/16 12:30:31  cako
Ticket#10368#

Revision 1.17  2007/05/23 19:48:09  luciano
#8815#

Revision 1.16  2007/04/27 20:51:31  luciano
#8815#

Revision 1.15  2007/04/23 14:32:27  luciano
#8815#

Revision 1.14  2007/04/17 20:15:01  luciano
#8815#

Revision 1.13  2007/03/08 14:28:21  rodrigo_sr
Bug #8630#

Revision 1.12  2007/03/05 17:35:49  luciano
#8389#

Revision 1.11  2006/12/11 22:21:20  cleisson
Bug #4513#

Revision 1.9  2006/07/26 13:22:03  jose.eduardo
Bug #4513#

Revision 1.8  2006/07/05 20:50:26  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO           );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"                  );
include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadeRelatorioAnexo15.class.php" );

class RContabilidadeRelatorioAnexo15 extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obTContabilidadeRelatorioAnexo15;
/**
    * @var String
    * @access Private
*/
var $stExercicio;
/**
    * @var String
    * @access Private
*/
var $stEntidades;
/**
    * @var String
    * @access Private
*/
var $stDataInicial;
/**
    * @var String
    * @access Private
*/
var $stDataFinal;
/**
    * @var String
    * @access Private
*/
var $stSituacao;

/**
     * @access Public
     * @param Object $valor
*/
function setTContabilidadeRelatorioAnexo15($valor) { $this->obTContabilidadeRelatorioAnexo15  = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setExercicio($valor) { $this->stExercicio      = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setEntidades($valor) { $this->stEntidades      = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setDataInicial($valor) { $this->stDataInicial    = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setDataFinal($valor) { $this->stDataFinal      = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setSituacao($valor) { $this->stSituacao       = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function getTContabilidadeRelatorioAnexo15() { return $this->obTContabilidadeRelatorioAnexo15; }
/**
     * @access Public
     * @param String $valor
*/
function getExercicio() { return $this->stExercicio                                 ; }
/**
     * @access Public
     * @param String $valor
*/
function getEntidades() { return $this->stEntidades                                 ; }
/**
     * @access Public
     * @param String $valor
*/
function getDataInicial() { return $this->stDataInicial                               ; }
/**
     * @access Public
     * @param String $valor
*/
function getDataFinal() { return $this->stDataFinal                                 ; }
/**
     * @access Public
     * @param String $valor
*/
function getSituacao() { return $this->stSituacao                                  ; }

/**
    * Método Construtor
    * @access Private
*/
function RContabilidadeRelatorioAnexo15()
{
    $this->obTContabilidadeRelatorioAnexo15 = new TContabilidadeRelatorioAnexo15;
    $this->obROrcamentoEntidade             = new ROrcamentoEntidade;
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordSet , $stOrder = "")
{
    $this->obTContabilidadeRelatorioAnexo15->setDado ( "exercicio"    , $this->stExercicio   );
    $this->obTContabilidadeRelatorioAnexo15->setDado ( "cod_entidade" , $this->stEntidades   );
    $this->obTContabilidadeRelatorioAnexo15->setDado ( "dt_inicial"   , $this->stDataInicial );
    $this->obTContabilidadeRelatorioAnexo15->setDado ( "dt_final"     , $this->stDataFinal   );

    // executa sql e retorna Record Set
    $obErro = $this->obTContabilidadeRelatorioAnexo15->recuperaTodos( $rsRecordSet );
    if ($this->stDataInicial == '01/01/'.$this->stExercicio AND $this->stDataFinal == '31/12/'.$this->stExercicio) {
        $obErro = $this->obTContabilidadeRelatorioAnexo15->recuperaDespesaEmpenho( $rsDespesaEmpenho );
    }

    if ( !$obErro->ocorreu() ) {

        $arDespesa   = array();
        $arReceita   = array();
        $arDespesa0  = array();
        $arDespesa1	 = array();
        $nuVlDespesa = 0;
        $nuVlReceita = 0;

        $arDespesa0[1]['descricao_receita'] = 'RECEITA ORÇAMENTÁRIA';
        $arDespesa0[1]['descricao_despesa'] = 'DESPESA ORÇAMENTÁRIA';
        $arDespesa0[1]['nivel_receita'    ] = 2;
        $arDespesa0[1]['nivel_despesa'    ] = 2;

        // Monta array com os valores das receitas e despesas
        if ( !$rsRecordSet->eof() ) {
            // Monta despesas do grupo 3, 4 e 9
            $inCount      = 2;
            $inDespesa    = 0;
            while ( substr( $rsRecordSet->getCampo( 'cod_estrutural' ), 0, 1 ) == '3' ) {
                if ( substr( $inCodEstruturalOld, 0, 5 ) != substr( $rsRecordSet->getCampo('cod_estrutural'), 0, 5 ) ) {
                        ${'arDespesa'.$inDespesa}[$inCount]['descricao_despesa'] = $rsRecordSet->getCampo('nom_conta' );
                        ${'arDespesa'.$inDespesa}[$inCount]['nivel_despesa']     = $rsRecordSet->getCampo('nivel'     );
                }

                $nuVlDespesa = bcadd( $nuVlDespesa, $rsRecordSet->getCampo('vl_arrecadado'), 4 );
                $nuVlSubTotalDespesa = bcadd( $nuVlSubTotalDespesa, $rsRecordSet->getCampo('vl_arrecadado'), 4 );

                $inCodEstruturalOld = $rsRecordSet->getCampo('cod_estrutural');
                $inNivelOld         = $rsRecordSet->getCampo('nivel'         );
                $rsRecordSet->proximo();
                if ( substr( $inCodEstruturalOld, 0, 5 ) != substr( $rsRecordSet->getCampo('cod_estrutural'), 0, 5 ) ) {
                    if ($inNivelOld > 2) {
                        ${'arDespesa'.$inDespesa}[$inCount]['vl_despesa'] = $nuVlDespesa;
                    }
                    $nuVlDespesa = 0;
                    $inCount++;
                }

                if ( substr( $inCodEstruturalOld,0,3) != substr( $rsRecordSet->getCampo('cod_estrutural'),0,3 ) ) {
                    ${'arSubTotalDespesa'.$inDespesa}[0]['descricao_despesa'] = '';
                    ${'arSubTotalDespesa'.$inDespesa}[1]['descricao_despesa'] = 'SUB-TOTAL';
                    ${'arSubTotalDespesa'.$inDespesa}[1]['vl_total_despesa']  = $nuVlSubTotalDespesa;
                    ${'arSubTotalDespesa'.$inDespesa}[2]['descricao_despesa'] = '';
                    $inDespesa++;
                    $inCount = 0;
                    $nuVlSubTotalDespesa = 0;
                    $nuVlTotalDespesa = bcadd( $nuVlTotalDespesa, $nuVlSubTotalDespesa, 4);
                }
            }

            if ($this->stDataInicial == '01/01/'.$this->stExercicio AND $this->stDataFinal == '31/12/'.$this->stExercicio) {
                $nuVlSubTotalDespesaEmpenho = 0;
                foreach ($arDespesa0 as $key => $arTemp) {
                    $rsDespesaEmpenho->setPrimeiroElemento();
                    while ( !$rsDespesaEmpenho->eof() ) {
                        if ( trim($arTemp['descricao_despesa']) == trim($rsDespesaEmpenho->getCampo('nom_conta'))) {
                            $arDespesa0[$key]['vl_despesa'] = $rsDespesaEmpenho->getCampo("valor");
                            $nuVlSubTotalDespesaEmpenho = bcadd( $nuVlSubTotalDespesaEmpenho, $rsDespesaEmpenho->getCampo("valor"), 4 );
                        }
                        $rsDespesaEmpenho->proximo();
                    }
                }
                $arSubTotalDespesa0[1]['vl_total_despesa'] = number_format($nuVlSubTotalDespesaEmpenho,4,'.','');

                $nuVlSubTotalDespesaEmpenho = 0;
                foreach ($arDespesa1 as $key => $arTemp) {
                    $rsDespesaEmpenho->setPrimeiroElemento();
                    while ( !$rsDespesaEmpenho->eof() ) {
                        if ( trim($arTemp['descricao_despesa']) == trim($rsDespesaEmpenho->getCampo('nom_conta'))) {
                            $arDespesa1[$key]['vl_despesa'] = $rsDespesaEmpenho->getCampo("valor");
                            $nuVlSubTotalDespesaEmpenho = bcadd( $nuVlSubTotalDespesaEmpenho, $rsDespesaEmpenho->getCampo("valor"), 4 );
                        }
                        $rsDespesaEmpenho->proximo();
                    }
                }
                $arSubTotalDespesa1[1]['vl_total_despesa'] = number_format($nuVlSubTotalDespesaEmpenho,'4','.','');
            }

            // Monta receitas do grupo 1, 2, 7, 8 e dedutoras (9)
            $inCount   = 2;
            $inReceita = 0;

            while ( substr( $rsRecordSet->getCampo('cod_estrutural'), 0, 1) == '4' ) {
               if ( substr( $inCodEstruturalOld, 0, 5 ) != substr( $rsRecordSet->getCampo('cod_estrutural'), 0, 5 ) ) {
                       ${'arDespesa'.$inReceita}[$inCount]['descricao_receita'] = $rsRecordSet->getCampo('nom_conta' );
                       ${'arDespesa'.$inReceita}[$inCount]['nivel_receita']     = $rsRecordSet->getCampo('nivel'     );
               }

               $nuVlReceita = bcadd( $nuVlReceita, $rsRecordSet->getCampo('vl_arrecadado'), 4 );
               $nuVlSubTotalArrecadado = bcadd( $nuVlSubTotalArrecadado, $rsRecordSet->getCampo('vl_arrecadado'), 4 );

               $inCodEstruturalOld = $rsRecordSet->getCampo('cod_estrutural');
               $inNivelOld         = $rsRecordSet->getCampo('nivel'         );
               $rsRecordSet->proximo();
               if ( substr( $inCodEstruturalOld, 0, 5 ) != substr( $rsRecordSet->getCampo('cod_estrutural'), 0, 5 ) ) {
                   if ($inNivelOld > 2) {
                       ${'arDespesa'.$inReceita}[$inCount]['vl_receita'] = $nuVlReceita;
                   }
                   $nuVlReceita = 0;
                   $inCount++;
               }

               /* MUDA DE GRUPO */
               if ( substr( $inCodEstruturalOld,0,3) != substr( $rsRecordSet->getCampo('cod_estrutural'),0,3 ) ) {
                   if ($inReceita==0) {
                       $inCountTmp = $inCount;
                       $inReceitaTmp = $inReceita;

                       $inCount47 = $inCount+1;
                   }
                   if ($inReceita==1) {
                       $inCount48 = $inCount;
                       $inReceita48 = $inReceita;
                   }

                   ${'arSubTotalDespesa'.$inReceita}[0]['descricao_receita'] = '';
                   ${'arSubTotalDespesa'.$inReceita}[1]['descricao_receita'] = 'SUB-TOTAL';
                   ${'arSubTotalDespesa'.$inReceita}[1]['vl_total_receita']  = $nuVlSubTotalArrecadado;
                   ${'arSubTotalDespesa'.$inReceita}[2]['descricao_receita'] = '';
                   $inCount = 0;
                   $inReceita++;
                   $nuVlSubTotalArrecadado = 0;
                   $nuVlTotalArrecadado = bcadd( $nuVlTotalArrecadado, $nuVlSubTotalArrecadado, 4);
               }

                /* 4.7 */
                while (substr( $rsRecordSet->getCampo('cod_estrutural'), 0, 3) == '4.7') {
                    if (substr( $rsRecordSet->getCampo('cod_estrutural'), 0, 5) == '4.7.0'  ) {
                        ${'arDespesa'.$inReceitaTmp}[$inCount47]['descricao_receita'] = $rsRecordSet->getCampo('nom_conta' );
                        ${'arDespesa'.$inReceitaTmp}[$inCount47]['nivel_receita']     = $rsRecordSet->getCampo('nivel'     );
                    }

                    ${'arDespesa'.$inReceitaTmp}[$inCount47]['vl_receita'] = bcadd(${'arDespesa'.$inReceitaTmp}[$inCount47]['vl_receita'], $rsRecordSet->getCampo('vl_arrecadado'),4);
                    $rsRecordSet->proximo();
                }
                ${'arDespesa'.$inReceitaTmp}[$inCount47]['vl_receita'] = ${'arDespesa'.$inReceitaTmp}[$inCount47]['vl_receita'];
                ${'arSubTotalDespesa'.$inReceitaTmp}[1]['vl_total_receita'] = number_format(${'arSubTotalDespesa'.$inReceitaTmp}[1]['vl_total_receita'] + ${'arDespesa'.$inReceitaTmp}[$inCount47]['vl_receita'],4,'.','');

                /* 4.8 */
                while (substr( $rsRecordSet->getCampo('cod_estrutural'), 0, 3) == '4.8') {
                    if (substr( $rsRecordSet->getCampo('cod_estrutural'), 0, 5) == '4.8.0'  ) {
                        ${'arDespesa'.$inReceita48}[$inCount48]['descricao_receita'] = $rsRecordSet->getCampo('nom_conta' );
                        ${'arDespesa'.$inReceita48}[$inCount48]['nivel_receita']     = $rsRecordSet->getCampo('nivel'     );
                    }

                    ${'arDespesa'.$inReceita48}[$inCount48]['vl_receita'] = bcadd(${'arDespesa'.$inReceita48}[$inCount48]['vl_receita'], $rsRecordSet->getCampo('vl_arrecadado'),4);
                    $rsRecordSet->proximo();
                }
                ${'arDespesa'.$inReceita48}[$inCount48]['vl_receita'] = ${'arDespesa'.$inReceita48}[$inCount48]['vl_receita'];
                ${'arSubTotalDespesa'.$inReceita48}[1]['vl_total_receita'] = number_format(${'arSubTotalDespesa'.$inReceita48}[1]['vl_total_receita'] + ${'arDespesa'.$inReceita48}[$inCount48]['vl_receita'],4,'.','');

                /* 4.9 */
                while (substr( $rsRecordSet->getCampo('cod_estrutural'), 0, 3) == '4.9') {
                    if (substr( $rsRecordSet->getCampo('cod_estrutural'), 0, 5) == '4.9.0'  ) {
                        ${'arDespesa'.$inReceitaTmp}[$inCountTmp]['descricao_receita'] = $rsRecordSet->getCampo('nom_conta' );
                        ${'arDespesa'.$inReceitaTmp}[$inCountTmp]['nivel_receita']     = $rsRecordSet->getCampo('nivel'     );
                    }

                    ${'arDespesa'.$inReceitaTmp}[$inCountTmp]['vl_receita'] = bcadd(${'arDespesa'.$inReceitaTmp}[$inCountTmp]['vl_receita'], $rsRecordSet->getCampo('vl_arrecadado'),4);
                    $rsRecordSet->proximo();
                }
                ${'arDespesa'.$inReceitaTmp}[$inCountTmp]['vl_receita'] = ${'arDespesa'.$inReceitaTmp}[$inCountTmp]['vl_receita'] * (-1);
                ${'arSubTotalDespesa'.$inReceitaTmp}[1]['vl_total_receita'] = number_format(${'arSubTotalDespesa'.$inReceitaTmp}[1]['vl_total_receita'] + ${'arDespesa'.$inReceitaTmp}[$inCountTmp]['vl_receita'],4,'.','');
            }

           if ((int) $this->stExercicio >=  2009) {
                /*9.1*/
                $inCorrente = $rsRecordSet->getCorrente();
                do {
                    if ( substr( $rsRecordSet->getCampo('cod_estrutural'), 0, 1) == '9' ) {
                        $rsRecordSet->proximo();
                        break;
                    }
                } while ( $rsRecordSet->proximo());

                while (substr( $rsRecordSet->getCampo('cod_estrutural'), 0, 3) == '9.1') {
                    if (substr( $rsRecordSet->getCampo('cod_estrutural'), 0, 5) == '9.1.0'  ) {
                                                    ${'arDespesa'.$inReceitaTmp}[$inCountTmp]['descricao_receita'] = $rsRecordSet->getCampo('nom_conta' );
                        ${'arDespesa'.$inReceitaTmp}[$inCountTmp]['nivel_receita']     = $rsRecordSet->getCampo('nivel'     );
                    }

                    ${'arDespesa'.$inReceitaTmp}[$inCountTmp]['vl_receita'] = bcadd(${'arDespesa'.$inReceitaTmp}[$inCountTmp]['vl_receita'], $rsRecordSet->getCampo('vl_arrecadado'),4);
                    $rsRecordSet->proximo();
                }
                ${'arDespesa'.$inReceitaTmp}[$inCountTmp]['vl_receita'] = ${'arDespesa'.$inReceitaTmp}[$inCountTmp]['vl_receita'] * (-1);
                ${'arSubTotalDespesa'.$inReceitaTmp}[1]['vl_total_receita'] = number_format(${'arSubTotalDespesa'.$inReceitaTmp}[1]['vl_total_receita'] + ${'arDespesa'.$inReceitaTmp}[$inCountTmp]['vl_receita'],4,'.','');
                $rsRecordSet->setCorrente($inCorrente);
            }

            // Concatena array da despesa e da receita em um unico array
            $inConta = 0;
            $arRecordSet = array();
            $inTotal = ( $inDespesa > $inReceita ) ? $inDespesa : $inReceita;

            for ($inKey = 0; $inKey <= $inTotal; $inKey++) {

                // Remove as contas zeradas
                if (is_array(${'arDespesa'.$inKey})) {
                    $keyReceita=0;
                    $keyDespesa=0;
                    foreach (${'arDespesa'.$inKey} as $key => $array) {

                        if (key_exists('vl_receita',$array) && $array['vl_receita'] != 0.0000) {
                            ${'arDespesaTmp'.$inKey}[$keyReceita]['descricao_receita'] = $array['descricao_receita'];
                            ${'arDespesaTmp'.$inKey}[$keyReceita]['nivel_receita']     = $array['nivel_receita'];
                            ${'arDespesaTmp'.$inKey}[$keyReceita]['vl_receita']        = $array['vl_receita'];
                            $keyReceita++;
                        } elseif (!key_exists('vl_receita',$array) && $array['descricao_receita'] != '') {
                                ${'arDespesaTmp'.$inKey}[$keyReceita]['descricao_receita'] = $array['descricao_receita'];
                                ${'arDespesaTmp'.$inKey}[$keyReceita]['nivel_receita']     = $array['nivel_receita'];
                                $keyReceita++;
                        }

                        if (key_exists('vl_despesa',$array) && $array['vl_despesa'] != 0.0000) {
                            ${'arDespesaTmp'.$inKey}[$keyDespesa]['descricao_despesa'] = $array['descricao_despesa'];
                            ${'arDespesaTmp'.$inKey}[$keyDespesa]['nivel_despesa']     = $array['nivel_despesa'];
                            ${'arDespesaTmp'.$inKey}[$keyDespesa]['vl_despesa']        = $array['vl_despesa'];
                            $keyDespesa++;
                        } elseif (!key_exists('vl_despesa',$array) && $array['descricao_despesa'] != '') {
                                ${'arDespesaTmp'.$inKey}[$keyDespesa]['descricao_despesa'] = $array['descricao_despesa'];
                                ${'arDespesaTmp'.$inKey}[$keyDespesa]['nivel_despesa']     = $array['nivel_despesa'];
                                $keyDespesa++;
                        }

                    }

                    $arRecordSet = array_merge_recursive( $arRecordSet, ${'arDespesaTmp'.$inKey} );
                    $arRecordSet = array_merge_recursive( $arRecordSet, ${'arSubTotalDespesa'.$inKey} );
                    $nuVlTotalArrecadado = bcadd( $nuVlTotalArrecadado, ${'arSubTotalDespesa'.$inKey}[1]['vl_total_receita'], 4 );
                    $nuVlTotalDespesa    = bcadd( $nuVlTotalDespesa   , ${'arSubTotalDespesa'.$inKey}[1]['vl_total_despesa'], 4 );
                    unset(${'arDespesa'.$inKey});
                    unset(${'arSubTotalDespesa'.$inKey});
                }
            }

            // Pula registros que não serão usados
            if (substr($rsRecordSet->getCampo('cod_estrutural'), 0, 5 ) < '5.1.2' ) {
                while ( ( ( substr( $rsRecordSet->getCampo('cod_estrutural'), 0, 5 ) < '5.1.2') && ( !$rsRecordSet->eof() ) ) ) {
                    $rsRecordSet->proximo();
                }
            }

            // Monta as interferencias
            $inConta    = count( $arRecordSet );
            $inConta2   = count( $arRecordSet );
            $nuVlInterferenciasDespesa = 0;
            while ( substr( $rsRecordSet->getCampo('cod_estrutural'), 0, 5) == '5.1.2' ) {
                if ( substr( $rsRecordSet->getCampo('cod_estrutural'), 0, 9 ) != substr( $inCodEstruturalOld, 0, 9 ) ) {
                    $arInterferencias[$inConta2]['descricao_despesa'] = $rsRecordSet->getCampo('nom_conta' );
                    $arInterferencias[$inConta2]['nivel_despesa'    ] = $rsRecordSet->getCampo('nivel'     );
                }

                $nuVlInterferenciasDespesa     = bcadd( $nuVlInterferenciasDespesa    , $rsRecordSet->getCampo('vl_arrecadado'), 4 );
                $nuVlSubTotalDespesaInterf = bcadd( $nuVlSubTotalDespesaInterf, $rsRecordSet->getCampo('vl_arrecadado'), 4 );

                $inCodEstruturalOld = $rsRecordSet->getCampo('cod_estrutural');
                $inNivelOld         = $rsRecordSet->getCampo('nivel'         );
                $rsRecordSet->proximo();
                if ( substr( $inCodEstruturalOld, 0, 9 ) != substr( $rsRecordSet->getCampo('cod_estrutural'), 0, 9 ) ) {
                    if ($inNivelOld > 4) {
                        $arInterferencias[$inConta2]['vl_despesa'] = $nuVlInterferenciasDespesa;
                    }
                    $nuVlInterferenciasDespesa = 0;
                    $inConta2++;
                }
            }

            // Mutações ( desincorporação )
            $arMutacoes[0]['descricao_despesa'] = 'MUTAÇÕES PATRIMONIAIS';
            $inCount = 1;
            while ( substr( $rsRecordSet->getCampo('cod_estrutural'), 0, 5 ) == '5.1.3' ) {
                if ( substr($inCodEstruturalOld,0,9) != substr( $rsRecordSet->getCampo('cod_estrutural'),0,9 ) ) {
                    $arMutacoes[$inCount]['descricao_despesa'] = $rsRecordSet->getCampo('nom_conta');
                    $arMutacoes[$inCount]['nivel_despesa'    ] = $rsRecordSet->getCampo('nivel'    );
                }

                $nuVlMutacao         = bcadd( $nuVlMutacao        , $rsRecordSet->getCampo('vl_arrecadado'), 4 );
                $nuVlSubTotalMutacao = bcadd( $nuVlSubTotalMutacao, $rsRecordSet->getCampo('vl_arrecadado'), 4 );

                $inCodEstruturalOld = $rsRecordSet->getCampo('cod_estrutural');
                $inNivelOld         = $rsRecordSet->getCampo('nivel'         );
                $rsRecordSet->proximo();
                if ( substr($inCodEstruturalOld,0,9) != substr( $rsRecordSet->getCampo('cod_estrutural'),0,9 ) ) {
                    if ($inNivelOld > 4) {
                        $arMutacoes[$inCount]['vl_despesa'] = $nuVlMutacao;
                    }
                    $nuVlMutacao = 0;
                    $inCount++;
                }
                $stCodEstrutural = $rsRecordSet->getCampo('cod_estrutural');
                if ( substr($stCodEstrutural,0,7) != '5.1.3.1' and substr($stCodEstrutura,0,7) != '5.1.3.0' ) {
                    break;
                }

            }

            // Pula possiveis registros que não serão usados
            if (substr($rsRecordSet->getCampo('cod_estrutural'), 0, 7 ) < '5.1.3.3' ) {
                while ( ( ( substr( $rsRecordSet->getCampo('cod_estrutural'), 0, 7 ) < '5.1.3.3' ) && ( !$rsRecordSet->eof() ) ) ) {
                    $rsRecordSet->proximo();
               }
            }

            // Mutações ( incorporação )
            while ( substr( $rsRecordSet->getCampo('cod_estrutural'), 0, 7 ) == '5.1.3.3' ) {
                if ( substr($inCodEstruturalOld,0,9) != substr( $rsRecordSet->getCampo('cod_estrutural'),0,9 ) ) {
                    $arMutacoes[$inCount]['descricao_despesa'] = $rsRecordSet->getCampo('nom_conta');
                    $arMutacoes[$inCount]['nivel_despesa'    ] = $rsRecordSet->getCampo('nivel'    );
                }

                $nuVlMutacao         = bcadd( $nuVlMutacao        , $rsRecordSet->getCampo('vl_arrecadado'), 4 );
                $nuVlSubTotalMutacao = bcadd( $nuVlSubTotalMutacao, $rsRecordSet->getCampo('vl_arrecadado'), 4 );

                $inCodEstruturalOld = $rsRecordSet->getCampo('cod_estrutural');
                $inNivelOld         = $rsRecordSet->getCampo('nivel'         );
                $rsRecordSet->proximo();
                if ( substr($inCodEstruturalOld,0,9) != substr( $rsRecordSet->getCampo('cod_estrutural'),0,9 ) ) {
                    if ($inNivelOld > 4) {
                        $arMutacoes[$inCount]['vl_despesa'] = $nuVlMutacao;
                    }
                    $nuVlMutacao = 0;
                    $inCount++;
                }

            }

            // Gera subtotal das mutações
            $arSubTotalMutacoes[0]['descricao_despesa'] = '';
            $arSubTotalMutacoes[1]['descricao_despesa'] = 'SUB-TOTAL';
            $arSubTotalMutacoes[1]['vl_total_despesa']  = $nuVlSubTotalMutacao;

            // Monta despesas extra-orcamentarias
            $arCreditoExtra[0]['descricao_despesa'] = 'INDEPENDENTES DA EXECUÇÃO ORÇAMENTÁRIA';
            $inCount = 1;
            while ( substr( $rsRecordSet->getCampo('cod_estrutural') , 0, 3 ) == '5.2' ) {
                $inNivel = ( substr( $rsRecordSet->getCampo('cod_estrutural'), 0, 5 ) != '5.2.3' ) ? 7 : 9;
                if ( substr( $inCodEstruturalOld, 0, $inNivel) != substr( $rsRecordSet->getCampo('cod_estrutural'), 0, $inNivel ) ) {
                    $arCreditoExtra[$inCount]['descricao_despesa'] = $rsRecordSet->getCampo('nom_conta');
                    $arCreditoExtra[$inCount]['nivel_despesa'    ] = $rsRecordSet->getCampo('nivel'    );
                }

                $nuVlDespesaExtra  = bcadd( $nuVlDespesaExtra , $rsRecordSet->getCampo('vl_arrecadado'), 4 );
                $nuVlSubTotalExtra = bcadd( $nuVlSubTotalExtra, $rsRecordSet->getCampo('vl_arrecadado'), 4 );

                $inCodEstruturalOld = $rsRecordSet->getCampo('cod_estrutural');
                $inNivelOld         = $rsRecordSet->getCampo('nivel'         );
                $inNivelOld  = ( $inNivel == 9 ) ? $inNivelOld-1 : $inNivelOld;
                $rsRecordSet->proximo();
                if ( substr( $inCodEstruturalOld, 0, $inNivel ) != substr( $rsRecordSet->getCampo('cod_estrutural'), 0, $inNivel ) ) {
                    if ($inNivelOld > 3) {
                        $arCreditoExtra[$inCount]['vl_despesa'] = $nuVlDespesaExtra;
                    }
                    $inCount++;
                    $nuVlDespesaExtra = 0;
                }
            }

            // Pula registros que não serão usados
            if (substr($rsRecordSet->getCampo('cod_estrutural'), 0, 5 ) < '6.1.2' ) {
                while ( ( ( substr( $rsRecordSet->getCampo('cod_estrutural'), 0, 5 ) < '6.1.2') && ( !$rsRecordSet->eof() ) ) ) {
                    $rsRecordSet->proximo();
                }
            }

            $nuVlInterferencias = 0;
            while ( substr( $rsRecordSet->getCampo('cod_estrutural'), 0, 5) == '6.1.2' ) {
                if ( substr( $rsRecordSet->getCampo('cod_estrutural'), 0, 9 ) != substr( $inCodEstruturalOld, 0, 9 ) ) {
                    $arInterferencias[$inConta]['descricao_receita'] = $rsRecordSet->getCampo('nom_conta' );
                    $arInterferencias[$inConta]['nivel_receita'    ] = $rsRecordSet->getCampo('nivel'     );
                }

                $nuVlInterferencias     = bcadd( $nuVlInterferencias    , $rsRecordSet->getCampo('vl_arrecadado'), 4 );
                $nuVlSubTotalArrecadado = bcadd( $nuVlSubTotalArrecadado, $rsRecordSet->getCampo('vl_arrecadado'), 4 );

                $inCodEstruturalOld = $rsRecordSet->getCampo('cod_estrutural');
                $inNivelOld         = $rsRecordSet->getCampo('nivel'         );
                $rsRecordSet->proximo();
                if ( substr( $inCodEstruturalOld, 0, 9 ) != substr( $rsRecordSet->getCampo('cod_estrutural'), 0, 9 ) ) {
                    if ($inNivelOld > 4) {
                        $arInterferencias[$inConta]['vl_receita'] = $nuVlInterferencias;
                    }
                    $nuVlInterferencias = 0;
                    $inConta++;
                }
            }

            // Remove as contas zeradas
             if (is_array($arInterferencias)) {
                $keyReceita=0;
                $keyDespesa=0;
                foreach ($arInterferencias as $key => $array) {

                    if (key_exists('vl_receita',$array) && $array['vl_receita'] != 0.0000) {
                       $arInterferenciasTmp[$keyReceita]['descricao_receita'] = $array['descricao_receita'];
                       $arInterferenciasTmp[$keyReceita]['nivel_receita']     = $array['nivel_receita'];
                       $arInterferenciasTmp[$keyReceita]['vl_receita']        = $array['vl_receita'];
                        $keyReceita++;
                    } elseif (!key_exists('vl_receita',$array) && $array['descricao_receita'] != '') {
                           $arInterferenciasTmp[$keyReceita]['descricao_receita'] = $array['descricao_receita'];
                           $arInterferenciasTmp[$keyReceita]['nivel_receita']     = $array['nivel_receita'];
                            $keyReceita++;
                    }

                    if (key_exists('vl_despesa',$array) && $array['vl_despesa'] != 0.0000) {
                       $arInterferenciasTmp[$keyDespesa]['descricao_despesa'] = $array['descricao_despesa'];
                       $arInterferenciasTmp[$keyDespesa]['nivel_despesa']     = $array['nivel_despesa'];
                       $arInterferenciasTmp[$keyDespesa]['vl_despesa']        = $array['vl_despesa'];
                        $keyDespesa++;
                    } elseif (!key_exists('vl_despesa',$array)  && $array['descricao_despesa'] != '') {
                           $arInterferenciasTmp[$keyDespesa]['descricao_despesa'] = $array['descricao_despesa'];
                           $arInterferenciasTmp[$keyDespesa]['nivel_despesa']     = $array['nivel_despesa'];
                            $keyDespesa++;
                    }
                }
            }

            $arRecordSet = array_merge_recursive($arRecordSet,$arInterferenciasTmp);

            // Monta SubTotal da interferencia e Totalizador da receita
            $nuVlTotalArrecadado = bcadd( $nuVlTotalArrecadado, $nuVlSubTotalArrecadado, 4 );
            $nuVlTotalDespesa = bcadd( $nuVlTotalDespesa, $nuVlSubTotalDespesaInterf,4);
            $arRecordSet[$inConta]['descricao_receita'] = '';
            $inConta++;
            $arRecordSet[$inConta]['descricao_receita'] = 'SUB-TOTAL';
            $arRecordSet[$inConta]['vl_total_receita']  = $nuVlSubTotalArrecadado;
            $arRecordSet[$inConta]['descricao_despesa'] = 'SUB-TOTAL';
            $arRecordSet[$inConta]['vl_total_despesa']  = $nuVlSubTotalDespesaInterf;
            $nuVlSubTotalArrecadado = 0;
            $inConta++;
            $arRecordSet[$inConta]['descricao_receita'] = '';
            $inConta++;
            $arRecordSet[$inConta]['descricao_receita'] = 'TOTAL';
            $arRecordSet[$inConta]['vl_total_receita']  = $nuVlTotalArrecadado;
            $arRecordSet[$inConta]['descricao_despesa'] = 'TOTAL';
            $arRecordSet[$inConta]['vl_total_despesa']  = $nuVlTotalDespesa;
            $inConta++;
            $arRecordSet[$inConta]['descricao_receita'] = '';
            $inConta++;

            // Mutações ( desincorporação )
            $arMutacoes[0]['descricao_receita'] = 'MUTAÇÕES PATRIMONIAIS';
            $inCount = 1;
            $nuVlMutacao1 = 0;
            while ( substr( $rsRecordSet->getCampo('cod_estrutural'), 0, 5 ) == '6.1.3' ) {
                if ( substr($inCodEstruturalOld,0,9) != substr( $rsRecordSet->getCampo('cod_estrutural'),0,9 ) ) {
                    $arMutacoes[$inCount]['descricao_receita'] = $rsRecordSet->getCampo('nom_conta');
                    $arMutacoes[$inCount]['nivel_receita'    ] = $rsRecordSet->getCampo('nivel'    );
                }

                $nuVlMutacao1           = bcadd( $nuVlMutacao1           , $rsRecordSet->getCampo('vl_arrecadado'), 4 );
                $nuVlSubTotalArrecadado = bcadd( $nuVlSubTotalArrecadado, $rsRecordSet->getCampo('vl_arrecadado'), 4 );

                $inCodEstruturalOld = $rsRecordSet->getCampo('cod_estrutural');
                $inNivelOld         = $rsRecordSet->getCampo('nivel'         );
                $rsRecordSet->proximo();
                if ( substr($inCodEstruturalOld,0,9) != substr( $rsRecordSet->getCampo('cod_estrutural'),0,9 ) ) {
                    if ($inNivelOld > 4) {
                        $arMutacoes[$inCount]['vl_receita'] = $nuVlMutacao1;
                    }
                    $nuVlMutacao1 = 0;
                    $inCount++;
                }
                $stCodEstrutural = $rsRecordSet->getCampo('cod_estrutural');
                if ( substr($stCodEstrutural,0,7) != '6.1.3.1' and substr($stCodEstrutura,0,7) != '6.1.3.0' ) {
                    break;
                }

            }

            // Pula possiveis registros que não serão usados
            if (substr($rsRecordSet->getCampo('cod_estrutural'), 0, 7 ) < '6.1.3.3' ) {
                while ( ( ( substr( $rsRecordSet->getCampo('cod_estrutural'), 0, 7 ) < '6.1.3.3' ) && ( !$rsRecordSet->eof() ) ) ) {
                    $rsRecordSet->proximo();
                }
            }

            // Mutações ( incorporação )
            while ( substr( $rsRecordSet->getCampo('cod_estrutural'), 0, 7 ) == '6.1.3.3' ) {
                if ( substr($inCodEstruturalOld,0,9) != substr( $rsRecordSet->getCampo('cod_estrutural'),0,9 ) ) {
                    $arMutacoes[$inCount]['descricao_receita'] = $rsRecordSet->getCampo('nom_conta');
                    $arMutacoes[$inCount]['nivel_receita'    ] = $rsRecordSet->getCampo('nivel'    );
                }

                $nuVlMutacao1           = bcadd( $nuVlMutacao1          , $rsRecordSet->getCampo('vl_arrecadado'), 4 );
                $nuVlSubTotalArrecadado = bcadd( $nuVlSubTotalArrecadado, $rsRecordSet->getCampo('vl_arrecadado'), 4 );

                $inCodEstruturalOld = $rsRecordSet->getCampo('cod_estrutural');
                $inNivelOld         = $rsRecordSet->getCampo('nivel'         );
                $rsRecordSet->proximo();
                if ( substr($inCodEstruturalOld,0,9) != substr( $rsRecordSet->getCampo('cod_estrutural'),0,9 ) ) {
                    if ($inNivelOld > 4) {
                        $arMutacoes[$inCount]['vl_receita'] = $nuVlMutacao1;
                    }
                    $nuVlMutacao1 = 0;
                    $inCount++;
                }

            }

             // Remove as contas zeradas
             if (is_array($arMutacoes)) {
                $keyReceita=0;
                $keyDespesa=0;
                foreach ($arMutacoes as $key => $array) {

                    if (key_exists('vl_receita',$array) && $array['vl_receita'] != 0.0000) {
                        $arMutacoesTmp[$keyReceita]['descricao_receita'] = $array['descricao_receita'];
                        $arMutacoesTmp[$keyReceita]['nivel_receita']     = $array['nivel_receita'];
                        $arMutacoesTmp[$keyReceita]['vl_receita']        = $array['vl_receita'];
                        $keyReceita++;
                    } elseif (!key_exists('vl_receita',$array)) {
                            $arMutacoesTmp[$keyReceita]['descricao_receita'] = $array['descricao_receita'];
                            $arMutacoesTmp[$keyReceita]['nivel_receita']     = $array['nivel_receita'];
                            $keyReceita++;
                    }

                    if (key_exists('vl_despesa',$array) && $array['vl_despesa'] != 0.0000) {
                        $arMutacoesTmp[$keyDespesa]['descricao_despesa'] = $array['descricao_despesa'];
                        $arMutacoesTmp[$keyDespesa]['nivel_despesa']     = $array['nivel_despesa'];
                        $arMutacoesTmp[$keyDespesa]['vl_despesa']        = $array['vl_despesa'];
                        $keyDespesa++;
                    } elseif (!key_exists('vl_despesa',$array)) {
                            $arMutacoesTmp[$keyDespesa]['descricao_despesa'] = $array['descricao_despesa'];
                            $arMutacoesTmp[$keyDespesa]['nivel_despesa']     = $array['nivel_despesa'];
                            $keyDespesa++;
                    }
                }
            }

            // Gera subtotal das mutações
            $arSubTotalMutacoes[0]['descricao_receita'] = '';
            $arSubTotalMutacoes[1]['descricao_receita'] = 'SUB-TOTAL';
            $arSubTotalMutacoes[1]['vl_total_receita']  = $nuVlSubTotalArrecadado;

            // Concatena array de mutações com array de seu subtotal
            $arMutacoes = array_merge_recursive( $arMutacoesTmp, $arSubTotalMutacoes );

            // Concatena array de mutações com array principal
            $arRecordSet = array_merge_recursive( $arRecordSet, $arMutacoes );
            unset( $arMutacoes );
            unset( $arSubTotalMutacoes );

            // Monta Totalizador
            $nuVlTotalArrecadado = bcadd( $nuVlTotalArrecadado, $nuVlSubTotalArrecadado, 4 );
            $nuVlTotalDespesa    = bcadd( $nuVlTotalDespesa   , $nuVlSubTotalMutacao   , 4 );
            $nuVlSubTotalArrecadado = 0;
            $nuVlSubTotalMutacao    = 0;
            $inConta = count( $arRecordSet );
            $arRecordSet[$inConta]['descricao_receita'] = '';
            $inConta++;
            $arRecordSet[$inConta]['descricao_receita'] = 'TOTAL';
            $arRecordSet[$inConta]['vl_total_receita']  = $nuVlTotalArrecadado;
            $arRecordSet[$inConta]['descricao_despesa'] = 'TOTAL';
            $arRecordSet[$inConta]['vl_total_despesa']  = $nuVlTotalDespesa;
            $inConta++;
            $arRecordSet[$inConta]['descricao_despesa'] = '';
            $inConta++;

            // Monta receitas extra-orcamentarias
            $arCreditoExtra[0]['descricao_receita'] = 'INDEPENDENTES DA EXECUÇÃO ORÇAMENTÁRIA';
            $inCount = 1;
            while ( substr( $rsRecordSet->getCampo('cod_estrutural') , 0, 3 ) == '6.2' ) {
//                while ( substr( $rsRecordSet->getCampo('cod_estrutural'), 0, 7 ) == '6.2.3.4' ) {
//                    $rsRecordSet->proximo();
//                }
                $inNivel = ( substr( $rsRecordSet->getCampo('cod_estrutural'), 0, 5 ) != '6.2.3' ) ? 7 : 9;
                if ( substr( $inCodEstruturalOld, 0, $inNivel) != substr( $rsRecordSet->getCampo('cod_estrutural'), 0, $inNivel ) ) {
                    $arCreditoExtra[$inCount]['descricao_receita'] = $rsRecordSet->getCampo('nom_conta');
                    $arCreditoExtra[$inCount]['nivel_receita'    ] = $rsRecordSet->getCampo('nivel'    );
                }

                $nuVlReceitaExtra       = bcadd( $nuVlReceitaExtra      , $rsRecordSet->getCampo('vl_arrecadado'), 4 );
                $nuVlSubTotalArrecadado = bcadd( $nuVlSubTotalArrecadado, $rsRecordSet->getCampo('vl_arrecadado'), 4 );

                $inCodEstruturalOld = $rsRecordSet->getCampo('cod_estrutural');
                $inNivelOld         = $rsRecordSet->getCampo('nivel'         );
                $inNivelOld  = ( $inNivel == 9 ) ? $inNivelOld-1 : $inNivelOld;
                $rsRecordSet->proximo();
                if ( substr( $inCodEstruturalOld, 0, $inNivel) != substr( $rsRecordSet->getCampo('cod_estrutural'), 0, $inNivel ) ) {
                    if ($inNivelOld > 3) {
                        $arCreditoExtra[$inCount]['vl_receita'] = $nuVlReceitaExtra;
                    }
                    $inCount++;
                    $nuVlReceitaExtra = 0;
                }
            }

        }

         // Remove as contas zeradas
         if (is_array($arCreditoExtra)) {
            $keyReceita=0;
            $keyDespesa=0;
            foreach ($arCreditoExtra as $key => $array) {

                if (key_exists('vl_receita',$array) && $array['vl_receita'] != 0.0000) {
                    $arCreditoExtraTmp[$keyReceita]['descricao_receita'] = $array['descricao_receita'];
                    $arCreditoExtraTmp[$keyReceita]['nivel_receita']     = $array['nivel_receita'];
                    $arCreditoExtraTmp[$keyReceita]['vl_receita']        = $array['vl_receita'];
                    $keyReceita++;
                } elseif (!key_exists('vl_receita',$array)) {
                        $arCreditoExtraTmp[$keyReceita]['descricao_receita'] = $array['descricao_receita'];
                        $arCreditoExtraTmp[$keyReceita]['nivel_receita']     = $array['nivel_receita'];
                        $keyReceita++;
                }

                if (key_exists('vl_despesa',$array) && $array['vl_despesa'] != 0.0000) {
                    $arCreditoExtraTmp[$keyDespesa]['descricao_despesa'] = $array['descricao_despesa'];
                    $arCreditoExtraTmp[$keyDespesa]['nivel_despesa']     = $array['nivel_despesa'];
                    $arCreditoExtraTmp[$keyDespesa]['vl_despesa']        = $array['vl_despesa'];
                    $keyDespesa++;
                } elseif (!key_exists('vl_despesa',$array)) {
                        $arCreditoExtraTmp[$keyDespesa]['descricao_despesa'] = $array['descricao_despesa'];
                        $arCreditoExtraTmp[$keyDespesa]['nivel_despesa']     = $array['nivel_despesa'];
                        $keyDespesa++;
                }
            }
        }

        // Concatena array de credito extra orcamentário com array principal
        $arRecordSet = array_merge_recursive( $arRecordSet, $arCreditoExtraTmp );

        // Totaliza credito extra-orcamentário
        $inConta = count( $arRecordSet );
        $arRecordSet[$inConta]['descricao_receita'] = '';
        $inConta++;
        $arRecordSet[$inConta]['descricao_receita'] = 'SUB-TOTAL';
        $arRecordSet[$inConta]['vl_total_receita']  = $nuVlSubTotalArrecadado;
        $arRecordSet[$inConta]['descricao_despesa'] = 'SUB-TOTAL';
        $arRecordSet[$inConta]['vl_total_despesa']  = $nuVlSubTotalExtra;

        // Monta Totalizadores
        $nuVlTotalArrecadado = bcadd( $nuVlTotalArrecadado, $nuVlSubTotalArrecadado, 4 );
        $nuVlTotalDespesa    = bcadd( $nuVlTotalDespesa   , $nuVlSubTotalExtra     , 4 );
        $inConta++;
        $arRecordSet[$inConta]['descricao_receita'] = '';
        $inConta++;
        $arRecordSet[$inConta]['descricao_receita'] = 'TOTAL';
        $arRecordSet[$inConta]['vl_total_receita']  = $nuVlTotalArrecadado;
        $arRecordSet[$inConta]['descricao_despesa'] = 'TOTAL';
        $arRecordSet[$inConta]['vl_total_despesa']  = $nuVlTotalDespesa;
        $inConta++;
        $arRecordSet[$inConta]['descricao_receita'] = '';
        $inConta++;
        $arRecordSet[$inConta]['descricao_receita'] = 'TOTAL DAS VARIAÇÕES ATIVAS';
        $arRecordSet[$inConta]['vl_total_receita']  = $nuVlTotalArrecadado;
        $arRecordSet[$inConta]['descricao_despesa'] = 'TOTAL DAS VARIAÇÕES PASSIVAS';
        $arRecordSet[$inConta]['vl_total_despesa']  = $nuVlTotalDespesa;
        $inConta++;
        $arRecordSet[$inConta]['descricao_receita'] = '';
        $inConta++;
        $nuVlDeficit   = ( $nuVlTotalDespesa > $nuVlTotalArrecadado ) ? bcsub( $nuVlTotalDespesa, $nuVlTotalArrecadado, 4 ) : '0.00';
        $nuVlSuperavit = ( $nuVlTotalDespesa < $nuVlTotalArrecadado ) ? bcsub( $nuVlTotalArrecadado, $nuVlTotalDespesa, 4 ) : '0.00';
        $arRecordSet[$inConta]['descricao_receita'] = 'RESULTADO PATRIMONIAL(DÉFICIT)';
        $arRecordSet[$inConta]['vl_total_receita']  = $nuVlDeficit;
        $arRecordSet[$inConta]['descricao_despesa'] = 'RESULTADO PATRIMONIAL(SUPERÁVIT)';
        $arRecordSet[$inConta]['vl_total_despesa']  = $nuVlSuperavit;
        $inConta++;
        $arRecordSet[$inConta]['descricao_receita'] = '';
        $inConta++;
        $arRecordSet[$inConta]['descricao_receita'] = 'TOTAL GERAL';
        $arRecordSet[$inConta]['vl_total_receita']  = bcadd( $nuVlTotalArrecadado, $nuVlDeficit, 4 );
        $arRecordSet[$inConta]['descricao_despesa'] = 'TOTAL GERAL';
        $arRecordSet[$inConta]['vl_total_despesa']  = bcadd( $nuVlTotalDespesa, $nuVlSuperavit, 4 );

        // Monta Entidades Relacionadas
        $inConta++;
        $arRecordSet[$inConta]['descricao_receita'] = '';
        $inConta++;
        $arRecordSet[$inConta]['descricao_receita'] = 'ENTIDADES RELACIONADAS';
        $inConta++;
        $arEntidades = explode( ',', $this->stEntidades );
        $this->obROrcamentoEntidade->setExercicio( $this->stExercicio );
        foreach ($arEntidades as $inCodEntidade) {
            $this->obROrcamentoEntidade->setCodigoEntidade( $inCodEntidade );
            $obErro = $this->obROrcamentoEntidade->consultarNomes( $rsLista );
            if ( $obErro->ocorreu() ) {
                break;
            } else {
                $arRecordSet[$inConta]['descricao_receita'] = $rsLista->getCampo("entidade");
                $arRecordSet[$inConta]['nivel_receita'    ] = 5;
                $inConta++;
            }
        }

        $arRecordSet[$inConta]['descricao_despesa'] = '';
        $inConta++;
        $arRecordSet[$inConta]['descricao_receita'] = 'Exercicio '.$this->stExercicio;
        $arRecordSet[$inConta]['nivel_receita']     = 5;

        // Assinatura
        $inConta++;
        $arRecordSet[$inConta]['descricao_receita'] = '';
        $inConta++;
        $arRecordSet[$inConta]['descricao_receita'] = '_________________________________________________';
        $arRecordSet[$inConta]['nivel_receita'    ] = 15;
        $arRecordSet[$inConta]['descricao_despesa'] = '_________________________________________________';
        $arRecordSet[$inConta]['nivel_despesa'    ] = 15;
        $inConta++;
        $arRecordSet[$inConta]['descricao_receita'] = '            Assinatura do Representante';
        $arRecordSet[$inConta]['nivel_receita'    ] = 15;
        $arRecordSet[$inConta]['descricao_despesa'] = '     Assinatura do Contador Responsável';
        $arRecordSet[$inConta]['nivel_despesa'    ] = 15;

        $rsRecordSet = new RecordSet;
        $rsRecordSet->preenche( $arRecordSet );
    }

    return $obErro;
}
}
