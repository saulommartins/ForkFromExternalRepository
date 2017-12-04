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
    * Classe de regra para Anexo2DespesaUnidade
    * Data de Criação   : 29/09/2004

    * @author Desenvolvedor: Eduardo Martins
    * @author Desenvolvedor: Diego Victória
    * @author Desenvolvedor: Gustavo Tourinho

    * @ignore

    $Revision: 32061 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.11
*/

/*
$Log$
Revision 1.6  2006/07/05 20:42:11  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO );
include_once( CAM_GF_ORC_MAPEAMENTO."FOrcamentoSomatorioDespesaUnidade.class.php" );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoUnidadeOrcamentaria.class.php"            );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoOrgaoOrcamentario.class.php"              );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php"                   );

class ROrcamentoRelatorioAnexo2DespesaUnidade
{
/**
    * @var Object
    * @access Private
*/
var $obFOrcamentoSomatorioDespesaUnidade;
var $stFiltro;
var $inExercicio;
var $inOrgao;
var $inUnidade;
var $stTipoRelatorio;

/**
     * @access Public
     * @param Object $valor
*/
function setFOrcamentoSomatorioDespesaUnidade($valor) { $this->obFOrcamentoSomatorioDespesaUnidade = $valor; }
function setFiltro($valor) { $this->stFiltro                          = $valor; }
function setExercicio($valor) { $this->inExercicio                       = $valor; }
function setOrgao($valor) { $this->inOrgao                           = $valor; }
function setUnidade($valor) { $this->inUnidade                         = $valor; }
function setTipoRelatorio($valor) { $this->inTipoRelatorio                   = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function getFOrcamentoSomatorioDespesaUnidade() { return $this->obFOrcamentoSomatorioDespesaUnidade; }
function getFiltro() { return $this->stFiltro                         ; }
function getExercicio() { return $this->inExercicio                      ; }
function getOrgao() { return $this->inOrgao                          ; }
function getUnidade() { return $this->inUnidade                        ; }
function getTipoRelatorio() { return $this->inTipoRelatorio                  ; }

/**
    * Método Construtor
    * @access Private
*/
function ROrcamentoRelatorioAnexo2DespesaUnidade()
{
    $this->setFOrcamentoSomatorioDespesaUnidade ( new FOrcamentoSomatorioDespesaUnidade );
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$arTodos, &$rsTotal, $stOrder = "")
{
    $arOrcamento = array();
    $arTotal     = array();
    $arTodos     = array();
    $rsResultado = new RecordSet;
    $rsTotal     = new RecordSet;

    $obROrgao   = new ROrcamentoOrgaoOrcamentario;
    $obRUnidade = new ROrcamentoUnidadeOrcamentaria;

    $this->obFOrcamentoSomatorioDespesaUnidade->setDado ("exercicio", $this->inExercicio);
    $this->obFOrcamentoSomatorioDespesaUnidade->setDado ("stFiltro",  $this->stFiltro);
    $stFiltro = "WHERE valor <> 0";

    $obREntidade = new ROrcamentoEntidade;
    $obREntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
    $obREntidade->listarUsuariosEntidade( $rsEntidades , " ORDER BY cod_entidade" );

    $obErro = $this->obFOrcamentoSomatorioDespesaUnidade->recuperaTodos( $rsRecordSet, $stFiltro, "" );

    $inCount = -1;
    $inCountArray = 0;

    $inTotalGeral = $inTotal = 0;

    $rsRecordSet->setPrimeiroElemento();
    $inOrgaoCorrente = 0;
    $stOrgaoUnidade  = 0;
    $stEspacos = "                                    ";
    while ( !$rsRecordSet->eof() ) {

        $rsRecordSet->proximo();
        if ( $rsRecordSet->eof() ) {
            $boVerifica = true;
        } else {
            $boVerifica = false;
        }
        $rsRecordSet->anterior();

        if ( ($rsRecordSet->getCampo( 'num_orgao' ).'-'.$rsRecordSet->getCampo( 'num_unidade' ) != $stOrgaoUnidade) || ($boVerifica)) {

            $inOrgaoCorrente   = $rsRecordSet->getCampo( 'num_orgao' );
            $inUnidadeCorrente = $rsRecordSet->getCampo( 'num_unidade' );
            $stOrgaoUnidade    = $inOrgaoCorrente.'-'.$inUnidadeCorrente;
            $obROrgao->setNumeroOrgao( $inOrgaoCorrente );
            $obRUnidade->setNumeroUnidade( $inUnidadeCorrente );
            $obRUnidade->obROrcamentoOrgaoOrcamentario->setNumeroOrgao( $inOrgaoCorrente );

            if ($inCount > 0) {

                if ($boVerifica) {

                    $arOrcamento[$inCount]['nivel']         = $rsRecordSet->getCampo('nivel');
                    $arOrcamento[$inCount]['classificacao'] = $rsRecordSet->getCampo('classificacao')."              ";
                    $arOrcamento[$inCount]['descricao']     = $rsRecordSet->getCampo('descricao');
                    $arOrcamento[$inCount]['alinhamento']   = $rsRecordSet->getCampo('alinhamento');
                    if ($stColuna == 'desdobramento') {
                        $arOrcamento[$inCount]['valor_d']       = number_format($inValor,2,",",".");
                        $arOrcamento[$inCount]['valor_e']       = '';
                        $arOrcamento[$inCount]['valor_c']       = '';
                    } elseif ($stColuna == 'elemento') {
                        $arOrcamento[$inCount]['valor_d']       = '';
                        $arOrcamento[$inCount]['valor_e']       = number_format($inValor,2,",",".");
                        $arOrcamento[$inCount]['valor_c']       = '';
                    } elseif ($stColuna == 'categoria_economica') {
                        $arOrcamento[$inCount]['valor_d']       = '';
                        $arOrcamento[$inCount]['valor_e']       = '';
                        $arOrcamento[$inCount]['valor_c']       = number_format($inValor,2,",",".");
                    }
                   $inCount++;

                }

                $arOrcamento[$inCount]['valor_e']       = 'Total..............:';
                $arOrcamento[$inCount]['valor_c']       = number_format($inTotal, 2, ",",".");

                $inTotalGeral = $inTotalGeral + $inTotal;
                $inTotal = 0;

                $rsResultado->preenche( $arOrcamento );
                $arTodos[$inCountArray] = clone $rsResultado;
                $arOrcamento = array();
                $inCount = -1;
                $inCountArray++;
            }

            $inCount++;
            $obROrgao->listar( $rsOrgao );
            $obRUnidade->listar( $rsUnidade );
            $arOrcamento[$inCount]['classificacao'] = str_pad($inOrgaoCorrente,2,"0",STR_PAD_LEFT);
            $arOrcamento[$inCount]['descricao'] = $rsOrgao->getCampo('nom_orgao');
            $inCount++;
            $arOrcamento[$inCount]['classificacao'] = str_pad($inUnidadeCorrente,2,"0",STR_PAD_LEFT);
            $arOrcamento[$inCount]['descricao'] = $rsUnidade->getCampo('nom_unidade');

            $inCount++;

            $arOrcamento[$inCount]['branco'] = "";

            $inCount++;

            $arOrcamento[$inCount]['classificacao'] = 'CÓDIGO'.$stEspacos;
            $arOrcamento[$inCount]['descricao']     = 'ESPECIFICAÇÃO';
            $arOrcamento[$inCount]['valor_d']       = 'DESDOBRAMENTO';
            $arOrcamento[$inCount]['valor_e']       = 'ELEMENTO';
            $arOrcamento[$inCount]['valor_c']       = 'CATEGORIA ECONÔMICA';

            $inCount++;

        }

        $arOrcamento[$inCount]['nivel']         = $rsRecordSet->getCampo('nivel');
        $arOrcamento[$inCount]['classificacao'] = $rsRecordSet->getCampo('classificacao')."              ";
        $arOrcamento[$inCount]['descricao']     = $rsRecordSet->getCampo('descricao');
        $arOrcamento[$inCount]['alinhamento']   = $rsRecordSet->getCampo('alinhamento');

        if ( $stClassifCorrente != $rsRecordSet->getCampo('classificacao') ) {
            $stClassifCorrente = $rsRecordSet->getCampo('classificacao');
            $arOrcamento[$inCount]['nivel'] = $rsRecordSet->getCampo('nivel');
        }

        $stColuna = trim($rsRecordSet->getCampo("coluna"));

        $inValor = $rsRecordSet->getCampo('valor');

        if ( trim($rsRecordSet->getCampo("nivel")) == 1 ) {
            $inTotal      = $inTotal      + $rsRecordSet->getCampo("valor");
        }

        if ($stColuna == 'desdobramento') {
            $arOrcamento[$inCount]['valor_d']       = number_format($inValor,2,",",".");
            $arOrcamento[$inCount]['valor_e']       = '';
            $arOrcamento[$inCount]['valor_c']       = '';
        } elseif ($stColuna == 'elemento') {
            $arOrcamento[$inCount]['valor_d']       = '';
            $arOrcamento[$inCount]['valor_e']       = number_format($inValor,2,",",".");
            $arOrcamento[$inCount]['valor_c']       = '';
        } elseif ($stColuna == 'categoria_economica') {
            $arOrcamento[$inCount]['valor_d']       = '';
            $arOrcamento[$inCount]['valor_e']       = '';
            $arOrcamento[$inCount]['valor_c']       = number_format($inValor,2,",",".");
        }

        $inCount++;
        $rsRecordSet->proximo();
    }

    $arTotal[0]['descricao'] = 'Total Geral..............:';
    $arTotal[0]['valor']     = number_format($inTotalGeral, 2, ",",".");

    $rsTotal->preenche( $arTotal );

    return $obErro;
}
}
