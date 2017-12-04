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

    * Extensão da Classe de Mapeamento TTCEALLotacoesServidores
    *
    * Data de Criação: 29/05/2014
    *
    * @author: Carolina Schwaab Marçal
    *
    * $Id: TTCEALLotacoesServidores.class.php 65682 2016-06-09 12:52:28Z lisiane $
    *
    * @ignore
    *
*/
class TTCEALLotacoesServidores extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEALLotacoesServidores()
    {
        parent::Persistente();
        $this->setDado('exercicio',Sessao::getExercicio());
    }
    
  public function listarExportacaoLotacoesServidores(&$rsRecordSet,$stFiltro="",$stOrder=" ",$boTransacao="")
    {
         $stSql = "
                        SELECT DISTINCT ( SELECT PJ.cnpj
                                            FROM orcamento.entidade
                                      INNER JOIN sw_cgm_pessoa_juridica as PJ
                                                  ON PJ.numcgm = entidade.numcgm
                                            WHERE entidade.exercicio = '".$this->getDado('stExercicio')."'
                                               AND entidade.cod_entidade = ".$this->getDado('entidade')."
                                      ) AS cod_und_gestora
                                  , (SELECT CASE WHEN configuracao_entidade.valor <> '' THEN valor ELSE '0000' END AS valor
                                        FROM administracao.configuracao_entidade
                                      WHERE configuracao_entidade.cod_modulo = 62
                                          AND configuracao_entidade.exercicio ='".$this->getDado('stExercicio')."'
                                          AND configuracao_entidade.parametro like 'tceal_configuracao_unidade_autonoma'
                                          AND configuracao_entidade.cod_entidade =  ".$this->getDado('entidade')."
                                     ) AS codigo_ua
                                 , cod_lotacao
                                 , descricao_lotacao
                          FROM  (
                                        SELECT servidor_pensionista.*
                                          FROM pessoal".$this->getDado('stEntidade').".contrato
                                                  , (
                                                 -- Inicio consulta servidores (ativos, aposentados e rescindidos)
                                                     SELECT contrato_servidor.cod_contrato as cod_contrato
                                                                , to_char(ultimo_contrato_servidor_nomeacao_posse.dt_admissao::date,'ddmmyyyy') as dt_admissao
                                                                , cargo.cod_cargo
                                                                , REPLACE(vw_orgao_nivel.orgao,'.','') as cod_lotacao
                                                                , recuperaDescricaoOrgao(ultimo_contrato_servidor_orgao.cod_orgao, to_date((select pega0datafinalcompetenciadoperiodomovimento(".$this->getDado('codPeriodoMovimentacao')."))::varchar, 'yyyy-mm-dd')) as descricao_lotacao
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

                                                 LEFT JOIN tceal.de_para_tipo_cargo
                                                             ON de_para_tipo_cargo.cod_sub_divisao = ultimo_contrato_servidor_sub_divisao_funcao.cod_sub_divisao_funcao
                                                           AND de_para_tipo_cargo.cod_entidade= ". $this->getDado('entidade')."

                                                 INNER JOIN ultimo_contrato_servidor_previdencia('".$this->getDado('inCodEntidade')."', '".$this->getDado('codPeriodoMovimentacao')."') as ultimo_contrato_servidor_previdencia
                                                             ON ultimo_contrato_servidor_previdencia.cod_contrato = contrato_servidor.cod_contrato
                                                           AND ultimo_contrato_servidor_previdencia.bo_excluido ='f'

                                                 INNER JOIN folhapagamento".$this->getDado('stEntidade').".previdencia
                                                             ON folhapagamento.previdencia.cod_previdencia  =  ultimo_contrato_servidor_previdencia.cod_previdencia

                                                 INNER JOIN ultimo_contrato_servidor_cargo_cbo('".$this->getDado('inCodEntidade')."', '".$this->getDado('codPeriodoMovimentacao')."') as ultimo_contrato_servidor_cargo_cbo
                                                             ON ultimo_contrato_servidor_cargo_cbo.cod_cargo = contrato_servidor.cod_cargo

                                                 INNER JOIN pessoal".$this->getDado('stEntidade').".cargo
                                                             ON cargo.cod_cargo = ultimo_contrato_servidor_cargo_cbo.cod_cargo

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
                                                               , to_char(ultimo_contrato_pensionista_nomeacao_posse.dt_admissao::date, 'ddmmyyyy') as dt_admissao
                                                               , cargo.cod_cargo
                                                               , REPLACE(vw_orgao_nivel.orgao,'.','') as cod_lotacao
                                                               , recuperaDescricaoOrgao(ultimo_contrato_pensionista_orgao.cod_orgao, to_date((select pega0datafinalcompetenciadoperiodomovimento(".$this->getDado('codPeriodoMovimentacao')."))::varchar, 'yyyy-mm-dd')) as descricao_lotacao
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

                                                    LEFT JOIN tceal.de_para_tipo_cargo
                                                             ON de_para_tipo_cargo.cod_sub_divisao = ultimo_contrato_pensionista_sub_divisao_funcao.cod_sub_divisao_funcao
                                                             AND de_para_tipo_cargo.cod_entidade= ". $this->getDado('entidade')."

                                                 INNER JOIN  ultimo_contrato_pensionista_previdencia('".$this->getDado('inCodEntidade')."', '".$this->getDado('codPeriodoMovimentacao')."') as ultimo_contrato_pensionista_previdencia
                                                             ON ultimo_contrato_pensionista_previdencia.cod_contrato = contrato_servidor.cod_contrato

                                                 INNER JOIN folhapagamento".$this->getDado('stEntidade').".previdencia
                                                             ON previdencia.cod_previdencia  = ultimo_contrato_pensionista_previdencia.cod_previdencia

                                                 INNER JOIN ultimo_contrato_servidor_cargo_cbo('".$this->getDado('inCodEntidade')."', '".$this->getDado('codPeriodoMovimentacao')."') as ultimo_contrato_servidor_cargo_cbo
                                                             ON ultimo_contrato_servidor_cargo_cbo.cod_cargo = contrato_servidor.cod_cargo

                                                 INNER JOIN pessoal".$this->getDado('stEntidade').".cargo
                                                             ON cargo.cod_cargo = ultimo_contrato_servidor_cargo_cbo.cod_cargo

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
                                       , dt_admissao
                                       , cod_cargo
                                       , cod_lotacao
                                       , descricao_lotacao



         ";

         return $this->executaRecuperaSql($stSql,$rsRecordSet,"","",$boTransacao);

     }
}
?>
