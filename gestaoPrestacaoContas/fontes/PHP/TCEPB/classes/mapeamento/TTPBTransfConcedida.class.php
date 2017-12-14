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

    * Data de Criação   : 23/04/2007

    * @author Analista: Gelson Gonçalves
    * @author Desenvolvedor: Rodrigo Soares Rodrigues

    * @ignore

    * Caso de uso: uc-06.03.00

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTPBTransfConcedida extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTPBTransfConcedida()
{
    parent::Persistente();
    $this->setDado('exercicio',Sessao::getExercicio());
}

function montaRecuperaTodos()
{
    $stSql = " SELECT
                      debito.cod_estrutural
                    , entidade.valor as   unidade_gestora
                    , LPAD(REPLACE(SUM(t.valor)::VARCHAR,'.',',')::VARCHAR, 16, '0') as valor_transferencia
                    , plano_conta_tipo_transferencia.cod_tipo as tipo_transf
                    , timestamp_transferencia
                 FROM
                      tesouraria.transferencia         as t
                      JOIN ( SELECT pa.cod_plano
                                 , pa.exercicio
                                 , pc.cod_estrutural
                                 , pa.cod_conta
                              FROM contabilidade.plano_analitica as pa
                                 , contabilidade.plano_conta     as pc
                             WHERE      pa.exercicio = pc.exercicio
                                   AND pa.cod_conta = pc.cod_conta
                                   AND (
                                            pc.cod_estrutural ilike '3.5.1.2.2.01%'
                                         OR pc.cod_estrutural ilike '3.5.1.3.2.%'
                                       )
                           ) debito
                        ON(     debito.cod_plano = t.cod_plano_debito
                            AND debito.exercicio = t.exercicio )

             LEFT JOIN (  SELECT
                              ent.cod_entidade            
                            , cgm.nom_cgm                 
                            , ce.valor
                            , ent.exercicio                    
                         FROM sw_cgm AS cgm     
                         JOIN orcamento.entidade AS ent     
                           ON (                         
                                cgm.numcgm = ent.numcgm  
                              )                            
                    LEFT JOIN administracao.configuracao_entidade ce      
                           ON (                                         
                                ent.exercicio   = ce.exercicio           
                            AND ent.cod_entidade= ce.cod_entidade        
                            AND ce.cod_modulo  = 41            
                            AND ce.parametro   = 'tcepb_codigo_unidade_gestora'              
                              )                                                                
                          WHERE ent.exercicio = '".$this->getDado('exercicio')."'
                       ) as entidade
                    ON ( entidade.exercicio = t.exercicio )
                   AND ( entidade.cod_entidade = t.cod_entidade )
                   
             LEFT JOIN tcepb.plano_conta_tipo_transferencia
                    ON tcepb.plano_conta_tipo_transferencia.cod_conta = debito.cod_conta
                   AND tcepb.plano_conta_tipo_transferencia.exercicio = debito.exercicio

                WHERE
                      t.exercicio = '".$this->getDado('exercicio')."'
                  AND t.cod_entidade IN ( ".$this->getDado('stEntidades')." )
                  AND to_char(t.timestamp_transferencia, 'mm') = '".$this->getDado('inMes')."'
             GROUP BY
                      debito.cod_estrutural, timestamp_transferencia, entidade.valor, plano_conta_tipo_transferencia.cod_tipo ";

    return $stSql;
}

}
