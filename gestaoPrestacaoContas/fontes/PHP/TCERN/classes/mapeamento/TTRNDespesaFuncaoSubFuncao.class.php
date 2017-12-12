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
    * Extensão da Classe de mapeamento
    * Data de Criação: 12/10/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 26070 $
    $Name$
    $Author: diego $
    $Date: 2007-10-13 18:51:13 -0300 (SÃ¡b, 13 Out 2007) $

    * Casos de uso: uc-06.06.00
*/

/*
$Log$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criação: 12/10/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTRNDespesaFuncaoSubFuncao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTRNDespesaFuncaoSubFuncao()
{
    $this->setEstrutura( array() );
    $this->setEstruturaAuxiliar( array() );
    $this->setDado('exercicio',Sessao::getExercicio());
}

function montaRecuperaRelacionamento()
{
//
//    $stSql .= "
//    SELECT   empe.exercicio
//            ,cont.cod_estrutural as cod_estrutural
//            ,dota.cod_funcao
//            ,dota.cod_subfuncao
//            ,lpad(replace(sum(dota.vl_original),'.',''),14,'0') as dotacao_inicial
//            ,lpad(replace(sum(emp_bim),'.',''),14,'0') as empenho_bimestre
//            ,lpad(replace(sum(emp_ano),'.',''),14,'0') as empenho_exercicio
//            ,lpad(replace(sum(liq_bim),'.',''),14,'0') as liquidacao_bimestre
//            ,lpad(replace(sum(liq_ano),'.',''),14,'0') as liquidacao_exercicio
//            ,coalesce(sum(emp_ano),0.00)
//            ,lpad(replace(coalesce(sum(liq_ano),0.00),'.',''),14,'0') as restos_pagar_nao_processados
//    FROM     empenho.empenho                as empe
//            ,empenho.pre_empenho            as pree
//            /*Saldo Empenho no Ano*/
//            LEFT JOIN
//            (   SELECT  ipe.exercicio, ipe.cod_pre_empenho, coalesce(sum(vl_total),0.00)-coalesce(sum(vl_anulado),0.00) as emp_ano
//                FROM    empenho.item_pre_empenho ipe
//                        LEFT JOIN
//                        empenho.empenho_anulado_item as eai
//                        ON (ipe.exercicio       = eai.exercicio
//                        AND ipe.cod_pre_empenho = eai.cod_pre_empenho
//                        AND ipe.num_item        = eai.num_item
//                        )
//                WHERE   ipe.exercicio = ".$this->getDado('exercicio')."
//                GROUP BY ipe.exercicio, ipe.cod_pre_empenho
//            ) as siea
//            ON (pree.exercicio      = siea.exercicio
//            AND pree.cod_pre_empenho= siea.cod_pre_empenho
//            )
//            /*Saldo Empenho no Bimestre*/
//            LEFT JOIN
//            (   SELECT  ipe.exercicio, ipe.cod_pre_empenho, coalesce(sum(vl_total),0.00)-coalesce(sum(vl_anulado),0.00) as emp_bim
//                FROM    empenho.item_pre_empenho ipe
//                        LEFT JOIN
//                        empenho.empenho_anulado_item as eai
//                        ON (ipe.exercicio       = eai.exercicio
//                        AND ipe.cod_pre_empenho = eai.cod_pre_empenho
//                        AND ipe.num_item        = eai.num_item
//                        )
//                WHERE   ipe.exercicio = ".$this->getDado('exercicio')."
//                GROUP BY ipe.exercicio, ipe.cod_pre_empenho
//            ) as sieb
//            ON (pree.exercicio      = sieb.exercicio
//            AND pree.cod_pre_empenho= sieb.cod_pre_empenho
//            )
//            /*Saldo Liquidação no Ano*/
//            LEFT JOIN
//            (   SELECT  ite.exercicio_item as exercicio, ite.cod_pre_empenho, coalesce(sum(vl_total),0.00)-coalesce(sum(vl_anulado),0.00) as liq_ano
//                FROM    empenho.nota_liquidacao_item ite
//                        LEFT JOIN
//                        empenho.nota_liquidacao_item_anulado as ian
//                        ON (ite.exercicio       = ian.exercicio
//                        AND ite.exercicio_item  = ian.exercicio_item
//                        AND ite.cod_pre_empenho = ian.cod_pre_empenho
//                        AND ite.cod_entidade    = ian.cod_entidade
//                        AND ite.cod_nota        = ian.cod_nota
//                        AND ite.num_item        = ian.num_item
//                        )
//                WHERE   ite.exercicio = ".$this->getDado('exercicio')."
//                ".($this->getDado('inCodEntidade')? " AND ite.cod_entidade =".$this->getDado('inCodEntidade')."  ":"")."
//                GROUP BY ite.exercicio_item, ite.cod_pre_empenho
//            ) as sila
//            ON (pree.exercicio      = sila.exercicio
//            AND pree.cod_pre_empenho= sila.cod_pre_empenho
//            )
//            /*Saldo Liquidação no Bimestre*/
//            LEFT JOIN
//            (   SELECT  ite.exercicio_item as exercicio, ite.cod_pre_empenho, coalesce(sum(vl_total),0.00)-coalesce(sum(vl_anulado),0.00) as liq_bim
//                FROM    empenho.nota_liquidacao_item ite
//                        LEFT JOIN
//                        empenho.nota_liquidacao_item_anulado as ian
//                        ON (ite.exercicio       = ian.exercicio
//                        AND ite.exercicio_item  = ian.exercicio_item
//                        AND ite.cod_pre_empenho = ian.cod_pre_empenho
//                        AND ite.cod_entidade    = ian.cod_entidade
//                        AND ite.cod_nota        = ian.cod_nota
//                        AND ite.num_item        = ian.num_item
//                        )
//                WHERE   ite.exercicio = ".$this->getDado('exercicio')."
//                ".($this->getDado('inCodEntidade')? " AND ite.cod_entidade =".$this->getDado('inCodEntidade')."  ":"")."
//                GROUP BY ite.exercicio_item, ite.cod_pre_empenho
//            ) as silb
//            ON (pree.exercicio      = silb.exercicio
//            AND pree.cod_pre_empenho= silb.cod_pre_empenho
//            )
//
//
//            LEFT JOIN
//             empenho.pre_empenho_despesa    as pred
//            ON (pree.exercicio      = pred.exercicio
//            AND pree.cod_pre_empenho= pred.cod_pre_empenho
//            )
//            ,orcamento.despesa              as dota
//            ,orcamento.conta_despesa        as cont
//
//    WHERE   empe.exercicio = ".$this->getDado('exercicio')."
//".($this->getDado('inCodEntidade')? " AND empe.cod_entidade =".$this->getDado('inCodEntidade')."  ":"")."
//    AND     empe.exercicio      = pree.exercicio
//    AND     empe.cod_pre_empenho= pree.cod_pre_empenho
//
//    AND     pred.exercicio      = dota.exercicio
//    AND     pred.cod_despesa    = dota.cod_despesa
//
//    AND     dota.exercicio      = cont.exercicio
//    AND     dota.cod_conta      = cont.cod_conta
//
//    GROUP BY empe.exercicio
//            ,cont.cod_estrutural
//            ,dota.cod_funcao
//            ,dota.cod_subfuncao
//
//    ORDER BY empe.exercicio
//            ,dota.cod_funcao
//            ,dota.cod_subfuncao
//
//    ";
    $stSql .= "
    SELECT  cod_funcao
         ,  cod_subfuncao
         ,  SUM(COALESCE(vl_original,0)) AS vl_original
         ,  SUM(COALESCE(vl_atualizado,0)) AS vl_atualizado
         ,  SUM(COALESCE(vl_empenho_bimestre,0)) AS vl_empenho_bimestre
         ,  SUM(COALESCE(vl_empenho_ano,0)) AS vl_empenho_ano
         ,  SUM(COALESCE(vl_liquidacao_bimestre,0)) AS vl_liquidacao_bimestre
         ,  SUM(COALESCE(vl_liquidacao_ano,0)) AS vl_liquidacao_ano
         ,  SUM(COALESCE(vl_restos_pagar,0)) AS vl_restos_pagar
            FROM    tcern.fn_exportacao_despesa_funcao('".$this->getDado('exercicio')."','".$this->getDado('inCodEntidade')."','".$this->getDado('dtInicial')."','".$this->getDado('dtFinal')."') as tabela
                    (   cod_funcao  integer
                        ,cod_subfuncao integer
                        ,vl_original numeric
                        ,vl_atualizado numeric
                        ,vl_empenho_bimestre numeric
                        ,vl_empenho_ano numeric
                        ,vl_liquidacao_bimestre numeric
                        ,vl_liquidacao_ano numeric
                        ,vl_restos_pagar numeric
                    )
          GROUP BY cod_funcao
                 , cod_subfuncao
        ";

    return $stSql;
}

function recuperaDadosSaldoDotacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaRelacionamentoSaldoDotacao().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoSaldoDotacao()
{
    $stSql .= " SELECT
                    lpad(replace(stn.fn_saldo_dotacao_atualizado('".$this->getDado('exercicio')."', '".$this->getDado('codEstrutural')."', '".$this->getDado('inCodEntidade')."', '".$this->getDado('dtInicial')."', '".$this->getDado('dtFinal')."')::varchar,'.',''),14,'0') as dotacao_acumulada   \n";

    return $stSql;
}

}
