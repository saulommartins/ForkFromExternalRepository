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
  * Data de Criação   :

  * @author Analista:
  * @author Desenvolvedor:
  $Id: $
  * @ignore
  */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTransparenciaServidor extends Persistente
{
     function recuperaServidor(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
     {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaServidor().$stOrdem;
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaServidor()
    {
        $stSql = "SELECT * FROM  (
                        SELECT
                                '".$this->getDado('inCodEntidade')."' as cod_entidade
                              , to_char((select pega0datafinalcompetenciadoperiodomovimento(".$this->getDado('inCompetencia').")::date), 'mm/yyyy') as mesano
                              , contrato.registro as matricula
                              , servidor_pensionista.*
                           FROM pessoal".$this->getDado('stEntidade').".contrato
                              , (
                                 -- Inicio consulta servidores (ativos, aposentados e rescindidos)
                                     SELECT
                                            contrato_servidor.cod_contrato
                                          , sw_cgm.nom_cgm
                                           ,recuperarSituacaoDoContratoLiteral(contrato_servidor.cod_contrato,'".$this->getDado('inCompetencia')."','".$this->getDado('stEntidade')."') as situacao
                                          , to_char(ultimo_contrato_servidor_nomeacao_posse.dt_admissao::date,'ddmmyyyy') as dt_admissao
                                          , (SELECT norma.num_norma||'/'||norma.exercicio FROM normas.norma WHERE norma.cod_norma = contrato_servidor.cod_norma) as ato_nomeacao
                                          , to_char(ultimo_contrato_servidor_caso_causa.dt_rescisao,'ddmmyyyy') as dt_rescisao
                                          , (SELECT causa_rescisao.descricao FROM pessoal".$this->getDado('stEntidade').".causa_rescisao, pessoal".$this->getDado('stEntidade').".caso_causa WHERE caso_causa.cod_caso_causa = ultimo_contrato_servidor_caso_causa.cod_caso_causa and caso_causa.cod_causa_rescisao=causa_rescisao.cod_causa_rescisao) as descricao_causa_rescisao
                                          , (SELECT descricao FROM pessoal".$this->getDado('stEntidade').".regime WHERE cod_regime = ultimo_contrato_servidor_regime_funcao.cod_regime_funcao) as descricao_regime_funcao
                                          , (SELECT descricao FROM pessoal".$this->getDado('stEntidade').".sub_divisao WHERE cod_sub_divisao = ultimo_contrato_servidor_sub_divisao_funcao.cod_sub_divisao_funcao) as descricao_sub_divisao_funcao
                                          , (SELECT descricao FROM pessoal".$this->getDado('stEntidade').".cargo WHERE cod_cargo = ultimo_contrato_servidor_funcao.cod_cargo) as descricao_funcao
                                          , (SELECT descricao FROM pessoal".$this->getDado('stEntidade').".especialidade WHERE especialidade.cod_especialidade = ultimo_contrato_servidor_especialidade_funcao.cod_especialidade_funcao) as descricao_especialidade_funcao
                                          , (SELECT descricao FROM folhapagamento".$this->getDado('stEntidade').".padrao WHERE padrao.cod_padrao = ultimo_contrato_servidor_padrao.cod_padrao) as descricao_padrao
                                          , ultimo_contrato_servidor_salario.horas_mensais
                                          , ( SELECT orgao
                                                FROM organograma.vw_orgao_nivel
                                               WHERE cod_orgao = ultimo_contrato_servidor_orgao.cod_orgao ) as lotacao
                                          , recuperaDescricaoOrgao(ultimo_contrato_servidor_orgao.cod_orgao, to_date((select pega0datafinalcompetenciadoperiodomovimento(".$this->getDado('inCompetencia')."))::varchar, 'yyyy-mm-dd')) as descricao_lotacao
                                          , local.descricao as descricao_local
                                       FROM pessoal".$this->getDado('stEntidade').".contrato_servidor

                                 INNER JOIN pessoal".$this->getDado('stEntidade').".servidor_contrato_servidor
                                         ON contrato_servidor.cod_contrato = servidor_contrato_servidor.cod_contrato

                                 INNER JOIN pessoal".$this->getDado('stEntidade').".servidor
                                         ON servidor_contrato_servidor.cod_servidor = servidor.cod_servidor

                                 INNER JOIN sw_cgm
                                         ON servidor.numcgm = sw_cgm.numcgm

                                 INNER JOIN ultimo_contrato_servidor_orgao('".$this->getDado('stEntidade')."', '".$this->getDado('inCompetencia')."') as ultimo_contrato_servidor_orgao
                                         ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_orgao.cod_contrato

                                 INNER JOIN ultimo_contrato_servidor_nomeacao_posse('".$this->getDado('stEntidade')."', '".$this->getDado('inCompetencia')."') as ultimo_contrato_servidor_nomeacao_posse
                                         ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_nomeacao_posse.cod_contrato
                                         AND ultimo_contrato_servidor_nomeacao_posse.dt_admissao<=(select pega0datafinalcompetenciadoperiodomovimento(".$this->getDado('inCompetencia').")::DATE)

                                 INNER JOIN ultimo_contrato_servidor_funcao('".$this->getDado('stEntidade')."', '".$this->getDado('inCompetencia')."') as ultimo_contrato_servidor_funcao
                                         ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_funcao.cod_contrato

                                 INNER JOIN ultimo_contrato_servidor_regime_funcao('".$this->getDado('stEntidade')."', '".$this->getDado('inCompetencia')."') as ultimo_contrato_servidor_regime_funcao
                                         ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_regime_funcao.cod_contrato

                                 INNER JOIN ultimo_contrato_servidor_padrao('".$this->getDado('stEntidade')."', '".$this->getDado('inCompetencia')."') as ultimo_contrato_servidor_padrao
                                         ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_padrao.cod_contrato

                                 INNER JOIN ultimo_contrato_servidor_salario('".$this->getDado('stEntidade')."', '".$this->getDado('inCompetencia')."') as ultimo_contrato_servidor_salario
                                         ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_salario.cod_contrato

                                 INNER JOIN ultimo_contrato_servidor_sub_divisao_funcao('".$this->getDado('stEntidade')."', '".$this->getDado('inCompetencia')."') as ultimo_contrato_servidor_sub_divisao_funcao
                                         ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_sub_divisao_funcao.cod_contrato

                                  LEFT JOIN pessoal".$this->getDado('stEntidade').".contrato_servidor_especialidade_cargo
                                         ON contrato_servidor.cod_contrato = contrato_servidor_especialidade_cargo.cod_contrato

                                  LEFT JOIN pessoal".$this->getDado('stEntidade').".especialidade
                                         ON especialidade.cod_especialidade = contrato_servidor_especialidade_cargo.cod_especialidade

                                  LEFT JOIN ultimo_contrato_servidor_local('".$this->getDado('stEntidade')."', '".$this->getDado('inCompetencia')."') as ultimo_contrato_servidor_local
                                         ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_local.cod_contrato

                                  LEFT JOIN organograma.local
                                         ON local.cod_local = ultimo_contrato_servidor_local.cod_local

                                  LEFT JOIN ultimo_contrato_servidor_especialidade_funcao('".$this->getDado('stEntidade')."', '".$this->getDado('inCompetencia')."') as ultimo_contrato_servidor_especialidade_funcao
                                         ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_especialidade_funcao.cod_contrato

                                  LEFT JOIN ultimo_contrato_servidor_caso_causa('".$this->getDado('stEntidade')."', '".$this->getDado('inCompetencia')."') as ultimo_contrato_servidor_caso_causa
                                         ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_caso_causa.cod_contrato
                                        --AND ultimo_contrato_servidor_caso_causa.dt_rescisao <= to_date((select pega0datafinalcompetenciadoperiodomovimento(".$this->getDado('inCompetencia')."))::varchar, 'yyyy-mm-dd')

                                      --WHERE ultimo_contrato_servidor_caso_causa.dt_rescisao IS NULL
                                         --OR TO_CHAR(ultimo_contrato_servidor_caso_causa.dt_rescisao,'yyyymmdd')::DATE >= (select pega0datafinalcompetenciadoperiodomovimento(".$this->getDado('inCompetencia').")::DATE)

                                      WHERE ultimo_contrato_servidor_caso_causa.dt_rescisao IS NULL
                                         OR TO_CHAR(ultimo_contrato_servidor_caso_causa.dt_rescisao,'yyyymmdd')::DATE >= (select pega0datainicialcompetenciadoperiodomovimento(".$this->getDado('inCompetencia').")::DATE)
                                         
                                  -- Fim consulta servidores (ativos, aposentados e rescindidos)

                                     UNION

                                 -- Inicio consulta pensionista
                                     SELECT
                                            contrato_pensionista.cod_contrato
                                          , sw_cgm.nom_cgm
                                          ,recuperarSituacaoDoContratoLiteral(contrato_pensionista.cod_contrato,'".$this->getDado('inCompetencia')."','".$this->getDado('stEntidade')."') as situacao
                                          , to_char(ultimo_contrato_pensionista_nomeacao_posse.dt_admissao::date, 'ddmmyyyy') as dt_admissao
                                          , (SELECT norma.num_norma||'/'||norma.exercicio FROM normas.norma WHERE norma.cod_norma = contrato_servidor.cod_norma) as ato_nomeacao
                                          , (CASE WHEN contrato_pensionista.dt_encerramento::date >= (select pega0datafinalcompetenciadoperiodomovimento(".$this->getDado('inCompetencia').")::date)
                                                  THEN to_char(contrato_pensionista.dt_encerramento::date, 'ddmmyyyy')
                                                  ELSE NULL
                                              END ) as dt_rescisao
                                          , (CASE WHEN contrato_pensionista.dt_encerramento::date >= (select pega0datafinalcompetenciadoperiodomovimento(".$this->getDado('inCompetencia').")::date)
                                                  THEN contrato_pensionista.motivo_encerramento
                                                  ELSE NULL
                                              END ) as descricao_causa_rescisao
                                         , (SELECT descricao FROM pessoal".$this->getDado('stEntidade').".regime WHERE cod_regime = ultimo_contrato_pensionista_regime_funcao.cod_regime_funcao) as descricao_regime_funcao
                                          , (SELECT descricao FROM pessoal".$this->getDado('stEntidade').".sub_divisao WHERE cod_sub_divisao = ultimo_contrato_pensionista_sub_divisao_funcao.cod_sub_divisao_funcao) as descricao_sub_divisao_funcao
                                          , (SELECT descricao FROM pessoal".$this->getDado('stEntidade').".cargo WHERE cod_cargo = ultimo_contrato_pensionista_funcao.cod_cargo) as descricao_funcao
                                          , (SELECT descricao FROM pessoal".$this->getDado('stEntidade').".especialidade WHERE especialidade.cod_especialidade = ultimo_contrato_pensionista_especialidade_funcao.cod_especialidade_funcao) as descricao_especialidade_funcao
                                          , (SELECT descricao FROM folhapagamento".$this->getDado('stEntidade').".padrao WHERE padrao.cod_padrao = ultimo_contrato_pensionista_padrao.cod_padrao) as descricao_padrao
                                          , ultimo_contrato_pensionista_salario.horas_mensais
                                          , ( SELECT orgao
                                                FROM organograma.vw_orgao_nivel
                                               WHERE cod_orgao = ultimo_contrato_pensionista_orgao.cod_orgao ) as lotacao
                                          , recuperaDescricaoOrgao(ultimo_contrato_pensionista_orgao.cod_orgao, to_date((select pega0datafinalcompetenciadoperiodomovimento(".$this->getDado('inCompetencia')."))::varchar, 'yyyy-mm-dd')) as descricao_lotacao
                                          , local.descricao as descricao_local
                                       FROM pessoal".$this->getDado('stEntidade').".contrato_pensionista

                                 INNER JOIN pessoal".$this->getDado('stEntidade').".pensionista
                                         ON contrato_pensionista.cod_pensionista = pensionista.cod_pensionista
                                        AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente

                                 INNER JOIN sw_cgm
                                         ON sw_cgm.numcgm = pensionista.numcgm

                                 INNER JOIN ultimo_contrato_pensionista_orgao('".$this->getDado('stEntidade')."', '".$this->getDado('inCompetencia')."') as ultimo_contrato_pensionista_orgao
                                         ON contrato_pensionista.cod_contrato = ultimo_contrato_pensionista_orgao.cod_contrato

                                 INNER JOIN ultimo_contrato_servidor_nomeacao_posse('".$this->getDado('stEntidade')."', '".$this->getDado('inCompetencia')."') as ultimo_contrato_pensionista_nomeacao_posse
                                         ON pensionista.cod_contrato_cedente = ultimo_contrato_pensionista_nomeacao_posse.cod_contrato

                                 INNER JOIN pessoal".$this->getDado('stEntidade').".contrato_servidor
                                         ON pensionista.cod_contrato_cedente = contrato_servidor.cod_contrato

                                 INNER JOIN ultimo_contrato_servidor_funcao('".$this->getDado('stEntidade')."', '".$this->getDado('inCompetencia')."') as ultimo_contrato_pensionista_funcao
                                         ON pensionista.cod_contrato_cedente = ultimo_contrato_pensionista_funcao.cod_contrato

                                 INNER JOIN ultimo_contrato_servidor_regime_funcao('".$this->getDado('stEntidade')."', '".$this->getDado('inCompetencia')."') as ultimo_contrato_pensionista_regime_funcao
                                         ON pensionista.cod_contrato_cedente = ultimo_contrato_pensionista_regime_funcao.cod_contrato

                                 INNER JOIN ultimo_contrato_servidor_padrao('".$this->getDado('stEntidade')."', '".$this->getDado('inCompetencia')."') as ultimo_contrato_pensionista_padrao
                                         ON pensionista.cod_contrato_cedente = ultimo_contrato_pensionista_padrao.cod_contrato

                                 INNER JOIN ultimo_contrato_servidor_salario('".$this->getDado('stEntidade')."', '".$this->getDado('inCompetencia')."') as ultimo_contrato_pensionista_salario
                                         ON pensionista.cod_contrato_cedente = ultimo_contrato_pensionista_salario.cod_contrato

                                 INNER JOIN ultimo_contrato_servidor_sub_divisao_funcao('".$this->getDado('stEntidade')."', '".$this->getDado('inCompetencia')."') as ultimo_contrato_pensionista_sub_divisao_funcao
                                         ON pensionista.cod_contrato_cedente = ultimo_contrato_pensionista_sub_divisao_funcao.cod_contrato

                                  LEFT JOIN pessoal".$this->getDado('stEntidade').".contrato_servidor_especialidade_cargo as contrato_pensionista_especialidade_cargo
                                         ON pensionista.cod_contrato_cedente = contrato_pensionista_especialidade_cargo.cod_contrato

                                  LEFT JOIN pessoal".$this->getDado('stEntidade').".especialidade
                                         ON especialidade.cod_especialidade = contrato_pensionista_especialidade_cargo.cod_especialidade

                                  LEFT JOIN ultimo_contrato_servidor_local('".$this->getDado('stEntidade')."', '".$this->getDado('inCompetencia')."') as ultimo_contrato_pensionista_local
                                         ON pensionista.cod_contrato_cedente = ultimo_contrato_pensionista_local.cod_contrato

                                  LEFT JOIN organograma.local
                                         ON local.cod_local = ultimo_contrato_pensionista_local.cod_local

                                  LEFT JOIN ultimo_contrato_servidor_especialidade_funcao('".$this->getDado('stEntidade')."', '".$this->getDado('inCompetencia')."') as ultimo_contrato_pensionista_especialidade_funcao
                                         ON pensionista.cod_contrato_cedente = ultimo_contrato_pensionista_especialidade_funcao.cod_contrato

                                      WHERE contrato_pensionista.dt_encerramento::date IS NULL
                                         OR contrato_pensionista.dt_encerramento::date >= (select pega0datafinalcompetenciadoperiodomovimento(".$this->getDado('inCompetencia').")::date)

                                 -- Fim consulta pensionista
                                  ) as servidor_pensionista
                         WHERE contrato.cod_contrato = servidor_pensionista.cod_contrato
                         ) as servidores
                         WHERE (substring(servidores.dt_admissao from 5 for 4)||substring(servidores.dt_admissao from 3 for 2)||substring(servidores.dt_admissao from 1 for 2))::integer < to_char((select pega0datafinalcompetenciadoperiodomovimento(".$this->getDado('inCompetencia').")::date), 'yyyymmdd')::integer
                         ORDER BY nom_cgm, matricula
        ";

        return $stSql;
    }
}
