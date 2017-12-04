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
* Classe de mapeamento da tabela TESOURARIA.BOLETIM_LOTE
* Data de Criação: 27/02/2007
* @author Analista: Gelson W. Gonçalves
* @author Desenvolvedor: Rodrigo S. Rodrigues
* @package URBEM
* @subpackage
* $Id: TTesourariaBoletimLoteArrecadacao.class.php 59612 2014-09-02 12:00:51Z gelson $
* Casos de uso: uc-02.04.33
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TTesourariaBoletimLoteArrecadacao extends Persistente
{
    public function TTesourariaBoletimLoteArrecadacao()
    {
        parent::Persistente();
        $this->setTabela("tesouraria.boletim_lote_arrecadacao");

        $this->setCampoCod('');
        $this->setComplementoChave('exercicio, cod_entidade, cod_boletim, cod_lote, timestamp_arrecadacao, cod_arrecadacao');

        $this->AddCampo('exercicio'             , 'char'        , true, '4' , true  , true );
        $this->AddCampo('cod_entidade'          , 'integer'     , true, ''  , true  , true );
        $this->AddCampo('cod_boletim'           , 'integer'     , true, ''  , true  , true );
        $this->AddCampo('cod_lote'              , 'integer'     , true, ''  , true  , true );
        $this->AddCampo('timestamp_arrecadacao' , 'timestamp'   , true, ''  , true  , true );
        $this->AddCampo('cod_arrecadacao'       , 'integer'     , true, ''  , true  , true );

    }

    public function verificaEstadoNumeracao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaverificaEstadoNumeracao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    }
    public function montaverificaEstadoNumeracao()
    {
        $stSQL = "
            select tesouraria.fn_verifica_creditos_acrescimos_por_numeracao
                ( '" . $this->getDado('numeracao'). "' , " . $this->getDado('exercicio') . " )
            as status;
        ";

        return $stSQL;
    }

    public function recuperaBoletimLote(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaBoletimLote",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    }

    public function montaRecuperaBoletimLote()
    {
        $stSQL = "
            select lote.cod_lote
                 , lote.data_lote
                 , to_char(lote.data_lote , 'dd/mm/YYYY') as data_br
                 , lote.cod_banco
                 , lote.cod_agencia
                 , lote.exercicio
                 , (  select num_agencia || ' - ' || nom_agencia
                     from monetario.agencia
                    where agencia.cod_agencia = lote.cod_agencia
                      and agencia.cod_banco = lote.cod_banco
                      limit  1
                   ) as agencia
                 , (  select num_banco || ' - ' || nom_banco
                      from monetario.banco
                     where banco.cod_banco = lote.cod_banco
                     limit 1
                   ) as banco
                 , case
                        when tesouraria.fn_verifica_creditos_acrescimos ( lote.cod_lote, lote.exercicio::int ) = false then
                            false
                        else
                            case
                                when ( select 1 from contabilidade.plano_banco where plano_banco.cod_banco = lote.cod_banco and plano_banco.cod_agencia = lote.cod_agencia limit 1 ) is null then
                                    false
                                else
                                    case
                                        when tesouraria.fn_verifica_conta_corrente_credito(lote.cod_lote, lote.exercicio::int)  = false then
                                            false
                                        else
                                            case
                                                when (  select 1
                                                        from contabilidade.plano_banco
                                                        inner join contabilidade.plano_analitica
                                                                on plano_analitica.cod_plano = plano_banco.cod_plano
                                                               and plano_analitica.exercicio = plano_banco.exercicio
                                                        inner join contabilidade.plano_conta
                                                                on plano_analitica.cod_conta = plano_conta.cod_conta
                                                               and plano_analitica.exercicio = plano_conta.exercicio
                                                        where plano_banco.cod_banco = lote.cod_banco
                                                          and plano_banco.cod_agencia = lote.cod_agencia
                                                          and plano_conta.cod_estrutural like '1.1.1.1.2%'
                                                        limit 1
                                                      ) is null then false
                                                else
                                                    true
                                            end
                                    end
                            end
                   end as ok
                 , case
                    when tesouraria.fn_verifica_creditos_acrescimos ( lote.cod_lote, lote.exercicio::int ) = false then
                            false
                        else
                            true
                    end as ok_credito
                 , case
                    when ( select 1 from contabilidade.plano_banco where plano_banco.cod_banco = lote.cod_banco and plano_banco.cod_agencia = lote.cod_agencia limit 1 ) is null then
                        false
                    else
                        true
                   end as ok_banco
                   ,case
                        when tesouraria.fn_verifica_conta_corrente_credito(lote.cod_lote, lote.exercicio::int)  = false  then
                            false
                        else
                            true
                   end as ok_conta_corrente
                 , case
                     when ( select 1
                            from contabilidade.plano_banco
                            inner join contabilidade.plano_analitica
                                    on plano_analitica.cod_plano = plano_banco.cod_plano
                                   and plano_analitica.exercicio = plano_banco.exercicio
                            inner join contabilidade.plano_conta
                                    on plano_analitica.cod_conta = plano_conta.cod_conta
                                   and plano_analitica.exercicio = plano_conta.exercicio
                            where plano_banco.cod_banco = lote.cod_banco
                              and plano_banco.cod_agencia = lote.cod_agencia
                              and plano_conta.cod_estrutural like '1.1.1.1.2%'
                              limit 1
                          ) is null then false
                    else
                        true
                    end as ok_plano_conta
              from arrecadacao.lote
                 inner join tesouraria.boletim
                         on lote.data_lote <= boletim.dt_boletim
                        and lote.exercicio = boletim.exercicio
             where
             not exists ( select 1
                             from tesouraria.boletim_lote_arrecadacao
                            where boletim_lote_arrecadacao.exercicio = lote.exercicio
                              and boletim_lote_arrecadacao.cod_lote = lote.cod_lote
                              and not exists ( select 1
                                                from tesouraria.boletim_lote_arrecadacao_estornado
                                               where boletim_lote_arrecadacao_estornado.cod_lote = boletim_lote_arrecadacao.cod_lote
                                                 and boletim_lote_arrecadacao_estornado.exercicio = boletim_lote_arrecadacao.exercicio
                                                 and boletim_lote_arrecadacao_estornado.cod_entidade = boletim_lote_arrecadacao.cod_entidade
                                                 and boletim_lote_arrecadacao_estornado.cod_arrecadacao = boletim_lote_arrecadacao.cod_arrecadacao
                                                 and boletim_lote_arrecadacao_estornado.timestamp_arrecadacao = boletim_lote_arrecadacao.timestamp_arrecadacao
                                                 limit 1
                                            )
                         )
               and not exists ( select 1
                             from tesouraria.boletim_lote_transferencia
                            where boletim_lote_transferencia.exercicio = lote.exercicio
                              and boletim_lote_transferencia.cod_lote_arrecadacao = lote.cod_lote
                              and not exists ( select 1
                                                from tesouraria.boletim_lote_transferencia_estornada
                                               where boletim_lote_transferencia_estornada.cod_lote = boletim_lote_transferencia.cod_lote
                                                 and boletim_lote_transferencia_estornada.exercicio = boletim_lote_transferencia.exercicio
                                                 and boletim_lote_transferencia_estornada.cod_lote_arrecadacao = boletim_lote_transferencia.cod_lote_arrecadacao
                                                 and boletim_lote_transferencia_estornada.cod_entidade = boletim_lote_transferencia.cod_entidade
                                                 and boletim_lote_transferencia_estornada.tipo = boletim_lote_transferencia.tipo
                                                 limit 1
                                            )
                         )
               and lote.exercicio = boletim.exercicio
               and lote.automatico = true

    ";

        return $stSQL;

    }

    public function recuperaFuncaoListaPagamentosLote(&$rsRecordSet, $arParametros, $stOrder = "",  $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaFuncaoListaPagamentosLote($arParametros);
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaFuncaoListaPagamentosLote($arParametros)
    {
        $stSql = "
                    select *
                      from tesouraria.fn_listar_pagamentos_via_banco
                          (	 " . $arParametros['cod_lote'] ."
                              ," . $arParametros['exercicio'] ."
                              ," . $arParametros['cod_entidade'] ."
                              ," . $arParametros['cod_boletim'] ."
                          )
                      as
                          ( 	  cod_lote int
                              , data_lote date
                              , exercicio char
                              , codigo int
                              , cod_plano int
                                    , tipo varchar
                              , soma numeric(14,2)
                          )
        ";

        return $stSql;
    }

    public function recuperaCreditosLote(&$rsRecordSet, $stFiltro ="", $stOrder = "",  $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stOrder = " group by credito.cod_credito
                            , credito.cod_especie
                            , credito.cod_genero
                            , credito.cod_natureza
                            , credito.descricao_credito
                            , situacao_acrescimo
                            , receita
                     order by credito.cod_credito";

        $stSql = $this->montaRecuperaCreditosLote().$stFiltro.$stOrder;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaCreditosLote()
    {
        $stSql = "
                    select    credito.cod_credito
                            , credito.cod_especie
                            , credito.cod_genero
                            , credito.cod_natureza
                            , credito.descricao_credito
                            , case
                                  when (   select credito_acrescimo.cod_acrescimo
                                             from monetario.credito_acrescimo
                                        left join contabilidade.plano_analitica_credito_acrescimo
                                               on plano_analitica_credito_acrescimo.cod_credito = credito_acrescimo.cod_credito
                                              and plano_analitica_credito_acrescimo.cod_especie = credito_acrescimo.cod_especie
                                              and plano_analitica_credito_acrescimo.cod_genero  = credito_acrescimo.cod_genero
                                              and plano_analitica_credito_acrescimo.cod_natureza= credito_acrescimo.cod_natureza
                                              and plano_analitica_credito_acrescimo.cod_acrescimo= credito_acrescimo.cod_acrescimo
                                              and plano_analitica_credito_acrescimo.cod_tipo= credito_acrescimo.cod_tipo
                                        left join orcamento.receita_credito_acrescimo
                                               on receita_credito_acrescimo.cod_credito   = credito_acrescimo.cod_credito
                                              and receita_credito_acrescimo.cod_especie   = credito_acrescimo.cod_especie
                                              and receita_credito_acrescimo.cod_genero    = credito_acrescimo.cod_genero
                                              and receita_credito_acrescimo.cod_natureza  = credito_acrescimo.cod_natureza
                                              and receita_credito_acrescimo.cod_acrescimo = credito_acrescimo.cod_acrescimo

                                            where credito_acrescimo.cod_credito   = credito.cod_credito
                                              and credito_acrescimo.cod_especie   = credito.cod_especie
                                              and credito_acrescimo.cod_genero    = credito.cod_genero
                                              and credito_acrescimo.cod_natureza  = credito.cod_natureza
                                              and plano_analitica_credito_acrescimo.cod_acrescimo is null
                                              and receita_credito_acrescimo.cod_acrescimo is null
                                            limit 1
                               ) is null
                                 and (
                                          select credito.cod_credito
                                            from monetario.credito as cred
                                                  left join contabilidade.plano_analitica_credito
                                                         on plano_analitica_credito.cod_credito = cred.cod_credito
                                                        and plano_analitica_credito.cod_especie = cred.cod_especie
                                                        and plano_analitica_credito.cod_genero  = cred.cod_genero
                                                        and plano_analitica_credito.cod_natureza= cred.cod_natureza
                                                  left join orcamento.receita_credito
                                                         on receita_credito.cod_credito   = cred.cod_credito
                                                        and receita_credito.cod_especie   = cred.cod_especie
                                                        and receita_credito.cod_genero    = cred.cod_genero
                                                        and receita_credito.cod_natureza  = cred.cod_natureza
                                                        and receita_credito.divida_ativa  = lancamento.divida

                                           where cred.cod_credito   = credito.cod_credito
                                             and cred.cod_especie   = credito.cod_especie
                                             and cred.cod_genero    = credito.cod_genero
                                             and cred.cod_natureza  = credito.cod_natureza
                                             and plano_analitica_credito.cod_credito is null
                                             and receita_credito.cod_credito is null
                                           limit 1

                                       ) is null
                              then
                                   true
                              else
                                   false
                              end as situacao_acrescimo
                            , ( count(credito.cod_credito)) as contador
                            , case
                                     -- orcamentaria
                                       when ( select 1
                                                from orcamento.receita_credito
                                               where receita_credito.cod_credito   = credito.cod_credito
                                                 and receita_credito.cod_especie   = credito.cod_especie
                                                 and receita_credito.cod_genero    = credito.cod_genero
                                                 and receita_credito.cod_natureza  = credito.cod_natureza
                                                 and receita_credito.divida_ativa  = lancamento.divida
                                               limit 1
                                            ) is not null
                                        then(	    select conta_receita.cod_estrutural || ' - ' ||conta_receita.descricao
                                                      from orcamento.receita_credito
                                                inner join orcamento.receita
                                                        on receita.cod_receita = receita_credito.cod_receita
                                                       and receita.exercicio = receita_credito.exercicio
                                                inner join orcamento.conta_receita
                                                        on conta_receita.cod_conta = receita.cod_conta
                                                       and conta_receita.exercicio = receita.exercicio
                                                     where receita_credito.cod_credito 	= credito.cod_credito
                                                       and receita_credito.cod_especie	= credito.cod_especie
                                                       and receita_credito.cod_genero 	= credito.cod_genero
                                                       and receita_credito.cod_natureza = credito.cod_natureza
                                                       and receita_credito.divida_ativa  = lancamento.divida
                                                     limit 1
                                            )::varchar
                                     -- extra-orcamentaria
                                       when ( 	select 1
                                                  from contabilidade.plano_analitica_credito
                                                 where plano_analitica_credito.cod_credito = credito.cod_credito
                                                   and plano_analitica_credito.cod_especie = credito.cod_especie
                                                   and plano_analitica_credito.cod_genero = credito.cod_genero
                                                   and plano_analitica_credito.cod_natureza = credito.cod_natureza
                                                 limit 1
                                            ) is not null
                                        then(	select plano_conta.cod_estrutural || ' - ' ||plano_conta.nom_conta as receita
                                                  from contabilidade.plano_analitica_credito
                                            inner join contabilidade.plano_analitica
                                                    on plano_analitica.cod_plano = plano_analitica_credito.cod_plano
                                                   and plano_analitica.exercicio = plano_analitica_credito.exercicio
                                            inner join contabilidade.plano_conta
                                                    on plano_conta.cod_conta = plano_analitica.cod_conta
                                                   and plano_conta.exercicio = plano_analitica.exercicio
                                                 where plano_analitica_credito.cod_credito = credito.cod_credito
                                                   and plano_analitica_credito.cod_especie = credito.cod_especie
                                                   and plano_analitica_credito.cod_genero = credito.cod_genero
                                                   and plano_analitica_credito.cod_natureza = credito.cod_natureza
                                                 limit 1
                                            )
                                        else '' :: varchar
                            end as receita
                    from arrecadacao.lote
              inner join arrecadacao.pagamento_lote
                      on pagamento_lote.cod_lote  = lote.cod_lote
                     and pagamento_lote.exercicio = lote.exercicio
              inner join arrecadacao.carne
                      on carne.numeracao    = pagamento_lote.numeracao
                     and carne.cod_convenio = pagamento_lote.cod_convenio
              inner join arrecadacao.parcela
                      on parcela.cod_parcela = carne.cod_parcela
              inner join arrecadacao.lancamento
                      on lancamento.cod_lancamento = parcela.cod_lancamento
              inner join arrecadacao.pagamento
                      on pagamento.numeracao               = pagamento_lote.numeracao
                     and pagamento.ocorrencia_pagamento    = pagamento_lote.ocorrencia_pagamento
                     and pagamento.cod_convenio            = pagamento_lote.cod_convenio
              inner join arrecadacao.pagamento_calculo
                      on pagamento_calculo.numeracao               = pagamento.numeracao
                     and pagamento_calculo.ocorrencia_pagamento    = pagamento.ocorrencia_pagamento
                     and pagamento_calculo.cod_convenio            = pagamento.cod_convenio
              inner join arrecadacao.calculo
                      on calculo.cod_calculo = pagamento_calculo.cod_calculo
              inner join monetario.credito
                      on credito.cod_credito = calculo.cod_credito
                     and credito.cod_especie = calculo.cod_especie
                     and credito.cod_genero  = calculo.cod_genero
                     and credito.cod_natureza= calculo.cod_natureza
        ";

        return $stSql;
    }

    public function montaRecuperaAcrescimosCreditoLote()
    {
        $stSql = "
                    select credito_acrescimo.*
                         , acrescimo.descricao_acrescimo
                         , case
                            when tesouraria.fn_verifica_acrescimo
                                ( 	credito_acrescimo.cod_credito
                                    ,credito_acrescimo.cod_especie
                                    ,credito_acrescimo.cod_genero
                                    ,credito_acrescimo.cod_natureza
                                    ,credito_acrescimo.cod_acrescimo
                                    ,credito_acrescimo.cod_tipo
                                ) = true then
                                'Ok'
                            else
                                'Não Ok'
                            end as situacao
                         , case
                                          -- orcamentaria
                                            when ( 		select 1
                                                                  from orcamento.receita_credito_acrescimo
                                         where receita_credito_acrescimo.cod_credito 	= credito_acrescimo.cod_credito
                                           and receita_credito_acrescimo.cod_especie	= credito_acrescimo.cod_especie
                                           and receita_credito_acrescimo.cod_genero 	= credito_acrescimo.cod_genero
                                           and receita_credito_acrescimo.cod_natureza = credito_acrescimo.cod_natureza
                                       and receita_credito_acrescimo.cod_acrescimo= credito_acrescimo.cod_acrescimo
                                       and receita_credito_acrescimo.cod_tipo     = credito_acrescimo.cod_tipo
                                       limit 1
                                                     ) is not null
                                                then
                                                    ( 		select conta_receita.cod_estrutural || ' - ' ||conta_receita.descricao
                                                                  from orcamento.receita_credito_acrescimo
                                                                             inner join orcamento.receita
                                                                                             on receita.cod_receita = receita_credito_acrescimo.cod_receita
                                                                                            and receita.exercicio = receita_credito_acrescimo.exercicio
                                                                             inner join orcamento.conta_receita
                                                                                             on conta_receita.cod_conta = receita.cod_conta
                                                                                            and conta_receita.exercicio = receita.exercicio
                                         where receita_credito_acrescimo.cod_credito 	= credito_acrescimo.cod_credito
                                           and receita_credito_acrescimo.cod_especie	= credito_acrescimo.cod_especie
                                           and receita_credito_acrescimo.cod_genero 	= credito_acrescimo.cod_genero
                                           and receita_credito_acrescimo.cod_natureza = credito_acrescimo.cod_natureza
                                       and receita_credito_acrescimo.cod_acrescimo= credito_acrescimo.cod_acrescimo
                                       and receita_credito_acrescimo.cod_tipo     = credito_acrescimo.cod_tipo
                                       limit 1
                                                    )::varchar
                                          -- extra-orcamentaria
                                            when ( 		select 1
                                                              from contabilidade.plano_analitica_credito_acrescimo
                                             where plano_analitica_credito_acrescimo.cod_credito = credito_acrescimo.cod_credito
                                               and plano_analitica_credito_acrescimo.cod_especie = credito_acrescimo.cod_especie
                                                 and plano_analitica_credito_acrescimo.cod_genero = credito_acrescimo.cod_genero
                                                 and plano_analitica_credito_acrescimo.cod_natureza = credito_acrescimo.cod_natureza
                                       and plano_analitica_credito_acrescimo.cod_acrescimo= credito_acrescimo.cod_acrescimo
                                       and plano_analitica_credito_acrescimo.cod_tipo= credito_acrescimo.cod_tipo
                                       limit 1
                                                     ) is not null
                                                then
                                                    (		  select plano_conta.cod_estrutural || ' - ' ||plano_conta.nom_conta as receita
                                                              from contabilidade.plano_analitica_credito_acrescimo
                                                                   inner join contabilidade.plano_analitica
                                                                    on plano_analitica.cod_plano = plano_analitica_credito_acrescimo.cod_plano
                                                                   and plano_analitica.exercicio = plano_analitica_credito_acrescimo.exercicio
                                                            inner join contabilidade.plano_conta
                                                                    on plano_conta.cod_conta = plano_analitica.cod_conta
                                                                   and plano_conta.exercicio = plano_analitica.exercicio
                                              where plano_analitica_credito_acrescimo.cod_credito = credito_acrescimo.cod_credito
                                                and plano_analitica_credito_acrescimo.cod_especie = credito_acrescimo.cod_especie
                                                and plano_analitica_credito_acrescimo.cod_genero = credito_acrescimo.cod_genero
                                                and plano_analitica_credito_acrescimo.cod_natureza = credito_acrescimo.cod_natureza
                                          and plano_analitica_credito_acrescimo.cod_acrescimo = credito_acrescimo.cod_acrescimo
                                          and plano_analitica_credito_acrescimo.cod_tipo = credito_acrescimo.cod_tipo
                                          limit 1
                                                    )
                                            else
                                                ''::varchar
                                 end as receita
                      from  monetario.credito_acrescimo
                            inner join monetario.acrescimo
                                    on acrescimo.cod_acrescimo = credito_acrescimo.cod_acrescimo
                                   and acrescimo.cod_tipo = credito_acrescimo.cod_tipo

        ";

        return $stSql;
    }
}
