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
    * Data de Criação: 08/10/2014
    * @author Analista: 
    * @author Desenvolvedor: 
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEPEReceitaExtra extends Persistente
{
function montaRecuperaTodos()
{
    $stSql = "  SELECT  
                         REPLACE(cod_estrutural,'.','') as conta_correspondente
                        ,tipo_registro
                        ,SUM(valor) as valor
                        ,'0' as unidade_gestora
                        , cod_fonte as cod_fonte_recurso
                FROM (
                        SELECT  
                                 transferencia.timestamp_transferencia as dt_transferencia
                                , transferencia.tipo
                                , transferencia.cod_tipo                                    
                                , transferencia.valor AS valor                                    
                                , '1' as tipo_registro
                                , plano_conta.cod_estrutural
                                , codigo_fonte_recurso.cod_fonte
                                    
                        FROM  tesouraria.transferencia
                                
                        LEFT JOIN  tesouraria.recibo_extra_transferencia
                             ON  recibo_extra_transferencia.cod_lote     = transferencia.cod_lote
                            AND  recibo_extra_transferencia.cod_entidade = transferencia.cod_entidade
                            AND  recibo_extra_transferencia.exercicio    = transferencia.exercicio
                            AND  recibo_extra_transferencia.tipo         = transferencia.tipo
                                   
                        JOIN contabilidade.plano_analitica
                             ON  plano_analitica.cod_plano = transferencia.cod_plano_credito
                            AND  plano_analitica.exercicio = transferencia.exercicio
                                
                        LEFT JOIN tcepe.plano_analitica_relacionamento
                             ON plano_analitica.cod_plano  = plano_analitica_relacionamento.cod_plano
                            AND plano_analitica.exercicio  = plano_analitica_relacionamento.exercicio
                            AND plano_analitica_relacionamento.tipo = 'R'

                        LEFT JOIN  contabilidade.plano_recurso
                             ON  plano_recurso.exercicio = plano_analitica.exercicio
                            AND  plano_recurso.cod_plano = plano_analitica.cod_plano
                              
                        LEFT JOIN  orcamento.recurso
                             ON  recurso.cod_recurso = plano_recurso.cod_recurso
                            AND  recurso.exercicio   = plano_recurso.exercicio

                        LEFT JOIN tcepe.codigo_fonte_recurso
                             ON codigo_fonte_recurso.cod_recurso = recurso.cod_recurso
                            AND codigo_fonte_recurso.exercicio   = recurso.exercicio
                                   
                        JOIN  contabilidade.plano_conta
                             ON  plano_conta.cod_conta = plano_analitica.cod_conta
                            AND  plano_conta.exercicio = plano_analitica.exercicio
                                
                        LEFT JOIN  contabilidade.plano_banco
                             ON  plano_banco.cod_plano = transferencia.cod_plano_credito
                            AND  plano_banco.exercicio = transferencia.exercicio
                              
                        LEFT JOIN  monetario.conta_corrente
                             ON  conta_corrente.cod_banco          = plano_banco.cod_banco
                            AND  conta_corrente.cod_agencia        = plano_banco.cod_agencia
                            AND  conta_corrente.cod_conta_corrente = plano_banco.cod_conta_corrente
                              
                        LEFT JOIN  monetario.banco
                             ON  banco.cod_banco = conta_corrente.cod_banco
                              
                        JOIN  orcamento.entidade
                             ON  entidade.cod_entidade = transferencia.cod_entidade
                            AND  entidade.exercicio    = transferencia.exercicio
                                    
                        JOIN sw_cgm as entidade_cgm
                             ON  entidade_cgm.numcgm = entidade.numcgm
                                  
                        WHERE entidade.cod_entidade IN (".$this->getDado('cod_entidade').")
                        AND entidade.exercicio = '".$this->getDado('exercicio')."'

                        UNION

                        SELECT  
                                 transferencia.timestamp_transferencia as dt_transferencia                                    
                                , transferencia.tipo
                                , transferencia.cod_tipo
                                , transferencia_estornada.valor
                                ,  '2' as tipo_registro
                                , plano_conta.cod_estrutural
                                , codigo_fonte_recurso.cod_fonte

                        FROM  tesouraria.transferencia
                                
                        JOIN  tesouraria.transferencia_estornada
                             ON  transferencia_estornada.exercicio    = transferencia.exercicio
                            AND  transferencia_estornada.cod_entidade = transferencia.cod_entidade
                            AND  transferencia_estornada.cod_lote     = transferencia.cod_lote
                            AND  transferencia_estornada.tipo         = transferencia.tipo
                                    
                        LEFT JOIN  tesouraria.recibo_extra_transferencia
                             ON  recibo_extra_transferencia.cod_lote     = transferencia.cod_lote
                            AND  recibo_extra_transferencia.cod_entidade = transferencia.cod_entidade
                            AND  recibo_extra_transferencia.exercicio    = transferencia.exercicio
                            AND  recibo_extra_transferencia.tipo         = transferencia.tipo
                              
                        JOIN  contabilidade.plano_analitica
                             ON  plano_analitica.cod_plano = transferencia.cod_plano_credito
                            AND  plano_analitica.exercicio = transferencia.exercicio
                                   
                        LEFT JOIN  tcepe.plano_analitica_relacionamento
                             ON  plano_analitica.cod_plano = plano_analitica_relacionamento.cod_plano
                            AND  plano_analitica.exercicio = plano_analitica_relacionamento.exercicio
                            AND plano_analitica_relacionamento.tipo = 'R'

                        LEFT JOIN  contabilidade.plano_recurso
                             ON  plano_recurso.exercicio = plano_analitica.exercicio
                            AND  plano_recurso.cod_plano = plano_analitica.cod_plano
                              
                        LEFT JOIN  orcamento.recurso
                             ON  recurso.cod_recurso = plano_recurso.cod_recurso
                            AND  recurso.exercicio   = plano_recurso.exercicio

                        LEFT JOIN tcepe.codigo_fonte_recurso
                             ON codigo_fonte_recurso.cod_recurso = recurso.cod_recurso
                            AND codigo_fonte_recurso.exercicio   = recurso.exercicio
                                   
                        JOIN  contabilidade.plano_conta
                             ON  plano_conta.cod_conta = plano_analitica.cod_conta
                            AND  plano_conta.exercicio = plano_analitica.exercicio
                              
                        LEFT JOIN  contabilidade.plano_banco
                             ON  plano_banco.cod_plano = transferencia.cod_plano_credito
                            AND  plano_banco.exercicio = transferencia.exercicio
                              
                        LEFT JOIN  monetario.conta_corrente
                             ON  conta_corrente.cod_banco          = plano_banco.cod_banco
                            AND  conta_corrente.cod_agencia        = plano_banco.cod_agencia
                            AND  conta_corrente.cod_conta_corrente = plano_banco.cod_conta_corrente
                              
                        LEFT JOIN  monetario.banco
                             ON  banco.cod_banco = conta_corrente.cod_banco
                                 
                        JOIN  orcamento.entidade
                             ON  entidade.cod_entidade = transferencia.cod_entidade
                            AND  entidade.exercicio = transferencia.exercicio
                                  
                        JOIN  sw_cgm as entidade_cgm
                             ON  entidade_cgm.numcgm = entidade.numcgm
                                
                        WHERE entidade.cod_entidade IN (".$this->getDado('cod_entidade').")
                        AND entidade.exercicio = '".$this->getDado('exercicio')."'
                ) AS relacao

                WHERE relacao.cod_tipo = 2
                AND TO_CHAR(dt_transferencia,'mm') = '".$this->getDado('mes')."'
                AND relacao.tipo = 'T'              
                AND substr(cod_estrutural,1,1) NOT IN ('4','9')

                GROUP BY conta_correspondente
                        ,tipo_registro
                        ,unidade_gestora           
                        ,cod_fonte_recurso

                ORDER BY cod_fonte_recurso
     ";

    return $stSql;
}

}//FIM CLASSE
