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
/*
 * Arquivo de mapeamento da tabela tcepb.configurar_ide
 * Data de Criação   : 07/01/2014

 * @author Analista      Eduardo Paculski Schitz
 * @author Desenvolvedor Franver Sarmento de Moraes

 * @package URBEM
 * @subpackage

 * @ignore

  $Id: TTCEMGConfiguracaoPERC.class.php 57368 2014-02-28 17:23:28Z diogo.zarpelon $
  $Date: 2014-02-28 14:23:28 -0300 (Fri, 28 Feb 2014) $
  $Author: diogo.zarpelon $
  $Rev: 57368 $

 */

class TTCEMGincamp extends Persistente
{

    public function recuperaRegistro10(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
    {
        return $this->executaRecupera("montaRecuperaRegistro10", $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
    }

    public function montaRecuperaRegistro10()
    {
        $stSql = "SELECT 10 AS tipo_registro
                        , 2 AS possui_sub_acao
                        , LPAD(acao.num_acao::VARCHAR, 4, '0') AS id_acao
                        , acao.cod_acao
                        , acao_dados.descricao AS desc_acao
                        , acao_dados.finalidade AS finalidade_acao
                        , produto.descricao AS produto
                        , unidade_medida.nom_unidade AS unidade_medida
                        , REPLACE(SUM(total_metas.ano1)::VARCHAR, '.',',') AS metas_ano_1
                        , REPLACE(SUM(total_metas.ano2)::VARCHAR, '.',',') AS metas_ano_2
                        , REPLACE(SUM(total_metas.ano3)::VARCHAR, '.',',') AS metas_ano_3
                        , REPLACE(SUM(total_metas.ano4)::VARCHAR, '.',',') AS metas_ano_4
                        , REPLACE(SUM(total_recursos.ano1)::VARCHAR, '.',',') AS recursos_ano_1
                        , REPLACE(SUM(total_recursos.ano2)::VARCHAR, '.',',') AS recursos_ano_2
                        , REPLACE(SUM(total_recursos.ano3)::VARCHAR, '.',',') AS recursos_ano_3
                        , REPLACE(SUM(total_recursos.ano4)::VARCHAR, '.',',') AS recursos_ano_4
                        
                        FROM ppa.acao
                        
                        JOIN ppa.programa
                          ON acao.cod_programa = programa.cod_programa
                          
                        JOIN ppa.programa_dados
                          ON programa.cod_programa = programa_dados.cod_programa
                         AND programa.ultimo_timestamp_programa_dados = programa_dados.timestamp_programa_dados
                         
                        JOIN ppa.programa_setorial
                          ON programa.cod_setorial = programa_setorial.cod_setorial
                          
                        JOIN ppa.macro_objetivo
                          ON programa_setorial.cod_macro = macro_objetivo.cod_macro
                          
                        JOIN ppa.ppa
                          ON macro_objetivo.cod_ppa = ppa.cod_ppa
                          
                        JOIN ppa.acao_dados
                          ON acao.cod_acao = acao_dados.cod_acao
                         AND acao.ultimo_timestamp_acao_dados = acao_dados.timestamp_acao_dados
                         
                   LEFT JOIN ppa.produto
                          ON acao_dados.cod_produto = produto.cod_produto
                          
                   LEFT JOIN administracao.unidade_medida
                          ON unidade_medida.cod_unidade  = acao_dados.cod_unidade_medida
                         AND unidade_medida.cod_grandeza = acao_dados.cod_grandeza
                         
                        JOIN (
                                SELECT ano1.cod_acao
                                     , ano1.timestamp_acao_dados
                                     , ano1.exercicio_recurso
                                     , COALESCE(ano1.valor, 0.00) AS ano1
                                     , COALESCE(ano2.valor, 0.00) AS ano2
                                     , COALESCE(ano3.valor, 0.00) AS ano3
                                     , COALESCE(ano4.valor, 0.00) AS ano4
                                FROM ppa.acao_recurso AS ano1
                          INNER JOIN orcamento.recurso('".Sessao::getExercicio()."') AS recurso
                                  ON ano1.cod_recurso   = recurso.cod_recurso
                           LEFT JOIN ppa.acao_recurso AS ano2
                                  ON ano2.ano = '2'
                                 AND ano1.cod_acao             = ano2.cod_acao
                                 AND ano1.timestamp_acao_dados = ano2.timestamp_acao_dados
                                 AND ano1.cod_recurso          = ano2.cod_recurso
                           LEFT JOIN ppa.acao_recurso AS ano3
                                  ON ano3.ano = '3'
                                 AND ano1.cod_acao             = ano3.cod_acao
                                 AND ano1.timestamp_acao_dados = ano3.timestamp_acao_dados
                                 AND ano1.cod_recurso          = ano3.cod_recurso
                           LEFT JOIN ppa.acao_recurso AS ano4
                                  ON ano4.ano = '4'
                                 AND ano1.cod_acao             = ano4.cod_acao
                                 AND ano1.timestamp_acao_dados = ano4.timestamp_acao_dados
                                 AND ano1.cod_recurso          = ano4.cod_recurso
                               WHERE ano1.ano = '1'
                            ) AS total_recursos
                          ON total_recursos.cod_acao = acao.cod_acao
                          
                        JOIN (
                                SELECT ano1.cod_acao
                                     , ano1.timestamp_acao_dados
                                     , COALESCE(ano1.valor, 0.00) AS ano1
                                     , COALESCE(ano2.valor, 0.00) AS ano2
                                     , COALESCE(ano3.valor, 0.00) AS ano3
                                     , COALESCE(ano4.valor, 0.00) AS ano4
                                FROM ppa.acao_quantidade as ano1
                           LEFT JOIN ppa.acao_quantidade as ano2
                                  ON ano2.ano = '2'
                                 AND ano1.cod_acao             = ano2.cod_acao
                                 AND ano1.timestamp_acao_dados = ano2.timestamp_acao_dados
                                 AND ano1.cod_recurso          = ano2.cod_recurso
                           LEFT JOIN ppa.acao_quantidade as ano3
                                  ON ano3.ano = '3'
                                 AND ano1.cod_acao             = ano3.cod_acao
                                 AND ano1.timestamp_acao_dados = ano3.timestamp_acao_dados
                                 AND ano1.cod_recurso          = ano3.cod_recurso
                           LEFT JOIN ppa.acao_quantidade as ano4
                                  ON ano4.ano = '4'
                                 AND ano1.cod_acao             = ano4.cod_acao
                                 AND ano1.timestamp_acao_dados = ano4.timestamp_acao_dados
                                 AND ano1.cod_recurso          = ano4.cod_recurso
                               WHERE ano1.ano = '1'
                            ) AS total_metas
                          ON total_metas.cod_acao = acao.cod_acao
                          
                       WHERE NOT EXISTS (
                                         SELECT cod_acao
                                           FROM tcemg.inscricao_acao
                                          WHERE inscricao_acao.cod_acao = acao_dados.cod_acao
                                            AND inscricao_acao.exercicio = acao_dados.exercicio
                                        )
                         AND NOT EXISTS (
                                         SELECT 1
                                           FROM tcemg.arquivo_incamp
                                          WHERE arquivo_incamp.cod_acao  = acao.cod_acao
                                            AND arquivo_incamp.exercicio = '".$this->getDado('exercicio')."'
                                            AND arquivo_incamp.mes < ".$this->getDado('mes')." 
                                        )
                         AND NOT EXISTS (
                                         SELECT 1
                                           FROM tcemg.arquivo_amp
                                          WHERE arquivo_amp.cod_acao  = acao.cod_acao
                                            AND arquivo_amp.exercicio = '".$this->getDado('exercicio')."'
                                            AND arquivo_amp.mes <= ".$this->getDado('mes')." 
                                        )
                                        
                    GROUP BY acao.num_acao
                           , acao.cod_acao
                           , acao_dados.descricao
                           , acao_dados.finalidade
                           , produto.descricao 
                           , unidade_medida.nom_unidade
                           
                    ORDER BY acao.num_acao";

        return $stSql;
    }
    
    public function recuperaRegistro12(&$rsRecordSet, $stCondicao = '' , $stOrdem = '' , $boTransacao = '')
    {
        return $this->executaRecupera("montaRecuperaRegistro12", $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
    }

    public function montaRecuperaRegistro12()
    {
        $stSql = "   SELECT 12 AS tipo_registro
                          , LPAD(".$this->getDado('cod_orgao')."::VARCHAR, 2, '0') AS cod_orgao
                          , LPAD(LPAD(acao_unidade_executora.num_orgao::VARCHAR, 2, '0')||LPAD(acao_unidade_executora.num_unidade::VARCHAR, 2, '0'),5,'0') AS cod_unidade
                          , LPAD(acao_dados.cod_funcao::VARCHAR,2,'0') AS cod_funcao
                          , LPAD(acao_dados.cod_subfuncao::VARCHAR,3,'0') AS cod_subfuncao
                          , LPAD(programa.num_programa::VARCHAR,4,'0') AS cod_programa
                          , acao.cod_acao
                          , LPAD(acao.num_acao::VARCHAR, 4, '0') AS id_acao
                          , ' ' AS id_sub_acao
                          , COALESCE((SELECT ppa.busca_valor_meta_ano_ppa(acao.cod_acao,'1',acao.ultimo_timestamp_acao_dados)), 0.00) AS metas_ano_1
                          , COALESCE((SELECT ppa.busca_valor_meta_ano_ppa(acao.cod_acao,'2',acao.ultimo_timestamp_acao_dados)), 0.00) AS metas_ano_2
                          , COALESCE((SELECT ppa.busca_valor_meta_ano_ppa(acao.cod_acao,'3',acao.ultimo_timestamp_acao_dados)), 0.00) AS metas_ano_3
                          , COALESCE((SELECT ppa.busca_valor_meta_ano_ppa(acao.cod_acao,'4',acao.ultimo_timestamp_acao_dados)), 0.00) AS metas_ano_4
                          , COALESCE((SELECT ppa.busca_valor_recurso_ano_ppa(acao.cod_acao,'1',acao.ultimo_timestamp_acao_dados)), 0.00) AS recursos_ano_1
                          , COALESCE((SELECT ppa.busca_valor_recurso_ano_ppa(acao.cod_acao,'2',acao.ultimo_timestamp_acao_dados)), 0.00) AS recursos_ano_2
                          , COALESCE((SELECT ppa.busca_valor_recurso_ano_ppa(acao.cod_acao,'3',acao.ultimo_timestamp_acao_dados)), 0.00) AS recursos_ano_3
                          , COALESCE((SELECT ppa.busca_valor_recurso_ano_ppa(acao.cod_acao,'4',acao.ultimo_timestamp_acao_dados)), 0.00) AS recursos_ano_4

                       FROM ppa.acao
                       
                       JOIN ppa.acao_dados
                         ON acao_dados.cod_acao = acao.cod_acao
                        AND acao_dados.timestamp_acao_dados = acao.ultimo_timestamp_acao_dados
                        
                       JOIN ppa.acao_unidade_executora
                         ON acao_unidade_executora.cod_acao = acao.cod_acao
                        AND acao_unidade_executora.timestamp_acao_dados = acao.ultimo_timestamp_acao_dados
                        
                       JOIN ppa.programa
                         ON programa.cod_programa = acao.cod_programa
                          
                      WHERE NOT EXISTS (
                                         SELECT cod_acao
                                           FROM tcemg.inscricao_acao
                                          WHERE inscricao_acao.cod_acao = acao_dados.cod_acao
                                            AND inscricao_acao.exercicio = acao_dados.exercicio
                                       )
                                       
                    GROUP BY tipo_registro
                           , cod_orgao
                           , cod_unidade
                           , programa.num_programa
                           , id_acao
                           , id_sub_acao
                           , acao_dados.cod_funcao
                           , acao.cod_acao
                           , acao_dados.cod_subfuncao
                           
                    ORDER BY acao.num_acao
         ";
         
        return $stSql;
    }
    
    public function __destruct(){}
    
}
?>
