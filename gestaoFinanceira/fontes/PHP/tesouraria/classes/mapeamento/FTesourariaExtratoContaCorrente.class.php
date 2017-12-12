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

    * Classe de mapeamento da tabela FN_RELATORIO_EXTRATO_CONTA_CORRENTE
    * Data de Criação: 16/11//2005

    * @author Analista: Eduardo Schitz
    * @author Desenvolvedor: Carolina Schwaab Marçal

    * @package URBEM
    * @subpackage Mapeamento

    $id: $
*/


include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FTesourariaExtratoContaCorrente extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FTesourariaExtratoContaCorrente()
{
    parent::Persistente();
}

function montaRecuperaTodos()
{
    $stSql  = "select *                                                                           
                       from tesouraria.fn_relatorio_extrato_conta_corrente
                            (" . $this->getDado("inCodPlano") .", 
                            '" . $this->getDado("stExercicio") . "',                                            
                            '" . $this->getDado("stEntidade")."',                                              
                            '" . $this->getDado("stDataInicial")."',                                             
                            '" . $this->getDado("stDataFinal")."',                                               
                            '" . $this->getDado("botcems")."' ,                                                    
                            '" . $this->getDado("credor")."'                
                            ) as retorno(                                               
                              hora                text                                                          
                            , data                text                                                        
                            , descricao           varchar                                                      
                            , valor               numeric                                                      
                            , cod_lote            numeric                                                       
                            , cod_arrecadacao     numeric                                                     
                            , tipo_valor          varchar                                                     
                            , situacao            varchar                                                     
                            , cod_situacao        varchar                                                     
                           ) order by data, cod_arrecadacao ASC ";
    return $stSql;
}

function recuperaSaldoAnteriorAtual(&$rsRecordSet, $stCondicao = "", $stOrder = "",  $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if ($stOrder != "") {
        if( !strstr( $stOrder, "ORDER BY" ) )
            $stOrder = " ORDER BY ".$stOrder;
    }
    $stSql = $this->montaRecuperaSaldoAnteriorAtual().$stCondicao;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaSaldoAnteriorAtual()
{
     $stSql  = "select  *                                                                                   \n";
     $stSql .= "     FROM                                                                                   \n";
     $stSql .= "         tesouraria.fn_saldo_conta_tesouraria ('". $this->getDado("stExercicio") ."',         \n";
     $stSql .= "          ". $this->getDado("inCodPlano") .",                                               \n";
     $stSql .= "         '". $this->getDado("stDtInicial")."',                                              \n";
     $stSql .= "         '". $this->getDado("stDtFinal")."',                                                \n";
     $stSql .= "         ". $this->getDado("boMovimentacao").")                                                \n";

    return $stSql;

}


function recuperaDadosContaCorrente(&$rsRecordSet, $stCondicao = "", $stOrder = "",  $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if ($stOrder == "") {
        $stOrder .= " ORDER BY 1";
    }
    $stSql = $this->montaRecuperaDadosContaCorrente().$stCondicao.$stOrder;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosContaCorrente()
{
    $stSql = "  SELECT plano_analitica.cod_plano
                                , plano_conta.nom_conta
                                , plano_conta.cod_estrutural
                                , (select publico.fn_nivel(plano_conta.cod_estrutural)) as nivel
                                , plano_banco.cod_banco
                                , plano_banco.cod_agencia
                                , plano_banco.conta_corrente
                                , MA.nom_agencia
                                , MB.nom_banco
                         FROM  contabilidade.plano_conta      
                             
                 INNER JOIN contabilidade.plano_analitica
                             ON plano_analitica.exercicio = plano_conta.exercicio
                           AND plano_analitica.cod_conta = plano_conta.cod_conta

                  INNER JOIN contabilidade.plano_banco                        
                              ON plano_banco.exercicio= plano_analitica.exercicio
                            AND plano_banco.cod_plano = plano_analitica.cod_plano
 
                     LEFT JOIN monetario.agencia as MA 
                              ON plano_banco.cod_banco    = MA.cod_banco
                            AND plano_banco.cod_agencia  = MA.cod_agencia
                    
                     LEFT JOIN monetario.banco as MB 
                              ON MA.cod_banco    = MB.cod_banco
                          
                    LEFT JOIN contabilidade.plano_recurso 
                             ON plano_recurso.exercicio = plano_analitica.exercicio
                           AND plano_recurso.cod_plano = plano_analitica.cod_plano
                           
                      WHERE  plano_banco.exercicio ='".$this->getDado("stExercicio")."'
             ";
    if ( ( $this->getDado("inCodPlanoInicial") ) and ( $this->getDado("inCodPlanoFinal") ) ) {
        $stSql .= " AND plano_analitica.cod_plano BETWEEN " . $this->getDado("inCodPlanoInicial") . " AND " . $this->getDado("inCodPlanoFinal") ;
    } elseif ( $this->getDado("inCodPlanoInicial") ) {
        $stSql .= " AND plano_analitica.cod_plano =" . $this->getDado("inCodPlanoInicial") ;
    } elseif ( $this->getDado("inCodPlanoFinal") ) {
        $stSql .= " AND plano_analitica.cod_plano =" . $this->getDado("inCodPlanoFinal") ;
    }
    if (  $this->getDado("inCodBanco")  ) {
        $stSql .= " AND plano_banco.cod_banco =" . $this->getDado("inCodBanco") ;
    }
    if (  $this->getDado("inCodAgencia")  ) {
        $stSql .= " AND plano_banco.cod_agencia =" . $this->getDado("inCodAgencia") ;
    }
    if (  $this->getDado("stContaCorrente")  ) {
        $stSql .= " AND plano_banco.conta_corrente ='" . $this->getDado("stContaCorrente") ."'" ;
    }
     if (  $this->getDado("inCodRecurso")  ) {
        $stSql .= " AND plano_recurso.cod_recurso =" . $this->getDado("inCodRecurso") ."" ;
    }

    return $stSql;

}


}
