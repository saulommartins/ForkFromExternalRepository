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
    * Data de Criação: 13/03/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 59612 $
    $Name$
    $Author: gelson $
    $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $

    * Casos de uso: uc-06.03.00
*/

/*
$Log$
Revision 1.5  2007/05/11 20:23:14  hboaventura
Arquivos para geração do TCEPB

Revision 1.4  2007/05/11 17:54:48  hboaventura
Arquivos para geração do TCEPB

Revision 1.3  2007/05/10 21:26:39  hboaventura
Arquivos para geração do TCEPB

Revision 1.2  2007/04/23 15:30:19  rodrigo_sr
uc-06.03.00

Revision 1.1  2007/04/18 22:02:11  tonismar
Geração de arquivos SalodInicial.txt e SaldoMensal.txt

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTPBSaldoInicial extends Persistente
{
    public function TTPBSaldoInicial()
    {
        parent::Persistente();
        $this->setDado('exercicio',Sessao::getExercicio());
    }

    public function montaRecuperaTodos()
    {
        $stSql .= "
            SELECT  TRIM(UPPER(REPLACE(conta_corrente.num_conta_corrente,'-',''))) as conta_corrente
                 ,  SUM(COALESCE(conciliacao.valor,0.00)) as vl_extrato
              FROM  contabilidade.plano_banco
        INNER JOIN  ( SELECT  plano_banco.cod_plano
                           ,  plano_banco.exercicio
                           ,  CASE WHEN ( mes_anterior.valor IS NOT NULL )
                                   THEN mes_anterior.mes
                                   ELSE mes_implantado.mes
                              END AS mes

                           ,  CASE WHEN ( mes_anterior.valor IS NOT NULL )
                                   THEN mes_anterior.valor
                                   ELSE mes_implantado.valor
                              END AS valor
                        FROM  contabilidade.plano_banco
                   LEFT JOIN  ( SELECT  plano_banco.cod_plano
                                     ,  plano_banco.exercicio
                                     ,  conciliacao.mes
                                     ,  conciliacao.vl_extrato AS valor
                                  FROM  contabilidade.plano_banco
                            INNER JOIN  tesouraria.conciliacao
                                    ON  plano_banco.cod_plano = conciliacao.cod_plano
                                   AND  plano_banco.exercicio = conciliacao.exercicio
                                 WHERE  plano_banco.exercicio = '".$this->getDado('exercicio')."'";
                  $stSql .= "
                                   AND  plano_banco.cod_entidade in (".$this->getDado('stEntidades').")
                              ) AS mes_anterior
                          ON  plano_banco.cod_plano = mes_anterior.cod_plano
                         AND  plano_banco.exercicio = mes_anterior.exercicio

                    /* procura por implantacao de saldo */

                     LEFT JOIN  ( SELECT  plano_banco.cod_plano
                                       ,  plano_banco.exercicio
                                       ,  0 AS mes
                                       ,  valor_lancamento.vl_lancamento AS valor
                                    FROM  contabilidade.plano_banco

                              INNER JOIN  ( SELECT  CASE WHEN ( conta_debito.cod_lote IS NOT NULL )
                                                         THEN conta_debito.cod_lote
                                                         ELSE conta_credito.cod_lote
                                                    END AS cod_lote
                                                 ,  CASE WHEN ( conta_debito.tipo IS NOT NULL )
                                                         THEN conta_debito.tipo
                                                         ELSE conta_credito.tipo
                                                    END AS tipo
                                                 ,  CASE WHEN ( conta_debito.sequencia IS NOT NULL )
                                                         THEN conta_debito.sequencia
                                                         ELSE conta_credito.sequencia
                                                    END AS sequencia
                                                 ,  CASE WHEN ( conta_debito.tipo_valor IS NOT NULL )
                                                         THEN conta_debito.tipo_valor
                                                         ELSE conta_credito.tipo_valor
                                                    END AS tipo_valor
                                                 ,  CASE WHEN ( conta_debito.cod_entidade IS NOT NULL )
                                                         THEN conta_debito.cod_entidade
                                                         ELSE conta_credito.cod_entidade
                                                    END AS cod_entidade
                                                 ,  plano_analitica.cod_plano
                                                 ,  plano_analitica.exercicio
                                              FROM  contabilidade.plano_analitica

                                         LEFT JOIN  contabilidade.conta_debito
                                                ON  conta_debito.cod_plano = plano_analitica.cod_plano
                                               AND  conta_debito.exercicio = plano_analitica.exercicio
                                               AND  conta_debito.tipo = 'I'

                                         LEFT JOIN  contabilidade.conta_credito
                                                ON  conta_credito.cod_plano = plano_analitica.cod_plano
                                               AND  conta_credito.exercicio = plano_analitica.exercicio
                                               AND  conta_credito.tipo = 'I'

                                          )  AS credito_debito
                                      ON  credito_debito.cod_plano = plano_banco.cod_plano
                                     AND  credito_debito.exercicio = plano_banco.exercicio
                              INNER JOIN  contabilidade.valor_lancamento
                                      ON  valor_lancamento.cod_lote = credito_debito.cod_lote
                                     AND  valor_lancamento.tipo = credito_debito.tipo
                                     AND  valor_lancamento.sequencia = credito_debito.sequencia
                                     AND  valor_lancamento.exercicio = credito_debito.exercicio
                                     AND  valor_lancamento.tipo_valor = credito_debito.tipo_valor
                                     AND  valor_lancamento.cod_entidade = credito_debito.cod_entidade

                              )  AS mes_implantado
                             ON  mes_implantado.cod_plano = plano_banco.cod_plano
                            AND  mes_implantado.exercicio = plano_banco.exercicio
                 )  AS conciliacao
                ON  conciliacao.cod_plano = plano_banco.cod_plano
               AND  conciliacao.exercicio = plano_banco.exercicio

        INNER JOIN  contabilidade.plano_analitica
                ON  plano_analitica.cod_plano = plano_banco.cod_plano
               AND  plano_analitica.exercicio = plano_banco.exercicio
        INNER JOIN  contabilidade.plano_conta
                ON  plano_conta.cod_conta = plano_analitica.cod_conta
               AND  plano_conta.exercicio = plano_analitica.exercicio
        INNER JOIN  monetario.conta_corrente
                ON  conta_corrente.cod_conta_corrente = plano_banco.cod_conta_corrente
               AND  conta_corrente.cod_banco   = plano_banco.cod_banco
               AND  conta_corrente.cod_agencia = plano_banco.cod_agencia
        INNER JOIN  monetario.agencia
                ON  agencia.cod_banco   = conta_corrente.cod_banco
               AND  agencia.cod_agencia = conta_corrente.cod_agencia
        INNER JOIN  monetario.banco
                ON  banco.cod_banco = agencia.cod_banco
             WHERE  plano_banco.exercicio = '".$this->getDado('exercicio')."'
               AND  plano_banco.cod_entidade in (".$this->getDado('stEntidades').")
          GROUP BY  TRIM(UPPER(REPLACE(conta_corrente.num_conta_corrente,'-','')))
        ";
        return $stSql;
    }
}
