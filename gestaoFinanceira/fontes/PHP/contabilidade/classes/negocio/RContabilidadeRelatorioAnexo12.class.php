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
    * Regra de negocio para anexo 12
    * Data de Criaçãoo   : 27/04/2005

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Author: eduardoschitz $
    $Date: 2008-03-26 17:57:40 -0300 (Qua, 26 Mar 2008) $

    * Casos de uso: uc-02.02.09

*/

/*
$Log$
Revision 1.11  2007/06/25 21:05:27  rodrigo_sr
Bug#9133#

Revision 1.10  2006/07/05 20:50:26  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO             );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"                          );
include_once( CAM_GF_CONT_MAPEAMENTO."FContabilidadeRelatorioBalancoOrcamentario.class.php" );

class RContabilidadeRelatorioAnexo12 extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obFContabilidadeRelatorioBalancoOrcamentario;
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
function setFContabilidadeRelatorioBalancoOrcamentario($valor) { $this->obFContabilidadeRelatorioBalancoOrcamentario  = $valor; }
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
function getFContabilidadeRelatorioBalancoOrcamentario() { return $this->obFContabilidadeRelatorioBalancoOrcamentario; }
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

function setaPrimeiraMaiuscula($stFrase)
{
    $stFrase = ucwords(strtolower($stFrase));
    if ( strpos( $stFrase, '-' ) ) {
         if ( trim(substr($stFrase,(strpos( $stFrase, '-' )+1),1 ) ) != '') {
            $stFrase[strpos( $stFrase, '-' )+1] = strtoupper( $stFrase[strpos( $stFrase, '-' )+1] );
         }
    }

    return $stFrase;
}

/**
    * Método Construtor
    * @access Private
*/
function RContabilidadeRelatorioAnexo12()
{
    $this->obFContabilidadeRelatorioBalancoOrcamentario = new FContabilidadeRelatorioBalancoOrcamentario;
    $this->obROrcamentoEntidade                         = new ROrcamentoEntidade;
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordSet , $stOrder = "")
{
    $this->obFContabilidadeRelatorioBalancoOrcamentario->setDado ( "exercicio"    , $this->stExercicio   );
    $this->obFContabilidadeRelatorioBalancoOrcamentario->setDado ( "cod_entidade" , $this->stEntidades   );
    $this->obFContabilidadeRelatorioBalancoOrcamentario->setDado ( "dt_inicial"   , $this->stDataInicial );
    $this->obFContabilidadeRelatorioBalancoOrcamentario->setDado ( "dt_final"     , $this->stDataFinal   );
    $this->obFContabilidadeRelatorioBalancoOrcamentario->setDado ( "situacao"     , $this->stSituacao    );

    public function setaPrimeiraMaiuscula($stFrase)
    {
        setlocale(LC_CTYPE, 'pt_BR');
        $stFrase = ucwords(strtolower($stFrase));
        if ( strpos( $stFrase, '-' ) ) {
            if ( trim(substr($stFrase,(strpos( $stFrase, '-' )+1),1 ) ) != '') {
                $stFrase[strpos( $stFrase, '-' )+1] = strtoupper( $stFrase[strpos( $stFrase, '-' )+1] );
            }
        }

    return $stFrase;
    }

    // executa sql e retorna Record Set
    $obErro = $this->obFContabilidadeRelatorioBalancoOrcamentario->recuperaTodos( $rsRecordSet );

    if ( !$obErro->ocorreu() ) {

        $inCount = 0;
        $inTotal1 = 0;
        $arRecordSet = array();

        // Monta array com os valores das receitas
        while ( !$rsRecordSet->eof() ) {
            if ($rsRecordSet->getCampo('nivel')==1) {
                $boInserirReceita = 'true';
                $inUltimoCount = $inCount;
                $inCodEstruturalReceita = $rsRecordSet->getCampo('reduzido_receita');
                $rsRecordSet->proximo();
                $inCodEstruturalProximoReceita = $rsRecordSet->getCampo('reduzido_receita');
                $rsRecordSet->anterior();

                if (substr($inCodEstruturalReceita, 0, 1) != substr($inCodEstruturalProximoReceita, 0, 1)) {
                    $boInserirReceita = 'false';
                }
            }

            if ($boInserirReceita == 'true') {
                if ( $rsRecordSet->getCampo('reduzido_receita') != 0.00 ) {

                    $arRecordSet[$inCount]['reduzido_receita'  ] = $rsRecordSet->getCampo( 'reduzido_receita' );
                    $arRecordSet[$inCount]['descricao_receita' ] = setaPrimeiraMaiuscula($rsRecordSet->getCampo( 'descricao_receita' ) );
                    $arRecordSet[$inCount]['vl_inicial_receita'] = $rsRecordSet->getCampo( 'vl_inicial_receita' );
                    $arRecordSet[$inCount]['vl_atual_receita'  ] = $rsRecordSet->getCampo( 'vl_atual_receita'   );
                    $nuVlDiferenca = bcsub($rsRecordSet->getCampo('vl_atual_receita'), $rsRecordSet->getCampo('vl_inicial_receita'), 4);
                    $arRecordSet[$inCount]['vl_dif_receita'    ] = $nuVlDiferenca;
                    $arRecordSet[$inCount]['nivel_receita'] = ( substr( $rsRecordSet->getCampo('reduzido_receita' ) ,2,1) != 0 ) ? 2 : 0;

                    // Totaliza valores
                    $nuTotalInicialReceita = bcadd( $nuTotalInicialReceita, $rsRecordSet->getCampo( 'vl_inicial_receita' ), 4 );
                    $nuTotalAtualReceita   = bcadd( $nuTotalAtualReceita  , $rsRecordSet->getCampo( 'vl_atual_receita'   ), 4 );
                    $nuTotalDifReceita     = bcadd( $nuTotalDifReceita    , $nuVlDiferenca                                , 4 );
                    $nuSomaInicialReceita  = bcadd( $nuSomaInicialReceita , $rsRecordSet->getCampo( 'vl_inicial_receita' ), 4 );
                    $nuSomaAtualReceita    = bcadd( $nuSomaAtualReceita   , $rsRecordSet->getCampo( 'vl_atual_receita'   ), 4 );
                    $nuSomaDifReceita      = bcadd( $nuSomaDifReceita     , $nuVlDiferenca                                , 4 );

                    $stReduzidoOld = $rsRecordSet->getCampo('reduzido_receita');
                    $inCount++;

                    // Se a proxima conta for diferente, poe totais no array
                    $rsRecordSet->proximo();
                    if ( substr( $rsRecordSet->getCampo('reduzido_receita'),0,1) != substr($stReduzidoOld,0,1 ) ) {
                        $inTotal1++;
            //            while ($arPosicao[$inTotal] > $inCount) {
                            $arRecordSet[$inCount]['descricao_receita'] = '';
                            $inCount++;
            //            }
                        $arRecordSet[$inCount]['descricao_receita' ] = " Total ";
                        $arRecordSet[$inCount]['vl_inicial_receita'] = $nuTotalInicialReceita;
                        $arRecordSet[$inCount]['vl_atual_receita'  ] = $nuTotalAtualReceita;
                        $arRecordSet[$inCount]['vl_dif_receita' ]    = $nuTotalDifReceita;
                        $arRecordSet[$inCount]['nivel_receita'] = 3;

                        if ($inUltimoCount>=0) {

                            $arRecordSet[$inUltimoCount]['vl_inicial_receita'] = $nuTotalInicialReceita;
                            $arRecordSet[$inUltimoCount]['vl_atual_receita'  ] = $nuTotalAtualReceita;
                            $arRecordSet[$inUltimoCount]['vl_dif_receita' ]    = $nuTotalDifReceita;
                        }

                        $nuTotalInicialReceita = 0;
                        $nuTotalAtualReceita   = 0;
                        $nuTotalDifReceita     = 0;
                        $arPosicao[$inTotal1]  = $inCount;
                        $inCount++;
                        $arRecordSet[$inCount]['descricao_receita'] = ' ';
                        $inCount++;

                        $inUltimoCont = "";
                    }
                    $rsRecordSet->anterior();
                }
            }
            $rsRecordSet->proximo();

        }

        $inCount = 0;
        $inTotal = 0;
        $rsRecordSet->setPrimeiroElemento();
        $inUltimoCont = '';

        // Concatena valores da despesa ao array
        while ( !$rsRecordSet->eof() ) {
            if ($rsRecordSet->getCampo('nivel')==1) {
                $boInserirDespesa = 'true';
                $inUltimoCount = $inCount;
                $inCodEstruturalDespesa = $rsRecordSet->getCampo('reduzido_despesa');
                $rsRecordSet->proximo();
                $inCodEstruturalProximoDespesa = $rsRecordSet->getCampo('reduzido_despesa');
                $rsRecordSet->anterior();

                if (substr($inCodEstruturalDespesa, 0, 1) != substr($inCodEstruturalProximoDespesa, 0, 1)) {
                    $boInserirDespesa = 'false';
                }
            }

            if ($boInserirDespesa == 'true') {
                if ( $rsRecordSet->getCampo('reduzido_despesa') != 0.00 ) {

                    $arRecordSet[$inCount]['reduzido_despesa'  ] = $rsRecordSet->getCampo( 'reduzido_despesa' );
                    $arRecordSet[$inCount]['descricao_despesa' ] = ucwords( strtolower( $rsRecordSet->getCampo( 'descricao_despesa' ) ) );
                    $arRecordSet[$inCount]['vl_inicial_despesa'] = $rsRecordSet->getCampo( 'vl_inicial_despesa' );
                    $arRecordSet[$inCount]['vl_atual_despesa'  ] = $rsRecordSet->getCampo( 'vl_atual_despesa'   );
                    $nuVlDiferenca = bcsub($rsRecordSet->getCampo('vl_atual_despesa'), $rsRecordSet->getCampo('vl_inicial_despesa'), 4);
                    $arRecordSet[$inCount]['vl_dif_despesa'    ] = $nuVlDiferenca;
                    $arRecordSet[$inCount]['nivel_despesa'] = ( substr( $rsRecordSet->getCampo('reduzido_despesa' ) ,2,1) != 0 ) ? 2 : 1;

                    // Totaliza valores
                    $nuTotalInicialDespesa = bcadd( $nuTotalInicialDespesa, $rsRecordSet->getCampo( 'vl_inicial_despesa' ), 4 );
                    $nuTotalAtualDespesa   = bcadd( $nuTotalAtualDespesa  , $rsRecordSet->getCampo( 'vl_atual_despesa'   ), 4 );
                    $nuTotalDifDespesa     = bcadd( $nuTotalDifDespesa    , $nuVlDiferenca                                , 4 );
                    $nuSomaInicialDespesa  = bcadd( $nuSomaInicialDespesa , $rsRecordSet->getCampo( 'vl_inicial_despesa' ), 4 );
                    $nuSomaAtualDespesa    = bcadd( $nuSomaAtualDespesa   , $rsRecordSet->getCampo( 'vl_atual_despesa'   ), 4 );
                    $nuSomaDifDespesa      = bcadd( $nuSomaDifDespesa     , $nuVlDiferenca                                , 4 );

                    $stReduzidoOld = $rsRecordSet->getCampo('reduzido_despesa');
                    $inCount++;

                    // Monta totalizador no array, caso o grupo da proxima conta seja diferente
                    $rsRecordSet->proximo();
                    if ( substr( $rsRecordSet->getCampo('reduzido_despesa'),0,1) != substr($stReduzidoOld,0,1 ) ) {

                        // Iguala posição dos totais no array
                        $inTotal++;
                        while ($arPosicao[$inTotal] > $inCount) {
                            $arRecordSet[$inCount]['descricao_despesa'] = '';
                            $inCount++;
                        }

                        $arRecordSet[$inCount]['descricao_despesa' ] = " Total ";
                        $arRecordSet[$inCount]['vl_inicial_despesa'] = $nuTotalInicialDespesa;
                        $arRecordSet[$inCount]['vl_atual_despesa'  ] = $nuTotalAtualDespesa;
                        $arRecordSet[$inCount]['vl_dif_despesa' ]    = $nuTotalDifDespesa;
                        $arRecordSet[$inCount]['nivel_despesa'] = 3;

                        if ($inUltimoCount>=0) {

                            $arRecordSet[$inUltimoCount]['vl_inicial_despesa'] = $nuTotalInicialDespesa;
                            $arRecordSet[$inUltimoCount]['vl_atual_despesa'  ] = $nuTotalAtualDespesa;
                            $arRecordSet[$inUltimoCount]['vl_dif_despesa' ]    = $nuTotalDifDespesa;
                        }

                        $nuTotalInicialDespesa = 0;
                        $nuTotalAtualDespesa   = 0;
                        $nuTotalDifDespesa     = 0;
                        $inCount++;
                        $arRecordSet[$inCount]['descricao_despesa'] = ' ';
                        $inCount++;

                        $inUltimoCount = '';
                    }
                    $rsRecordSet->anterior();
                }
            }

            $rsRecordSet->proximo();

        }

        // Ordena array pela chave e escolhe maior indice
        ksort($arRecordSet);
        $inCount = ( $inCount1 > $inCount ) ? $inCount1 : $inCount;

        // Monta Soma das despesas e receitas
        $arRecordSet[$inCount]['descricao_receita' ] = 'Soma';
        $arRecordSet[$inCount]['vl_inicial_receita'] = $nuSomaInicialReceita;
        $arRecordSet[$inCount]['vl_atual_receita'  ] = $nuSomaAtualReceita;
        $arRecordSet[$inCount]['vl_dif_receita'    ] = $nuSomaDifReceita;
        $arRecordSet[$inCount]['nivel_receita'     ] = 2;
        $arRecordSet[$inCount]['descricao_despesa' ] = 'Soma';
        $arRecordSet[$inCount]['vl_inicial_despesa'] = $nuSomaInicialDespesa;
        $arRecordSet[$inCount]['vl_atual_despesa'  ] = $nuSomaAtualDespesa;
        $arRecordSet[$inCount]['vl_dif_despesa'    ] = $nuSomaDifDespesa;
        $arRecordSet[$inCount]['nivel_despesa'     ] = 2;

        // Monta deficit e superavit
        $previsao = bcsub( $nuSomaInicialReceita, $nuSomaInicialDespesa, 4 );
        $execucao = bcsub( $nuSomaAtualReceita  , $nuSomaAtualDespesa  , 4 );
        $diferenca= bcsub( $nuSomaDifReceita  , $nuSomaDifDespesa  , 4 );

        $inCount++;
        $arRecordSet[$inCount]['descricao_receita'] = '';
        $inCount++;
        $arRecordSet[$inCount]['descricao_receita' ] = 'Superávit';
        $arRecordSet[$inCount]['vl_inicial_receita'] = ($previsao < 0) ? $previsao : "";
        $arRecordSet[$inCount]['vl_atual_receita'  ] = ($execucao < 0) ? $execucao : "";
        $arRecordSet[$inCount]['vl_dif_receita'    ] = ($diferenca < 0) ? $diferenca : "";
        $arRecordSet[$inCount]['descricao_despesa' ] = 'Superávit';
        $arRecordSet[$inCount]['vl_inicial_despesa'] = ($previsao > 0) ? $previsao : "";
        $arRecordSet[$inCount]['vl_atual_despesa'  ] = ($execucao > 0) ? $execucao : "";
        $arRecordSet[$inCount]['vl_dif_despesa'    ] = ($diferenca > 0) ? $diferenca : "";

        // Monta Totalizador geral
        $previsao = bcsub( $nuSomaInicialDespesa, $nuSomaInicialReceita, 4 );
        $execucao = bcsub( $nuSomaAtualDespesa  , $nuSomaAtualReceita  , 4 );
        $diferenca= bcsub( $nuSomaDifDespesa  , $nuSomaDifReceita  , 4 );
        $inCount++;
        $arRecordSet[$inCount]['descricao_receita'] = '';
        $inCount++;
        $arRecordSet[$inCount]['descricao_receita' ] = 'Total das Receitas';
        $arRecordSet[$inCount]['vl_inicial_receita'] = bcadd( $nuSomaInicialReceita,(($previsao > 0) ? $previsao : 0), 4 );
        $arRecordSet[$inCount]['vl_atual_receita'  ] = bcadd( $nuSomaAtualReceita  ,(($execucao > 0) ? $execucao : 0), 4 );
        $arRecordSet[$inCount]['vl_dif_receita'    ] = bcadd( $nuSomaDifReceita    ,(($diferenca > 0) ? $diferenca : 0), 4 );
        $arRecordSet[$inCount]['descricao_despesa' ] = 'Total das Despesas';
        $arRecordSet[$inCount]['vl_inicial_despesa'] = bcadd( $nuSomaInicialDespesa,(($previsao < 0) ? ($previsao*-1) : 0), 4 );
        $arRecordSet[$inCount]['vl_atual_despesa'  ] = bcadd( $nuSomaAtualDespesa  ,(($execucao < 0) ? ($execucao*-1) : 0),4 );
        $arRecordSet[$inCount]['vl_dif_despesa'    ] = bcadd( $nuSomaDifDespesa    ,(($diferenca < 0) ? ($diferenca*-1) : 0), 4 );

        // Entidades Relacionadas
        $inCount++;
        $arRecordSet[$inCount]['descricao_despesa'] = '';
        $inCount++;
        $arRecordSet[$inCount]['descricao_receita'] = 'Entidades Relacionadas';
        $arRecordSet[$inCount]['nivel_receita']     = 5;
        $inCount++;
        $arEntidades = explode( ',', $this->stEntidades );
        $this->obROrcamentoEntidade->setExercicio( $this->stExercicio );
        foreach ($arEntidades as $inCodEntidade) {
            $this->obROrcamentoEntidade->setCodigoEntidade( $inCodEntidade );
            $obErro = $this->obROrcamentoEntidade->consultarNomes( $rsLista );
            if ( $obErro->ocorreu() ) {
                break;
            } else {
                $arRecordSet[$inCount]['descricao_receita'] = $rsLista->getCampo("entidade");
                $arRecordSet[$inCount]['nivel_receita'    ] = 5;
                $inCount++;
            }
        }

        $arRecordSet[$inCount]['descricao_despesa'] = '';
        $inCount++;
        $arRecordSet[$inCount]['descricao_receita'] = 'Exercicio '.$this->stExercicio;
        $arRecordSet[$inCount]['nivel_receita']     = 5;

        $rsRecordSet = new RecordSet;
        $rsRecordSet->preenche( $arRecordSet );
    }

    return $obErros;
}
}
