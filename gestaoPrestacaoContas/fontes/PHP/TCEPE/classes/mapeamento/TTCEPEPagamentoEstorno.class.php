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
    * Data de Criação: 09/10/2014
    * @author Analista: 
    * @author Desenvolvedor: 
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEPEPagamentoEstorno extends Persistente
{

    function montaRecuperaTodos()
    {
        $stSql = "  SELECT
                            0 AS reservado_tce,
                            retorno.exercicio,
                            LPAD(despesa.num_orgao::VARCHAR, 2, '0') || LPAD(despesa.num_unidade::VARCHAR, 2, '0') AS unidade_orcamentaria,
                            retorno.empenho AS num_empenho,
                            1 AS num_estorno,
                            REPLACE(retorno.data::VARCHAR, '/', '') AS dt_estorno,
                            COALESCE(SUM(retorno.valor),0.00) AS vl_estorno,
                            retorno.descricao AS historico_estorno
                            
                    FROM (SELECT * FROM empenho.fn_empenho_empenhado_pago_liquidado ('".$this->getDado('exercicio')."',  
                                                                                     '',
                                                                                     '".$this->getDado('dt_inicial')."', 
                                                                                     '".$this->getDado('dt_final')."',
                                                                                     '".$this->getDado('cod_entidade')."',
                                                                                     '','',
                                                                                     '','',
                                                                                     '','',
                                                                                     '','',
                                                                                     '5','',
                                                                                     'data','',
                                                                                     '','', 
                                                                                     '', '', true
                                                                                    ) as retorno (                        
                                                                                                    entidade            integer,                                           
                                                                                                    descricao_categoria varchar,                                           
                                                                                                    nom_tipo            varchar,                                           
                                                                                                    empenho             integer,                                           
                                                                                                    exercicio           char(4),                                           
                                                                                                    cgm                 integer,                                           
                                                                                                    razao_social        varchar,                                           
                                                                                                    cod_nota            integer,                                           
                                                                                                    data                text,                                              
                                                                                                    ordem               integer,                                           
                                                                                                    conta               integer,                                           
                                                                                                    nome_conta          varchar,                                           
                                                                                                    valor               numeric,                                           
                                                                                                    valor_anulado       numeric,                                           
                                                                                                    descricao           varchar,                                           
                                                                                                    recurso             varchar,                                           
                                                                                                    despesa             varchar                                            
                                                                                                )
                        ) AS retorno
                        
                    JOIN empenho.empenho
                      ON empenho.cod_empenho = retorno.empenho
                     AND empenho.cod_entidade = retorno.entidade
                     AND empenho.exercicio = retorno.exercicio
                     
                    JOIN empenho.pre_empenho
                      ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                     AND pre_empenho.exercicio = empenho.exercicio
                     
                    JOIN empenho.pre_empenho_despesa
                      ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                     AND pre_empenho_despesa.exercicio = pre_empenho.exercicio
                     
                    JOIN orcamento.despesa
                      ON despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                     AND despesa.exercicio = pre_empenho_despesa.exercicio
                     
                    GROUP BY reservado_tce, retorno.exercicio, despesa.num_orgao, despesa.num_unidade, retorno.empenho, retorno.data, retorno.descricao
            ";
            
        return $stSql;
        
    }

}//FIM CLASSE
