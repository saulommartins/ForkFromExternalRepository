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
    * Classe de Regra de Manutenção de Datas
    * Data de Criação   : 07/06/2005

    * @author Analista:      Diego B Victoria
    * @author Desenvolvedor: Cleisson Barboza

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2006-09-28 06:56:56 -0300 (Qui, 28 Set 2006) $

    * Casos de uso: uc-02.03.16
*/

/*
$Log$
Revision 1.7  2006/09/28 09:52:29  eduardo
Bug #7060#

Revision 1.6  2006/07/05 20:47:06  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS  ."Transacao.class.php"                     );

class REmpenhoManutencaoDatas
{
/**
    * @access Private
    * @var Integer
*/
var $inCodEntidade;
/**
    * @access Private
    * @var Integer
*/
var $inCodEmpenho;
/**
    * @access Private
    * @var Varchar
*/
var $stExercicioEmpenho;

/**
     * @access Public
     * @param String $valor
*/
function setExercicioEmpenho($valor) { $this->stExercicioEmpenho           = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setCodEmpenho($valor) { $this->inCodEmpenho                 = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setCodEntidade($valor) { $this->inCodEntidade                = $valor; }

/**
     * @access Public
     * @return Integer
*/
function getExercicioEmpenho() { return $this->stExercicioEmpenho;                 }

/**
     * @access Public
     * @return Integer
*/
function getCodEmpenho() { return $this->inCodEmpenho;                 }
/**
     * @access Public
     * @return Integer
*/
function getCodEntidade() { return $this->inCodEntidade;                 }

/**
     * Método construtor
     * @access Public
*/
function REmpenhoManutencaoDatas()
{
    $this->obTransacao                       =  new Transacao;
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listar(&$rsRecordSet, $stOrder = "" , $obTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php"               );
    $obTEmpenhoEmpenho                = new TEmpenhoEmpenho;

    $stFiltro= "";
    $obTEmpenhoEmpenho->setDado ("stExercicio", $this->getExercicioEmpenho() );
    $obTEmpenhoEmpenho->setDado( "cod_entidade", $this->getCodEntidade()       );
    $obErro = $obTEmpenhoEmpenho->recuperaRelacionamentoManutencaoDatas( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarDadosAnulacaoEmpenho(&$rsRecordSet, $stOrder = "" , $obTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenhoAnuladoItem.class.php"    );
    $obTEmpenhoEmpenhoAnuladoItem     = new TEmpenhoEmpenhoAnuladoItem;

    $stFiltro= "";
    $obTEmpenhoEmpenhoAnuladoItem->setDado( "cod_entidade"    , $this->getCodEntidade()       );
    $obTEmpenhoEmpenhoAnuladoItem->setDado( "exercicio"       , $this->getExercicioEmpenho()  );
    $obTEmpenhoEmpenhoAnuladoItem->setDado( "cod_empenho"     , $this->getCodEmpenho()        );
    $obErro = $obTEmpenhoEmpenhoAnuladoItem->recuperaRelacionamentoManutencaoDatas( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}
/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarDadosLiquidacao(&$rsRecordSet, $stOrder = "" , $obTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoNotaLiquidacao.class.php"    );
    $obTEmpenhoNotaLiquidacao         = new TEmpenhoNotaLiquidacao;

    $stFiltro= "";
    $obTEmpenhoNotaLiquidacao->setDado( "cod_entidade"    , $this->getCodEntidade()       );
    $obTEmpenhoNotaLiquidacao->setDado( "exercicio"       , $this->getExercicioEmpenho()  );
    $obTEmpenhoNotaLiquidacao->setDado( "cod_empenho"     , $this->getCodEmpenho()        );
    $obErro = $obTEmpenhoNotaLiquidacao->recuperaRelacionamentoManutencaoDatas( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}
/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarDadosAnulacaoLiquidacao(&$rsRecordSet, $stOrder = "" , $obTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoNotaLiquidacaoItemAnulado.class.php"    );
    $obTEmpenhoNotaLiquidacaoItemAnulado  = new TEmpenhoNotaLiquidacaoItemAnulado;

    $stFiltro= "";
    $obTEmpenhoNotaLiquidacaoItemAnulado->setDado( "cod_entidade"    , $this->getCodEntidade()       );
    $obTEmpenhoNotaLiquidacaoItemAnulado->setDado( "exercicio"       , $this->getExercicioEmpenho()  );
    $obTEmpenhoNotaLiquidacaoItemAnulado->setDado( "cod_empenho"     , $this->getCodEmpenho()        );
    $obErro = $obTEmpenhoNotaLiquidacaoItemAnulado->recuperaRelacionamentoManutencaoDatas( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarDadosOrdemPagamento(&$rsRecordSet, $stOrder = "" , $obTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoOrdemPagamento.class.php"    );
    $obTEmpenhoOrdemPagamento         = new TEmpenhoOrdemPagamento;

    $stFiltro= "";
    $obTEmpenhoOrdemPagamento->setDado( "cod_entidade"    , $this->getCodEntidade()       );
    $obTEmpenhoOrdemPagamento->setDado( "exercicio"       , $this->getExercicioEmpenho()  );
    $obTEmpenhoOrdemPagamento->setDado( "cod_empenho"     , $this->getCodEmpenho()        );
    $obErro = $obTEmpenhoOrdemPagamento->recuperaRelacionamentoManutencaoDatas( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}
/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarDadosAnulacaoOrdemPagamento(&$rsRecordSet, $stOrder = "" , $obTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoOrdemPagamentoAnulada.class.php"    );
    $obTEmpenhoOrdemPagamentoAnulada  = new TEmpenhoOrdemPagamentoAnulada;

    $stFiltro= "";
    $obTEmpenhoOrdemPagamentoAnulada->setDado( "cod_entidade"    , $this->getCodEntidade()       );
    $obTEmpenhoOrdemPagamentoAnulada->setDado( "exercicio"       , $this->getExercicioEmpenho()  );
    $obTEmpenhoOrdemPagamentoAnulada->setDado( "cod_empenho"     , $this->getCodEmpenho()        );
    $obErro = $obTEmpenhoOrdemPagamentoAnulada->recuperaRelacionamentoManutencaoDatas( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarDadosPagamento(&$rsRecordSet, $stOrder = "" , $obTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoNotaLiquidacaoPaga.class.php"    );
    $obTEmpenhoNotaLiquidacaoPaga     = new TEmpenhoNotaLiquidacaoPaga;

    $stFiltro= "";
    $obTEmpenhoNotaLiquidacaoPaga->setDado( "cod_entidade"    , $this->getCodEntidade()       );
    $obTEmpenhoNotaLiquidacaoPaga->setDado( "exercicio"       , $this->getExercicioEmpenho()  );
    $obTEmpenhoNotaLiquidacaoPaga->setDado( "cod_empenho"     , $this->getCodEmpenho()        );
    $obErro = $obTEmpenhoNotaLiquidacaoPaga->recuperaRelacionamentoManutencaoDatas( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}
/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarDadosAnulacaoPagamento(&$rsRecordSet, $stOrder = "" , $obTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoNotaLiquidacaoPagaAnulada.class.php"    );
    $obTEmpenhoNotaLiquidacaoPagaAnulada  = new TEmpenhoNotaLiquidacaoPagaAnulada;

    $stFiltro= "";
    $obTEmpenhoNotaLiquidacaoPagaAnulada->setDado( "cod_entidade"    , $this->getCodEntidade()       );
    $obTEmpenhoNotaLiquidacaoPagaAnulada->setDado( "exercicio"       , $this->getExercicioEmpenho()  );
    $obTEmpenhoNotaLiquidacaoPagaAnulada->setDado( "cod_empenho"     , $this->getCodEmpenho()        );
    $obErro = $obTEmpenhoNotaLiquidacaoPagaAnulada->recuperaRelacionamentoManutencaoDatas( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Salva as novas datas corrigidas nas respectivas tabelas
    * Método Criado por Diego B. Victoria
*/
function salvar($boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php"               );
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoNotaLiquidacao.class.php"    );
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoNotaLiquidacaoItemAnulado.class.php"    );
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoOrdemPagamento.class.php"    );
    $obTEmpenhoOrdemPagamento         = new TEmpenhoOrdemPagamento;
    $obTEmpenhoNotaLiquidacaoItemAnulado  = new TEmpenhoNotaLiquidacaoItemAnulado;
    $obTEmpenhoNotaLiquidacao         = new TEmpenhoNotaLiquidacao;
    $obTEmpenhoEmpenho                = new TEmpenhoEmpenho;

    $stUsername = "nac";
    $stPassword = "linuxx";

    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        /* Empenho */
        $obTEmpenhoEmpenho->setDado('exercicio'   ,$_REQUEST['stExercicio'] );
        $obTEmpenhoEmpenho->setDado('cod_entidade',$_REQUEST['inCodEntidade'] );
        $obTEmpenhoEmpenho->setDado('cod_empenho' ,$_REQUEST['inCodEmpenho'] );
        $obTEmpenhoEmpenho->recuperaPorChave($rsRec, $boTransacao);
        $inCodPreEmpenho = $rsRec->getCampo('cod_pre_empenho');
        if ($rsRec->getCampo('dt_empenho') <> $_REQUEST['stDtEmpenho'] ) {
            $obTEmpenhoEmpenho->setDado('dt_empenho' ,$_REQUEST['stDtEmpenho'] );
            $obTEmpenhoEmpenho->setDado('cod_pre_empenho'  ,$rsRec->getCampo('cod_pre_empenho') );
            $obTEmpenhoEmpenho->setDado('dt_vencimento'    ,$rsRec->getCampo('dt_vencimento') );
            $obTEmpenhoEmpenho->setDado('vl_saldo_anterior',$rsRec->getCampo('vl_saldo_anterior') );

            $obErro = $obTEmpenhoEmpenho->alteracao($boTransacao);

            if($obErro->ocorreu())

                return $obErro;
        }
        foreach ($_REQUEST as $key => $value) {
            /*
                Empenho - Anulação
            */
            if ( strstr($key,'stDataEmpenhoAnulacao_') ) {
                $arVal = explode( "_" , $key );
                $dtTimestampAntigo = str_replace('@','.',str_replace(';',' ',$arVal[1]));

                $stTimestamp = substr( $dtTimestampAntigo ,0,10);
                $dtTimestamp = substr($stTimestamp,8,2)."/".substr($stTimestamp,5,2)."/".substr($stTimestamp,0,4);

                if ($dtTimestamp <> $value) {
                    $dtTimestampNovo = substr($value,6,4)."-".substr($value,3,2)."-".substr($value,0,2)." 12:12:12.99";

                    /* Dropa a FK */
                    $obCon   = new Conexao;
                    $obCon->setUser( $stUsername );
                    $obCon->setPassWord( $stPassword );
                    $stSql = 'ALTER TABLE empenho.empenho_anulado_item drop constraint fk_empenho_anulado_item_1';
                    $obErro = $obCon->executaSql($rsXXX, $stSql , $boTransacao);
                    if($obErro->ocorreu())

                        return $obErro;

                    //$obCon   = new Conexao;
                    $stSql  = " UPDATE empenho.empenho_anulado_item SET timestamp = '$dtTimestampNovo' ";
                    $stSql .= " WHERE cod_empenho = ".$_REQUEST['inCodEmpenho']." AND exercicio = '".$_REQUEST['stExercicio']."' AND cod_entidade = ".$_REQUEST['inCodEntidade']." AND cod_pre_empenho = ".$inCodPreEmpenho." AND timestamp = '".$dtTimestampAntigo."' ";
                    $obErro = $obCon->executaSql($rsXXX, $stSql , $boTransacao);
                    if($obErro->ocorreu())

                        return $obErro;

                    //$obCon   = new Conexao;
                    $stSql  = " UPDATE empenho.empenho_anulado SET timestamp = '$dtTimestampNovo' ";
                    $stSql .= " WHERE cod_empenho = ".$_REQUEST['inCodEmpenho']." AND exercicio = '".$_REQUEST['stExercicio']."' AND cod_entidade = ".$_REQUEST['inCodEntidade']." AND timestamp = '".$dtTimestampAntigo."' ";
                    $obErro = $obCon->executaSql($rsXXX, $stSql , $boTransacao);
                    if($obErro->ocorreu())

                        return $obErro;

                    /* Cria a FK */
                    //$obCon   = new Conexao;
                    //$obCon->setUser( $stUsername );
                    //$obCon->setPassWord( $stPassword );
                    $stSql = 'ALTER TABLE empenho.empenho_anulado_item add constraint fk_empenho_anulado_item_1 FOREIGN KEY (exercicio, cod_entidade, cod_empenho, "timestamp") REFERENCES empenho.empenho_anulado(exercicio, cod_entidade, cod_empenho, "timestamp")';
                    $obErro = $obCon->executaSql($rsXXX, $stSql , $boTransacao);
                    if($obErro->ocorreu())

                        return $obErro;

                }
            }
            /*
                Liquidação
            */
            if ( strstr($key,'stDataLiquidacao_') ) {
                $arVal = explode( "_" , $key );
                $obTEmpenhoNotaLiquidacao->setDado('exercicio'       ,$arVal[1] );
                $obTEmpenhoNotaLiquidacao->setDado('cod_entidade'    ,$_REQUEST['inCodEntidade'] );
                $obTEmpenhoNotaLiquidacao->setDado('cod_nota'        ,$arVal[2] );

                $obTEmpenhoNotaLiquidacao->recuperaPorChave($rsRec, $boTransacao);

                if ( $rsRec->getCampo('dt_liquidacao') <> $value ) {
                    $obTEmpenhoNotaLiquidacao->setDado('dt_liquidacao' , $value );
                    $obTEmpenhoNotaLiquidacao->setDado('exercicio_empenho', $rsRec->getCampo('exercicio_empenho') );
                    $obTEmpenhoNotaLiquidacao->setDado('cod_empenho'      , $rsRec->getCampo('cod_empenho') );
                    $obTEmpenhoNotaLiquidacao->setDado('dt_vencimento'    , $rsRec->getCampo('dt_vencimento') );
                    $obTEmpenhoNotaLiquidacao->setDado('observacao'       , $rsRec->getCampo('observacao') );

                    $obErro = $obTEmpenhoNotaLiquidacao->alteracao($boTransacao);
                    if($obErro->ocorreu())

                        return $obErro;
                }

            }
            /*
                Liquidação - Anulação
            */
            if ( strstr($key,'stDataLiquidacaoAnulacao_') ) {
                $arVal = explode( "_" , $key );
                $dtTimestampAntigo = str_replace('@','.',str_replace(';',' ',$arVal[3]));

                $stTimestamp = substr( $dtTimestampAntigo ,0,10);
                $dtTimestamp = substr($stTimestamp,8,2)."/".substr($stTimestamp,5,2)."/".substr($stTimestamp,0,4);

                if ($dtTimestamp <> $value) {
                    $dtTimestampNovo = substr($value,6,4)."-".substr($value,3,2)."-".substr($value,0,2)." 12:12:12.99";

                    $obCon   = new Conexao;
                    $stSql  = " UPDATE empenho.nota_liquidacao_item_anulado SET timestamp = '$dtTimestampNovo' ";
                    $stSql .= " WHERE cod_nota = ".$arVal[2]." AND exercicio_item = '".$_REQUEST['stExercicio']."' AND exercicio = '".$arVal[1]."' AND cod_entidade = ".$_REQUEST['inCodEntidade']." AND cod_pre_empenho = ".$inCodPreEmpenho." AND timestamp = '".$dtTimestampAntigo."' ";
                    $obErro = $obCon->executaSql($rsXXX, $stSql , $boTransacao);
                    if($obErro->ocorreu())

                        return $obErro;
                }
                $obTEmpenhoNotaLiquidacaoItemAnulado->setComplementoChave('cod_entidade,cod_nota,exercicio,timestamp,cod_pre_empenho,num_item,exercicio_item');
            }
            /*
                Ordem
            */
            if ( strstr($key,'stDataOrdem_') ) {
                $arVal = explode( "_" , $key );
                $obTEmpenhoOrdemPagamento->setDado('exercicio'    ,$arVal[1] );
                $obTEmpenhoOrdemPagamento->setDado('cod_ordem'    ,$arVal[2] );
                $obTEmpenhoOrdemPagamento->setDado('cod_entidade' ,$_REQUEST['inCodEntidade'] );

                $obTEmpenhoOrdemPagamento->recuperaPorChave($rsRec, $boTransacao);

                if ( $rsRec->getCampo('dt_emissao') <> $value ) {
                    $obTEmpenhoOrdemPagamento->setDado('dt_emissao'    , $value );
                    $obTEmpenhoOrdemPagamento->setDado('dt_vencimento' , $rsRec->getCampo('dt_vencimento') );
                    $obTEmpenhoOrdemPagamento->setDado('observacao'    , $rsRec->getCampo('observacao') );

                    $obErro = $obTEmpenhoOrdemPagamento->alteracao($boTransacao);
                    if($obErro->ocorreu())

                        return $obErro;
                }
            }
            /*
                Ordem - Anulação
            */
            if ( strstr($key,'stDataOrdemAnulacao_') ) {
                $arVal = explode( "_" , $key );
                $dtTimestampAntigo = str_replace('@','.',str_replace(';',' ',$arVal[3]));

                $stTimestamp = substr( $dtTimestampAntigo ,0,10);
                $dtTimestamp = substr($stTimestamp,8,2)."/".substr($stTimestamp,5,2)."/".substr($stTimestamp,0,4);

                if ($dtTimestamp <> $value) {
                    $dtTimestampNovo = substr($value,6,4)."-".substr($value,3,2)."-".substr($value,0,2)." 12:12:12.99";

                    $obCon   = new Conexao;

                    $stSql = "alter TABLE empenho.ordem_pagamento_liquidacao_anulada Drop constraint  fk_ordem_pagamento_liquidacao_anulada_2";
                    $obErro = $obCon->executaSql($rsXXX, $stSql , $boTransacao);

                    if ( !$obErro->ocorreu() ) {

                        $stSql  = " UPDATE empenho.ordem_pagamento_anulada SET timestamp = '$dtTimestampNovo' ";
                        $stSql .= " WHERE cod_ordem = ".$arVal[2]." AND exercicio = '".$arVal[1]."' AND cod_entidade = ".$_REQUEST['inCodEntidade']." AND timestamp = '".$dtTimestampAntigo."' ";
                        $obErro = $obCon->executaSql($rsXXX, $stSql , $boTransacao);

                       if ( !$obErro->ocorreu() ) {
                           $stSql  = " UPDATE empenho.ordem_pagamento_liquidacao_anulada SET timestamp = '$dtTimestampNovo' ";
                           $stSql .= " WHERE cod_ordem = ".$arVal[2]." AND exercicio = '".$arVal[1]."' AND cod_entidade = ".$_REQUEST['inCodEntidade']." AND timestamp = '".$dtTimestampAntigo."' ";
                           $obErro = $obCon->executaSql($rsXXX, $stSql , $boTransacao);

                           if ( !$obErro->ocorreu() ) {
                                $stSql  = "Alter Table   empenho.ordem_pagamento_liquidacao_anulada \n";
                                $stSql .= "Add   Constraint fk_ordem_pagamento_liquidacao_anulada_2 \n";
                                $stSql .= "FOREIGN KEY (exercicio, cod_entidade, cod_ordem, 'timestamp') \n";
                                $stSql .= "REFERENCES empenho.ordem_pagamento_anulada(exercicio, cod_entidade, cod_ordem, 'timestamp') \n";
                                $obErro = $obCon->executaSql($rsXXX, $stSql , $boTransacao);
                            }
                        }
                      }
                    }
                    if ($obErro->ocorreu())
                        return $obErro;

            }
//            echo "TESTE";
//            die();
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTEmpenhoEmpenho );

    return $obErro;
}
}
