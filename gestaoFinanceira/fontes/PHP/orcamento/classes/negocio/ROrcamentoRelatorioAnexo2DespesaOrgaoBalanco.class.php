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

    * @author Desenvolvedor: Cleisson Barboza

    * @ignore

    $Revision: 32086 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.11
*/

/*
$Log$
Revision 1.4  2006/07/05 20:42:11  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO );
include_once( CAM_GF_ORC_MAPEAMENTO."FOrcamentoSomatorioDespesaOrgaoBalanco.class.php"           );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoOrgaoOrcamentario.class.php"                                );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php"                                          );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"                                         );

class ROrcamentoRelatorioAnexo2DespesaOrgaoBalanco
{
/**
    * @var Object
    * @access Private
*/
var $obFOrcamentoSomatorioDespesaOrgaoBalanco;
var $stFiltro;
var $inExercicio;
var $inOrgao;
var $inUnidade;
var $stDataInicial;
var $stDataFinal;
var $stSituacao;
var $stEntidades;
var $obREntidade;

var $stTipoRelatorio;

/**
     * @access Public
     * @param Object $valor
*/
function setFOrcamentoSomatorioDespesaOrgaoBalanco($valor) { $this->obFOrcamentoSomatorioDespesaOrgaoBalanco = $valor; }
function setFiltro($valor) { $this->stFiltro                      = $valor; }
function setExercicio($valor) { $this->inExercicio                   = $valor; }
function setOrgao($valor) { $this->inOrgao                       = $valor; }
function setUnidade($valor) { $this->inUnidade                     = $valor; }
function setDataInicial($valor) { $this->stDataInicial                 = $valor; }
function setDataFinal($valor) { $this->stDataFinal                   = $valor; }
function setSituacao($valor) { $this->stSituacao                    = $valor; }
function setEntidades($valor) { $this->stEntidades                   = $valor; }
function setREntidade($valor) { $this->obREntidade                   = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function getFOrcamentoSomatorioDespesaOrgaoBalanco() { return $this->obFOrcamentoSomatorioDespesaOrgaoBalanco; }
function getFiltro() { return $this->stFiltro                    ; }
function getExercicio() { return $this->inExercicio                 ; }
function getOrgao() { return $this->inOrgao                     ; }
function getUnidade() { return $this->inUnidade                   ; }
function getDataInicial() { return $this->stDataInicial               ; }
function getDataFinal() { return $this->stDataFinal                 ; }
function getSituacao() { return $this->stSituacao                  ; }
function getEntidades() { return $this->stEntidades                 ; }
function getREntidade() { return $this->obREntidade                 ; }

/**
    * Método Construtor
    * @access Private
*/
function ROrcamentoRelatorioAnexo2DespesaOrgaoBalanco()
{
    $this->setFOrcamentoSomatorioDespesaOrgaoBalanco ( new FOrcamentoSomatorioDespesaOrgaoBalanco );
    $this->obREntidade                  = new ROrcamentoEntidade;
    $this->obREntidade->obRCGM->setNumCGM( Sessao::read('numCgm')         );

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

    $this->obFOrcamentoSomatorioDespesaOrgaoBalanco->setDado ("stDataInicial",$this->stDataInicial);
    $this->obFOrcamentoSomatorioDespesaOrgaoBalanco->setDado ("stDataFinal",  $this->stDataFinal  );
    $this->obFOrcamentoSomatorioDespesaOrgaoBalanco->setDado ("stSituacao",   $this->stSituacao   );
    $this->obFOrcamentoSomatorioDespesaOrgaoBalanco->setDado ("stEntidades",  $this->stEntidades  );
    $this->obFOrcamentoSomatorioDespesaOrgaoBalanco->setDado ("exercicio",    $this->inExercicio  );
    $this->obFOrcamentoSomatorioDespesaOrgaoBalanco->setDado ("stFiltro",     $this->stFiltro     );
    $this->obFOrcamentoSomatorioDespesaOrgaoBalanco->setDado ("inOrgao",      $this->inOrgao      );
    $this->obFOrcamentoSomatorioDespesaOrgaoBalanco->setDado ("inUnidade",    $this->inUnidade    );

    $stFiltro = "WHERE valor <> 0";

    $obREntidade = new ROrcamentoEntidade;
    $obREntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
    $obREntidade->listarUsuariosEntidade( $rsEntidades , " ORDER BY cod_entidade" );

    $obErro = $this->obFOrcamentoSomatorioDespesaOrgaoBalanco->recuperaTodos( $rsRecordSet, $stFiltro, "" );

    $inCount = -1;
    $inCountArray = 0;

    $inTotalGeral = $inTotal = 0;

    $rsRecordSet->setPrimeiroElemento();
    $inOrgaoCorrente = 0;
    $stEspacos = "                                    ";

    while ( !$rsRecordSet->eof() ) {

        $rsRecordSet->proximo();
        if ( $rsRecordSet->eof() ) {
            $boVerifica = true;
        } else {
            $boVerifica = false;
        }
        $rsRecordSet->anterior();

        if ( ($rsRecordSet->getCampo( 'num_orgao' ) != $inOrgaoCorrente) || ($boVerifica)) {

            $inOrgaoCorrente   = $rsRecordSet->getCampo( 'num_orgao' );
            $obROrgao->setNumeroOrgao( $inOrgaoCorrente );

            if ($inCount > 0) {

                if ($boVerifica) {
                    $inValor                                = $rsRecordSet->getCampo('valor');
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
            $arOrcamento[$inCount]['classificacao'] = str_pad($inOrgaoCorrente,2,"0",STR_PAD_LEFT);
            $arOrcamento[$inCount]['descricao'] = $rsOrgao->getCampo('nom_orgao');
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
    //die(0);
    return $obErro;
}
}
