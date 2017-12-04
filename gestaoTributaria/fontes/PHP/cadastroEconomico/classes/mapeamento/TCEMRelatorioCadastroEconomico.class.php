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
  * Classe de mapeamento do relatório do CADASTRO_ECONOMICO
  * Data de Criação: 08/09/2006

  * @author Analista: Fabio Bertoldi
  * @author Desenvolvedor: Diego Bueno Coelho

  * @package URBEM
  * @subpackage Mapeamento

    * $Id: TCEMRelatorioCadastroEconomico.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.02.17
*/

/*
$Log$
Revision 1.3  2007/01/11 10:22:14  dibueno
Bug #8042#

Revision 1.2  2006/09/15 12:08:26  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TCEMRelatorioCadastroEconomico extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCEMRelatorioCadastroEconomico()
{
    parent::Persistente();
    $this->setTabela('economico.cadastro_economico');

    $this->setCampoCod('inscricao_economica');
    $this->setComplementoChave('');

    $this->AddCampo('inscricao_economica','integer',true,'',true,false);
    $this->AddCampo('timestamp','timestamp',false,'',false,false);
    $this->AddCampo('dt_abertura','date',false,'',false,false);
}

function relatorioCadastroEconomico(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "", $boEmpresaFato = '', $boEmpresaDireito = '', $boEmpresaAutonoma = '', $boSocio = '', $boAtividade = "", $boLicenca = "", $boLogradouro = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRelatorioCadastroEconomico ( $boEmpresaFato, $boEmpresaDireito, $boEmpresaAutonoma, $boSocio, $boAtividade, $boLicenca, $boLogradouro ).$stFiltro.$stOrdem;
    $this->stDebug = $stSql;

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRelatorioCadastroEconomico($boEmpresaFato, $boEmpresaDireito, $boEmpresaAutonoma, $boSocio, $boAtividade, $boLicenca, $boLogradouro)
{
    $stSQL .= " SELECT DISTINCT \n";
    $stSQL .= "     ce.inscricao_economica,                                                         \n";

    if (!$boEmpresaAutonoma && !$boEmpresaDireito && !$boEmpresaFato) { $stSQL .= "  coalesce ( ef.numcgm, ed.numcgm, au.numcgm ) as numcgm_empresa, \n"; } else {
        if ( $boEmpresaAutonoma ) $stSQL .= "  au.numcgm as numcgm_empresa,                         \n";
        if ( $boEmpresaDireito ) $stSQL .= "  ed.numcgm as numcgm_empresa,                          \n";
        if ( $boEmpresaFato ) $stSQL .= "  ef.numcgm as numcgm_empresa,                             \n";
    }

    $stSQL .= "     CASE WHEN ba.dt_inicio IS NOT NULL THEN
                        CASE WHEN ba.dt_termino IS NOT NULL THEN
                            'Ativo'
                        ELSE
                            'Baixado'
                        END
                    ELSE
                        'Ativo'
                    END AS situacao_cadastro,                                                       \n";
    $stSQL .= "     cgm.nom_cgm as nome,                                                            \n";
    $stSQL .= "     cgmf.cpf,                                                                       \n";
    $stSQL .= "     cgmj.cnpj,                                                                      \n";
    $stSQL .= "     cerc.numcgm as cgm_contador,                                                    \n";
    $stSQL .= "     cerc.nom_cgm as nom_contador,                                                   \n";
    $stSQL .= "     ce.dt_abertura as inicio,                                                       \n";
    $stSQL .= "     TO_CHAR( ce.dt_abertura, 'DD/MM/YYYY') as inicio_br,                            \n";
    $stSQL .= "     arrecadacao.fn_consulta_endereco_empresa( ce.inscricao_economica) as endereco   \n";
    $stSQL .= "     , cerc.numcgm                                                                   \n";

    if (!$boEmpresaAutonoma && !$boEmpresaDireito && !$boEmpresaFato) {
        $stSQL .= " ,case                                                                           \n";
        $stSQL .= " when                                                                            \n";
        $stSQL .= "     cast( ef.numcgm as varchar) is not null                                     \n";
        $stSQL .= "     then 'Empresa de Fato'                                                      \n";
        $stSQL .= " when                                                                            \n";
        $stSQL .= "     cast( ed.numcgm as varchar) is not null                                     \n";
        $stSQL .= "     then 'Empresa de Direito'                                                   \n";
        $stSQL .= " when                                                                            \n";
        $stSQL .= "     cast( au.numcgm as varchar) is not null                                     \n";
        $stSQL .= "     then 'Autonomo'                                                             \n";
        $stSQL .= " end as tipoEmpresa                                                              \n";
        $stSQL .= " , coalesce ( ed.nom_categoria, null ) as nom_categoria                          \n";
    } else {
        if ( $boEmpresaAutonoma ) $stSQL .= " ,'Autonomo' as tipoEmpresa            \n";
        if ( $boEmpresaDireito ) $stSQL .= "  ,'Empresa de Direito' as tipoEmpresa ,coalesce ( ed.nom_categoria, null ) as nom_categoria \n";
        if ( $boEmpresaFato ) $stSQL .= "  ,'Empresa de Fato' as tipoEmpresa        \n";
    }

    $stSQL .= " , economico.fn_busca_sociedade(ce.inscricao_economica) as sociedade                 \n";

    $stSQL .= " FROM                                                                                \n";
    $stSQL .= "     economico.cadastro_economico as ce                                              \n";

    $stSQL .= " LEFT JOIN
                   (
                    SELECT
                        T.*,
                        COALESCE( confrontacao_trecho.cod_logradouro, di.cod_logradouro ) AS cod_logradouro
                    FROM
                        (
                            SELECT
                                max(tmp.timestamp) AS timestamp,
                                tmp.inscricao_economica
                            FROM
                                (
                                    SELECT
                                        timestamp,
                                        inscricao_economica

                                    FROM
                                        economico.domicilio_fiscal

                                UNION
                                    SELECT
                                        timestamp,
                                        inscricao_economica

                                    FROM
                                        economico.domicilio_informado
                                )AS tmp
                            GROUP BY
                                tmp.inscricao_economica
                        )AS T

                    LEFT JOIN
                        economico.domicilio_informado AS di
                    ON
                        T.inscricao_economica = di.inscricao_economica
                        AND T.timestamp = di.timestamp

                    LEFT JOIN
                        economico.domicilio_fiscal AS df
                    ON
                        T.inscricao_economica = df.inscricao_economica
                        AND T.timestamp = df.timestamp

                    LEFT JOIN
                        imobiliario.imovel_confrontacao
                    ON
                        imovel_confrontacao.inscricao_municipal = df.inscricao_municipal

                    LEFT JOIN
                        imobiliario.confrontacao_trecho
                    ON
                        confrontacao_trecho.cod_lote = imovel_confrontacao.cod_lote
                        AND confrontacao_trecho.cod_confrontacao = imovel_confrontacao.cod_confrontacao
                   )AS logradouro
            ON
                logradouro.inscricao_economica = ce.inscricao_economica
    \n";

    if ($boSocio) {
        $stSQL .= " INNER JOIN economico.sociedade as cesocio ON cesocio.inscricao_economica = ce.inscricao_economica   \n";
    }

    if ($boAtividade || $boLicenca) {
        $stSQL .= " INNER JOIN economico.atividade_cadastro_economico as eace ON eace.inscricao_economica = ce.inscricao_economica

        INNER JOIN
            (
                SELECT
                    tmp1.*
                FROM
                    economico.atividade AS tmp1

                INNER JOIN
                    (
                        SELECT
                            max(timestamp) AS timestamp,
                            cod_atividade,
                            cod_vigencia
                        FROM
                            economico.atividade
                        GROUP BY
                            cod_atividade,
                            cod_vigencia
                    )AS tmp2
                ON
                    tmp1.cod_atividade = tmp2.cod_atividade
                    AND tmp1.cod_vigencia = tmp2.cod_vigencia
                    AND tmp1.timestamp = tmp2.timestamp

                WHERE
                    tmp1.cod_vigencia = (
                        SELECT
                            cod_vigencia
                        FROM
                            economico.vigencia_atividade
                        WHERE
                            dt_inicio <= now()::date
                        ORDER BY
                            timestamp DESC
                        LIMIT 1
                    )
            )AS atividade
        ON
            atividade.cod_atividade = eace.cod_atividade
        \n";
        if ($boLicenca) {
            $stSQL .= "
                INNER JOIN (
                    SELECT
                        COALESCE( licenca_atividade.cod_licenca, licenca_especial.cod_licenca ) AS cod_licenca,
                        COALESCE( licenca_atividade.exercicio, licenca_especial.exercicio ) AS exercicio,
                        atividade_cadastro_economico.cod_atividade,
                        atividade_cadastro_economico.ocorrencia_atividade,
                        atividade_cadastro_economico.inscricao_economica

                    FROM
                        economico.atividade_cadastro_economico

                    LEFT JOIN
                        economico.licenca_atividade
                    ON
                        licenca_atividade.cod_atividade = atividade_cadastro_economico.cod_atividade
                        AND licenca_atividade.ocorrencia_atividade = atividade_cadastro_economico.ocorrencia_atividade
                        AND licenca_atividade.inscricao_economica = atividade_cadastro_economico.inscricao_economica

                    LEFT JOIN
                        economico.licenca_especial
                    ON
                        licenca_especial.cod_atividade = atividade_cadastro_economico.cod_atividade
                        AND licenca_especial.ocorrencia_atividade = atividade_cadastro_economico.ocorrencia_atividade
                        AND licenca_especial.inscricao_economica = atividade_cadastro_economico.inscricao_economica

                    WHERE
                        ( licenca_especial.cod_atividade IS NOT NULL OR licenca_atividade.cod_atividade IS NOT NULL )

                )AS elic
                ON
                    elic.cod_atividade = eace.cod_atividade
                    AND elic.ocorrencia_atividade = eace.ocorrencia_atividade
                    AND elic.inscricao_economica = eace.inscricao_economica ";
        }

    }

    $stSQL .= " LEFT JOIN                                                                          \n";
    $stSQL .= " ( SELECT                                                                            \n";
    $stSQL .= "     cerc.*, cgm.nom_cgm                                                             \n";
    $stSQL .= "   FROM                                                                              \n";
    $stSQL .= "     economico.cadastro_econ_resp_contabil as cerc                                   \n";
    $stSQL .= "     INNER JOIN sw_cgm as cgm ON cerc.numcgm = cgm.numcgm                            \n";
    $stSQL .= " ) as cerc ON cerc.inscricao_economica = ce.inscricao_economica                      \n";

    if (!$boEmpresaAutonoma && !$boEmpresaDireito && !$boEmpresaFato) {

        $stSQL .= " LEFT JOIN economico.cadastro_economico_empresa_fato as ef                       \n";
        $stSQL .= " ON ce.inscricao_economica = ef.inscricao_economica                              \n";

        $stSQL .= " LEFT JOIN economico.cadastro_economico_autonomo as au                           \n";
        $stSQL .= " ON ce.inscricao_economica = au.inscricao_economica                              \n";

        $stSQL .= " LEFT JOIN                                                                       \n";
        $stSQL .= " ( select ed.inscricao_economica, ed.numcgm, cat.nom_categoria                   \n";
        $stSQL .= "   from                                                                          \n";
        $stSQL .= "   economico.cadastro_economico_empresa_direito as ed                            \n";
        $stSQL .= "   INNER JOIN economico.categoria as cat ON cat.cod_categoria = ed.cod_categoria \n";
        $stSQL .= " ) as ed  ON ce.inscricao_economica = ed.inscricao_economica                     \n";

    } else {
        if ($boEmpresaAutonoma) {
            $stSQL .= " INNER JOIN economico.cadastro_economico_autonomo as au  ON au.inscricao_economica = ce.inscricao_economica \n";
        } elseif ($boEmpresaFato) {
            $stSQL .= " INNER JOIN economico.cadastro_economico_empresa_fato as ef ON ef.inscricao_economica = ce.inscricao_economica \n";
        } elseif ($boEmpresaDireito) {
            $stSQL .= " INNER JOIN                                                                      \n";
            $stSQL .= " ( select ed.inscricao_economica, ed.numcgm, cat.nom_categoria                   \n";
            $stSQL .= " from                                                                            \n";
            $stSQL .= " economico.cadastro_economico_empresa_direito as ed                              \n";
            $stSQL .= " INNER JOIN economico.categoria as cat ON cat.cod_categoria = ed.cod_categoria   \n";
            $stSQL .= " ) as ed  ON ce.inscricao_economica = ed.inscricao_economica                     \n";
        }
    }

    $stSQL .= " LEFT JOIN (        SELECT
                            tmp2.*
                        FROM
                            economico.baixa_cadastro_economico AS tmp2
                        INNER JOIN
                            (
                                SELECT
                                    max(tmp.timestamp) AS timestamp,
                                    tmp.inscricao_economica
                                FROM
                                    economico.baixa_cadastro_economico AS tmp
                                GROUP BY
                                    inscricao_economica
                            )AS tmp
                        ON
                            tmp.timestamp = tmp2.timestamp
                            AND tmp.inscricao_economica = tmp2.inscricao_economica) as ba                                      \n";
    $stSQL .= " ON ce.inscricao_economica = ba.inscricao_economica                                      \n";
    $stSQL .= " , sw_cgm as cgm                                                                         \n";
    $stSQL .= " LEFT JOIN sw_cgm_pessoa_fisica as cgmf ON cgm.numcgm = cgmf.numcgm                      \n";
    $stSQL .= " LEFT JOIN sw_cgm_pessoa_juridica as cgmj ON cgm.numcgm = cgmj.numcgm                    \n";

    if (!$boEmpresaAutonoma && !$boEmpresaDireito && !$boEmpresaFato) {
        $stSQL .= "  WHERE   coalesce ( ef.numcgm, ed.numcgm, au.numcgm ) = cgm.numcgm                  \n";
    } else {
        if ( $boEmpresaAutonoma ) $stSQL .= "  WHERE   au.numcgm = cgm.numcgm                           \n";
        if ( $boEmpresaDireito ) $stSQL .= "  WHERE   ed.numcgm = cgm.numcgm                            \n";
        if ( $boEmpresaFato ) $stSQL .= "  WHERE   ef.numcgm = cgm.numcgm                               \n";
    }

    $stSQL .= $stFiltro;

    return $stSQL;

}

function recuperaAtividadeRelatorio(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaAtividadeRelatorio().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaAtividadeRelatorio()
{
    $stSQL .= " SELECT                                                      \n";
    $stSQL .= "     ace.inscricao_economica,                                \n";
    $stSQL .= "     A.cod_atividade,                                        \n";
    $stSQL .= "     A.cod_estrutural,                                       \n";
    $stSQL .= "     A.nom_atividade,                                        \n";
    $stSQL .= "     A.cod_nivel,                                            \n";
    $stSQL .= "     ace.ocorrencia_atividade,                               \n";
    $stSQL .= "     economico.fn_busca_modalidade_atividade( ace.inscricao_economica, A.cod_atividade, ace.ocorrencia_atividade) as modalidade,  \n";
    $stSQL .= "     SA.cod_servico,                                         \n";
    $stSQL .= "     S.cod_estrutural AS cod_estrutural_servico,             \n";
    $stSQL .= "     S.nom_servico,                                          \n";
    $stSQL .= "     TO_CHAR( V.dt_inicio, 'DD/MM/YYYY') as dt_inicio,       \n";
    $stSQL .= "     AL.valor AS aliquota_atividade,                         \n";
    $stSQL .= "     ALS.valor AS aliquota_servico,
                    elic.cod_licenca,
                    elic.exercicio,
                    economico.fn_consulta_situacao_licenca(elic.cod_licenca, elic.exercicio) as situacao  \n";

    $stSQL .= " FROM                                                        \n";
    $stSQL .= "     economico.atividade_cadastro_economico ace              \n";

    $stSQL .= " INNER JOIN (
                    SELECT
                        COALESCE( licenca_atividade.cod_licenca, licenca_especial.cod_licenca ) AS cod_licenca,
                        COALESCE( licenca_atividade.exercicio, licenca_especial.exercicio ) AS exercicio,
                        atividade_cadastro_economico.cod_atividade,
                        atividade_cadastro_economico.ocorrencia_atividade,
                        atividade_cadastro_economico.inscricao_economica

                    FROM
                        economico.atividade_cadastro_economico

                    LEFT JOIN
                        economico.licenca_atividade
                    ON
                        licenca_atividade.cod_atividade = atividade_cadastro_economico.cod_atividade
                        AND licenca_atividade.ocorrencia_atividade = atividade_cadastro_economico.ocorrencia_atividade
                        AND licenca_atividade.inscricao_economica = atividade_cadastro_economico.inscricao_economica

                    LEFT JOIN
                        economico.licenca_especial
                    ON
                        licenca_especial.cod_atividade = atividade_cadastro_economico.cod_atividade
                        AND licenca_especial.ocorrencia_atividade = atividade_cadastro_economico.ocorrencia_atividade
                        AND licenca_especial.inscricao_economica = atividade_cadastro_economico.inscricao_economica


                )AS elic
                ON
                    elic.cod_atividade = ace.cod_atividade
                    AND elic.ocorrencia_atividade = ace.ocorrencia_atividade
                    AND elic.inscricao_economica = ace.inscricao_economica \n";

    $stSQL .= "
        INNER JOIN
            (
                SELECT
                    tmp1.*
                FROM
                    economico.atividade AS tmp1

                INNER JOIN
                    (
                        SELECT
                            max(timestamp) AS timestamp,
                            cod_atividade,
                            cod_vigencia
                        FROM
                            economico.atividade
                        GROUP BY
                            cod_atividade,
                            cod_vigencia
                    )AS tmp2
                ON
                    tmp1.cod_atividade = tmp2.cod_atividade
                    AND tmp1.cod_vigencia = tmp2.cod_vigencia
                    AND tmp1.timestamp = tmp2.timestamp

                WHERE
                    tmp1.cod_vigencia = (
                        SELECT
                            cod_vigencia
                        FROM
                            economico.vigencia_atividade
                        WHERE
                            dt_inicio <= now()::date
                        ORDER BY
                            timestamp DESC
                        LIMIT 1
                    )
            )AS A
        ON
            A.cod_atividade = ace.cod_atividade            \n";

    $stSQL .= "     INNER JOIN economico.vigencia_atividade as V ON V.cod_vigencia = A.cod_vigencia     \n";

    $stSQL .= "     LEFT JOIN economico.aliquota_atividade AL ON            \n";
    $stSQL .= "         AL.cod_atividade = A.cod_atividade                  \n";

    $stSQL .= "     LEFT JOIN economico.servico_atividade SA ON             \n";
    $stSQL .= "         A.cod_atividade = SA.cod_atividade                  \n";
    $stSQL .= "     LEFT JOIN economico.servico S ON                        \n";
    $stSQL .= "         SA.cod_servico  = S.cod_servico                     \n";
    $stSQL .= "     LEFT JOIN economico.aliquota_servico ALS ON             \n";
    $stSQL .= "         ALS.cod_servico = SA.cod_servico                    \n";

    $stSQL .= "     LEFT JOIN (                                             \n";
    $stSQL .= "         SELECT   cod_atividade, max(timestamp) as timestamp \n";
    $stSQL .= "         FROM     economico.aliquota_atividade               \n";
    $stSQL .= "         GROUP BY cod_atividade                              \n";
    $stSQL .= " ) AS MAL ON                                                 \n";
    $stSQL .= "     MAL.cod_atividade = AL.cod_atividade AND                \n";
    $stSQL .= "     MAL.timestamp     = AL.timestamp                        \n";

    return $stSQL;
}

function recuperaSociedadeRelatorio(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaSociedadeRelatorio().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    //$this->debug();
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaSociedadeRelatorio()
{
    $stSQL  = " SELECT DISTINCT                                                     \n";
    $stSQL .= "     consulta.numcgm,                                                \n";
    $stSQL .= "     consulta.nom_cgm,                                               \n";
    $stSQL .= "     consulta.socio,                                                 \n";
    $stSQL .= "     sociedade.quota_socio                                             \n";
    $stSQL .= "     FROM (                                                          \n";
    $stSQL .= " SELECT                                                              \n";
    $stSQL .= "     cesocio.numcgm,                                                 \n";
    $stSQL .= "     cgm.nom_cgm,                                                    \n";
    $stSQL .= "     cesocio.numcgm || ' - ' || cgm.nom_cgm as socio,                \n";
    $stSQL .= "     max(cesocio.timestamp) as timestamp                             \n";
    $stSQL .= " FROM                                                                \n";
    $stSQL .= "     economico.sociedade as cesocio                                  \n";
    $stSQL .= "     INNER JOIN sw_cgm as cgm ON cgm.numcgm = cesocio.numcgm         \n";

    return $stSQL;

}

function recuperaAtributosDinamicos(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaAtributosDinamicos($stFiltro, $stOrdem);
    $this->stDebug = $stSql;

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}
function montaRecuperaAtributosDinamicos($stFiltro, $stOrdem)
{
    $stSQL = "   SELECT DISTINCT
                      CASE WHEN efv.cod_atributo is not null THEN
                                  efv.cod_atributo
                             WHEN eav.cod_atributo is not null THEN
                                  eav.cod_atributo
                             ELSE
                                  edv.cod_atributo
                        END as cod_atributo,
                             ad.nom_atributo,
                        CASE WHEN efv.valor is not null THEN
                             ltrim(efv.valor, '0')
                             WHEN eav.valor is not null THEN
                             ltrim(eav.valor, '0')
                             ELSE
                             ltrim(edv.valor, '0')
                        END as valor

                       FROM (
                      SELECT
                          principal.cod_atributo
                         ,principal.cod_cadastro
                         ,principal.cod_modulo
                         ,max(principal.timestamp) as timestamp
                      FROM (
                       SELECT DISTINCT
                        CASE WHEN efv.cod_atributo is not null THEN
                                  efv.cod_atributo
                             WHEN eav.cod_atributo is not null THEN
                                  eav.cod_atributo
                             ELSE
                                  edv.cod_atributo
                        END as cod_atributo,
                        CASE WHEN efv.cod_cadastro is not null THEN
                                  efv.cod_cadastro
                             WHEN eav.cod_cadastro is not null THEN
                                  eav.cod_cadastro
                             ELSE
                                  edv.cod_cadastro
                        END as cod_cadastro,
                        CASE WHEN efv.cod_modulo is not null THEN
                                  efv.cod_modulo
                             WHEN eav.cod_modulo is not null THEN
                                  eav.cod_modulo
                             ELSE
                                  edv.cod_modulo
                        END as cod_modulo,
                        CASE WHEN efv.timestamp is not null THEN
                                  efv.timestamp
                             WHEN eav.timestamp is not null THEN
                                  eav.timestamp
                             ELSE
                                  edv.timestamp
                             END as timestamp
                       FROM
                        administracao.atributo_dinamico as ad
                           LEFT JOIN economico.atributo_empresa_fato_valor as efv
                           ON efv.cod_atributo = ad.cod_atributo
                           and efv.cod_cadastro = ad.cod_cadastro
                           and efv.cod_modulo   = ad.cod_modulo
                           LEFT JOIN economico.atributo_cad_econ_autonomo_valor as eav
                           ON eav.cod_atributo = ad.cod_atributo
                           and eav.cod_cadastro = ad.cod_cadastro
                           and eav.cod_modulo   = ad.cod_modulo
                           LEFT JOIN economico.atributo_empresa_direito_valor as edv
                           ON edv.cod_atributo = ad.cod_atributo
                           and edv.cod_cadastro = ad.cod_cadastro
                           and edv.cod_modulo   = ad.cod_modulo

                           ".$stFiltro.$stOrdem."

                          ) as principal
                       GROUP BY 1,2,3
                      ) as consulta

                           LEFT JOIN  administracao.atributo_dinamico as ad
                           ON ad.cod_atributo = consulta.cod_atributo
                           and ad.cod_cadastro = consulta.cod_cadastro
                           and ad.cod_modulo   = consulta.cod_modulo

                           LEFT JOIN economico.atributo_empresa_fato_valor as efv
                           ON efv.cod_atributo = consulta.cod_atributo
                           and efv.cod_cadastro = consulta.cod_cadastro
                           and efv.cod_modulo   = consulta.cod_modulo
                           and efv.timestamp    = consulta.timestamp
                           LEFT JOIN economico.atributo_cad_econ_autonomo_valor as eav
                           ON eav.cod_atributo = consulta.cod_atributo
                           and eav.cod_cadastro = consulta.cod_cadastro
                           and eav.cod_modulo   = consulta.cod_modulo
                           and eav.timestamp    = consulta.timestamp
                           LEFT JOIN economico.atributo_empresa_direito_valor as edv
                           ON edv.cod_atributo = consulta.cod_atributo
                           and edv.cod_cadastro = consulta.cod_cadastro
                           and edv.cod_modulo   = consulta.cod_modulo
                           and edv.timestamp    = consulta.timestamp
               ".$stFiltro.$stOrdem." ";

               return $stSQL;

}
}
