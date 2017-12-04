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

    * Mapeamento da
    * Data de Criação   : 18/09/2013

    * @author Desenvolvedor: Carolina Schwaab Marçal

    * @package URBEM
    * @subpackage Configuração

    * Casos de uso: uc-02.08.07
*/

include_once CLA_PERSISTENTE;

class TExportacaoExecucao extends Persistente
{
    /**
     * Método Construtor da classe TExportacaoExecução
     *
     * @author      Desenvolvedor   Carolina Schwaab Marçal
     *
     * @return void
     */
    public function __construct()
    {
        parent::Persistente();
    }

    /**
     * Método que retorna dados para informar no xml da exportação
     *
     * @author      Desenvolvedor   Carolina Schwaab Marçal
     * @param object  $rsRecordSet
     * @param string  $stFiltro    Filtros alternativos que podem ser passados
     * @param string  $stOrder     Ordenacao do SQL
     * @param boolean $boTransacao Usar transacao
     *
     * @return object $obErro
     */
    public function buscaDatas(&$rsRecordSet,$stFiltro="",$stOrder=" ",$boTransacao="")
    {
        $stSql =" SELECT * FROM publico.bimestre('".$this->getDado('stExercicio')."', ".$this->getDado('inBimestre')." )";

        return $this->executaRecuperaSql($stSql,$rsRecordSet,"","",$boTransacao);
    }

    public function listarExportacaoContaDisponibilidade(&$rsRecordSet,$stFiltro="",$stOrder=" ",$boTransacao="")
    {

        $stSql = "
                          SELECT sw_cgm_pessoa_juridica.cnpj as cod_und_gestora
                                    , (SELECT CASE WHEN configuracao_entidade.valor is not null THEN valor ELSE '0000' END AS valor
                                         FROM administracao.configuracao_entidade
                                        WHERE configuracao_entidade.cod_modulo = 62
                                          AND configuracao_entidade.exercicio ='".$this->getDado('stExercicio')."'
                                          AND configuracao_entidade.parametro like 'tceal_configuracao_unidade_autonoma'
                                          AND configuracao_entidade.cod_entidade = despesa.cod_entidade
                                      ) AS codigo_ua
                                    , uniorcam.num_orgao as cod_orgao
                                    , uniorcam.num_unidade as cod_und_orcamentaria
                                    , REPLACE(plano_conta.cod_estrutural,'.','') as cod_conta_balancete
                                    , recurso.cod_recurso as cod_rec_vinculado
                                    , CASE WHEN SUBSTR(REPLACE(plano_conta.cod_estrutural::varchar,'.',''),1,7) = '1111101' THEN '1'
                                           WHEN SUBSTR(REPLACE(plano_conta.cod_estrutural::varchar,'.',''),1,7) = '1111106' THEN '2'
                                           WHEN SUBSTR(REPLACE(plano_conta.cod_estrutural::varchar,'.',''),1,7) = '1111119' THEN '2'
                                           WHEN SUBSTR(REPLACE(plano_conta.cod_estrutural::varchar,'.',''),1,7) = '1111150' AND SUBSTR(REPLACE(plano_conta.cod_estrutural::varchar,'.',''),1,9) <>  '111115005' THEN '3'
                                           WHEN SUBSTR(REPLACE(plano_conta.cod_estrutural::varchar,'.',''),1,9) = '111115005' THEN  '6'
                                      ELSE '9'
                                      END AS tipo
                                    , plano_banco.cod_banco as cod_banco
                                    , plano_banco.cod_agencia as cod_agencia_banco
                                    , plano_banco.conta_corrente as num_conta_corrente
                                  , (SELECT CASE WHEN parametro = 'cod_entidade_prefeitura' THEN '1'
                                                   WHEN parametro = 'cod_entidade_camara' THEN '2'
                                                   WHEN parametro = 'cod_entidade_rpps' THEN '3'
                                                   ELSE 9
                                                    END AS classificacao
                                         FROM administracao.configuracao
                                        WHERE valor = despesa.cod_entidade::varchar
                                          AND cod_modulo = 8
                                          AND exercicio = '".$this->getDado('stExercicio')."'
                                          AND parametro like 'cod_entidade_%') AS classificacao

                             FROM contabilidade.plano_banco

                     INNER JOIN contabilidade.plano_analitica
                                 ON plano_analitica.exercicio = plano_banco.exercicio
                               AND plano_analitica.cod_plano = plano_banco.cod_plano

                     INNER JOIN contabilidade.plano_conta
                                 ON plano_conta.exercicio = plano_analitica.exercicio
                               AND plano_conta.cod_conta = plano_analitica.cod_conta

                     INNER JOIN contabilidade.plano_recurso
                                ON plano_recurso.exercicio = plano_analitica.exercicio
                              AND plano_recurso.cod_plano = plano_analitica.cod_plano

                    INNER JOIN orcamento.recurso
                                ON recurso.exercicio = plano_recurso.exercicio
                              AND recurso.cod_recurso = plano_recurso.cod_recurso

                    INNER JOIN orcamento.despesa
                                ON despesa.exercicio = recurso.exercicio
                              AND despesa.cod_recurso = recurso.cod_recurso

                    INNER JOIN tceal.uniorcam
                                ON uniorcam.exercicio = despesa.exercicio
                              AND uniorcam.num_unidade = despesa.num_unidade
                              AND uniorcam.num_orgao = despesa.num_orgao

                      INNER JOIN orcamento.entidade
                            ON entidade.exercicio = despesa.exercicio
                           AND entidade.cod_entidade = despesa.cod_entidade

                    LEFT JOIN sw_cgm_pessoa_juridica
                           ON sw_cgm_pessoa_juridica.numcgm = entidade.numcgm
                       WHERE plano_analitica.exercicio ='".$this->getDado('stExercicio')."'
                            AND despesa.cod_entidade IN (".$this->getDado('stEntidades').")

                   GROUP BY cod_und_gestora
                                    , codigo_ua
                                    , plano_analitica.exercicio
                                    , uniorcam.num_orgao
                                    , uniorcam.num_unidade
                                    , plano_conta.cod_estrutural
                                    , recurso.cod_recurso
                                    , tipo
                                    , plano_banco.cod_banco
                                    , plano_banco.cod_agencia
                                    , plano_banco.conta_corrente
                                    , classificacao


                    ";

        return $this->executaRecuperaSql($stSql,$rsRecordSet,"","",$boTransacao);
    }

   public function listarExportacaoRecDespExtraOrcamentarias(&$rsRecordSet,$stFiltro="",$stOrder=" ",$boTransacao="")
    {

        $stSql = "
                       SELECT (SELECT CASE WHEN configuracao_entidade.valor is not null THEN valor ELSE '0000' END AS valor
                                        FROM administracao.configuracao_entidade
                                      WHERE configuracao_entidade.cod_modulo = 62
                                          AND configuracao_entidade.exercicio ='".$this->getDado('stExercicio')."'
                                          AND configuracao_entidade.parametro like 'tceal_configuracao_unidade_autonoma'
                                          AND configuracao_entidade.cod_entidade = transferencia.cod_entidade
                                     ) AS codigo_ua
                                , transferencia.cod_lote as numero_da_extra_orcamentario
                                , CASE WHEN transferencia.cod_tipo = 1 THEN REPLACE(plano_conta_debito.cod_estrutural,'.','')
                                       WHEN transferencia.cod_tipo = 2 THEN REPLACE(plano_conta_credito.cod_estrutural,'.','')
                                  END AS cod_conta_balancete
                                , CASE WHEN transferencia.cod_tipo = 1 THEN 'D'
                                       WHEN transferencia.cod_tipo = 2 THEN 'C'
                                  END AS identificador_dc
                                , transferencia.valor
                                , CASE WHEN transferencia.cod_tipo = 1 THEN 'D'
                                       WHEN transferencia.cod_tipo = 2 THEN 'C'
                                  END AS identificador_dr
                                , CASE WHEN transferencia.cod_tipo = 1 AND transferencia_estornada.cod_lote_estorno is null THEN 1
                                       WHEN transferencia.cod_tipo = 2 AND transferencia_estornada.cod_lote_estorno is null THEN 2
                                       WHEN transferencia_estornada.cod_lote_estorno is not null THEN 3
                                       ELSE 4
                                  END AS tipo_movimentacao
                                , CASE WHEN transferencia.cod_tipo = 1 THEN plano_banco_debito.cod_banco
                                       WHEN transferencia.cod_tipo = 2 THEN plano_banco_credito.cod_banco
                                  END AS cod_banco
                                , CASE WHEN transferencia.cod_tipo = 1 THEN plano_banco_debito.cod_agencia
                                       WHEN transferencia.cod_tipo = 2 THEN plano_banco_credito.cod_agencia
                                  END AS cod_agencia_banco
                                , CASE WHEN transferencia.cod_tipo = 1 THEN plano_banco_debito.cod_conta_corrente
                                       WHEN transferencia.cod_tipo = 2 THEN plano_banco_credito.cod_conta_corrente
                                  END AS num_conta_corrente
                                , receita_despesa_extra.classificacao
                                , tipo_pagamento.tipo_pagamento
                                , tipo_pagamento.descricao
                                , transferencia_estornada.cod_lote_estorno
                        FROM tesouraria.transferencia

                   LEFT JOIN tesouraria.transferencia_estornada
                            ON transferencia_estornada.cod_entidade = transferencia.cod_entidade
                          AND transferencia_estornada.tipo = transferencia.tipo
                          AND transferencia_estornada.exercicio = transferencia.exercicio
                          AND transferencia_estornada.cod_lote = transferencia.cod_lote

                INNER JOIN contabilidade.lote
                            ON lote.exercicio = transferencia.exercicio
                          AND lote.cod_entidade = transferencia.cod_entidade
                          AND lote.tipo = transferencia.tipo
                          AND lote.cod_lote = transferencia.cod_lote

                INNER JOIN contabilidade.valor_lancamento
                            ON valor_lancamento.exercicio = lote.exercicio
                          AND valor_lancamento.cod_entidade = lote.cod_entidade
                          AND valor_lancamento.tipo = lote.tipo
                          AND valor_lancamento.cod_lote = lote.cod_lote

                INNER JOIN contabilidade.plano_analitica as plano_debito
                            ON plano_debito.cod_plano = transferencia.cod_plano_debito
                          AND plano_debito.exercicio = transferencia.exercicio

                INNER JOIN contabilidade.plano_banco as plano_banco_debito
                            ON plano_banco_debito.exercicio = plano_debito.exercicio
                          AND plano_banco_debito.cod_plano = plano_debito.cod_plano

                INNER JOIN contabilidade.plano_conta as plano_conta_debito
                            ON plano_conta_debito.exercicio = plano_debito.exercicio
                          AND plano_conta_debito.cod_conta = plano_debito.cod_conta

                INNER JOIN contabilidade.plano_analitica as plano_credito
                            ON plano_credito.cod_plano = transferencia.cod_plano_credito
                          AND plano_credito.exercicio = transferencia.exercicio

                INNER JOIN contabilidade.plano_banco as plano_banco_credito
                             ON plano_banco_credito.exercicio = plano_credito.exercicio
                           AND plano_banco_credito.cod_plano = plano_credito.cod_plano

                 INNER JOIN contabilidade.plano_conta as plano_conta_credito
                            ON plano_conta_credito.exercicio = plano_credito.exercicio
                          AND plano_conta_credito.cod_conta = plano_credito.cod_conta

                INNER JOIN tceal.tipo_pagamento
                            ON tipo_pagamento.cod_entidade = transferencia.cod_entidade
                          AND tipo_pagamento.tipo = transferencia.tipo
                          AND tipo_pagamento.exercicio = transferencia.exercicio
                          AND tipo_pagamento.cod_lote = transferencia.cod_lote

                INNER JOIN tceal.despesa_receita_extra
                            ON receita_despesa_extra.exercicio  = plano_debito.exercicio
                           AND receita_despesa_extra.cod_plano =plano_debito.cod_plano

                        WHERE transferencia.exercicio ='".$this->getDado('stExercicio')."'
                             AND transferencia.cod_entidade IN (".$this->getDado('stEntidades').")
                             AND transferencia.cod_tipo = 1
                               OR transferencia.cod_tipo = 2
                             AND TO_DATE(TO_CHAR(transferencia.timestamp_transferencia, 'dd/mm/yyyy'), 'dd/mm/yyyy') BETWEEN to_date('".$this->getDado('dtInicial')."', 'dd/mm/yyyy') AND to_date('".$this->getDado('dtFinal')."', 'dd/mm/yyyy')


                     ";

        return $this->executaRecuperaSql($stSql,$rsRecordSet,"","",$boTransacao);
    }

     public function listarExportacaoServidores(&$rsRecordSet,$stFiltro="",$stOrder=" ",$boTransacao="")
    {
         $stSql = "
                        SELECT  (    SELECT PJ.cnpj
                                            FROM orcamento.entidade
                                      INNER JOIN sw_cgm_pessoa_juridica as PJ
                                                  ON PJ.numcgm = entidade.numcgm
                                            WHERE entidade.exercicio = '".$this->getDado('stExercicio')."'
                                               AND entidade.cod_entidade = ".$this->getDado('entidade')."
                                      ) AS cod_und_gestora
                                  , (SELECT CASE WHEN configuracao_entidade.valor is not null THEN valor ELSE '0000' END AS valor
                                        FROM administracao.configuracao_entidade
                                      WHERE configuracao_entidade.cod_modulo = 62
                                          AND configuracao_entidade.exercicio ='".$this->getDado('stExercicio')."'
                                          AND configuracao_entidade.parametro like 'tceal_configuracao_unidade_autonoma'
                                          AND configuracao_entidade.cod_entidade =  ".$this->getDado('entidade')."
                                     ) AS codigo_ua
                                  , (SELECT SUM(valor) from recuperarEventosCalculados(1,'".$this->getDado('codPeriodoMovimentacao')."',servidores.cod_contrato,0,'','') WHERE natureza ='P') as salario_bruto
                                  , (SELECT SUM(valor) from recuperarEventosCalculados(1,'".$this->getDado('codPeriodoMovimentacao')."',servidores.cod_contrato,0,'','') WHERE natureza ='P' or natureza ='D' ) as salario_liquido
                                  , *
                          FROM  (
                                        SELECT contrato.registro as matricula
                                                  , servidor_pensionista.*
                                          FROM pessoal".$this->getDado('stEntidade').".contrato
                                                  , (
                                                 -- Inicio consulta servidores (ativos, aposentados e rescindidos)
                                                     SELECT contrato_servidor.cod_contrato as cod_contrato
                                                                , sw_cgm_pessoa_fisica.cpf as cpf
                                                                , sw_cgm.nom_cgm as nome
                                                                , sw_cgm_pessoa_fisica.dt_nascimento as data_nascimento
                                                                , servidor.nome_mae
                                                                , servidor.nome_pai
                                                                , sw_cgm_pessoa_fisica.servidor_pis_pasep as pis_pasep
                                                                , servidor.nr_titulo_eleitor as titulo_eleitoral
                                                                , to_char(ultimo_contrato_servidor_nomeacao_posse.dt_admissao::date,'ddmmyyyy') as dt_admissao
                                                                , de_para_tipo_cargo.cod_tipo_cargo_tce as cod_vinculo_empregaticio
                                                                , CASE WHEN previdencia.cod_regime_previdencia = 1 THEN 2 WHEN previdencia.cod_regime_previdencia = 2 THEN 1 END AS cod_regime_previdenciario
                                                                , CASE WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 5 THEN 1
                                                                       WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 4 THEN 2
                                                                       WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 7 THEN 3
                                                                       WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 6 THEN 4
                                                                       WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 9 THEN 5
                                                                       WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 8 THEN 6
                                                                       ELSE 7
                                                                  END AS cod_escolaridade
                                                                , CASE WHEN adido_cedido.tipo_cedencia= 'a' THEN 2
                                                                       WHEN adido_cedido.tipo_cedencia= 'c' THEN 1
                                                                       ELSE 0
                                                                  END AS sob_cessao
                                                                , cgm_cessionario.cnpj as cnpj_entidade
                                                                , cgm_cessionario.nom_fantasia as nome_entidade
                                                                , adido_cedido.dt_inicial as data_cessao
                                                                , adido_cedido.dt_final as data_retorno_cessao
                                                                , NULL as margem_consignada
                                                                , ultimo_contrato_servidor_cargo_cbo.cod_cbo as cbo
                                                                , ultimo_contrato_servidor_cargo_cbo.cod_cargo
                                                                , (SELECT cod_cargo FROM pessoal.cargo WHERE cod_cargo = ultimo_contrato_servidor_funcao.cod_cargo) as cod_funcao
                                                                , REPLACE(vw_orgao_nivel.orgao,'.','') as cod_lotacao
                                                        FROM pessoal".$this->getDado('stEntidade').".contrato_servidor

                                                INNER JOIN pessoal".$this->getDado('stEntidade').".servidor_contrato_servidor
                                                            ON contrato_servidor.cod_contrato = servidor_contrato_servidor.cod_contrato

                                                INNER JOIN pessoal".$this->getDado('stEntidade').".servidor
                                                            ON servidor_contrato_servidor.cod_servidor = servidor.cod_servidor

                                                 INNER JOIN sw_cgm
                                                             ON servidor.numcgm = sw_cgm.numcgm

                                                 INNER JOIN sw_cgm_pessoa_fisica
                                                             ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm

                                                 INNER JOIN pessoal".$this->getDado('stEntidade').".contrato_servidor_nomeacao_posse
                                                             ON contrato_servidor_nomeacao_posse.cod_contrato = contrato_servidor.cod_contrato

                                                 INNER JOIN ultimo_contrato_servidor_orgao('".$this->getDado('inCodEntidade')."', '".$this->getDado('codPeriodoMovimentacao')."') as ultimo_contrato_servidor_orgao
                                                             ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_orgao.cod_contrato

                                                 INNER JOIN organograma.vw_orgao_nivel
                                                             ON ultimo_contrato_servidor_orgao.cod_orgao = vw_orgao_nivel.cod_orgao

                                                 INNER JOIN ultimo_contrato_servidor_nomeacao_posse('".$this->getDado('inCodEntidade')."', '".$this->getDado('codPeriodoMovimentacao')."') as ultimo_contrato_servidor_nomeacao_posse
                                                             ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_nomeacao_posse.cod_contrato

                                                 INNER JOIN ultimo_contrato_servidor_funcao('".$this->getDado('inCodEntidade')."', '".$this->getDado('codPeriodoMovimentacao')."') as ultimo_contrato_servidor_funcao
                                                             ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_funcao.cod_contrato

                                                 INNER JOIN ultimo_contrato_servidor_regime_funcao('".$this->getDado('inCodEntidade')."', '".$this->getDado('codPeriodoMovimentacao')."') as ultimo_contrato_servidor_regime_funcao
                                                             ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_regime_funcao.cod_contrato

                                                 INNER JOIN ultimo_contrato_servidor_padrao('".$this->getDado('inCodEntidade')."', '".$this->getDado('codPeriodoMovimentacao')."') as ultimo_contrato_servidor_padrao
                                                             ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_padrao.cod_contrato

                                                 INNER JOIN ultimo_contrato_servidor_salario('".$this->getDado('inCodEntidade')."', '".$this->getDado('codPeriodoMovimentacao')."') as ultimo_contrato_servidor_salario
                                                             ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_salario.cod_contrato

                                                 INNER JOIN ultimo_contrato_servidor_sub_divisao_funcao('".$this->getDado('inCodEntidade')."', '".$this->getDado('codPeriodoMovimentacao')."') as ultimo_contrato_servidor_sub_divisao_funcao
                                                             ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_sub_divisao_funcao.cod_contrato

                                                 INNER JOIN tceal.de_para_tipo_cargo
                                                             ON de_para_tipo_cargo.cod_sub_divisao = ultimo_contrato_servidor_sub_divisao_funcao.cod_sub_divisao_funcao
                                                           AND de_para_tipo_cargo.cod_entidade= ". $this->getDado('entidade')."

                                                 INNER JOIN ultimo_contrato_servidor_previdencia('".$this->getDado('inCodEntidade')."', '".$this->getDado('codPeriodoMovimentacao')."') as ultimo_contrato_servidor_previdencia
                                                             ON ultimo_contrato_servidor_previdencia.cod_contrato = contrato_servidor.cod_contrato
                                                           AND ultimo_contrato_servidor_previdencia.bo_excluido ='f'

                                                 INNER JOIN folhapagamento".$this->getDado('stEntidade').".previdencia
                                                             ON folhapagamento.previdencia.cod_previdencia  =  ultimo_contrato_servidor_previdencia.cod_previdencia

                                                 INNER JOIN ultimo_contrato_servidor_cargo_cbo('".$this->getDado('inCodEntidade')."', '".$this->getDado('codPeriodoMovimentacao')."') as ultimo_contrato_servidor_cargo_cbo
                                                             ON ultimo_contrato_servidor_cargo_cbo.cod_cargo = contrato_servidor.cod_cargo


                                                  LEFT JOIN pessoal".$this->getDado('stEntidade').".adido_cedido
                                                            ON adido_cedido.cod_contrato= contrato_servidor.cod_contrato

                                                  LEFT JOIN sw_cgm_pessoa_juridica as cgm_cessionario
                                                           ON cgm_cessionario.numcgm = adido_cedido.cgm_cedente_cessionario

                                                  LEFT JOIN pessoal".$this->getDado('stEntidade').".contrato_servidor_especialidade_cargo
                                                           ON contrato_servidor.cod_contrato = contrato_servidor_especialidade_cargo.cod_contrato

                                                  LEFT JOIN pessoal".$this->getDado('stEntidade').".especialidade
                                                            ON especialidade.cod_especialidade = contrato_servidor_especialidade_cargo.cod_especialidade

                                                  LEFT JOIN ultimo_contrato_servidor_local('".$this->getDado('inCodEntidade')."', '".$this->getDado('codPeriodoMovimentacao')."') as ultimo_contrato_servidor_local
                                                           ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_local.cod_contrato

                                                  LEFT JOIN organograma.local
                                                           ON local.cod_local = ultimo_contrato_servidor_local.cod_local

                                                  LEFT JOIN ultimo_contrato_servidor_especialidade_funcao('".$this->getDado('inCodEntidade')."', '".$this->getDado('codPeriodoMovimentacao')."') as ultimo_contrato_servidor_especialidade_funcao
                                                           ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_especialidade_funcao.cod_contrato

                                                  LEFT JOIN ultimo_contrato_servidor_caso_causa('".$this->getDado('inCodEntidade')."', '".$this->getDado('codPeriodoMovimentacao')."') as ultimo_contrato_servidor_caso_causa
                                                            ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_caso_causa.cod_contrato
                                                          AND ultimo_contrato_servidor_caso_causa.dt_rescisao <= to_date((select pega0datafinalcompetenciadoperiodomovimento(".$this->getDado('codPeriodoMovimentacao')."))::varchar, 'yyyy-mm-dd')

                                                      WHERE ultimo_contrato_servidor_caso_causa.dt_rescisao IS NULL
                                                             OR TO_CHAR(ultimo_contrato_servidor_caso_causa.dt_rescisao,'yyyymmdd')::DATE >= (select pega0datafinalcompetenciadoperiodomovimento(".$this->getDado('codPeriodoMovimentacao').")::DATE)

                                                  -- Fim consulta servidores (ativos, aposentados e rescindidos)

                                                     UNION

                                                 -- Inicio consulta pensionista
                                                     SELECT contrato_pensionista.cod_contrato AS cod_contrato
                                                          , sw_cgm_pessoa_fisica.cpf AS cpf
                                                          , sw_cgm.nom_cgm as nome
                                                          , sw_cgm_pessoa_fisica.dt_nascimento as data_nascimento
                                                          , 'Não Informado' as nome_mae
                                                          , 'Não Informado' as nome_pai
                                                          , sw_cgm_pessoa_fisica.servidor_pis_pasep as pis_pasep
                                                          , servidor.nr_titulo_eleitor as titulo_eleitoral
                                                          , to_char(ultimo_contrato_pensionista_nomeacao_posse.dt_admissao::date, 'ddmmyyyy') as dt_admissao
                                                          , de_para_tipo_cargo.cod_tipo_cargo_tce as cod_vinculo_empregaticio
                                                          , CASE WHEN previdencia.cod_regime_previdencia = 1 THEN 2 WHEN previdencia.cod_regime_previdencia = 2 THEN 1 END AS cod_regime_previdenciario
                                                          , CASE WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 5 THEN 1
                                                                 WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 4 THEN 2
                                                                 WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 7 THEN 3
                                                                 WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 6 THEN 4
                                                                 WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 9 THEN 5
                                                                 WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 8 THEN 6
                                                                 ELSE 7
                                                            END AS cod_escolaridade
                                                          , NULL AS sob_cessao
                                                          , '' as cnpj_entidade
                                                          , '' as nome_entidade
                                                          , NULL as data_cessao
                                                          , NULL as data_retorno_cessao
                                                          , NULL as margem_consignada
                                                          , ultimo_contrato_servidor_cargo_cbo.cod_cbo as cbo
                                                          , ultimo_contrato_servidor_cargo_cbo.cod_cargo
                                                          , (SELECT cod_cargo FROM pessoal.cargo WHERE cod_cargo = ultimo_contrato_pensionista_funcao.cod_cargo) as cod_funcao
                                                          , REPLACE(vw_orgao_nivel.orgao,'.','') as cod_lotacao

                                                       FROM pessoal".$this->getDado('stEntidade').".contrato_pensionista

                                                 INNER JOIN pessoal".$this->getDado('stEntidade').".pensionista
                                                             ON contrato_pensionista.cod_pensionista = pensionista.cod_pensionista
                                                           AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente

                                                 INNER JOIN pessoal".$this->getDado('stEntidade').".contrato
                                                             ON contrato.cod_contrato = contrato_pensionista.cod_contrato

                                                 INNER JOIN pessoal".$this->getDado('stEntidade').".contrato_servidor
                                                             ON contrato_servidor.cod_contrato = contrato.cod_contrato

                                                 INNER JOIN pessoal".$this->getDado('stEntidade').".servidor_contrato_servidor
                                                             ON servidor_contrato_servidor.cod_contrato = contrato_servidor.cod_contrato

                                                 INNER JOIN pessoal".$this->getDado('stEntidade').".servidor
                                                             ON servidor.cod_servidor = servidor_contrato_servidor.cod_servidor

                                                 INNER JOIN sw_cgm
                                                             ON sw_cgm.numcgm = pensionista.numcgm

                                                 INNER JOIN sw_cgm_pessoa_fisica
                                                             ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm

                                                 INNER JOIN ultimo_contrato_pensionista_orgao('".$this->getDado('inCodEntidade')."', '".$this->getDado('codPeriodoMovimentacao')."') as ultimo_contrato_pensionista_orgao
                                                             ON contrato_pensionista.cod_contrato = ultimo_contrato_pensionista_orgao.cod_contrato

                                                 INNER JOIN organograma.vw_orgao_nivel
                                                             ON ultimo_contrato_pensionista_orgao.cod_orgao = vw_orgao_nivel.cod_orgao

                                                 INNER JOIN ultimo_contrato_servidor_nomeacao_posse('".$this->getDado('inCodEntidade')."', '".$this->getDado('codPeriodoMovimentacao')."') as ultimo_contrato_pensionista_nomeacao_posse
                                                             ON pensionista.cod_contrato_cedente = ultimo_contrato_pensionista_nomeacao_posse.cod_contrato

                                                 INNER JOIN ultimo_contrato_servidor_funcao('".$this->getDado('inCodEntidade')."', '".$this->getDado('codPeriodoMovimentacao')."') as ultimo_contrato_pensionista_funcao
                                                             ON pensionista.cod_contrato_cedente = ultimo_contrato_pensionista_funcao.cod_contrato

                                                 INNER JOIN ultimo_contrato_servidor_regime_funcao('".$this->getDado('inCodEntidade')."', '".$this->getDado('codPeriodoMovimentacao')."') as ultimo_contrato_pensionista_regime_funcao
                                                             ON pensionista.cod_contrato_cedente = ultimo_contrato_pensionista_regime_funcao.cod_contrato

                                                 INNER JOIN ultimo_contrato_servidor_padrao('".$this->getDado('inCodEntidade')."', '".$this->getDado('codPeriodoMovimentacao')."') as ultimo_contrato_pensionista_padrao
                                                             ON pensionista.cod_contrato_cedente = ultimo_contrato_pensionista_padrao.cod_contrato

                                                 INNER JOIN ultimo_contrato_servidor_salario('".$this->getDado('inCodEntidade')."', '".$this->getDado('codPeriodoMovimentacao')."') as ultimo_contrato_pensionista_salario
                                                             ON pensionista.cod_contrato_cedente = ultimo_contrato_pensionista_salario.cod_contrato

                                                 INNER JOIN ultimo_contrato_servidor_sub_divisao_funcao('".$this->getDado('inCodEntidade')."', '".$this->getDado('codPeriodoMovimentacao')."') as ultimo_contrato_pensionista_sub_divisao_funcao
                                                             ON pensionista.cod_contrato_cedente = ultimo_contrato_pensionista_sub_divisao_funcao.cod_contrato

                                                 INNER JOIN tceal.de_para_tipo_cargo
                                                             ON de_para_tipo_cargo.cod_sub_divisao = ultimo_contrato_pensionista_sub_divisao_funcao.cod_sub_divisao_funcao
                                                             AND de_para_tipo_cargo.cod_entidade= ". $this->getDado('entidade')."

                                                 INNER JOIN  ultimo_contrato_pensionista_previdencia('".$this->getDado('inCodEntidade')."', '".$this->getDado('codPeriodoMovimentacao')."') as ultimo_contrato_pensionista_previdencia
                                                             ON ultimo_contrato_pensionista_previdencia.cod_contrato = contrato_servidor.cod_contrato

                                                 INNER JOIN folhapagamento".$this->getDado('stEntidade').".previdencia
                                                             ON previdencia.cod_previdencia  = ultimo_contrato_pensionista_previdencia.cod_previdencia

                                                 INNER JOIN ultimo_contrato_servidor_cargo_cbo('".$this->getDado('inCodEntidade')."', '".$this->getDado('codPeriodoMovimentacao')."') as ultimo_contrato_servidor_cargo_cbo
                                                             ON ultimo_contrato_servidor_cargo_cbo.cod_cargo = contrato_servidor.cod_cargo

                                                  LEFT JOIN pessoal".$this->getDado('stEntidade').".contrato_servidor_especialidade_cargo as contrato_pensionista_especialidade_cargo
                                                            ON pensionista.cod_contrato_cedente = contrato_pensionista_especialidade_cargo.cod_contrato

                                                  LEFT JOIN pessoal".$this->getDado('stEntidade').".especialidade
                                                            ON especialidade.cod_especialidade = contrato_pensionista_especialidade_cargo.cod_especialidade

                                                  LEFT JOIN ultimo_contrato_servidor_local('".$this->getDado('inCodEntidade')."', '".$this->getDado('codPeriodoMovimentacao')."') as ultimo_contrato_pensionista_local
                                                           ON pensionista.cod_contrato_cedente = ultimo_contrato_pensionista_local.cod_contrato

                                                  LEFT JOIN organograma.local
                                                           ON local.cod_local = ultimo_contrato_pensionista_local.cod_local

                                                  LEFT JOIN ultimo_contrato_servidor_especialidade_funcao('".$this->getDado('inCodEntidade')."', '".$this->getDado('codPeriodoMovimentacao')."') as ultimo_contrato_pensionista_especialidade_funcao
                                                            ON pensionista.cod_contrato_cedente = ultimo_contrato_pensionista_especialidade_funcao.cod_contrato

                                                      WHERE contrato_pensionista.dt_encerramento::date IS NULL
                                                             OR contrato_pensionista.dt_encerramento::date >= (select pega0datafinalcompetenciadoperiodomovimento(".$this->getDado('codPeriodoMovimentacao').")::date)

                                                 -- Fim consulta pensionista
                                                  ) as servidor_pensionista


                        WHERE contrato.cod_contrato = servidor_pensionista.cod_contrato
                         ) as servidores
                         WHERE (substring(servidores.dt_admissao from 5 for 4)||substring(servidores.dt_admissao from 3 for 2)||substring(servidores.dt_admissao from 1 for 2))::integer < to_char((select pega0datafinalcompetenciadoperiodomovimento(".$this->getDado('codPeriodoMovimentacao').")::date), 'yyyymmdd')::integer
                        GROUP BY cod_contrato
                               , cpf
                               , nome
                               , data_nascimento
                               , nome_mae
                               , nome_pai
                               , pis_pasep
                               , titulo_eleitoral
                               , cod_vinculo_empregaticio
                               , cod_regime_previdenciario
                               , cod_escolaridade
                               , sob_cessao
                               , cnpj_entidade
                               , nome_entidade
                               , data_cessao
                               , data_retorno_cessao
                               , dt_admissao
                               , salario_bruto
                               , salario_liquido
                               , margem_consignada
                               , cbo
                               , cod_cargo
                               , cod_funcao
                               , cod_lotacao
                               , matricula

                        ORDER BY nome, matricula

         ";

         return $this->executaRecuperaSql($stSql,$rsRecordSet,"","",$boTransacao);

     }

}
