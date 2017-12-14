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

/**
 * Extensão da Classe de Mapeamento TTCETOReceita
 *
 * Data de Criação: 12/11/2014
 *
 * @author Desenvolvedor: Diogo Zarpelon <diogo.zarpelon@cnm.org.br>
 *
 * @ignore
 *
*/
class TTCETOReceita extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCETOReceita()
    {
        parent::Persistente();
        $this->setDado('exercicio',Sessao::getExercicio());
    }
    
    /**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaReceita.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
    */
    public function recuperaReceita(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaReceita().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaReceita()
    {       
        $stSql = "
                    SELECT
                            Cod_Und_Gestora
                          , Codigo_UA
                          , bimestre
                          , exercicio
                          , cod_orgao
                          , cod_und_orcamentaria
                          , REPLACE(cod_receita,'.','') as cod_receita
                          , REPLACE(cod_conta_contabil,'.','') as cod_conta_contabil
                          , ABS(realizada_jan) as realizada_jan
                          , ABS(realizada_fev) as realizada_fev
                          , ABS(realizada_mar) as realizada_mar
                          , ABS(realizada_abr) as realizada_abr
                          , ABS(realizada_mai) as realizada_mai
                          , ABS(realizada_jun) as realizada_jun
                          , ABS(realizada_jul) as realizada_jul
                          , ABS(realizada_ago) as realizada_ago
                          , ABS(realizada_set) as realizada_set
                          , ABS(realizada_out) as realizada_out
                          , ABS(realizada_nov) as realizada_nov
                          , ABS(realizada_dez) as realizada_dez
                          , '000' AS carac_peculiar
                    FROM ( SELECT
                                (SELECT PJ.cnpj
                                       FROM orcamento.entidade
                                       JOIN sw_cgm
                                         ON sw_cgm.numcgm = entidade.numcgm
                                       JOIN sw_cgm_pessoa_juridica AS PJ
                                         ON sw_cgm.numcgm = PJ.numcgm
                                      WHERE entidade.exercicio    = '".$this->getDado('exercicio')."'
                                        AND entidade.cod_entidade = ".$this->getDado('und_gestora')."
                                ) AS Cod_Und_Gestora
                                , (SELECT LPAD(valor,4,'0') AS valor
                                       FROM administracao.configuracao_entidade
                                      WHERE exercicio = '".$this->getDado('exercicio')."'
                                        AND cod_entidade = ".$this->getDado('und_gestora')."
                                        AND cod_modulo   = 64
                                        AND parametro    = 'tceal_configuracao_unidade_autonoma'
                                ) AS Codigo_UA
                                , '".$this->getDado('bimestre')."' AS bimestre
                                , '".$this->getDado('exercicio')."' AS exercicio
                                , (SELECT LPAD(valor,2,'0') AS valor
                                        FROM administracao.configuracao
                                        WHERE exercicio = '".$this->getDado('exercicio')."'                                    
                                        AND cod_modulo  = 64
                                        AND parametro   = '".$this->getDado('poder_cod_orgao')."'
                                    ) AS cod_orgao
                                , (SELECT LPAD(valor,4,'0')
                                        FROM administracao.configuracao
                                        WHERE exercicio = '".$this->getDado('exercicio')."'                                    
                                        AND cod_modulo  = 64
                                        AND parametro   = '".$this->getDado('poder_cod_unidade')."'
                                ) AS cod_und_orcamentaria
                                , conta_receita.cod_estrutural AS cod_receita
                                , CASE WHEN conta_receita.cod_estrutural ilike '9.1.7%' THEN
                                            '6.2.1.3.1.01.00.00.00.00'::VARCHAR
                                        ELSE
                                            '6.2.1.2.0.00.00.00.00.00'::VARCHAR
                                  END as cod_conta_contabil                                                                
                ";
                $arSql = array();
                $arSql[] = "(SELECT tceal.fn_detalhamento_receitas('".$this->getDado('exercicio')."','','01/01/".$this->getDado('exercicio')."','".SistemaLegado::retornaUltimoDiaMes('1',$this->getDado('exercicio'))."','".$this->getDado('cod_entidade')."',conta_receita.cod_estrutural,conta_receita.cod_estrutural,receita.cod_receita::varchar,receita.cod_receita::varchar,recurso.cod_recurso::varchar,'','')) AS realizada_jan \n";
                $arSql[] = "(SELECT tceal.fn_detalhamento_receitas('".$this->getDado('exercicio')."','','01/02/".$this->getDado('exercicio')."','".SistemaLegado::retornaUltimoDiaMes('2',$this->getDado('exercicio'))."','".$this->getDado('cod_entidade')."',conta_receita.cod_estrutural,conta_receita.cod_estrutural,receita.cod_receita::varchar,receita.cod_receita::varchar,recurso.cod_recurso::varchar,'','')) AS realizada_fev \n";
                $arSql[] = "(SELECT tceal.fn_detalhamento_receitas('".$this->getDado('exercicio')."','','01/03/".$this->getDado('exercicio')."','".SistemaLegado::retornaUltimoDiaMes('3',$this->getDado('exercicio'))."','".$this->getDado('cod_entidade')."',conta_receita.cod_estrutural,conta_receita.cod_estrutural,receita.cod_receita::varchar,receita.cod_receita::varchar,recurso.cod_recurso::varchar,'','')) AS realizada_mar \n";
                $arSql[] = "(SELECT tceal.fn_detalhamento_receitas('".$this->getDado('exercicio')."','','01/04/".$this->getDado('exercicio')."','".SistemaLegado::retornaUltimoDiaMes('4',$this->getDado('exercicio'))."','".$this->getDado('cod_entidade')."',conta_receita.cod_estrutural,conta_receita.cod_estrutural,receita.cod_receita::varchar,receita.cod_receita::varchar,recurso.cod_recurso::varchar,'','')) AS realizada_abr \n";
                $arSql[] = "(SELECT tceal.fn_detalhamento_receitas('".$this->getDado('exercicio')."','','01/05/".$this->getDado('exercicio')."','".SistemaLegado::retornaUltimoDiaMes('5',$this->getDado('exercicio'))."','".$this->getDado('cod_entidade')."',conta_receita.cod_estrutural,conta_receita.cod_estrutural,receita.cod_receita::varchar,receita.cod_receita::varchar,recurso.cod_recurso::varchar,'','')) AS realizada_mai \n";
                $arSql[] = "(SELECT tceal.fn_detalhamento_receitas('".$this->getDado('exercicio')."','','01/06/".$this->getDado('exercicio')."','".SistemaLegado::retornaUltimoDiaMes('6',$this->getDado('exercicio'))."','".$this->getDado('cod_entidade')."',conta_receita.cod_estrutural,conta_receita.cod_estrutural,receita.cod_receita::varchar,receita.cod_receita::varchar,recurso.cod_recurso::varchar,'','')) AS realizada_jun \n";
                $arSql[] = "(SELECT tceal.fn_detalhamento_receitas('".$this->getDado('exercicio')."','','01/07/".$this->getDado('exercicio')."','".SistemaLegado::retornaUltimoDiaMes('7',$this->getDado('exercicio'))."','".$this->getDado('cod_entidade')."',conta_receita.cod_estrutural,conta_receita.cod_estrutural,receita.cod_receita::varchar,receita.cod_receita::varchar,recurso.cod_recurso::varchar,'','')) AS realizada_jul \n";
                $arSql[] = "(SELECT tceal.fn_detalhamento_receitas('".$this->getDado('exercicio')."','','01/08/".$this->getDado('exercicio')."','".SistemaLegado::retornaUltimoDiaMes('8',$this->getDado('exercicio'))."','".$this->getDado('cod_entidade')."',conta_receita.cod_estrutural,conta_receita.cod_estrutural,receita.cod_receita::varchar,receita.cod_receita::varchar,recurso.cod_recurso::varchar,'','')) AS realizada_ago \n";
                $arSql[] = "(SELECT tceal.fn_detalhamento_receitas('".$this->getDado('exercicio')."','','01/09/".$this->getDado('exercicio')."','".SistemaLegado::retornaUltimoDiaMes('9',$this->getDado('exercicio'))."','".$this->getDado('cod_entidade')."',conta_receita.cod_estrutural,conta_receita.cod_estrutural,receita.cod_receita::varchar,receita.cod_receita::varchar,recurso.cod_recurso::varchar,'','')) AS realizada_set \n";
                $arSql[] = "(SELECT tceal.fn_detalhamento_receitas('".$this->getDado('exercicio')."','','01/10/".$this->getDado('exercicio')."','".SistemaLegado::retornaUltimoDiaMes('10',$this->getDado('exercicio'))."','".$this->getDado('cod_entidade')."',conta_receita.cod_estrutural,conta_receita.cod_estrutural,receita.cod_receita::varchar,receita.cod_receita::varchar,recurso.cod_recurso::varchar,'','')) AS realizada_out \n";
                $arSql[] = "(SELECT tceal.fn_detalhamento_receitas('".$this->getDado('exercicio')."','','01/11/".$this->getDado('exercicio')."','".SistemaLegado::retornaUltimoDiaMes('11',$this->getDado('exercicio'))."','".$this->getDado('cod_entidade')."',conta_receita.cod_estrutural,conta_receita.cod_estrutural,receita.cod_receita::varchar,receita.cod_receita::varchar,recurso.cod_recurso::varchar,'','')) AS realizada_nov \n";
                $arSql[] = "(SELECT tceal.fn_detalhamento_receitas('".$this->getDado('exercicio')."','','01/12/".$this->getDado('exercicio')."','".SistemaLegado::retornaUltimoDiaMes('12',$this->getDado('exercicio'))."','".$this->getDado('cod_entidade')."',conta_receita.cod_estrutural,conta_receita.cod_estrutural,receita.cod_receita::varchar,receita.cod_receita::varchar,recurso.cod_recurso::varchar,'','')) AS realizada_dez \n";
                
                switch ($this->getDado('bimestre')) {
                    case 1:
                        $stSql .= ", ".$arSql[0].",".$arSql[1]."\n, 0.00 AS realizada_mar \n, 0.00 AS realizada_abr \n, 0.00 AS realizada_mai \n, 0.00 AS realizada_jun \n, 0.00 AS realizada_jul \n, 0.00 AS realizada_ago \n, 0.00 AS realizada_set \n, 0.00 AS realizada_out \n, 0.00 AS realizada_nov \n, 0.00 AS realizada_dez";
                    break;
                    case 2:
                        $stSql .= ", ".$arSql[0].",".$arSql[1].", ".$arSql[2].", ".$arSql[3].", 0.00 AS realizada_mai, 0.00 AS realizada_jun, 0.00 AS realizada_jul, 0.00 AS realizada_ago, 0.00 AS realizada_set, 0.00 AS realizada_out, 0.00 AS realizada_nov, 0.00 AS realizada_dez";
                    break;
                    case 3:
                        $stSql .= ", ".$arSql[0].",".$arSql[1].",".$arSql[2].", ".$arSql[3].", ".$arSql[4].", ".$arSql[5].", 0.00 AS realizada_jul, 0.00 AS realizada_ago, 0.00 AS realizada_set, 0.00 AS realizada_out, 0.00 AS realizada_nov, 0.00 AS realizada_dez";
                    break;
                    case 4:
                        $stSql .= ", ".$arSql[0].",".$arSql[1].",".$arSql[2].", ".$arSql[3].", ".$arSql[4].", ".$arSql[5].", ".$arSql[6].", ".$arSql[7].", 0.00 AS realizada_set, 0.00 AS realizada_out, 0.00 AS realizada_nov, 0.00 AS realizada_dez";
                    break;
                    case 5:
                        $stSql .= ", ".$arSql[0].",".$arSql[1].",".$arSql[2].", ".$arSql[3].", ".$arSql[4].", ".$arSql[5].", ".$arSql[6].", ".$arSql[7].", ".$arSql[8].", ".$arSql[9].", 0.00 AS realizada_nov, 0.00 AS realizada_dez";
                    break;
                    case 0:
                    case 6:
                    case 7:
                        $stSql .= ", ".$arSql[0].",".$arSql[1].",".$arSql[2].", ".$arSql[3].", ".$arSql[4].", ".$arSql[5].", ".$arSql[6].", ".$arSql[7].", ".$arSql[8].", ".$arSql[9].", ".$arSql[10].", ".$arSql[11]."";
                    break;
                }
                
                $stSql .= "
                                  
                            FROM orcamento.receita
                            
                            JOIN orcamento.conta_receita
                              ON conta_receita.cod_conta = receita.cod_conta
                             AND conta_receita.exercicio = receita.exercicio
                             
                            JOIN orcamento.recurso
                              ON recurso.cod_recurso = receita.cod_recurso
                             AND recurso.exercicio = receita.exercicio
                            
                           WHERE receita.exercicio = '".Sessao::getExercicio()."'
                             AND receita.cod_entidade IN (".$this->getDado('cod_entidade').")
                             AND receita.vl_original <> 0.00
                        ) AS tabela
                ORDER BY cod_receita
        ";
        
        return $stSql;
    }
}
?>
