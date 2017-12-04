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
    * Data de Criação: 18/04/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTCMGOAtivoPassivoConpensados.class.php 65168 2016-04-29 16:36:09Z michel $

    * Casos de uso: uc-06.04.00
*/

include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeBalancoFinanceiro.class.php";

class TTCMGOAtivoPassivoConpensados extends TContabilidadeBalancoFinanceiro
{
    public function __construct()
    {
        parent::TContabilidadeBalancoFinanceiro();
        $this->setDado('exercicio', Sessao::getExercicio() );
    }

    public function montaRecuperaTodos()
    {
        $stEntidades = $this->getDado ( 'stEntidades' );
        $stExercicio = $this->getDado ( 'exercicio' );

        $stSql = "
                    select consulta.*
                         ,case when tipo_lancamento = 2
                           then total_debitos
                           else total_creditos
                          end as valor_inscricao
                         , ( total_creditos - total_debitos ) as saldo_atual
                      from (
                            select plano_conta.cod_estrutural
                                 , plano_conta.nom_conta
                                 , plano_conta.exercicio
                                 , balanco_comaaaa.tipo_lancamento
                                 , orgao_plano_banco.num_orgao
                                 , unidade.num_unidade
                                 ---- soma dos debitos
                                 ,coalesce(  ( select sum ( vl_lancamento  )
                                                 from contabilidade.conta_debito
                                                 join contabilidade.valor_lancamento
                                                   on ( conta_debito.exercicio    = valor_lancamento.exercicio
                                                  and   conta_debito.cod_entidade = valor_lancamento.cod_entidade
                                                  and   conta_debito.tipo         = valor_lancamento.tipo
                                                  and   conta_debito.cod_lote     = valor_lancamento.cod_lote
                                                  and   conta_debito.sequencia    = valor_lancamento.sequencia
                                                  and   conta_debito.tipo_valor   = valor_lancamento.tipo_valor )
                                                where conta_debito.exercicio = plano_analitica.exercicio
                                                  and conta_debito.cod_plano = plano_analitica.cod_plano
                                                  and valor_lancamento.tipo <> 'I'
                                                  and conta_debito.cod_entidade in ( $stEntidades )  )
                                            , 0 ) as total_debitos
                                 ---- soma dos creditos
                                 ,coalesce ( ( select sum ( vl_lancamento  )
                                                from contabilidade.conta_credito
                                                join contabilidade.valor_lancamento
                                                  on ( conta_credito.exercicio    = valor_lancamento.exercicio
                                                 and   conta_credito.cod_entidade = valor_lancamento.cod_entidade
                                                 and   conta_credito.tipo         = valor_lancamento.tipo
                                                 and   conta_credito.cod_lote     = valor_lancamento.cod_lote
                                                 and   conta_credito.sequencia    = valor_lancamento.sequencia
                                                 and   conta_credito.tipo_valor   = valor_lancamento.tipo_valor )
                                               where conta_credito.exercicio = plano_analitica.exercicio
                                                 and conta_credito.cod_plano = plano_analitica.cod_plano
                                                 and valor_lancamento.tipo <> 'I'
                                                 and conta_credito.cod_entidade in ( $stEntidades ) )
                                            , 0 ) as total_creditos

                                  ---- saldo inicial
                                 , (
                                        coalesce(
                                        ( select sum ( vl_lancamento  )
                                                 from contabilidade.conta_debito
                                                 join contabilidade.valor_lancamento
                                                   on ( conta_debito.exercicio    = valor_lancamento.exercicio
                                                  and   conta_debito.cod_entidade = valor_lancamento.cod_entidade
                                                  and   conta_debito.tipo         = valor_lancamento.tipo
                                                  and   conta_debito.cod_lote     = valor_lancamento.cod_lote
                                                  and   conta_debito.sequencia    = valor_lancamento.sequencia
                                                  and   conta_debito.tipo_valor   = valor_lancamento.tipo_valor )
                                                where conta_debito.exercicio = plano_analitica.exercicio
                                                  and conta_debito.cod_plano = plano_analitica.cod_plano
                                                  and valor_lancamento.tipo = 'I'
                                                  and conta_debito.cod_entidade in ( $stEntidades ) ), 0 )
                                          +
                                         coalesce (
                                         ( select sum ( vl_lancamento  )
                                             from contabilidade.conta_credito
                                             join contabilidade.valor_lancamento
                                               on ( conta_credito.exercicio    = valor_lancamento.exercicio
                                              and   conta_credito.cod_entidade = valor_lancamento.cod_entidade
                                              and   conta_credito.tipo         = valor_lancamento.tipo
                                              and   conta_credito.cod_lote     = valor_lancamento.cod_lote
                                              and   conta_credito.sequencia    = valor_lancamento.sequencia
                                              and   conta_credito.tipo_valor   = valor_lancamento.tipo_valor )
                                            where conta_credito.exercicio = plano_analitica.exercicio
                                              and conta_credito.cod_plano = plano_analitica.cod_plano
                                              and valor_lancamento.tipo = 'I'
                                              and conta_credito.cod_entidade in ( $stEntidades ) ), 0 )
                                    )  as saldo_anterior
                            from contabilidade.plano_conta
                            join contabilidade.plano_analitica
                              on ( plano_analitica.cod_conta = plano_conta.cod_conta
                             and   plano_analitica.exercicio = plano_conta.exercicio )
                            join tcmgo.balanco_comaaaa
                              on ( plano_analitica.cod_plano = balanco_comaaaa.cod_plano
                             and   plano_analitica.exercicio = balanco_comaaaa.exercicio )
                            join tcmgo.orgao_plano_banco
                              on ( plano_analitica.exercicio = orgao_plano_banco.exercicio
                             and   plano_analitica.cod_plano = orgao_plano_banco.cod_plano )
                            join orcamento.unidade
                              on ( orgao_plano_banco.exercicio = unidade.exercicio
                             and   orgao_plano_banco.num_orgao = unidade.num_orgao )
                           where plano_conta.exercicio = '$stExercicio'
                         order by  plano_conta.cod_estrutural
                        ) as consulta
                 ";

        return $stSql;
    }

}

?>
