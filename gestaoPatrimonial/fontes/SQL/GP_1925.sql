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
/*
* Script de DDL e DML
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id:  $
*
* Versão 1.92.5
*/

----------------
-- Ticket #15978
----------------

ALTER TABLE orcamento.reserva_saldos         ALTER COLUMN motivo          TYPE VARCHAR(255);
ALTER TABLE orcamento.reserva_saldos_anulada ALTER COLUMN motivo_anulacao TYPE VARCHAR(255);

    INSERT
      INTO orcamento.reserva_saldos_anulada
         ( cod_reserva
         , exercicio
         , dt_anulacao
         , motivo_anulacao            
         )
    SELECT solicitacao_homologada_reserva.cod_reserva
         , solicitacao_homologada_reserva.exercicio
         , CAST(MAX(solicitacao_item_dotacao_anulacao.timestamp) AS DATE) as dt_anulacao
         , 'Entidade: '||solicitacao_homologada_reserva.cod_entidade||' - '
           ||
           (
               SELECT  nom_cgm
                 FROM  orcamento.entidade
        
           INNER JOIN  sw_cgm
                   ON  sw_cgm.numcgm = entidade.numcgm
             
                WHERE  entidade.cod_entidade = solicitacao_homologada_reserva.cod_entidade
                  AND  entidade.exercicio    = solicitacao_homologada_reserva.exercicio
           )
           ||', SolicitaÃ§Ã£o de Compras: '||solicitacao_homologada_reserva.cod_solicitacao||'/'||solicitacao_homologada_reserva.exercicio
           ||', Item: '||solicitacao_homologada_reserva.cod_item
           ||', Centro de Custo: '||solicitacao_homologada_reserva.cod_centro
           ||' (Origem da AnulaÃ§Ã£o: Anular SolicitaÃ§Ã£o).' as motivo_anulacao
      FROM  compras.solicitacao
INNER JOIN  compras.solicitacao_homologada
        ON  solicitacao_homologada.exercicio       = solicitacao.exercicio
       AND  solicitacao_homologada.cod_entidade    = solicitacao.cod_entidade
       AND  solicitacao_homologada.cod_solicitacao = solicitacao.cod_solicitacao
INNER JOIN  compras.solicitacao_item
        ON  solicitacao_item.exercicio       = solicitacao.exercicio
       AND  solicitacao_item.cod_entidade    = solicitacao.cod_entidade
       AND  solicitacao_item.cod_solicitacao = solicitacao.cod_solicitacao
INNER JOIN  compras.solicitacao_item_dotacao
        ON  solicitacao_item_dotacao.exercicio       = solicitacao_item.exercicio 
       AND  solicitacao_item_dotacao.cod_entidade    = solicitacao_item.cod_entidade
       AND  solicitacao_item_dotacao.cod_solicitacao = solicitacao_item.cod_solicitacao
       AND  solicitacao_item_dotacao.cod_centro      = solicitacao_item.cod_centro
       AND  solicitacao_item_dotacao.cod_item        = solicitacao_item.cod_item
INNER JOIN  compras.solicitacao_item_dotacao_anulacao
        ON  solicitacao_item_dotacao_anulacao.exercicio       =  solicitacao_item_dotacao.exercicio
       AND  solicitacao_item_dotacao_anulacao.cod_entidade    =  solicitacao_item_dotacao.cod_entidade
       AND  solicitacao_item_dotacao_anulacao.cod_solicitacao =  solicitacao_item_dotacao.cod_solicitacao
       AND  solicitacao_item_dotacao_anulacao.cod_centro      =  solicitacao_item_dotacao.cod_centro
       AND  solicitacao_item_dotacao_anulacao.cod_item        =  solicitacao_item_dotacao.cod_item
       AND  solicitacao_item_dotacao_anulacao.cod_conta       =  solicitacao_item_dotacao.cod_conta
       AND  solicitacao_item_dotacao_anulacao.cod_despesa     =  solicitacao_item_dotacao.cod_despesa
INNER JOIN  compras.solicitacao_homologada_reserva
        ON  solicitacao_homologada_reserva.exercicio       =  solicitacao_item_dotacao.exercicio
       AND  solicitacao_homologada_reserva.cod_entidade    =  solicitacao_item_dotacao.cod_entidade
       AND  solicitacao_homologada_reserva.cod_solicitacao =  solicitacao_item_dotacao.cod_solicitacao
       AND  solicitacao_homologada_reserva.cod_centro      =  solicitacao_item_dotacao.cod_centro
       AND  solicitacao_homologada_reserva.cod_item        =  solicitacao_item_dotacao.cod_item
       AND  solicitacao_homologada_reserva.cod_conta       =  solicitacao_item_dotacao.cod_conta
       AND  solicitacao_homologada_reserva.cod_despesa     =  solicitacao_item_dotacao.cod_despesa
INNER JOIN  orcamento.reserva_saldos
        ON  reserva_saldos.cod_reserva = solicitacao_homologada_reserva.cod_reserva
       AND  reserva_saldos.exercicio   = solicitacao_homologada_reserva.exercicio
     WHERE  1=1
       AND  NOT EXISTS
            (
                SELECT  1
                  FROM  orcamento.reserva_saldos_anulada
                 WHERE  reserva_saldos_anulada.cod_reserva = reserva_saldos.cod_reserva 
                   AND  reserva_saldos_anulada.exercicio   = reserva_saldos.exercicio
            )
       AND  NOT EXISTS
            (
                SELECT  1
                  FROM  compras.mapa_solicitacao
                 WHERE  mapa_solicitacao.exercicio_solicitacao = solicitacao_homologada.exercicio
                   AND  mapa_solicitacao.cod_entidade          = solicitacao_homologada.cod_entidade
                   AND  mapa_solicitacao.cod_solicitacao       = solicitacao_homologada.cod_solicitacao
            )
  GROUP BY
            solicitacao_homologada_reserva.cod_item
         ,  solicitacao_homologada_reserva.cod_centro   
         ,  solicitacao_homologada_reserva.cod_solicitacao
         ,  solicitacao_homologada_reserva.cod_entidade
         ,  solicitacao_homologada_reserva.exercicio
         ,  solicitacao_homologada_reserva.cod_reserva
         ,  solicitacao_item_dotacao.vl_reserva
    HAVING  (
                (
                    solicitacao_item_dotacao.vl_reserva - 
                    SUM(solicitacao_item_dotacao_anulacao.vl_anulacao)                
                ) = 0
            )
  ORDER BY  solicitacao_homologada_reserva.cod_reserva
         ;

