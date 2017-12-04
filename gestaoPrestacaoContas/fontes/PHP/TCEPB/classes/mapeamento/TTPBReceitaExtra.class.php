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
    * Data de Criação: 02/03/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Cleisson da Silva Barboza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 59827 $
    $Name$
    $Author: arthur $
    $Date: 2014-09-12 16:26:04 -0300 (Fri, 12 Sep 2014) $

    * Casos de uso: uc-06.03.00
*/

/*
$Log$
Revision 1.4  2007/05/14 20:10:31  hboaventura
Arquivos para geração do TCEPB

Revision 1.3  2007/04/23 15:29:33  rodrigo_sr
uc-06.03.00

Revision 1.2  2007/03/07 00:26:50  cleisson
ajustes

Revision 1.1  2007/03/02 23:22:53  cleisson
inclusão
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTPBReceitaExtra extends Persistente
{
function montaRecuperaTodos()
{
$stSql = " SELECT estrutural
                , tipo_registro
                , sum(valor) as valor
                , dt_transferencia 
            FROM (
                    SELECT  rpad(trim(replace(cod_estrutural,'.','')),15,'0') as estrutural
                            , tipo_registro
                            ,sum(coalesce(valor,0)) as valor
                            , TO_CHAR(dt_transferencia,'mmyyyy') AS dt_transferencia
                    FROM (

                            SELECT  transferencia.exercicio
                                    , transferencia.timestamp_transferencia as dt_transferencia
                                    , transferencia.timestamp_transferencia
                                    , transferencia.cod_lote
                                    , transferencia.tipo
                                    , transferencia.cod_tipo
                                    , plano_analitica.cod_plano
                                    , transferencia.cod_plano_credito
                                    , transferencia.valor AS valor
                                    , recibo_extra_transferencia.cod_recibo_extra
                                    , 'ARR' AS tipo_receita
                                    , '1' as tipo_registro
                                    , conta_corrente.num_conta_corrente
                                    , banco.nom_banco
                                    , entidade.cod_entidade
                                    , entidade_cgm.nom_cgm as nom_entidade
                                    , plano_conta.nom_conta
                                    , plano_conta.cod_estrutural
                                    , CASE WHEN plano_recurso.cod_recurso  IS NULL
                                                THEN '9999999999'
                                            ELSE TO_CHAR(plano_recurso.cod_recurso,'9999999999')
                                      END as cod_recurso
                                    , recurso.nom_recurso
                                FROM  tesouraria.transferencia
                                
                              LEFT JOIN  tesouraria.recibo_extra_transferencia
                                     ON  recibo_extra_transferencia.cod_lote     = transferencia.cod_lote
                                    AND  recibo_extra_transferencia.cod_entidade = transferencia.cod_entidade
                                    AND  recibo_extra_transferencia.exercicio    = transferencia.exercicio
                                    AND  recibo_extra_transferencia.tipo         = transferencia.tipo
                                   
                                   JOIN  contabilidade.plano_analitica
                                     ON  plano_analitica.cod_plano = transferencia.cod_plano_credito
                                    AND  plano_analitica.exercicio = transferencia.exercicio
                                
                              LEFT JOIN  tcepb.plano_analitica_relacionamento
                                     ON  plano_analitica.cod_plano  = plano_analitica_relacionamento.cod_plano
                                    AND  plano_analitica.exercicio  = plano_analitica_relacionamento.exercicio
                              
                              LEFT JOIN  contabilidade.plano_recurso
                                     ON  plano_recurso.exercicio = plano_analitica.exercicio
                                    AND  plano_recurso.cod_plano = plano_analitica.cod_plano
                              
                              LEFT JOIN  orcamento.recurso
                                     ON  recurso.cod_recurso = plano_recurso.cod_recurso
                                    AND  recurso.exercicio   = plano_recurso.exercicio
                                   
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
                                  
                                  WHERE entidade.cod_entidade IN (".$this->getDado("stEntidades").")
                                    AND entidade.exercicio = '".$this->getDado("exercicio")."'

                            UNION

                            SELECT  transferencia.exercicio
                                    , transferencia.timestamp_transferencia as dt_transferencia
                                    , transferencia_estornada.timestamp_estornada as timestamp_transferencia
                                    , transferencia.cod_lote
                                    , transferencia.tipo
                                    , transferencia.cod_tipo
                                    , transferencia.cod_plano_credito
                                    , plano_analitica.cod_plano
                                    , transferencia_estornada.valor
                                    , recibo_extra_transferencia.cod_recibo_extra
                                    , 'EST' AS tipo_receita
                                    ,  '2' as tipo_registro
                                    , conta_corrente.num_conta_corrente
                                    , banco.nom_banco
                                    , entidade.cod_entidade
                                    , entidade_cgm.nom_cgm as nom_entidade
                                    , plano_conta.nom_conta
                                    , plano_conta.cod_estrutural
                                    , CASE WHEN plano_recurso.cod_recurso  IS NULL
                                            THEN '9999999999'
                                        ELSE TO_CHAR(plano_recurso.cod_recurso,'9999999999')
                                      END as cod_recurso
                                    , recurso.nom_recurso
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
                                   
                                   JOIN  tcepb.plano_analitica_relacionamento
                                     ON  plano_analitica.cod_plano = plano_analitica_relacionamento.cod_plano
                                    AND  plano_analitica.exercicio = plano_analitica_relacionamento.exercicio
                                
                              LEFT JOIN  contabilidade.plano_recurso
                                     ON  plano_recurso.exercicio = plano_analitica.exercicio
                                    AND  plano_recurso.cod_plano = plano_analitica.cod_plano
                              
                              LEFT JOIN  orcamento.recurso
                                     ON  recurso.cod_recurso = plano_recurso.cod_recurso
                                    AND  recurso.exercicio   = plano_recurso.exercicio
                                   
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
                                
                                  WHERE entidade.cod_entidade IN (".$this->getDado("stEntidades").")
                                    AND entidade.exercicio = '".$this->getDado("exercicio")."'
                    ) AS relacao
        
            WHERE relacao.cod_tipo = 2
              AND TO_CHAR(dt_transferencia,'mm') = '".$this->getDado("inMes")."'
              AND relacao.tipo = 'T'
              AND substr(trim(replace(cod_estrutural,'.','')),1,3) <> '512'
        GROUP BY cod_estrutural, tipo_registro, dt_transferencia
        ) AS tbl
    GROUP BY estrutural
           , tipo_registro
           , dt_transferencia
    
    ORDER BY estrutural ";

return $stSql;
}
}
