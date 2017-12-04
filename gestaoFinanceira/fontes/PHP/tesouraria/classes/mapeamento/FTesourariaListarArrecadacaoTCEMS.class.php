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
    * Classe de mapeamento da função fn_listar_arrecadacao
    * Data de Criação: 15/12//2005

    * @author Analista: Lucas Leusin Oiagem
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Mapeamento

    $Id: FTesourariaListarArrecadacaoTCEMS.class.php 64692 2016-03-22 13:36:45Z michel $

    * Casos de uso: uc-02.04.08
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class FTesourariaListarArrecadacaoTCEMS extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FTesourariaListarArrecadacaoTCEMS()
{
    parent::Persistente();
}

function montaRecuperaTodos()
{
    $stSql = "";

    if (!$this->getDado('retencao')) {
        $stSql .= "
              SELECT *
                FROM tesouraria.fn_listar_arrecadacao_tce( '".$this->getDado("stFiltro")."'
                                                         , '".$this->getDado("stFiltro")."'
                                                         );

              SELECT tbl.exercicio
                    ,tbl.cod_entidade
                    ,tbl.cod_boletim
                    ,tbl.conta_debito
                    ,tbl.conta_credito
                    ,tbl.numeracao
                    ,tbl.cod_receita
                    ,tbl.tipo
                    ,( tbl.valor - tbl.vl_desconto + tbl.vl_juros + tbl.vl_multa ) AS valor
                    ,CPCD.cod_estrutural AS cod_estrutural_debito
                    ,CPCC.cod_estrutural AS cod_estrutural_credito
                    ,tbl.cod_historico
                FROM(
                     SELECT tmp_arrecadacao.cod_entidade
                          ,tmp_arrecadacao.exercicio
                          ,tmp_arrecadacao.cod_boletim
                          ,tmp_arrecadacao.conta_debito
                          ,tmp_arrecadacao.conta_credito
                          ,tmp_arrecadacao.numeracao
                          ,tmp_arrecadacao.cod_receita
                          ,'A' as tipo
                          ,SUM( valor ) AS valor
                          ,SUM( vl_desconto ) AS vl_desconto
                          ,SUM( vl_juros ) AS vl_juros
                          ,SUM( vl_multa ) as vl_multa
                          ,tmp_arrecadacao.cod_historico
                       FROM tmp_arrecadacao
                      WHERE NOT EXISTS
                            ( SELECT aopr.cod_arrecadacao
                                FROM tesouraria.arrecadacao_ordem_pagamento_retencao as aopr
                               WHERE aopr.cod_arrecadacao       = tmp_arrecadacao.cod_arrecadacao
                                 AND aopr.timestamp_arrecadacao = tmp_arrecadacao.timestamp_arrecadacao
                                 AND aopr.exercicio             = tmp_arrecadacao.exercicio
                                 AND aopr.cod_entidade          = tmp_arrecadacao.cod_entidade
                                 AND aopr.cod_plano             = tmp_arrecadacao.conta_credito
                            )
                   GROUP BY exercicio
                          ,cod_entidade
                          ,cod_boletim
                          ,conta_debito
                          ,conta_credito
                          ,numeracao
                          ,cod_receita
                          ,cod_historico

                      UNION ALL

                     SELECT tmp_arrecadacao_estornada.cod_entidade
                          ,tmp_arrecadacao_estornada.exercicio
                          ,tmp_arrecadacao_estornada.cod_boletim
                          ,tmp_arrecadacao_estornada.conta_debito
                          ,tmp_arrecadacao_estornada.conta_credito
                          ,tmp_arrecadacao_estornada.numeracao
                          ,tmp_arrecadacao_estornada.cod_receita
                          ,'E' as tipo
                          ,SUM( valor ) as valor
                          ,SUM( vl_desconto ) as vl_desconto
                          ,SUM( vl_juros ) AS vl_juros
                          ,SUM( vl_multa ) as vl_multa
                          ,tmp_arrecadacao_estornada.cod_historico
                       FROM tmp_arrecadacao_estornada
                      WHERE NOT EXISTS
                            ( SELECT aeopr.cod_arrecadacao
                                FROM tesouraria.arrecadacao_estornada_ordem_pagamento_retencao as aeopr
                               WHERE aeopr.cod_arrecadacao       = tmp_arrecadacao_estornada.cod_arrecadacao
                                 AND aeopr.timestamp_arrecadacao = tmp_arrecadacao_estornada.timestamp_arrecadacao
                                 AND aeopr.exercicio             = tmp_arrecadacao_estornada.exercicio
                                 AND aeopr.cod_entidade          = tmp_arrecadacao_estornada.cod_entidade
                                 AND aeopr.cod_plano             = tmp_arrecadacao_estornada.conta_debito
                            )
                   GROUP BY exercicio
                          ,cod_entidade
                          ,cod_boletim
                          ,conta_debito
                          ,conta_credito
                          ,numeracao
                          ,cod_receita
                          ,cod_historico
                   
                   ORDER BY exercicio
                          ,cod_entidade
                          ,cod_boletim
                          ,conta_debito
                          ,conta_credito
                          ,numeracao
                    ) AS tbl
           LEFT JOIN contabilidade.plano_analitica CPAD
                  ON tbl.conta_debito = CPAD.cod_plano
                 AND tbl.exercicio    = CPAD.exercicio
           LEFT JOIN contabilidade.plano_conta  CPCD
                  ON CPAD.cod_conta = CPCD.cod_conta
                 AND CPAD.exercicio = CPCD.exercicio
           LEFT JOIN contabilidade.plano_analitica CPAC
                  ON tbl.conta_credito = CPAC.cod_plano
                 AND tbl.exercicio     = CPAC.exercicio
           LEFT JOIN contabilidade.plano_conta CPCC
                  ON CPAC.cod_conta = CPCC.cod_conta
                 AND CPAC.exercicio = CPCC.exercicio

            ORDER BY tipo
                   ,exercicio
                   ,cod_entidade
                   ,conta_debito
                   ,conta_credito ";
    }else{
        $stSql .= "
              SELECT tbl.exercicio
                    ,tbl.cod_entidade
                    ,tbl.cod_boletim
                    ,tbl.conta_debito
                    ,tbl.conta_credito
                    ,tbl.numeracao
                    ,tbl.cod_receita
                    ,tbl.tipo
                    ,tbl.cod_ordem
                    ,tbl.cod_plano
                    ,( tbl.valor - tbl.vl_desconto + tbl.vl_juros + tbl.vl_multa ) AS valor
                    ,CPCD.cod_estrutural AS cod_estrutural_debito
                    ,CPCC.cod_estrutural AS cod_estrutural_credito
                    ,tbl.cod_historico
                FROM(
                     SELECT tmp_arrecadacao.cod_entidade
                          ,tmp_arrecadacao.exercicio
                          ,tmp_arrecadacao.cod_boletim
                          ,tmp_arrecadacao.conta_debito
                          ,tmp_arrecadacao.conta_credito
                          ,tmp_arrecadacao.numeracao
                          ,tmp_arrecadacao.cod_receita
                          ,'A' as tipo
                          ,aopr.cod_ordem
                          ,aopr.cod_plano
                          ,valor
                          ,vl_desconto
                          ,vl_juros
                          ,vl_multa
                          ,tmp_arrecadacao.cod_historico
                       FROM tmp_arrecadacao
                 INNER JOIN tesouraria.arrecadacao_ordem_pagamento_retencao as aopr
                         ON (    aopr.cod_arrecadacao       = tmp_arrecadacao.cod_arrecadacao
                             AND aopr.timestamp_arrecadacao = tmp_arrecadacao.timestamp_arrecadacao
                             AND aopr.exercicio             = tmp_arrecadacao.exercicio
                             AND aopr.cod_entidade          = tmp_arrecadacao.cod_entidade
                             AND aopr.cod_plano             = tmp_arrecadacao.conta_credito
                            )
                      WHERE EXISTS
                            ( SELECT aopr.cod_arrecadacao
                                FROM tesouraria.arrecadacao_ordem_pagamento_retencao as aopr
                               WHERE aopr.cod_arrecadacao       = tmp_arrecadacao.cod_arrecadacao
                                 AND aopr.timestamp_arrecadacao = tmp_arrecadacao.timestamp_arrecadacao
                                 AND aopr.exercicio             = tmp_arrecadacao.exercicio
                                 AND aopr.cod_entidade          = tmp_arrecadacao.cod_entidade
                                 AND aopr.cod_plano             = tmp_arrecadacao.conta_credito
                            )

                      UNION ALL

                     SELECT tmp_arrecadacao_estornada.cod_entidade
                          ,tmp_arrecadacao_estornada.exercicio
                          ,tmp_arrecadacao_estornada.cod_boletim
                          ,tmp_arrecadacao_estornada.conta_debito
                          ,tmp_arrecadacao_estornada.conta_credito
                          ,tmp_arrecadacao_estornada.numeracao
                          ,tmp_arrecadacao_estornada.cod_receita
                          ,'E' as tipo
                          ,aeopr.cod_ordem
                          ,aeopr.cod_plano
                          ,valor
                          ,vl_desconto
                          ,vl_juros
                          ,vl_multa
                          ,tmp_arrecadacao_estornada.cod_historico
                       FROM tmp_arrecadacao_estornada
                 INNER JOIN tesouraria.arrecadacao_estornada_ordem_pagamento_retencao as aeopr
                         ON (    aeopr.cod_arrecadacao       = tmp_arrecadacao_estornada.cod_arrecadacao
                             AND aeopr.timestamp_arrecadacao = tmp_arrecadacao_estornada.timestamp_arrecadacao
                             AND aeopr.exercicio             = tmp_arrecadacao_estornada.exercicio
                             AND aeopr.cod_entidade          = tmp_arrecadacao_estornada.cod_entidade
                             AND aeopr.cod_plano             = tmp_arrecadacao_estornada.conta_debito
                            )
                      WHERE EXISTS
                            ( SELECT aeopr.cod_arrecadacao
                                FROM tesouraria.arrecadacao_estornada_ordem_pagamento_retencao as aeopr
                               WHERE aeopr.cod_arrecadacao       = tmp_arrecadacao_estornada.cod_arrecadacao
                                 AND aeopr.timestamp_arrecadacao = tmp_arrecadacao_estornada.timestamp_arrecadacao
                                 AND aeopr.exercicio             = tmp_arrecadacao_estornada.exercicio
                                 AND aeopr.cod_entidade          = tmp_arrecadacao_estornada.cod_entidade
                                 AND aeopr.cod_plano             = tmp_arrecadacao_estornada.conta_debito
                            )
                   ORDER BY exercicio
                          ,cod_entidade
                          ,cod_boletim
                          ,conta_debito
                          ,conta_credito
                          ,numeracao
                    ) AS tbl
           LEFT JOIN contabilidade.plano_analitica CPAD
                  ON tbl.conta_debito = CPAD.cod_plano
                 AND tbl.exercicio    = CPAD.exercicio
           LEFT JOIN contabilidade.plano_conta  CPCD
                  ON CPAD.cod_conta = CPCD.cod_conta
                 AND CPAD.exercicio = CPCD.exercicio
           LEFT JOIN contabilidade.plano_analitica CPAC
                  ON tbl.conta_credito = CPAC.cod_plano
                 AND tbl.exercicio     = CPAC.exercicio
           LEFT JOIN contabilidade.plano_conta CPCC
                  ON CPAC.cod_conta = CPCC.cod_conta
                 AND CPAC.exercicio = CPCC.exercicio
            ORDER BY tipo
                   ,exercicio
                   ,cod_entidade
                   ,cod_ordem
                   ,conta_debito
                   ,conta_credito ";
    }

    return $stSql;
}

}
