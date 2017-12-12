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

  * Classe de mapeamento da tabela ECONOMICO.LICENCA
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento

    * $Id: TCEMLicenca.class.php 63399 2015-08-25 13:39:55Z arthur $

* Casos de uso: uc-05.02.12

*/


include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TCEMLicenca extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
public function __construct()
{
    parent::Persistente();
    $this->setTabela('economico.licenca');

    $this->setCampoCod('cod_licenca');
    $this->setComplementoChave('cod_licenca,exercicio');

    $this->AddCampo('cod_licenca','integer',true,'',true,false);
    $this->AddCampo('exercicio','char',true,'4',true,false);
    $this->AddCampo('dt_inicio','date',true,'',false,false);
    $this->AddCampo('dt_termino','date',false,'',false,false);

}

public function montaRecuperaRelacionamento()
{
    $stSql  = " SELECT                                              \n";
    $stSql .= "     MAX (COD_LICENCA)                               \n";
    $stSql .= " FROM                                                \n";
    $stSql .= "     economico.licenca                               \n";

    return $stSql;

}

public function recuperaLicencasConsulta(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaLicencasConsulta().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

public function montaRecuperaLicencasConsulta()
{
    $stSql .= " SELECT                                                                                  \n";
    $stSql .= "     l.cod_licenca                                                                       \n";
    $stSql .= "     , TO_CHAR(l.dt_inicio,'DD/MM/YYYY') as dt_inicio                                    \n";
    $stSql .= "     , TO_CHAR(l.dt_termino,'DD/MM/YYYY') as dt_termino                                  \n";
    $stSql .= "     , economico.fn_consulta_situacao_licenca(l.cod_licenca, l.exercicio) as situacao    \n";
    $stSql .= "     , economico.fn_consulta_processo_licenca(l.cod_licenca, l.exercicio) as processo    \n";
    $stSql .= "     , TO_CHAR(bl.dt_inicio , 'DD/MM/YYYY') as baixa_inicio                              \n";
    $stSql .= "     , TO_CHAR(bl.dt_termino, 'DD/MM/YYYY')  as baixa_termino                            \n";
    $stSql .= "     , bl.motivo                                                                         \n";
    $stSql .= "     , lpad( l.exercicio, 4, '0')::varchar as exercicio                                  \n";
    $stSql .= "     , ( CASE WHEN lca.inscricao_economica IS NOT NULL THEN                              \n";
    $stSql .= "             'Atividade'::varchar                                                        \n";
    $stSql .= "         WHEN lce.inscricao_economica IS NOT NULL THEN                                   \n";
    $stSql .= "             'Especial'::varchar                                                         \n";
    $stSql .= "         ELSE NULL                                                                       \n";
    $stSql .= "         END                                                                             \n";
    $stSql .= "     ) AS especie_licenca                                                                \n";
    $stSql .= "     , eld.cod_documento                                                                 \n";
    $stSql .= "     , eld.cod_tipo_documento                                                            \n";
    $stSql .= "     , amd.nome_documento                                                                \n";
    $stSql .= "     , amd.nome_arquivo_agt as nome_arquivo_template                                     \n";
    $stSql .= "     , coalesce ( lca.ocorrencia_licenca, lce.ocorrencia_licenca ) as ocorrencia_licenca \n";
    $stSql .= "     , max(eace.ocorrencia_atividade) as ocorrencia_atividade                            \n";
    $stSql .= " FROM                                                                                    \n";
    $stSql .= "     economico.licenca l                                                                 \n";

    $stSql .= "     LEFT JOIN economico.baixa_licenca bl                                                \n";
    $stSql .= "     ON bl.cod_licenca = l.cod_licenca                                                   \n";
    $stSql .= "     AND bl.exercicio   = l.exercicio                                                    \n";
    $stSql .= "     AND bl.dt_inicio <= now()::date                                                     \n";
    $stSql .= "     AND CASE WHEN bl.dt_termino IS NOT NULL THEN                                        \n";
    $stSql .= "             bl.dt_termino >= now()::date                                                \n";
    $stSql .= "         ELSE true::boolean                                                              \n";
    $stSql .= "         END                                                                             \n";

    $stSql .= "     LEFT JOIN economico.licenca_atividade lca                                           \n";
    $stSql .= "     ON lca.cod_licenca = l.cod_licenca                                                  \n";
    $stSql .= "     AND lca.exercicio = l.exercicio                                                     \n";

    $stSql .= "     LEFT JOIN economico.atividade_cadastro_economico as eace                            \n";
    $stSql .= "     ON eace.inscricao_economica = lca.inscricao_economica                               \n";
    $stSql .= "     AND eace.cod_atividade = lca.cod_atividade                                          \n";
    $stSql .= "     AND eace.principal = true                                                           \n";

    $stSql .= "     LEFT JOIN economico.licenca_especial lce                                            \n";
    $stSql .= "     ON lce.cod_licenca = l.cod_licenca                                                  \n";
    $stSql .= "     AND lce.exercicio = l.exercicio                                                     \n";

    $stSql .= "     LEFT JOIN economico.licenca_documento as eld                                        \n";
    $stSql .= "     ON eld.cod_licenca = l.cod_licenca                                                  \n";
    $stSql .= "     AND eld.exercicio = l.exercicio                                                     \n";

    $stSql .= "     LEFT JOIN administracao.modelo_documento as amd                                     \n";
    $stSql .= "     ON amd.cod_tipo_documento = eld.cod_tipo_documento                                  \n";
    $stSql .= "     AND amd.cod_documento = eld.cod_documento                                           \n";

    return $stSql;

}

public function buscaUltimoRegistro(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaBuscaUltimoRegistro().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;

}

public function montaBuscaUltimoRegistro()
{
    $stSql = "	SELECT                                                              \n";
    $stSql .="      max(cod_licenca) as valor                                       \n";
    $stSql .="  FROM                                                                \n";
    $stSql .="		economico.licenca                                               \n";

    return $stSql;

}

public function buscaAtributoInscricaoMunicipalLicensaManaquiri(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montabuscaAtributoInscricaoMunicipalLicensaManaquiri( $inInscricaoEconomica ).$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;

}

public function montabuscaAtributoInscricaoMunicipalLicensaManaquiri($inInscricaoEconomica)
{
    $stSql  = " select CASE WHEN (aedv.inscricao_economica is not null)                                                                                     \n";
    $stSql .= "             THEN aedv.valor                                                                                                                 \n";
    $stSql .= "             ELSE CASE WHEN (aefv.inscricao_economica is not null)                                                                           \n";
    $stSql .= "             THEN aefv.valor                                                                                                                 \n";
    $stSql .= "             ELSE  ''                                                                                                                        \n";
    $stSql .= "             END                                                                                                                             \n";
    $stSql .= "         END AS inscricao_municipal                                                                                                          \n";
    $stSql .= "      from economico.cadastro_economico                                                                                                      \n";
    $stSql .= " left join economico.atributo_empresa_direito_valor aedv                                                                                     \n";
    $stSql .= "        on cadastro_economico.inscricao_economica = aedv.inscricao_economica                                                                 \n";
    $stSql .= "       and aedv.cod_modulo = 14                                                                                                              \n";
    $stSql .= "       and aedv.cod_cadastro = 2                                                                                                             \n";
    $stSql .= "       and aedv.cod_atributo = 5048                                                                                                          \n";
    $stSql .= "       and aedv.timestamp = (select max(timestamp) from economico.atributo_empresa_direito_valor                                             \n";
    $stSql .= "                                                        where atributo_empresa_direito_valor.inscricao_economica = aedv.inscricao_economica  \n";
    $stSql .= "                                                          and atributo_empresa_direito_valor.cod_modulo   = 14                               \n";
    $stSql .= "                                                          and atributo_empresa_direito_valor.cod_cadastro = 2                                \n";
    $stSql .= "                                                          and atributo_empresa_direito_valor.cod_atributo = 5048)                            \n";
    $stSql .= " left join economico.atributo_empresa_fato_valor aefv                                                                                        \n";
    $stSql .= "        on cadastro_economico.inscricao_economica = aefv.inscricao_economica                                                                 \n";
    $stSql .= "       and aefv.cod_modulo = 14                                                                                                              \n";
    $stSql .= "       and aefv.cod_cadastro = 2                                                                                                             \n";
    $stSql .= "       and aefv.cod_atributo = 5048                                                                                                          \n";
    $stSql .= "       and aefv.timestamp = (select max(timestamp) from economico.atributo_empresa_fato_valor                                                \n";
    $stSql .= "                                                        where atributo_empresa_fato_valor.inscricao_economica = aedv.inscricao_economica     \n";
    $stSql .= "                                                          and atributo_empresa_fato_valor.cod_modulo   = 14                                  \n";
    $stSql .= "                                                          and atributo_empresa_fato_valor.cod_cadastro = 2                                   \n";
    $stSql .= "                                                          and atributo_empresa_fato_valor.cod_atributo = 5048)                               \n";
    $stSql .= "     where 1=1                                                                                                                               \n";

    return $stSql;
}

public function buscaDadosConcederLicencaAtividade(&$rsRecordSet, $inExercicioConf, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaBuscaDadosConcederLicencaAtividade( $inExercicioConf ).$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;

}

public function montaBuscaDadosConcederLicencaAtividade($inExercicioConf)
{
    $stSql = "  SELECT DISTINCT                                                                     \n";
    $stSql .="      LPAD (ela.cod_licenca::varchar, 8, '0') as cod_licenca                                   \n";
    $stSql .="      , licenca_documento.num_alvara                                                  \n";
    $stSql .="      , lpad( ela.exercicio, 4, '0')::varchar as exercicio                            \n";
    $stSql .="      , TO_CHAR ( ela.dt_inicio,'dd/mm/yyyy' ) as inicio_licenca                      \n";
    $stSql .="      , TO_CHAR ( ela.dt_termino,'dd/mm/yyyy' ) as termino_licenca                    \n";
    $stSql .="      , ece.inscricao_economica as IE                                                 \n";
    $stSql .="      , (CASE WHEN edi.timestamp > edf.timestamp  THEN
                         null
                       ELSE
                        edf.inscricao_municipal
                       end) as IM            \n";
    $stSql .="      , coalesce ( eceA.numcgm, eceD.numcgm, eceF.numcgm ) as numcgm                  \n";
    $stSql .="      , cgm.nom_cgm as razao_social                                                   \n";
    $stSql .="      , cgmPJ.nom_fantasia                                                            \n";
    $stSql .="      , cgmPF.rg                                                                      \n";
    $stSql .="      , cgmPF.cpf                                                                     \n";
    $stSql .="      , cgmPJ.cnpj as cnpj                                                            \n";
    $stSql .="      , NULL as num_emissao                                                           \n";
    $stSql .="      , eceD.nom_natureza                                                             \n";
    $stSql .="      , elo.observacao                                                                \n";
    $stSql .="      , LPAD (ela.cod_licenca::varchar, 4, '0')||ela.exercicio as codigo_barra                 \n";
    $stSql .="      , diretor_tributos.nom_cgm as diretor_tributos                                  \n";
    $stSql .="      , usuario.nom_cgm as usuario                                                    \n";
    $stSql .="      , TO_CHAR ( now()::date,'dd/mm/yyyy' ) as data_emissao                          \n";

    $stSql .="      , ( CASE WHEN edi.endereco is null OR ( edi.timestamp < COALESCE( edf.timestamp, '1900-01-01 00:00:00' ) ) THEN    \n";
    $stSql .="              split_part ( edf.endereco, '§', 1 ) ||' '||                                 \n";
    $stSql .="              split_part ( edf.endereco, '§', 3 ) ||' - '||                               \n";
    $stSql .="              split_part ( edf.endereco, '§', 4 ) ||' '||                                 \n";
    $stSql .="              split_part ( edf.endereco, '§', 5 ) ||' Bairro: '||                         \n";
    $stSql .="              split_part ( edf.endereco, '§', 6 ) ||' CEP: '||                            \n";
    $stSql .="              split_part ( edf.endereco, '§', 7 )                                         \n";
    $stSql .="          ELSE                                                                            \n";
    $stSql .="              split_part ( edi.endereco, '§', 1 ) ||' '||                                 \n";
    $stSql .="              split_part ( edi.endereco, '§', 3 ) ||' - '||                               \n";
    $stSql .="              split_part ( edi.endereco, '§', 4 ) ||' '||                                 \n";
    $stSql .="              split_part ( edi.endereco, '§', 5 ) ||' Bairro: '||                         \n";
    $stSql .="              split_part ( edi.endereco, '§', 6 ) ||' CEP: '||                            \n";
    $stSql .="              split_part ( edi.endereco, '§', 7 )                                         \n";
    $stSql .="          END                                                                         \n";
    $stSql .="      ) as rua                                                                        \n";

    $stSql .="      , ( CASE WHEN edi.endereco is null OR ( edi.timestamp < COALESCE( edf.timestamp, '1900-01-01 00:00:00' ) ) THEN    \n";
    $stSql .="              split_part ( edf.endereco,'§',5)                                        \n";

    $stSql .="          ELSE                                                                        \n";
    $stSql .="              split_part ( edi.endereco,'§',5)                                        \n";
    $stSql .="          END                                                                         \n";
    $stSql .="      ) as complemento                                                                \n";

    $stSql .="      , ( CASE WHEN edi.endereco is null OR ( edi.timestamp < COALESCE( edf.timestamp, '1900-01-01 00:00:00' ) ) THEN    \n";
    $stSql .="              split_part ( edf.endereco,'§',9) ||' / '||                              \n";
    $stSql .="              split_part ( edf.endereco,'§',11 )                                      \n";
    $stSql .="          ELSE                                                                        \n";
    $stSql .="              split_part ( edi.endereco,'§',9) ||' / '||                              \n";
    $stSql .="              split_part ( edi.endereco,'§',11 )                                      \n";
    $stSql .="          END                                                                         \n";
    $stSql .="      ) as cidade                                                                     \n";
    $stSql .="      , ( CASE WHEN edi.endereco is null OR ( edi.timestamp < COALESCE( edf.timestamp, '1900-01-01 00:00:00' ) ) THEN    \n";
    $stSql .="              split_part ( edf.endereco,'§',7)                                        \n";
    $stSql .="          ELSE                                                                        \n";
    $stSql .="              split_part ( edi.endereco,'§',7)                                        \n";
    $stSql .="          END                                                                         \n";
    $stSql .="      ) as cep                                                                        \n";
    $stSql .="      , ativide_principal.cod_atividade                                               \n";
    $stSql .="      , ativide_principal.nom_atividade                                               \n";
    $stSql .="      , ativide_principal.dt_inicio as inicio_atividade                               \n";
    $stSql .="      , upper (prefeitura_nome.valor) as prefeitura_nome                              \n";
    $stSql .="      , prefeitura_cnpj.valor as prefeitura_cnpj                                      \n";
    $stSql .="      , ( prefeitura_tl.valor||' '||prefeitura_logr.valor||', '||                     \n";
    $stSql .="          prefeitura_logr_nr.valor||' '||prefeitura_complem.valor||' - '||            \n";
    $stSql .="          prefeitura_bairro.valor||' - CEP: '||                                       \n";
    $stSql .="          substring (prefeitura_cep.valor from 1 for 5)||'-'||                        \n";
    $stSql .="          substring( prefeitura_cep.valor from 6 for 9)                               \n";
    $stSql .="      ) as prefeitura_endereco                                                        \n";
    $stSql .="      , prefeitura_municipio.nom_municipio as prefeitura_municipio                    \n";
    $stSql .="      , prefeitura_uf.nom_uf as prefeitura_uf                                         \n";
    $stSql .="      , prefeitura_uf.sigla_uf as prefeitura_uf_sigla                                 \n";
    $stSql .="      , ela.ocorrencia_licenca                                                        \n";
    $stSql .="      , coalesce (domingo.hr_inicio,  '00:00:00') as domingo_inicio                   \n";
    $stSql .="      , coalesce (domingo.hr_termino, '00:00:00') as domingo_termino                  \n";
    $stSql .="      , coalesce (segunda.hr_inicio,  '00:00:00') as segunda_inicio                   \n";
    $stSql .="      , coalesce (segunda.hr_termino, '00:00:00') as segunda_termino                  \n";
    $stSql .="      , coalesce (terca.hr_inicio,    '00:00:00') as terca_inicio                     \n";
    $stSql .="      , coalesce (terca.hr_termino,   '00:00:00') as terca_termino                    \n";
    $stSql .="      , coalesce (quarta.hr_inicio,   '00:00:00') as quarta_inicio                    \n";
    $stSql .="      , coalesce (quarta.hr_termino,  '00:00:00') as quarta_termino                   \n";
    $stSql .="      , coalesce (quinta.hr_inicio,   '00:00:00') as quinta_inicio                    \n";
    $stSql .="      , coalesce (quinta.hr_termino,  '00:00:00') as quinta_termino                   \n";
    $stSql .="      , coalesce (sexta.hr_inicio,    '00:00:00') as sexta_inicio                     \n";
    $stSql .="      , coalesce (sexta.hr_termino,   '00:00:00') as sexta_termino                    \n";
    $stSql .="      , coalesce (sabado.hr_inicio,   '00:00:00') as sabado_inicio                    \n";
    $stSql .="      , coalesce (sabado.hr_termino,  '00:00:00') as sabado_termino                   \n";

    $stSql .="      , ( CASE WHEN (edf.inscricao_municipal IS NOT NULL) AND (edf.timestamp > COALESCE( edi.timestamp, '1900-01-01 00:00:00' )) THEN                          \n";
    $stSql .="          (                                                                           \n";
    $stSql .="          select valor from imobiliario.atributo_lote_urbano_valor                    \n";
    $stSql .="          where cod_lote = (                                                          \n";
    $stSql .="                  select cod_lote from imobiliario.imovel_lote                        \n";
    $stSql .="                  where inscricao_municipal = edf.inscricao_municipal                 \n";
    $stSql .="              )                                                                       \n";
    $stSql .="              and cod_atributo = (                                                    \n";
    $stSql .="                  SELECT cod_atributo from administracao.atributo_dinamico            \n";
    $stSql .="                  WHERE  cod_modulo = 12  AND nom_atributo = 'Lote'                   \n";
    $stSql .="              )                                                                       \n";
    $stSql .="          ORDER BY timestamp DESC                                                     \n";
    $stSql .="          LIMIT 1                                                                     \n";
    $stSql .="          )                                                                           \n";
    $stSql .="          END                                                                         \n";
    $stSql .="      ) as Lote                                                                       \n";
    $stSql .="      , ( CASE WHEN (edf.inscricao_municipal IS NOT NULL) and (edf.timestamp > COALESCE( edi.timestamp, '1900-01-01 00:00:00' )) THEN                          \n";
    $stSql .="          (                                                                           \n";
    $stSql .="          select valor from imobiliario.atributo_lote_urbano_valor                    \n";
    $stSql .="          where cod_lote = (                                                          \n";
    $stSql .="                  select cod_lote from imobiliario.imovel_lote                        \n";
    $stSql .="                  where inscricao_municipal = edf.inscricao_municipal                 \n";
    $stSql .="              )                                                                       \n";
    $stSql .="              and cod_atributo = (                                                    \n";
    $stSql .="                  SELECT cod_atributo from administracao.atributo_dinamico            \n";
    $stSql .="                  WHERE  cod_modulo = 12  AND nom_atributo = 'Quadra'                 \n";
    $stSql .="              )                                                                       \n";
    $stSql .="          ORDER BY timestamp DESC                                                     \n";
    $stSql .="          LIMIT 1                                                                     \n";
    $stSql .="          )                                                                           \n";
    $stSql .="          END                                                                         \n";
    $stSql .="      ) as quadra                                                                     \n";
    $stSql .="      , (CASE WHEN COALESCE( edi.timestamp, '1900-01-01 00:00:00' ) > COALESCE( edf.timestamp, '1900-01-01 00:00:00' )  THEN
                         ''
                       ELSE \n";
    $stSql .="       (   SELECT                                                                      \n";
    $stSql .="              iln.nom_localizacao                                                     \n";
    $stSql .="          FROM                                                                        \n";
    $stSql .="              imobiliario.imovel_lote as iil                                          \n";
    $stSql .="              INNER JOIN imobiliario.lote_localizacao as ill                          \n";
    $stSql .="              ON ill.cod_lote = iil.cod_lote                                          \n";
    $stSql .="              INNER JOIN imobiliario.localizacao AS iloc                              \n";
    $stSql .="              ON iloc.cod_localizacao = ill.cod_localizacao                           \n";
    $stSql .="              INNER JOIN (                                                            \n";
    $stSql .="                  SELECT                                                              \n";
    $stSql .="                      nom_localizacao                                                 \n";
    $stSql .="                      , iln.cod_localizacao                                           \n";
    $stSql .="                  FROM                                                                \n";
    $stSql .="                      imobiliario.localizacao AS iloc                                 \n";
    $stSql .="                      INNER JOIN  imobiliario.localizacao_nivel AS iln                \n";
    $stSql .="                      ON iloc.codigo_composto = iln.valor || '.00'                    \n";
    $stSql .="                      AND iln.cod_nivel = 1                                           \n";
    $stSql .="              ) as iln                                                                \n";
    $stSql .="              ON iln.cod_localizacao = iloc.cod_localizacao                           \n";
    $stSql .="          WHERE                                                                       \n";
    $stSql .="              iil.inscricao_municipal = edf.inscricao_municipal)                       \n";
    $stSql .="     END ) AS regiao                                                                     \n";

    $stSql .="      , (CASE WHEN COALESCE( edi.timestamp, '1900-01-01 00:00:00' ) > COALESCE( edf.timestamp, '1900-01-01 00:00:00' )  THEN
                         ''
                       ELSE \n";
    $stSql .="      (    SELECT                                                                      \n";
    $stSql .="              iloc.nom_localizacao                                                    \n";
    $stSql .="          FROM                                                                        \n";
    $stSql .="              imobiliario.imovel_lote as iil                                          \n";
    $stSql .="              INNER JOIN imobiliario.lote_localizacao as ill                          \n";
    $stSql .="              ON ill.cod_lote = iil.cod_lote                                          \n";
    $stSql .="              INNER JOIN imobiliario.localizacao AS iloc                              \n";
    $stSql .="              ON iloc.cod_localizacao = ill.cod_localizacao                           \n";
    $stSql .="          WHERE iil.inscricao_municipal = edf.inscricao_municipal)               \n";
    $stSql .="       END) as distrito                                                                   \n";

    $stSql .="  FROM                                                                                \n";
    $stSql .="  (                                                                                   \n";
    $stSql .="  select                                                                              \n";
    $stSql .="      ela.*                                                                           \n";
    $stSql .="  from                                                                                \n";
    $stSql .="      economico.licenca_atividade as ela                                              \n";
    $stSql .="      INNER JOIN  (                                                                   \n";
    $stSql .="          select                                                                      \n";
    $stSql .="              inscricao_economica,                                                    \n";
    $stSql .="              cod_licenca,                                                            \n";
    $stSql .="              max(ocorrencia_licenca) as ocorrencia                                   \n";
    $stSql .="          from                                                                        \n";
    $stSql .="              economico.licenca_atividade                                             \n";
    $stSql .="          group by inscricao_economica, cod_licenca                                   \n";
    $stSql .="      ) as ela2                                                                       \n";
    $stSql .="      ON ela2.inscricao_economica = ela.inscricao_economica                           \n";
    $stSql .="      AND ela2.cod_licenca = ela.cod_licenca                                          \n";
    $stSql .="      AND ela2.ocorrencia = ela.ocorrencia_licenca                                    \n";
    $stSql .="  group by                                                                            \n";
    $stSql .="      ela.inscricao_economica,                                                        \n";
    $stSql .="      ela.cod_licenca,                                                                \n";
    $stSql .="      ela.exercicio,                                                                  \n";
    $stSql .="      ela.ocorrencia_atividade,                                                       \n";
    $stSql .="      ela.cod_atividade,                                                              \n";
    $stSql .="      ela.ocorrencia_licenca,                                                         \n";
    $stSql .="      ela.dt_inicio,                                                                  \n";
    $stSql .="      ela.dt_termino                                                                  \n";
    $stSql .="  order by                                                                            \n";
    $stSql .="      ela.cod_licenca                                                                 \n";
    $stSql .="  ) as ela                                                                            \n";

    $stSql .="  INNER JOIN
                    economico.licenca_documento
                ON
                    licenca_documento.cod_licenca = ela.cod_licenca
                    AND licenca_documento.exercicio = ela.exercicio
    \n";

    $stSql .="  INNER JOIN economico.licenca as el                                                  \n";
    $stSql .="  ON el.cod_licenca = ela.cod_licenca                                                 \n";
    $stSql .="  AND el.exercicio = ela.exercicio                                                    \n";

    $stSql .="  LEFT JOIN economico.licenca_observacao as elo                                       \n";
    $stSql .="  ON elo.cod_licenca = el.cod_licenca                                                 \n";
    $stSql .="  AND elo.exercicio = el.exercicio                                                    \n";

    $stSql .="  INNER JOIN economico.cadastro_economico as ece                                      \n";
    $stSql .="  ON ece.inscricao_economica = ela.inscricao_economica                                \n";

    $stSql .="  LEFT JOIN economico.cadastro_economico_autonomo as eceA                             \n";
    $stSql .="  ON eceA.inscricao_economica = ece.inscricao_economica                               \n";

    $stSql .="  LEFT JOIN economico.cadastro_economico_empresa_fato as eceF                         \n";
    $stSql .="  ON eceF.inscricao_economica = ece.inscricao_economica                               \n";

    $stSql .="  LEFT JOIN (                                                                         \n";
    $stSql .="      select                                                                          \n";
    $stSql .="          eceD.inscricao_economica,                                                   \n";
    $stSql .="          eceD.numcgm,                                                                \n";
    $stSql .="          enj.nom_natureza                                                            \n";
    $stSql .="      from                                                                            \n";
    $stSql .="          economico.cadastro_economico_empresa_direito as eceD                        \n";
    $stSql .="          LEFT JOIN (                                                                 \n";
    $stSql .="              select                                                                  \n";
    $stSql .="                  inscricao_economica,                                                \n";
    $stSql .="                  cod_natureza,                                                       \n";
    $stSql .="                  max(timestamp)                                                      \n";
    $stSql .="              from                                                                    \n";
    $stSql .="                  economico.empresa_direito_natureza_juridica as eceDNJ               \n";
    $stSql .="              group by                                                                \n";
    $stSql .="                  inscricao_economica,                                                \n";
    $stSql .="                  cod_natureza                                                        \n";
    $stSql .="          ) as eceDNJ                                                                 \n";
    $stSql .="          on eceDNJ.inscricao_economica = eced.inscricao_economica                    \n";
    $stSql .="          LEFT JOIN economico.natureza_juridica as enj                                \n";
    $stSql .="          on enj.cod_natureza = eceDNJ.cod_natureza                                   \n";
    $stSql .="  ) as eceD                                                                           \n";
    $stSql .="  ON eceD.inscricao_economica = ece.inscricao_economica                               \n";

    $stSql .="  LEFT JOIN economico.empresa_direito_natureza_juridica as eceDNJ                     \n";
    $stSql .="  ON eceDNJ.inscricao_economica = ece.inscricao_economica                             \n";

    $stSql .="  INNER JOIN sw_cgm as cgm                                                            \n";
    $stSql .="  ON cgm.numcgm = coalesce ( eceA.numcgm, eceD.numcgm, eceF.numcgm )                  \n";

    $stSql .="  LEFT JOIN sw_cgm_pessoa_fisica as cgmPF                                             \n";
    $stSql .="  ON cgmPF.numcgm = cgm.numcgm                                                        \n";

    $stSql .="  LEFT JOIN sw_cgm_pessoa_juridica as cgmPJ                                           \n";
    $stSql .="  ON cgmPJ.numcgm = cgm.numcgm                                                        \n";

    $stSql .="  LEFT JOIN  (                                                                        \n";
    $stSql .="      select                                                                          \n";
    $stSql .="          edf1.inscricao_economica, edf1.inscricao_municipal                          \n";
    $stSql .="          , edf1.endereco, edf1.timestamp                                             \n";
    $stSql .="      from                                                                            \n";
    $stSql .="      (                                                                               \n";
    $stSql .="          select                                                                      \n";
    $stSql .="              inscricao_economica                                                     \n";
    $stSql .="              , inscricao_municipal                                                   \n";
    $stSql .="              , timestamp                                                             \n";
    $stSql .="              , economico.fn_busca_domicilio_fiscal (inscricao_economica) as endereco \n";
    $stSql .="          from economico.domicilio_fiscal                                             \n";
    $stSql .="      ) as edf1                                                                       \n";
    $stSql .="      INNER JOIN  (                                                                   \n";
    $stSql .="          select                                                                      \n";
    $stSql .="              inscricao_economica, max (timestamp) as timestamp                       \n";
    $stSql .="          from economico.domicilio_fiscal                                             \n";
    $stSql .="          group by inscricao_economica                                                \n";
    $stSql .="      ) as edf2                                                                       \n";
    $stSql .="      ON edf2.inscricao_economica = edf1.inscricao_economica                          \n";
    $stSql .="      AND edf2.timestamp = edf1.timestamp                                             \n";
    $stSql .="  ) as edf                                                                            \n";
    $stSql .="  ON edf.inscricao_economica = ece.inscricao_economica                                \n";

    $stSql .="  LEFT JOIN (                                                                         \n";
    $stSql .="      select edi1.*                                                                   \n";
    $stSql .="      from                                                                            \n";
    $stSql .="      (                                                                               \n";
    $stSql .="          select                                                                      \n";
    $stSql .="              inscricao_economica, timestamp,                                         \n";
    $stSql .="              economico.fn_busca_domicilio_informado (inscricao_economica) as endereco\n";
    $stSql .="          from                                                                        \n";
    $stSql .="              economico.domicilio_informado as edi                                    \n";
    $stSql .="      ) as edi1                                                                       \n";
    $stSql .="      INNER JOIN (                                                                    \n";
    $stSql .="          select inscricao_economica, max (timestamp) as timestamp                    \n";
    $stSql .="          from economico.domicilio_informado                                          \n";
    $stSql .="          group by inscricao_economica                                                \n";
    $stSql .="      ) as edi2                                                                       \n";
    $stSql .="      ON  edi2.inscricao_economica = edi1.inscricao_economica                         \n";
    $stSql .="      AND edi2.timestamp = edi1.timestamp                                             \n";
    $stSql .="  ) as edi                                                                            \n";
    $stSql .="  ON edi.inscricao_economica = ece.inscricao_economica                                \n";

    $stSql .="  INNER JOIN (                                                                        \n";
    $stSql .="      SELECT                                                                          \n";
    $stSql .="          atv.cod_atividade,                                                          \n";
    $stSql .="          ATE.inscricao_economica,                                                    \n";
    $stSql .="          atv.nom_atividade,                                                          \n";
    $stSql .="          atv.cod_estrutural,                                                         \n";
    $stSql .="          ATE.PRINCIPAL,                                                              \n";
    $stSql .="          coalesce ( TO_CHAR ( ATE.DT_INICIO,'dd/mm/yyyy' ) , '-') AS dt_inicio,      \n";
    $stSql .="          coalesce ( TO_CHAR ( ATE.DT_TERMINO,'dd/mm/yyyy' ), '-') AS dt_termino,     \n";
    $stSql .="          ATE.OCORRENCIA_ATIVIDADE                                                    \n";
    $stSql .="      FROM                                                                            \n";
    $stSql .="          economico.atividade  AS ATV                                                 \n";
    $stSql .="          INNER JOIN economico.atividade_cadastro_economico AS ATE                    \n";
    $stSql .="          ON ATV.COD_ATIVIDADE = ATE.COD_ATIVIDADE                                    \n";
    $stSql .="INNER JOIN (                                                                          \n";
    $stSql .="      SELECT MAX(ocorrencia_atividade) AS ocorrencia_atividade                        \n";
    $stSql .="           , inscricao_economica                                                      \n";
    $stSql .="        FROM economico.atividade_cadastro_economico                                   \n";
    $stSql .="    GROUP BY inscricao_economica                                                      \n";
    $stSql .="      ) AS max_atividade ON max_atividade.inscricao_economica = ATE.inscricao_economica \n";
    $stSql .="      AND max_atividade.ocorrencia_atividade = ATE.ocorrencia_atividade               \n";
    //$stSql .="      WHERE                                                                           \n";
    //$stSql .="          ATE.PRINCIPAL = true                                                        \n";
    $stSql .="  ) as ativide_principal                                                              \n";
    $stSql .="  ON ativide_principal.inscricao_economica = ece.inscricao_economica                  \n";
    $stSql .="  AND ativide_principal.cod_atividade = ela.cod_atividade                             \n";

    $stSql .="  LEFT JOIN (                                                                         \n";
    $stSql .="      SELECT                                                                          \n";
    $stSql .="          dias_cadastro_economico.inscricao_economica,                                \n";
    $stSql .="          coalesce(hr_inicio, '00:00:00') as hr_inicio,                               \n";
    $stSql .="          coalesce (hr_termino, '00:00:00') as hr_termino                             \n";
    $stSql .="      FROM                                                                            \n";
    $stSql .="          economico.dias_cadastro_economico                                           \n";
    $stSql .="      INNER JOIN
                        (
                            SELECT
                                max(timestamp) AS timestamp,
                                cod_dia,
                                inscricao_economica
                            FROM
                                economico.dias_cadastro_economico
                            GROUP BY
                                cod_dia,
                                inscricao_economica
                        )AS tmp
                    ON
                        tmp.cod_dia = dias_cadastro_economico.cod_dia
                        AND tmp.inscricao_economica = dias_cadastro_economico.inscricao_economica
                        AND tmp.timestamp = dias_cadastro_economico.timestamp                       \n";

    $stSql .="      where dias_cadastro_economico.cod_dia = 1                                       \n";
    $stSql .="  ) as domingo                                                                        \n";
    $stSql .="  ON domingo.inscricao_economica = ece.inscricao_economica                            \n";
    $stSql .="  LEFT JOIN (                                                                         \n";
    $stSql .="      SELECT                                                                          \n";
    $stSql .="          dias_cadastro_economico.inscricao_economica,                                \n";
    $stSql .="          coalesce(hr_inicio, '00:00:00') as hr_inicio,                               \n";
    $stSql .="          coalesce (hr_termino, '00:00:00') as hr_termino                             \n";
    $stSql .="      FROM                                                                            \n";
    $stSql .="          economico.dias_cadastro_economico                                           \n";
    $stSql .="      INNER JOIN
                        (
                            SELECT
                                max(timestamp) AS timestamp,
                                cod_dia,
                                inscricao_economica
                            FROM
                                economico.dias_cadastro_economico
                            GROUP BY
                                cod_dia,
                                inscricao_economica
                        )AS tmp
                    ON
                        tmp.cod_dia = dias_cadastro_economico.cod_dia
                        AND tmp.inscricao_economica = dias_cadastro_economico.inscricao_economica
                        AND tmp.timestamp = dias_cadastro_economico.timestamp                       \n";
    $stSql .="      where dias_cadastro_economico.cod_dia = 2                                       \n";
    $stSql .="  ) as segunda                                                                        \n";
    $stSql .="  ON segunda.inscricao_economica = ece.inscricao_economica                            \n";
    $stSql .="  LEFT JOIN (                                                                         \n";
    $stSql .="      SELECT                                                                          \n";
    $stSql .="          dias_cadastro_economico.inscricao_economica,                                \n";
    $stSql .="          coalesce(hr_inicio, '00:00:00') as hr_inicio,                               \n";
    $stSql .="          coalesce (hr_termino, '00:00:00') as hr_termino                             \n";
    $stSql .="      FROM                                                                            \n";
    $stSql .="          economico.dias_cadastro_economico                                           \n";
    $stSql .="      INNER JOIN
                        (
                            SELECT
                                max(timestamp) AS timestamp,
                                cod_dia,
                                inscricao_economica
                            FROM
                                economico.dias_cadastro_economico
                            GROUP BY
                                cod_dia,
                                inscricao_economica
                        )AS tmp
                    ON
                        tmp.cod_dia = dias_cadastro_economico.cod_dia
                        AND tmp.inscricao_economica = dias_cadastro_economico.inscricao_economica
                        AND tmp.timestamp = dias_cadastro_economico.timestamp                       \n";

    $stSql .="      where dias_cadastro_economico.cod_dia = 3                                       \n";
    $stSql .="  ) as terca                                                                          \n";
    $stSql .="  ON terca.inscricao_economica = ece.inscricao_economica                              \n";
    $stSql .="  LEFT JOIN (                                                                         \n";
    $stSql .="      SELECT                                                                          \n";
    $stSql .="          dias_cadastro_economico.inscricao_economica,                                \n";
    $stSql .="          coalesce(hr_inicio, '00:00:00') as hr_inicio,                               \n";
    $stSql .="          coalesce (hr_termino, '00:00:00') as hr_termino                             \n";
    $stSql .="      FROM                                                                            \n";
    $stSql .="          economico.dias_cadastro_economico                                           \n";
    $stSql .="      INNER JOIN
                        (
                            SELECT
                                max(timestamp) AS timestamp,
                                cod_dia,
                                inscricao_economica
                            FROM
                                economico.dias_cadastro_economico
                            GROUP BY
                                cod_dia,
                                inscricao_economica
                        )AS tmp
                    ON
                        tmp.cod_dia = dias_cadastro_economico.cod_dia
                        AND tmp.inscricao_economica = dias_cadastro_economico.inscricao_economica
                        AND tmp.timestamp = dias_cadastro_economico.timestamp                       \n";
    $stSql .="      where dias_cadastro_economico.cod_dia = 4                                       \n";
    $stSql .="  ) as quarta                                                                         \n";
    $stSql .="  ON quarta.inscricao_economica = ece.inscricao_economica                             \n";
    $stSql .="  LEFT JOIN (                                                                         \n";
    $stSql .="      SELECT                                                                          \n";
    $stSql .="          dias_cadastro_economico.inscricao_economica,                                \n";
    $stSql .="          coalesce(hr_inicio, '00:00:00') as hr_inicio,                               \n";
    $stSql .="          coalesce (hr_termino, '00:00:00') as hr_termino                             \n";
    $stSql .="      FROM                                                                            \n";
    $stSql .="          economico.dias_cadastro_economico                                           \n";
    $stSql .="      INNER JOIN
                        (
                            SELECT
                                max(timestamp) AS timestamp,
                                cod_dia,
                                inscricao_economica
                            FROM
                                economico.dias_cadastro_economico
                            GROUP BY
                                cod_dia,
                                inscricao_economica
                        )AS tmp
                    ON
                        tmp.cod_dia = dias_cadastro_economico.cod_dia
                        AND tmp.inscricao_economica = dias_cadastro_economico.inscricao_economica
                        AND tmp.timestamp = dias_cadastro_economico.timestamp                       \n";

    $stSql .="      where dias_cadastro_economico.cod_dia = 5                                       \n";
    $stSql .="  ) as quinta                                                                         \n";
    $stSql .="  ON quinta.inscricao_economica = ece.inscricao_economica                             \n";
    $stSql .="  LEFT JOIN  (                                                                        \n";
    $stSql .="      SELECT                                                                          \n";
    $stSql .="          dias_cadastro_economico.inscricao_economica,                                \n";
    $stSql .="          coalesce(hr_inicio, '00:00:00') as hr_inicio,                               \n";
    $stSql .="          coalesce (hr_termino, '00:00:00') as hr_termino                             \n";
    $stSql .="      FROM                                                                            \n";
    $stSql .="          economico.dias_cadastro_economico                                           \n";
    $stSql .="      INNER JOIN
                        (
                            SELECT
                                max(timestamp) AS timestamp,
                                cod_dia,
                                inscricao_economica
                            FROM
                                economico.dias_cadastro_economico
                            GROUP BY
                                cod_dia,
                                inscricao_economica
                        )AS tmp
                    ON
                        tmp.cod_dia = dias_cadastro_economico.cod_dia
                        AND tmp.inscricao_economica = dias_cadastro_economico.inscricao_economica
                        AND tmp.timestamp = dias_cadastro_economico.timestamp                       \n";

    $stSql .="      where dias_cadastro_economico.cod_dia = 6                                       \n";
    $stSql .="  ) as sexta                                                                          \n";
    $stSql .="  ON sexta.inscricao_economica = ece.inscricao_economica                              \n";
    $stSql .="  LEFT JOIN (                                                                         \n";
    $stSql .="      SELECT                                                                          \n";
    $stSql .="          dias_cadastro_economico.inscricao_economica,                                \n";
    $stSql .="          coalesce(hr_inicio, '00:00:00') as hr_inicio,                               \n";
    $stSql .="          coalesce (hr_termino, '00:00:00') as hr_termino                             \n";
    $stSql .="      FROM                                                                            \n";
    $stSql .="          economico.dias_cadastro_economico                                           \n";
    $stSql .="      INNER JOIN
                        (
                            SELECT
                                max(timestamp) AS timestamp,
                                cod_dia,
                                inscricao_economica
                            FROM
                                economico.dias_cadastro_economico
                            GROUP BY
                                cod_dia,
                                inscricao_economica
                        )AS tmp
                    ON
                        tmp.cod_dia = dias_cadastro_economico.cod_dia
                        AND tmp.inscricao_economica = dias_cadastro_economico.inscricao_economica
                        AND tmp.timestamp = dias_cadastro_economico.timestamp                       \n";

    $stSql .="      where dias_cadastro_economico.cod_dia = 7                                       \n";
    $stSql .="  ) as sabado                                                                         \n";
    $stSql .="  ON sabado.inscricao_economica = ece.inscricao_economica                             \n";

    $stSql .="  , (                                                                                 \n";
    $stSql .="      SELECT                                                                          \n";
    $stSql .="          valor                                                                       \n";
    $stSql .="      FROM                                                                            \n";
    $stSql .="          administracao.configuracao                                                  \n";
    $stSql .="      WHERE                                                                           \n";
    $stSql .="          parametro = 'nom_prefeitura'                                                \n";
    $stSql .="          AND cod_modulo = 2                                                          \n";
    $stSql .="          AND exercicio = '". $inExercicioConf ."'                                      \n";
    $stSql .="  ) as prefeitura_nome                                                                \n";

    $stSql .="  , (                                                                                 \n";
    $stSql .="      SELECT                                                                          \n";
    $stSql .="          valor                                                                       \n";
    $stSql .="      FROM                                                                            \n";
    $stSql .="          administracao.configuracao                                                  \n";
    $stSql .="      WHERE                                                                           \n";
    $stSql .="          parametro = 'tipo_logradouro'                                               \n";
    $stSql .="          and cod_modulo = 2                                                          \n";
    $stSql .="          AND exercicio = '". $inExercicioConf ."'                                      \n";
    $stSql .="  ) as prefeitura_tl                                                                  \n";

    $stSql .="  , (                                                                                 \n";
    $stSql .="      SELECT                                                                          \n";
    $stSql .="          valor                                                                       \n";
    $stSql .="      FROM                                                                            \n";
    $stSql .="          administracao.configuracao                                                  \n";
    $stSql .="      where                                                                           \n";
    $stSql .="          parametro = 'logradouro'                                                    \n";
    $stSql .="          and cod_modulo = 2                                                          \n";
    $stSql .="          AND exercicio = '". $inExercicioConf ."'                                      \n";
    $stSql .="  ) as prefeitura_logr                                                                \n";

    $stSql .="  , (                                                                                 \n";
    $stSql .="      SELECT                                                                          \n";
    $stSql .="          valor                                                                       \n";
    $stSql .="      FROM                                                                            \n";
    $stSql .="          administracao.configuracao                                                  \n";
    $stSql .="      where                                                                           \n";
    $stSql .="          parametro = 'numero'                                                        \n";
    $stSql .="          and cod_modulo = 2                                                          \n";
    $stSql .="          AND exercicio = '". $inExercicioConf ."'                                      \n";
    $stSql .="  ) as prefeitura_logr_nr                                                             \n";

    $stSql .="  , (                                                                                 \n";
    $stSql .="      SELECT                                                                          \n";
    $stSql .="          valor                                                                       \n";
    $stSql .="      FROM                                                                            \n";
    $stSql .="          administracao.configuracao                                                  \n";
    $stSql .="      where                                                                           \n";
    $stSql .="          parametro = 'complemento'                                                   \n";
    $stSql .="          and cod_modulo = 2                                                          \n";
    $stSql .="          AND exercicio = '". $inExercicioConf ."'                                      \n";
    $stSql .="  ) as prefeitura_complem                                                             \n";

    $stSql .="  , (                                                                                 \n";
    $stSql .="      SELECT                                                                          \n";
    $stSql .="          valor                                                                       \n";
    $stSql .="      FROM                                                                            \n";
    $stSql .="          administracao.configuracao                                                  \n";
    $stSql .="      WHERE                                                                           \n";
    $stSql .="          parametro = 'bairro'                                                        \n";
    $stSql .="          and cod_modulo = 2                                                          \n";
    $stSql .="          AND exercicio = '". $inExercicioConf ."'                                      \n";
    $stSql .="  ) as prefeitura_bairro                                                              \n";

    $stSql .="  , (                                                                                 \n";
    $stSql .="      SELECT valor                                                                    \n";
    $stSql .="      FROM administracao.configuracao                                                 \n";
    $stSql .="      where parametro = 'cep'                                                         \n";
    $stSql .="      and cod_modulo = 2                                                              \n";
    $stSql .="      AND exercicio = '". $inExercicioConf ."'                                          \n";
    $stSql .="  ) as prefeitura_cep                                                                 \n";

    $stSql .="  , (                                                                                 \n";
    $stSql .="      SELECT                                                                          \n";
    $stSql .="          valor                                                                       \n";
    $stSql .="      FROM                                                                            \n";
    $stSql .="          administracao.configuracao                                                  \n";
    $stSql .="      WHERE                                                                           \n";
    $stSql .="          parametro = 'cnpj'                                                          \n";
    $stSql .="          and cod_modulo = 2                                                          \n";
    $stSql .="          AND exercicio = '". $inExercicioConf ."'                                      \n";
    $stSql .="  ) as prefeitura_cnpj                                                                \n";

    $stSql .="  , (                                                                                 \n";
    $stSql .="      SELECT                                                                          \n";
    $stSql .="          nom_uf,                                                                     \n";
    $stSql .="          sigla_uf                                                                    \n";
    $stSql .="      FROM                                                                            \n";
    $stSql .="          sw_uf                                                                       \n";
    $stSql .="          INNER JOIN (                                                                \n";
    $stSql .="              select                                                                  \n";
    $stSql .="                  valor                                                               \n";
    $stSql .="              from                                                                    \n";
    $stSql .="                  administracao.configuracao                                          \n";
    $stSql .="              where  parametro = 'cod_uf'                                             \n";
    $stSql .="                  and cod_modulo = 2                                                  \n";
    $stSql .="                  AND exercicio = '". $inExercicioConf ."'                              \n";
    $stSql .="          ) as uf_config                                                              \n";
    $stSql .="          ON uf_config.valor = sw_uf.cod_uf::varchar                                           \n";
    $stSql .="  ) as prefeitura_uf                                                                  \n";

    $stSql .="  , (                                                                                 \n";
    $stSql .="      SELECT                                                                          \n";
    $stSql .="          nom_municipio                                                               \n";
    $stSql .="      FROM                                                                            \n";
    $stSql .="          sw_municipio                                                                \n";
    $stSql .="          INNER JOIN (                                                                \n";
    $stSql .="              select                                                                  \n";
    $stSql .="                  valor,                                                              \n";
    $stSql .="                  exercicio                                                           \n";
    $stSql .="              from                                                                    \n";
    $stSql .="                  administracao.configuracao                                          \n";
    $stSql .="              where                                                                   \n";
    $stSql .="                  parametro = 'cod_municipio'                                         \n";
    $stSql .="                  and cod_modulo = 2                                                  \n";
    $stSql .="                  AND exercicio = '". $inExercicioConf ."'                              \n";
    $stSql .="          ) as mun_conf                                                               \n";
    $stSql .="          ON mun_conf.valor = sw_municipio.cod_municipio::varchar                              \n";
    $stSql .="          INNER JOIN (                                                                \n";
    $stSql .="              select                                                                  \n";
    $stSql .="                  valor,                                                              \n";
    $stSql .="                  exercicio                                                           \n";
    $stSql .="              from                                                                    \n";
    $stSql .="                  administracao.configuracao                                          \n";
    $stSql .="              where                                                                   \n";
    $stSql .="                  parametro = 'cod_uf'                                                \n";
    $stSql .="                  and cod_modulo = 2                                                  \n";
    $stSql .="                  AND exercicio = '". $inExercicioConf ."'                              \n";
    $stSql .="          ) as uf_config                                                              \n";
    $stSql .="          ON uf_config.valor = sw_municipio.cod_uf::varchar                                    \n";
    $stSql .="          AND uf_config.exercicio = mun_conf.exercicio                                \n";
    $stSql .="  ) as prefeitura_municipio                                                           \n";

    $stSql .="  , (                                                                                 \n";
    $stSql .="      SELECT                                                                          \n";
    $stSql .="          admc.valor,                                                                 \n";
    $stSql .="          nom_cgm                                                                     \n";
    $stSql .="      FROM                                                                            \n";
    $stSql .="          administracao.configuracao as admc                                          \n";
    $stSql .="          INNER JOIN sw_cgm as cgm                                                    \n";
    $stSql .="          ON cgm.numcgm::varchar = admc.valor                                                  \n";
    $stSql .="      WHERE                                                                           \n";
    $stSql .="          cod_modulo = 14                                                             \n";
    $stSql .="          AND parametro = 'diretor_tributos'                                          \n";
    $stSql .="          AND exercicio = '". $inExercicioConf ."'                                      \n";
    $stSql .="  ) as diretor_tributos                                                               \n";

    $stSql .="  , (  SELECT numcgm, nom_cgm from sw_cgm ) as usuario                                \n";

    return $stSql;

}

public function buscaDadosConcederLicencaEspecial(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    return $this->executaRecupera   (   "montaBuscaDadosConcederLicencaEspecial",
                                        $rsRecordSet, $stFiltro, $stOrder, $boTransacao
                                    );

}

public function montaBuscaDadosConcederLicencaEspecial()
{
    $inExercicioConf = Sessao::getExercicio();

    $stSql = "  SELECT DISTINCT                                                                         \n";
    $stSql .="      LPAD (ela.cod_licenca::varchar, 8, '0') as cod_licenca                                       \n";
    $stSql .="      , licenca_documento.num_alvara                                                      \n";
    $stSql .="      , ela.exercicio                                                                     \n";
    $stSql .="      , TO_CHAR ( ela.dt_inicio,'dd/mm/yyyy' ) as inicio_licenca                          \n";
    $stSql .="      , TO_CHAR ( ela.dt_termino,'dd/mm/yyyy' ) as termino_licenca                        \n";
    $stSql .="      , ece.inscricao_economica as IE                                                     \n";
    $stSql .="      , edf.inscricao_municipal as IM                                                     \n";
    $stSql .="      , coalesce ( eceA.numcgm, eceF.numcgm, eceD.numcgm ) as numcgm                      \n";
    $stSql .="      , cgm.nom_cgm as razao_social                                                       \n";
    $stSql .="      , cgmPF.rg                                                                          \n";
    $stSql .="      , cgmPF.cpf                                                                         \n";
    $stSql .="      , cgmPJ.cnpj as cnpj_cpf                                                            \n";
    $stSql .="      , NULL as num_emissao                                                               \n";
    $stSql .="      , eceD.nom_natureza                                                                 \n";
    $stSql .="      , elo.observacao                                                                    \n";
    $stSql .="      , LPAD (ela.cod_licenca::varchar, 4, '0')||ela.exercicio as codigo_barra                     \n";
    $stSql .="      , diretor_tributos.nom_cgm as diretor_tributos                                      \n";
    $stSql .="      , NULL as num_emissao                                                               \n";
    $stSql .="      , usuario.nom_cgm as usuario                                                        \n";
    $stSql .="      , TO_CHAR ( now()::date,'dd/mm/yyyy' ) as data_emissao                              \n";
    $stSql .="      , ( CASE WHEN edi.endereco is null OR ( edi.timestamp > edf.timestamp ) THEN        \n";
    $stSql .="              split_part ( edf.endereco, '§', 1 ) ||' '||                                 \n";
    $stSql .="              split_part ( edf.endereco, '§', 3 ) ||' - '||                               \n";
    $stSql .="              split_part ( edf.endereco, '§', 4 ) ||' '||                                 \n";
    $stSql .="              split_part ( edf.endereco, '§', 5 ) ||' Bairro: '||                         \n";
    $stSql .="              split_part ( edf.endereco, '§', 6 ) ||' CEP: '||                            \n";
    $stSql .="              split_part ( edf.endereco, '§', 7 )                                         \n";
    $stSql .="          ELSE                                                                            \n";
    $stSql .="              split_part ( edi.endereco, '§', 1 ) ||' '||                                 \n";
    $stSql .="              split_part ( edi.endereco, '§', 3 ) ||' - '||                               \n";
    $stSql .="              split_part ( edi.endereco, '§', 4 ) ||' '||                                 \n";
    $stSql .="              split_part ( edi.endereco, '§', 5 ) ||' Bairro: '||                         \n";
    $stSql .="              split_part ( edi.endereco, '§', 6 ) ||' CEP: '||                            \n";
    $stSql .="              split_part ( edi.endereco, '§', 7 )                                         \n";
    $stSql .="          END                                                                             \n";
    $stSql .="      ) as rua                                                                            \n";
    $stSql .="      , ( CASE WHEN edi.endereco is null OR ( edi.timestamp > edf.timestamp ) THEN        \n";
    $stSql .="              split_part ( edf.endereco,'§',9)||' / '||split_part ( edf.endereco,'§',11 ) \n";
    $stSql .="          ELSE                                                                            \n";
    $stSql .="              split_part ( edi.endereco,'§',9) ||' / '|| split_part( edi.endereco,'§',11) \n";
    $stSql .="          END                                                                             \n";
    $stSql .="      ) as cidade                                                                         \n";
    $stSql .="      , ( CASE WHEN edi.endereco is null OR ( edi.timestamp > edf.timestamp ) THEN        \n";
    $stSql .="              split_part ( edi.endereco,'§',7)                                            \n";
    $stSql .="          ELSE                                                                            \n";
    $stSql .="              split_part ( edi.endereco,'§',7)                                            \n";
    $stSql .="          END                                                                             \n";
    $stSql .="      ) as cep                                                                            \n";

    $stSql .="      , ativide_principal.cod_atividade                                                   \n";
    $stSql .="      , ativide_principal.nom_atividade                                                   \n";
    $stSql .="      , ativide_principal.dt_inicio as inicio_atividade                                   \n";
    $stSql .="      , upper (prefeitura_nome.valor) as prefeitura_nome                                  \n";
    $stSql .="      , prefeitura_cnpj.valor as prefeitura_cnpj                                          \n";
    $stSql .="      , ( prefeitura_tl.valor||' '||prefeitura_logr.valor||', '||                         \n";
    $stSql .="          prefeitura_logr_nr.valor||' '||prefeitura_complem.valor||' - '||                \n";
    $stSql .="          prefeitura_bairro.valor||' - CEP: '||                                           \n";
    $stSql .="          substring (prefeitura_cep.valor from 1 for 5)||'-'||                            \n";
    $stSql .="          substring( prefeitura_cep.valor from 6 for 9)                                   \n";
    $stSql .="      ) as prefeitura_endereco                                                            \n";
    $stSql .="      , prefeitura_municipio.nom_municipio as prefeitura_municipio                        \n";
    $stSql .="      , prefeitura_uf.nom_uf as prefeitura_uf                                             \n";
    $stSql .="      , prefeitura_uf.sigla_uf as prefeitura_uf_sigla                                     \n";

    $stSql .="      , ela.ocorrencia_licenca                                                            \n";

    $stSql .="      , coalesce (domingo.hr_inicio,  '00:00:00') as domingo_inicio                       \n";
    $stSql .="      , coalesce (domingo.hr_termino, '00:00:00') as domingo_termino                      \n";
    $stSql .="      , coalesce (segunda.hr_inicio,  '00:00:00') as segunda_inicio                       \n";
    $stSql .="      , coalesce (segunda.hr_termino, '00:00:00') as segunda_termino                      \n";
    $stSql .="      , coalesce (terca.hr_inicio,    '00:00:00') as terca_inicio                         \n";
    $stSql .="      , coalesce (terca.hr_termino,   '00:00:00') as terca_termino                        \n";
    $stSql .="      , coalesce (quarta.hr_inicio,   '00:00:00') as quarta_inicio                        \n";
    $stSql .="      , coalesce (quarta.hr_termino,  '00:00:00') as quarta_termino                       \n";
    $stSql .="      , coalesce (quinta.hr_inicio,   '00:00:00') as quinta_inicio                        \n";
    $stSql .="      , coalesce (quinta.hr_termino,  '00:00:00') as quinta_termino                       \n";
    $stSql .="      , coalesce (sexta.hr_inicio,    '00:00:00') as sexta_inicio                         \n";
    $stSql .="      , coalesce (sexta.hr_termino,   '00:00:00') as sexta_termino                        \n";
    $stSql .="      , coalesce (sabado.hr_inicio,   '00:00:00') as sabado_inicio                        \n";
    $stSql .="      , coalesce (sabado.hr_termino,  '00:00:00') as sabado_termino                       \n";

    $stSql .="  FROM                                                                                    \n";
    $stSql .="      (                                                                                   \n";
    $stSql .="      SELECT                                                                              \n";
    $stSql .="          ela.*                                                                           \n";
    $stSql .="      FROM                                                                                \n";
    $stSql .="          economico.licenca_especial as ela                                               \n";
    $stSql .="          INNER JOIN  (                                                                   \n";
    $stSql .="              select                                                                      \n";
    $stSql .="                  inscricao_economica                                                     \n";
    $stSql .="                  , max(ocorrencia_licenca) as ocorrencia                                 \n";
    $stSql .="              from                                                                        \n";
    $stSql .="                  economico.licenca_especial                                              \n";
    $stSql .="              group by inscricao_economica                                                \n";
    $stSql .="          ) as ela2                                                                       \n";
    $stSql .="          ON ela2.inscricao_economica = ela.inscricao_economica                           \n";
    $stSql .="          AND ela2.ocorrencia = ela.ocorrencia_licenca                                    \n";
    $stSql .="      GROUP BY                                                                            \n";
    $stSql .="          ela.inscricao_economica,                                                        \n";
    $stSql .="          ela.cod_licenca,                                                                \n";
    $stSql .="          ela.exercicio,                                                                  \n";
    $stSql .="          ela.ocorrencia_atividade,                                                       \n";
    $stSql .="          ela.cod_atividade,                                                              \n";
    $stSql .="          ela.ocorrencia_licenca,                                                         \n";
    $stSql .="          ela.dt_inicio,                                                                  \n";
    $stSql .="          ela.dt_termino                                                                  \n";
    $stSql .="      ORDER BY                                                                            \n";
    $stSql .="          ela.cod_licenca                                                                 \n";
    $stSql .="      ) as ela                                                                            \n";

    $stSql .="      INNER JOIN
                        economico.licenca_documento
                    ON
                        licenca_documento.cod_licenca = ela.cod_licenca
                        AND licenca_documento.exercicio = ela.exercicio                                 \n";

    $stSql .="      INNER JOIN economico.licenca as el                                                  \n";
    $stSql .="      ON el.cod_licenca = ela.cod_licenca                                                 \n";
    $stSql .="      AND el.exercicio = ela.exercicio                                                    \n";

    $stSql .="      LEFT JOIN economico.licenca_observacao as elo                                       \n";
    $stSql .="      ON elo.cod_licenca = el.cod_licenca                                                 \n";
    $stSql .="      AND elo.exercicio = el.exercicio                                                    \n";

    $stSql .="      INNER JOIN economico.cadastro_economico as ece                                      \n";
    $stSql .="      ON ece.inscricao_economica = ela.inscricao_economica                                \n";

    $stSql .="      LEFT JOIN economico.cadastro_economico_autonomo as eceA                             \n";
    $stSql .="      ON eceA.inscricao_economica = ece.inscricao_economica                               \n";

    $stSql .="      LEFT JOIN economico.cadastro_economico_empresa_fato as eceF                         \n";
    $stSql .="      ON eceF.inscricao_economica = ece.inscricao_economica                               \n";

    $stSql .="      LEFT JOIN (                                                                         \n";
    $stSql .="          select                                                                          \n";
    $stSql .="              eceD.inscricao_economica,                                                   \n";
    $stSql .="              eceD.numcgm,                                                                \n";
    $stSql .="              enj.nom_natureza                                                            \n";
    $stSql .="          from                                                                            \n";
    $stSql .="              economico.cadastro_economico_empresa_direito as eceD                        \n";
    $stSql .="              LEFT JOIN (                                                                 \n";
    $stSql .="                  select                                                                  \n";
    $stSql .="                      inscricao_economica,                                                \n";
    $stSql .="                      cod_natureza,                                                       \n";
    $stSql .="                      max(timestamp)                                                      \n";
    $stSql .="                  from                                                                    \n";
    $stSql .="                      economico.empresa_direito_natureza_juridica as eceDNJ               \n";
    $stSql .="                  group by                                                                \n";
    $stSql .="                      inscricao_economica,                                                \n";
    $stSql .="                      cod_natureza                                                        \n";
    $stSql .="              ) as eceDNJ                                                                 \n";
    $stSql .="              ON eceDNJ.inscricao_economica = eced.inscricao_economica                    \n";

    $stSql .="              LEFT JOIN economico.natureza_juridica as enj                                \n";
    $stSql .="              ON enj.cod_natureza = eceDNJ.cod_natureza                                   \n";
    $stSql .="      ) as eceD                                                                           \n";
    $stSql .="      ON eceD.inscricao_economica = ece.inscricao_economica                               \n";

    $stSql .="      LEFT JOIN economico.empresa_direito_natureza_juridica as eceDNJ                     \n";
    $stSql .="      ON eceDNJ.inscricao_economica = ece.inscricao_economica                             \n";

    $stSql .="      INNER JOIN sw_cgm as cgm                                                            \n";
    $stSql .="      ON cgm.numcgm = coalesce ( eceA.numcgm, eceF.numcgm, eceD.numcgm )                  \n";

    $stSql .="      LEFT JOIN (                                                                         \n";
    $stSql .="          select                                                                          \n";
    $stSql .="              edf1.inscricao_economica, edf1.inscricao_municipal                          \n";
    $stSql .="              , edf1.endereco, edf1.timestamp                                             \n";
    $stSql .="          from                                                                            \n";
    $stSql .="              (                                                                           \n";
    $stSql .="              select                                                                      \n";
    $stSql .="                  inscricao_economica                                                     \n";
    $stSql .="                  , inscricao_municipal                                                   \n";
    $stSql .="                  , timestamp                                                             \n";
    $stSql .="                  , economico.fn_busca_domicilio_fiscal (inscricao_municipal) as endereco \n";
    $stSql .="              from economico.domicilio_fiscal                                             \n";
    $stSql .="              ) as edf1                                                                   \n";
    $stSql .="              INNER JOIN  (                                                               \n";
    $stSql .="                  select                                                                  \n";
    $stSql .="                      inscricao_economica, max (timestamp) as timestamp                   \n";
    $stSql .="                  from economico.domicilio_fiscal                                         \n";
    $stSql .="                  group by inscricao_economica                                            \n";
    $stSql .="              ) as edf2                                                                   \n";
    $stSql .="              ON edf2.inscricao_economica = edf1.inscricao_economica                      \n";
    $stSql .="              AND edf2.timestamp = edf1.timestamp                                         \n";
    $stSql .="      ) as edf                                                                            \n";
    $stSql .="      ON edf.inscricao_economica = ece.inscricao_economica                                \n";
    $stSql .="      LEFT JOIN (                                                                         \n";
    $stSql .="          select                                                                          \n";
    $stSql .="              edi1.*                                                                      \n";
    $stSql .="          from                                                                            \n";
    $stSql .="          (                                                                               \n";
    $stSql .="              select                                                                      \n";
    $stSql .="                  inscricao_economica, timestamp                                          \n";
    $stSql .="                  ,economico.fn_busca_domicilio_informado(inscricao_economica) as endereco\n";
    $stSql .="              from                                                                        \n";
    $stSql .="                  economico.domicilio_informado as edi                                    \n";
    $stSql .="          ) as edi1                                                                       \n";
    $stSql .="          INNER JOIN (                                                                    \n";
    $stSql .="              select                                                                      \n";
    $stSql .="                  inscricao_economica, max (timestamp) as timestamp                       \n";
    $stSql .="              from economico.domicilio_informado                                          \n";
    $stSql .="              group by inscricao_economica                                                \n";
    $stSql .="          ) as edi2                                                                       \n";
    $stSql .="          ON  edi2.inscricao_economica = edi1.inscricao_economica                         \n";
    $stSql .="          AND edi2.timestamp = edi1.timestamp                                             \n";
    $stSql .="      ) as edi                                                                            \n";
    $stSql .="      ON edi.inscricao_economica = ece.inscricao_economica                                \n";
    $stSql .="      LEFT JOIN sw_cgm_pessoa_fisica as cgmPF                                             \n";
    $stSql .="      ON cgmPF.numcgm = cgm.numcgm                                                        \n";

    $stSql .="      LEFT JOIN sw_cgm_pessoa_juridica as cgmPJ                                           \n";
    $stSql .="      ON cgmPJ.numcgm = cgm.numcgm                                                        \n";

    $stSql .="      INNER JOIN (                                                                        \n";
    $stSql .="          SELECT                                                                          \n";
    $stSql .="              atv.cod_atividade,                                                          \n";
    $stSql .="              ATE.inscricao_economica,                                                    \n";
    $stSql .="              atv.nom_atividade,                                                          \n";
    $stSql .="              atv.cod_estrutural,                                                         \n";
    $stSql .="              ATE.PRINCIPAL,                                                              \n";
    $stSql .="              coalesce ( TO_CHAR ( ATE.DT_INICIO,'dd/mm/yyyy' ) , '-') AS dt_inicio,      \n";
    $stSql .="              coalesce ( TO_CHAR ( ATE.DT_TERMINO,'dd/mm/yyyy' ), '-') AS dt_termino,     \n";
    $stSql .="              ATE.OCORRENCIA_ATIVIDADE                                                    \n";
    $stSql .="          FROM                                                                            \n";
    $stSql .="              economico.atividade  AS ATV                                                 \n";
    $stSql .="              INNER JOIN economico.atividade_cadastro_economico AS ATE                    \n";
    $stSql .="              ON ATV.COD_ATIVIDADE = ATE.COD_ATIVIDADE                                    \n";
//    $stSql .="          WHERE                                                                           \n";
//    $stSql .="              ATE.PRINCIPAL = true                                                        \n";
    $stSql .="      ) as ativide_principal                                                              \n";
    $stSql .="      ON ativide_principal.inscricao_economica = ece.inscricao_economica                  \n";
    $stSql .="      AND ativide_principal.cod_atividade = ela.cod_atividade                             \n";

    $stSql .="      LEFT JOIN (                                                                         \n";
    $stSql .="          SELECT                                                                          \n";
    $stSql .="              dias_cadastro_economico.inscricao_economica,                                \n";
    $stSql .="              coalesce(hr_inicio, '00:00:00') as hr_inicio,                               \n";
    $stSql .="              coalesce (hr_termino, '00:00:00') as hr_termino                             \n";
    $stSql .="          FROM                                                                            \n";
    $stSql .="              economico.dias_cadastro_economico                                           \n";
    $stSql .="          INNER JOIN
                            (
                                SELECT
                                    max(timestamp) AS timestamp,
                                    cod_dia,
                                    inscricao_economica
                                FROM
                                    economico.dias_cadastro_economico
                                GROUP BY
                                    cod_dia,
                                    inscricao_economica
                            )AS tmp
                        ON
                            tmp.cod_dia = dias_cadastro_economico.cod_dia
                            AND tmp.inscricao_economica = dias_cadastro_economico.inscricao_economica
                            AND tmp.timestamp = dias_cadastro_economico.timestamp                       \n";

    $stSql .="          where dias_cadastro_economico.cod_dia = 1                                       \n";

    $stSql .="      ) as domingo                                                                        \n";
    $stSql .="      ON domingo.inscricao_economica = ece.inscricao_economica                            \n";

    $stSql .="      LEFT JOIN (                                                                         \n";
    $stSql .="         SELECT                                                                           \n";
    $stSql .="              dias_cadastro_economico.inscricao_economica,                                \n";
    $stSql .="             coalesce(hr_inicio, '00:00:00') as hr_inicio,                                \n";
    $stSql .="             coalesce (hr_termino, '00:00:00') as hr_termino                              \n";
    $stSql .="         FROM                                                                             \n";
    $stSql .="             economico.dias_cadastro_economico                                            \n";
    $stSql .="         INNER JOIN
                            (
                                SELECT
                                    max(timestamp) AS timestamp,
                                    cod_dia,
                                    inscricao_economica
                                FROM
                                    economico.dias_cadastro_economico
                                GROUP BY
                                    cod_dia,
                                    inscricao_economica
                            )AS tmp
                       ON
                            tmp.cod_dia = dias_cadastro_economico.cod_dia
                            AND tmp.inscricao_economica = dias_cadastro_economico.inscricao_economica
                            AND tmp.timestamp = dias_cadastro_economico.timestamp                       \n";

    $stSql .="         where dias_cadastro_economico.cod_dia = 2                                        \n";
    $stSql .="      ) as segunda                                                                        \n";
    $stSql .="      ON segunda.inscricao_economica = ece.inscricao_economica                            \n";

    $stSql .="      LEFT JOIN (                                                                         \n";
    $stSql .="          SELECT                                                                          \n";
    $stSql .="              dias_cadastro_economico.inscricao_economica,                                \n";
    $stSql .="              coalesce(hr_inicio, '00:00:00') as hr_inicio,                               \n";
    $stSql .="              coalesce (hr_termino, '00:00:00') as hr_termino                             \n";
    $stSql .="          FROM                                                                            \n";
    $stSql .="              economico.dias_cadastro_economico                                           \n";
    $stSql .="         INNER JOIN
                            (
                                SELECT
                                    max(timestamp) AS timestamp,
                                    cod_dia,
                                    inscricao_economica
                                FROM
                                    economico.dias_cadastro_economico
                                GROUP BY
                                    cod_dia,
                                    inscricao_economica
                            )AS tmp
                       ON
                            tmp.cod_dia = dias_cadastro_economico.cod_dia
                            AND tmp.inscricao_economica = dias_cadastro_economico.inscricao_economica
                            AND tmp.timestamp = dias_cadastro_economico.timestamp                       \n";

    $stSql .="         where dias_cadastro_economico.cod_dia = 3                                        \n";
    $stSql .="      ) as terca                                                                          \n";
    $stSql .="      ON terca.inscricao_economica = ece.inscricao_economica                              \n";

    $stSql .="      LEFT JOIN (                                                                         \n";
    $stSql .="          SELECT                                                                          \n";
    $stSql .="              dias_cadastro_economico.inscricao_economica,                                \n";
    $stSql .="              coalesce(hr_inicio, '00:00:00') as hr_inicio,                               \n";
    $stSql .="              coalesce (hr_termino, '00:00:00') as hr_termino                             \n";
    $stSql .="          FROM                                                                            \n";
    $stSql .="              economico.dias_cadastro_economico                                           \n";
    $stSql .="         INNER JOIN
                            (
                                SELECT
                                    max(timestamp) AS timestamp,
                                    cod_dia,
                                    inscricao_economica
                                FROM
                                    economico.dias_cadastro_economico
                                GROUP BY
                                    cod_dia,
                                    inscricao_economica
                            )AS tmp
                       ON
                            tmp.cod_dia = dias_cadastro_economico.cod_dia
                            AND tmp.inscricao_economica = dias_cadastro_economico.inscricao_economica
                            AND tmp.timestamp = dias_cadastro_economico.timestamp                       \n";

    $stSql .="         where dias_cadastro_economico.cod_dia = 4                                        \n";
    $stSql .="      ) as quarta                                                                         \n";
    $stSql .="      ON quarta.inscricao_economica = ece.inscricao_economica                             \n";

    $stSql .="      LEFT JOIN (                                                                         \n";
    $stSql .="          SELECT                                                                          \n";
    $stSql .="              dias_cadastro_economico.inscricao_economica,                                \n";
    $stSql .="              coalesce(hr_inicio, '00:00:00') as hr_inicio,                               \n";
    $stSql .="              coalesce (hr_termino, '00:00:00') as hr_termino                             \n";
    $stSql .="          FROM                                                                            \n";
    $stSql .="              economico.dias_cadastro_economico                                           \n";
    $stSql .="         INNER JOIN
                            (
                                SELECT
                                    max(timestamp) AS timestamp,
                                    cod_dia,
                                    inscricao_economica
                                FROM
                                    economico.dias_cadastro_economico
                                GROUP BY
                                    cod_dia,
                                    inscricao_economica
                            )AS tmp
                       ON
                            tmp.cod_dia = dias_cadastro_economico.cod_dia
                            AND tmp.inscricao_economica = dias_cadastro_economico.inscricao_economica
                            AND tmp.timestamp = dias_cadastro_economico.timestamp                       \n";

    $stSql .="         where dias_cadastro_economico.cod_dia = 5                                        \n";
    $stSql .="      ) as quinta                                                                         \n";
    $stSql .="      ON quinta.inscricao_economica = ece.inscricao_economica                             \n";

    $stSql .="      LEFT JOIN  (                                                                        \n";
    $stSql .="          SELECT                                                                          \n";
    $stSql .="              dias_cadastro_economico.inscricao_economica,                                \n";
    $stSql .="              coalesce(hr_inicio, '00:00:00') as hr_inicio,                               \n";
    $stSql .="              coalesce (hr_termino, '00:00:00') as hr_termino                             \n";
    $stSql .="          FROM                                                                            \n";
    $stSql .="              economico.dias_cadastro_economico                                           \n";
    $stSql .="         INNER JOIN
                            (
                                SELECT
                                    max(timestamp) AS timestamp,
                                    cod_dia,
                                    inscricao_economica
                                FROM
                                    economico.dias_cadastro_economico
                                GROUP BY
                                    cod_dia,
                                    inscricao_economica
                            )AS tmp
                       ON
                            tmp.cod_dia = dias_cadastro_economico.cod_dia
                            AND tmp.inscricao_economica = dias_cadastro_economico.inscricao_economica
                            AND tmp.timestamp = dias_cadastro_economico.timestamp                       \n";

    $stSql .="         where dias_cadastro_economico.cod_dia = 6                                        \n";
    $stSql .="      ) as sexta                                                                          \n";
    $stSql .="      ON sexta.inscricao_economica = ece.inscricao_economica                              \n";

    $stSql .="      LEFT JOIN (                                                                         \n";
    $stSql .="          SELECT                                                                          \n";
    $stSql .="              dias_cadastro_economico.inscricao_economica,                                \n";
    $stSql .="              coalesce(hr_inicio, '00:00:00') as hr_inicio,                               \n";
    $stSql .="              coalesce (hr_termino, '00:00:00') as hr_termino                             \n";
    $stSql .="          FROM                                                                            \n";
    $stSql .="              economico.dias_cadastro_economico                                           \n";
    $stSql .="         INNER JOIN
                            (
                                SELECT
                                    max(timestamp) AS timestamp,
                                    cod_dia,
                                    inscricao_economica
                                FROM
                                    economico.dias_cadastro_economico
                                GROUP BY
                                    cod_dia,
                                    inscricao_economica
                            )AS tmp
                       ON
                            tmp.cod_dia = dias_cadastro_economico.cod_dia
                            AND tmp.inscricao_economica = dias_cadastro_economico.inscricao_economica
                            AND tmp.timestamp = dias_cadastro_economico.timestamp                       \n";

    $stSql .="         where dias_cadastro_economico.cod_dia = 7                                        \n";
    $stSql .="      ) as sabado                                                                         \n";
    $stSql .="      ON sabado.inscricao_economica = ece.inscricao_economica                             \n";

    $stSql .="      INNER JOIN (                                                                        \n";
    $stSql .="          SELECT                                                                          \n";
    $stSql .="              valor,                                                                      \n";
    $stSql .="              exercicio                                                                   \n";
    $stSql .="          FROM                                                                            \n";
    $stSql .="              administracao.configuracao                                                  \n";
    $stSql .="          WHERE parametro = 'nom_prefeitura'                                              \n";
    $stSql .="          AND cod_modulo = 2                                                              \n";
    $stSql .="      ) as prefeitura_nome                                                                \n";
    $stSql .="      ON prefeitura_nome.exercicio = '".$inExercicioConf."'                               \n";

    $stSql .="      INNER JOIN (                                                                        \n";
    $stSql .="          SELECT                                                                          \n";
    $stSql .="              valor,                                                                      \n";
    $stSql .="              exercicio                                                                   \n";
    $stSql .="          FROM                                                                            \n";
    $stSql .="              administracao.configuracao                                                  \n";
    $stSql .="          WHERE                                                                           \n";
    $stSql .="              parametro = 'tipo_logradouro'                                               \n";
    $stSql .="              and cod_modulo = 2                                                          \n";
    $stSql .="      ) as prefeitura_tl                                                                  \n";
    $stSql .="      ON prefeitura_tl.exercicio = '".$inExercicioConf."'                                 \n";

    $stSql .="      INNER JOIN (                                                                        \n";
    $stSql .="          SELECT                                                                          \n";
    $stSql .="              valor,                                                                      \n";
    $stSql .="              exercicio                                                                   \n";
    $stSql .="          FROM                                                                            \n";
    $stSql .="              administracao.configuracao                                                  \n";
    $stSql .="          where                                                                           \n";
    $stSql .="              parametro = 'logradouro'                                                    \n";
    $stSql .="              and cod_modulo = 2                                                          \n";
    $stSql .="      ) as prefeitura_logr                                                                \n";
    $stSql .="      ON prefeitura_logr.exercicio = '".$inExercicioConf."'                               \n";

    $stSql .="      INNER JOIN (                                                                        \n";
    $stSql .="          SELECT                                                                          \n";
    $stSql .="              valor,                                                                      \n";
    $stSql .="              exercicio                                                                   \n";
    $stSql .="          FROM                                                                            \n";
    $stSql .="              administracao.configuracao                                                  \n";
    $stSql .="          where                                                                           \n";
    $stSql .="              parametro = 'numero'                                                        \n";
    $stSql .="              and cod_modulo = 2                                                          \n";
    $stSql .="      ) as prefeitura_logr_nr                                                             \n";
    $stSql .="      ON prefeitura_logr_nr.exercicio = '".$inExercicioConf."'                            \n";

    $stSql .="      INNER JOIN (                                                                        \n";
    $stSql .="          SELECT                                                                          \n";
    $stSql .="              valor,                                                                      \n";
    $stSql .="              exercicio                                                                   \n";
    $stSql .="          FROM                                                                            \n";
    $stSql .="              administracao.configuracao                                                  \n";
    $stSql .="          where                                                                           \n";
    $stSql .="              parametro = 'complemento'                                                   \n";
    $stSql .="              and cod_modulo = 2                                                          \n";
    $stSql .="      ) as prefeitura_complem                                                             \n";
    $stSql .="      ON prefeitura_complem.exercicio = '".$inExercicioConf."'                            \n";

    $stSql .="      INNER JOIN (                                                                        \n";
    $stSql .="          SELECT                                                                          \n";
    $stSql .="              valor,                                                                      \n";
    $stSql .="              exercicio                                                                   \n";
    $stSql .="          FROM                                                                            \n";
    $stSql .="              administracao.configuracao                                                  \n";
    $stSql .="          WHERE                                                                           \n";
    $stSql .="              parametro = 'bairro'                                                        \n";
    $stSql .="              and cod_modulo = 2                                                          \n";
    $stSql .="      ) as prefeitura_bairro                                                              \n";
    $stSql .="      ON prefeitura_bairro.exercicio = '".$inExercicioConf."'                             \n";

    $stSql .="      INNER JOIN (                                                                        \n";
    $stSql .="          SELECT valor, exercicio                                                         \n";
    $stSql .="          FROM administracao.configuracao                                                 \n";
    $stSql .="          where                                                                           \n";
    $stSql .="              parametro = 'cep'                                                           \n";
    $stSql .="              and cod_modulo = 2                                                          \n";
    $stSql .="      ) as prefeitura_cep                                                                 \n";
    $stSql .="      ON prefeitura_cep.exercicio = '".$inExercicioConf."'                                \n";

    $stSql .="      INNER JOIN (                                                                        \n";
    $stSql .="          SELECT                                                                          \n";
    $stSql .="              valor,                                                                      \n";
    $stSql .="              exercicio                                                                   \n";
    $stSql .="          FROM                                                                            \n";
    $stSql .="              administracao.configuracao                                                  \n";
    $stSql .="          WHERE                                                                           \n";
    $stSql .="              parametro = 'cnpj'                                                          \n";
    $stSql .="              and cod_modulo = 2                                                          \n";
    $stSql .="      ) as prefeitura_cnpj                                                                \n";
    $stSql .="      ON prefeitura_cnpj.exercicio = '".$inExercicioConf."'                               \n";

    $stSql .="      INNER JOIN (                                                                        \n";
    $stSql .="          SELECT                                                                          \n";
    $stSql .="              nom_uf,                                                                     \n";
    $stSql .="              sigla_uf,                                                                   \n";
    $stSql .="              exercicio                                                                   \n";
    $stSql .="          FROM                                                                            \n";
    $stSql .="              sw_uf                                                                       \n";
    $stSql .="          INNER JOIN (                                                                    \n";
    $stSql .="              select                                                                      \n";
    $stSql .="                  valor,                                                                  \n";
    $stSql .="                  exercicio                                                               \n";
    $stSql .="              from                                                                        \n";
    $stSql .="                  administracao.configuracao                                              \n";
    $stSql .="              where                                                                       \n";
    $stSql .="                  parametro = 'cod_uf'                                                    \n";
    $stSql .="                  and cod_modulo = 2                                                      \n";
    $stSql .="          ) as uf_config                                                                  \n";
    $stSql .="          ON uf_config.valor = sw_uf.cod_uf::varchar                                      \n";
    $stSql .="      ) as prefeitura_uf                                                                  \n";
    $stSql .="      ON prefeitura_uf.exercicio = '".$inExercicioConf."'                                 \n";

    $stSql .="      INNER JOIN (                                                                        \n";
    $stSql .="          SELECT                                                                          \n";
    $stSql .="              nom_municipio,                                                              \n";
    $stSql .="              mun_conf.exercicio                                                          \n";
    $stSql .="          FROM                                                                            \n";
    $stSql .="              sw_municipio                                                                \n";
    $stSql .="              INNER JOIN (                                                                \n";
    $stSql .="                  select                                                                  \n";
    $stSql .="                      valor,                                                              \n";
    $stSql .="                      exercicio                                                           \n";
    $stSql .="                  from                                                                    \n";
    $stSql .="                      administracao.configuracao                                          \n";
    $stSql .="                  where                                                                   \n";
    $stSql .="                      parametro = 'cod_municipio'                                         \n";
    $stSql .="                      and cod_modulo = 2                                                  \n";
    $stSql .="              ) as mun_conf                                                               \n";
    $stSql .="              ON mun_conf.valor = sw_municipio.cod_municipio::varchar                     \n";
    $stSql .="              INNER JOIN (                                                                \n";
    $stSql .="                  select                                                                  \n";
    $stSql .="                      valor,                                                              \n";
    $stSql .="                      exercicio                                                           \n";
    $stSql .="                  from                                                                    \n";
    $stSql .="                      administracao.configuracao                                          \n";
    $stSql .="                  where                                                                   \n";
    $stSql .="                      parametro = 'cod_uf'                                                \n";
    $stSql .="                      and cod_modulo = 2                                                  \n";
    $stSql .="              ) as uf_config                                                              \n";
    $stSql .="              ON uf_config.valor = sw_municipio.cod_uf::varchar                           \n";
    $stSql .="              AND uf_config.exercicio = mun_conf.exercicio                                \n";
    $stSql .="      ) as prefeitura_municipio                                                           \n";
    $stSql .="      ON prefeitura_municipio.exercicio = '".$inExercicioConf."'                          \n";

    $stSql .="      INNER JOIN (                                                                        \n";
    $stSql .="          SELECT                                                                          \n";
    $stSql .="              admc.valor,                                                                 \n";
    $stSql .="              nom_cgm, exercicio                                                          \n";
    $stSql .="          FROM                                                                            \n";
    $stSql .="              administracao.configuracao as admc                                          \n";
    $stSql .="              INNER JOIN sw_cgm as cgm                                                    \n";
    $stSql .="              ON cgm.numcgm = admc.valor::integer                                         \n";
    $stSql .="          WHERE                                                                           \n";
    $stSql .="              cod_modulo = 14                                                             \n";
    $stSql .="              AND parametro = 'diretor_tributos'                                          \n";
    $stSql .="      ) as diretor_tributos                                                               \n";
    $stSql .="      ON diretor_tributos.exercicio = '".$inExercicioConf."'                              \n";

    $stSql .="      ,(  SELECT numcgm, nom_cgm from sw_cgm ) as usuario                                 \n";

    return $stSql;

}

public function buscaDadosDocumentoSanitario(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaBuscaDadosDocumentoSanitario();
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;

}

public function montaBuscaDadosDocumentoSanitario()
{
    ;
    //-------------
    $stSql = " SELECT
                    (
                        SELECT
                            valor
                        FROM
                            administracao.configuracao
                        WHERE
                            configuracao.cod_modulo = 14
                            AND configuracao.parametro = 'sanit_secretaria'
                            AND configuracao.exercicio = '".Sessao::getExercicio()."'
                    )AS sanit_secretaria,
                    (
                        SELECT
                            valor
                        FROM
                            administracao.configuracao
                        WHERE
                            configuracao.cod_modulo = 14
                            AND configuracao.parametro = 'sanit_departamento'
                            AND configuracao.exercicio = '".Sessao::getExercicio()."'
                    )AS sanit_departamento
    \n";

    return $stSql;
}

public function buscaDadosConcederLicencaDiversa(&$rsRecordSet, $inExercicioConf, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaBuscaDadosConcederLicencaDiversa( $inExercicioConf ).$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;

}

public function montaBuscaDadosConcederLicencaDiversa($inExercicioConf)
{
    $stSql = "  SELECT DISTINCT                                                                             \n";
    $stSql .="      LPAD (ela.cod_licenca::varchar, 8, '0') as cod_licenca                                           \n";
    $stSql .= "     , economico.fn_consulta_processo_licenca(ela.cod_licenca, ela.exercicio) as processo    \n";
    $stSql .="      , licenca_documento.num_alvara                                                          \n";
    $stSql .="      , lpad( ela.exercicio, 4, '0')::varchar as exercicio                                    \n";
    $stSql .="      , TO_CHAR ( el.dt_inicio,'dd/mm/yyyy' ) as inicio_licenca                               \n";
    $stSql .="      , TO_CHAR ( el.dt_termino,'dd/mm/yyyy' ) as termino_licenca                             \n";
    $stSql .="      , etld.cod_tipo                                                                         \n";
    $stSql .="      , etld.nom_tipo                                                                         \n";
    $stSql .="      , cgm.numcgm                                                                            \n";
    $stSql .="      , cgm.nom_cgm as razao_social                                                           \n";
    $stSql .="      , ativide_principal.nom_atividade                                                       \n";
    $stSql .="      , cgmPJ.nom_fantasia                                                                    \n";
    $stSql .="      , cgmPF.rg                                                                              \n";
    $stSql .="      , cgmPF.cpf                                                                             \n";
    $stSql .="      , cgmPJ.cnpj as cnpj                                                                    \n";
    $stSql .="      , COALESCE (cgmPJ.cnpj, cgmPF.cpf ) as cnpj_cpf                                         \n";
    $stSql .="      , NULL as num_emissao                                                                   \n";
    $stSql .="      , elo.observacao                                                                        \n";
    $stSql .="      , LPAD (ela.cod_licenca::varchar, 4, '0') || ela.exercicio as codigo_barra                       \n";
    $stSql .="      , diretor_tributos.nom_cgm as diretor_tributos                                          \n";
    $stSql .="      , usuario.nom_cgm as usuario                                                            \n";
    $stSql .="      , TO_CHAR ( now()::date,'dd/mm/yyyy' ) as data_emissao                                  \n";
    $stSql .="      , ( cgm.tipo_logradouro||' '||cgm.logradouro||', '||cgm.numero) as rua                  \n";
    $stSql .="      , cgm.complemento as complemento                                                        \n";
    $stSql .="      , ( select mun.nom_municipio::varchar||' / '||uf.sigla_uf::varchar                      \n";
    $stSql .="          from                                                                                \n";
    $stSql .="              sw_municipio as mun                                                             \n";
    $stSql .="              INNER JOIN sw_uf as uf                                                          \n";
    $stSql .="              ON uf.cod_uf = mun.cod_uf                                                       \n";
    $stSql .="          WHERE                                                                               \n";
    $stSql .="              mun.cod_municipio = cgm.cod_municipio                                           \n";
    $stSql .="              AND mun.cod_uf = cgm.cod_uf                                                     \n";
    $stSql .="      ) as cidade                                                                             \n";
    $stSql .="      ,cgm.cep                                                                                \n";
    $stSql .="      , upper (prefeitura_nome.valor) as prefeitura_nome                                      \n";
    $stSql .="      , prefeitura_cnpj.valor as prefeitura_cnpj                                              \n";
    $stSql .="      , ( prefeitura_tl.valor||' '||prefeitura_logr.valor||', '||prefeitura_logr_nr.valor||'  \n";
    $stSql .="          '||prefeitura_complem.valor||' - '||prefeitura_bairro.valor||' - CEP: '||           \n";
    $stSql .="          substring (prefeitura_cep.valor from 1 for 5)||'-'||                                \n";
    $stSql .="          substring( prefeitura_cep.valor from 6 for 9)                                       \n";
    $stSql .="      ) as prefeitura_endereco                                                                \n";
    $stSql .="      , prefeitura_municipio.nom_municipio as prefeitura_municipio                            \n";
    $stSql .="      , prefeitura_uf.nom_uf as prefeitura_uf                                                 \n";
    $stSql .="      , prefeitura_uf.sigla_uf as prefeitura_uf_sigla                                         \n";

    $stSql .="  FROM                                                                                        \n";
    $stSql .="      economico.licenca_diversa as ela                                                        \n";
    $stSql .="  INNER JOIN
                    economico.licenca_documento
                ON
                    licenca_documento.cod_licenca = ela.cod_licenca                                         \n";
    $stSql .="      INNER JOIN economico.licenca as el                                                      \n";
    $stSql .="      ON el.cod_licenca = ela.cod_licenca                                                     \n";
    $stSql .="      AND el.exercicio = ela.exercicio                                                        \n";

    $stSql .="      INNER JOIN economico.tipo_licenca_diversa as etld                                       \n";
    $stSql .="      ON etld.cod_tipo = ela.cod_tipo                                                         \n";

    $stSql .="      LEFT JOIN economico.licenca_observacao as elo                                           \n";
    $stSql .="      ON elo.cod_licenca = el.cod_licenca                                                     \n";
    $stSql .="      AND elo.exercicio = el.exercicio                                                        \n";

    $stSql .="      INNER JOIN sw_cgm as cgm                                                                \n";
    $stSql .="      ON cgm.numcgm = ela.numcgm                                                              \n";

    $stSql .="      LEFT JOIN sw_cgm_pessoa_fisica as cgmPF                                                 \n";
    $stSql .="      ON cgmPF.numcgm = cgm.numcgm                                                            \n";

    $stSql .="      LEFT JOIN sw_cgm_pessoa_juridica as cgmPJ                                               \n";
    $stSql .="      ON cgmPJ.numcgm = cgm.numcgm                                                            \n";
    $stSql .="
                    LEFT JOIN (
                        SELECT
                            atv.cod_atividade,
                            atv.nom_atividade,
                            atv.cod_estrutural,
                            ATE.PRINCIPAL,
                            coalesce ( TO_CHAR ( ATE.DT_INICIO,'dd/mm/yyyy' ) , '-') AS dt_inicio,
                            coalesce ( TO_CHAR ( ATE.DT_TERMINO,'dd/mm/yyyy' ), '-') AS dt_termino,
                            ATE.OCORRENCIA_ATIVIDADE,
                            COALESCE( cadastro_economico_empresa_direito.numcgm, cadastro_economico_autonomo.numcgm, cadastro_economico_empresa_fato.numcgm ) AS numcgm
                        FROM
                            economico.atividade AS ATV

                        INNER JOIN
                            economico.atividade_cadastro_economico AS ATE
                        ON
                            ATV.COD_ATIVIDADE = ATE.COD_ATIVIDADE

                        LEFT JOIN
                            economico.cadastro_economico_autonomo
                        ON
                            cadastro_economico_autonomo.inscricao_economica = ATE.inscricao_economica

                        LEFT JOIN
                            economico.cadastro_economico_empresa_fato
                        ON
                            cadastro_economico_empresa_fato.inscricao_economica = ATE.inscricao_economica

                        LEFT JOIN
                            economico.cadastro_economico_empresa_direito
                        ON
                            cadastro_economico_empresa_direito.inscricao_economica = ATE.inscricao_economica

                        WHERE
                            ATE.PRINCIPAL = true
                    ) as ativide_principal
                    ON ativide_principal.numcgm = cgm.numcgm                                                \n";
    $stSql .="      , (                                                                                     \n";
    $stSql .="          SELECT                                                                              \n";
    $stSql .="              valor                                                                           \n";
    $stSql .="          FROM                                                                                \n";
    $stSql .="              administracao.configuracao                                                      \n";
    $stSql .="          WHERE                                                                               \n";
    $stSql .="              parametro = 'nom_prefeitura'                                                    \n";
    $stSql .="              AND cod_modulo = 2                                                              \n";
    $stSql .="              AND exercicio = '". $inExercicioConf ."'                                          \n";
    $stSql .="      ) as prefeitura_nome                                                                    \n";

    $stSql .="      , ( SELECT                                                                              \n";
    $stSql .="              valor                                                                           \n";
    $stSql .="          FROM                                                                                \n";
    $stSql .="              administracao.configuracao                                                      \n";
    $stSql .="          WHERE                                                                               \n";
    $stSql .="              parametro = 'tipo_logradouro'                                                   \n";
    $stSql .="              AND cod_modulo = 2                                                              \n";
    $stSql .="              AND exercicio = '". $inExercicioConf ."'                                          \n";
    $stSql .="      ) as prefeitura_tl                                                                      \n";

    $stSql .="      , ( SELECT                                                                              \n";
    $stSql .="              valor                                                                           \n";
    $stSql .="          FROM                                                                                \n";
    $stSql .="              administracao.configuracao                                                      \n";
    $stSql .="          where                                                                               \n";
    $stSql .="              parametro = 'logradouro'                                                        \n";
    $stSql .="              AND cod_modulo = 2                                                              \n";
    $stSql .="              AND exercicio = '". $inExercicioConf ."'                                          \n";
    $stSql .="      ) as prefeitura_logr                                                                    \n";

    $stSql .="      , ( SELECT                                                                              \n";
    $stSql .="              valor                                                                           \n";
    $stSql .="          FROM                                                                                \n";
    $stSql .="              administracao.configuracao                                                      \n";
    $stSql .="          WHERE                                                                               \n";
    $stSql .="              parametro = 'numero'                                                            \n";
    $stSql .="              AND cod_modulo = 2                                                              \n";
    $stSql .="              AND exercicio = '". $inExercicioConf ."'                                          \n";
    $stSql .="      ) as prefeitura_logr_nr                                                                 \n";

    $stSql .="      , ( SELECT                                                                              \n";
    $stSql .="              valor                                                                           \n";
    $stSql .="          FROM                                                                                \n";
    $stSql .="              administracao.configuracao                                                      \n";
    $stSql .="          where                                                                               \n";
    $stSql .="              parametro = 'complemento'                                                       \n";
    $stSql .="              and cod_modulo = 2                                                              \n";
    $stSql .="              AND exercicio = '". $inExercicioConf ."'                                          \n";
    $stSql .="      ) as prefeitura_complem                                                                 \n";

    $stSql .="      , ( SELECT                                                                              \n";
    $stSql .="              valor                                                                           \n";
    $stSql .="          FROM                                                                                \n";
    $stSql .="              administracao.configuracao                                                      \n";
    $stSql .="          WHERE                                                                               \n";
    $stSql .="              parametro = 'bairro'                                                            \n";
    $stSql .="              and cod_modulo = 2                                                              \n";
    $stSql .="              AND exercicio = '". $inExercicioConf ."'                                          \n";
    $stSql .="      ) as prefeitura_bairro                                                                  \n";

    $stSql .="      , ( SELECT                                                                              \n";
    $stSql .="              valor                                                                           \n";
    $stSql .="          FROM                                                                                \n";
    $stSql .="              administracao.configuracao                                                      \n";
    $stSql .="          WHERE                                                                               \n";
    $stSql .="              parametro = 'cep'                                                               \n";
    $stSql .="              and cod_modulo = 2                                                              \n";
    $stSql .="              AND exercicio = '". $inExercicioConf ."'                                          \n";
    $stSql .="      ) as prefeitura_cep                                                                     \n";

    $stSql .="      , ( SELECT                                                                              \n";
    $stSql .="              valor                                                                           \n";
    $stSql .="          FROM                                                                                \n";
    $stSql .="              administracao.configuracao                                                      \n";
    $stSql .="          WHERE                                                                               \n";
    $stSql .="              parametro = 'cnpj'                                                              \n";
    $stSql .="              and cod_modulo = 2                                                              \n";
    $stSql .="              AND exercicio = '". $inExercicioConf ."'                                          \n";
    $stSql .="      ) as prefeitura_cnpj                                                                    \n";

    $stSql .="      , ( SELECT                                                                              \n";
    $stSql .="              nom_uf                                                                          \n";
    $stSql .="              , sigla_uf                                                                      \n";
    $stSql .="          FROM                                                                                \n";
    $stSql .="              sw_uf                                                                           \n";
    $stSql .="              INNER JOIN (                                                                    \n";
    $stSql .="                  select                                                                      \n";
    $stSql .="                      valor                                                                   \n";
    $stSql .="                  from                                                                        \n";
    $stSql .="                      administracao.configuracao                                              \n";
    $stSql .="                  where                                                                       \n";
    $stSql .="                      parametro = 'cod_uf'                                                    \n";
    $stSql .="                      and cod_modulo = 2                                                      \n";
    $stSql .="                      AND exercicio = '". $inExercicioConf ."'                                  \n";
    $stSql .="              ) as uf_config                                                                  \n";
    $stSql .="              ON uf_config.valor = sw_uf.cod_uf::varchar                                               \n";
    $stSql .="      ) as prefeitura_uf                                                                      \n";

    $stSql .="      , ( SELECT                                                                              \n";
    $stSql .="              nom_municipio                                                                   \n";
    $stSql .="          FROM                                                                                \n";
    $stSql .="              sw_municipio                                                                    \n";
    $stSql .="              INNER JOIN (                                                                    \n";
    $stSql .="                  select                                                                      \n";
    $stSql .="                      valor                                                                   \n";
    $stSql .="                      , exercicio                                                             \n";
    $stSql .="                  from                                                                        \n";
    $stSql .="                      administracao.configuracao                                              \n";
    $stSql .="                  where                                                                       \n";
    $stSql .="                      parametro = 'cod_municipio'                                             \n";
    $stSql .="                      and cod_modulo = 2                                                      \n";
    $stSql .="                      AND exercicio = '". $inExercicioConf ."'                                  \n";
    $stSql .="              ) as mun_conf                                                                   \n";
    $stSql .="              ON mun_conf.valor = sw_municipio.cod_municipio::varchar                                  \n";
    $stSql .="              INNER JOIN (                                                                    \n";
    $stSql .="                  select                                                                      \n";
    $stSql .="                      valor                                                                   \n";
    $stSql .="                      , exercicio                                                             \n";
    $stSql .="                  from                                                                        \n";
    $stSql .="                      administracao.configuracao                                              \n";
    $stSql .="                  where                                                                       \n";
    $stSql .="                      parametro = 'cod_uf'                                                    \n";
    $stSql .="                      and cod_modulo = 2                                                      \n";
    $stSql .="                      AND exercicio = '". $inExercicioConf ."'                                  \n";
    $stSql .="              ) as uf_config                                                                  \n";
    $stSql .="              ON uf_config.valor = sw_municipio.cod_uf::varchar                                        \n";
    $stSql .="              AND uf_config.exercicio = mun_conf.exercicio                                    \n";
    $stSql .="      ) as prefeitura_municipio                                                               \n";

    $stSql .="      , ( SELECT                                                                              \n";
    $stSql .="              admc.valor                                                                      \n";
    $stSql .="              , nom_cgm                                                                       \n";
    $stSql .="          FROM                                                                                \n";
    $stSql .="              administracao.configuracao as admc                                              \n";
    $stSql .="              INNER JOIN sw_cgm as cgm                                                        \n";
    $stSql .="              ON cgm.numcgm::varchar = admc.valor                                                      \n";
    $stSql .="          WHERE                                                                               \n";
    $stSql .="              cod_modulo = 14                                                                 \n";
    $stSql .="              AND parametro = 'diretor_tributos'                                              \n";
    $stSql .="              AND exercicio = '". $inExercicioConf ."'                                          \n";
    $stSql .="      ) as diretor_tributos                                                                   \n";

    $stSql .="      , (  SELECT numcgm, nom_cgm from sw_cgm ) as usuario                                    \n";

    return $stSql;

}

public function recuperaLicencasAlvaras(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaLicencasAlvaras();
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    return $obErro;
}

public function montaRecuperaLicencasAlvaras()
{
    $stFiltro="";
    $stSql = "  SELECT licenca.cod_licenca::varchar || '/' || lpad( licenca.exercicio, 4, '0')::varchar as licenca   
                                 , CASE 
                                        WHEN li_atividade.cod_licenca IS NOT NULL THEN
                                            li_atividade.inscricao_economica     
                                        WHEN li_especial.cod_licenca IS NOT NULL THEN
                                            li_especial.inscricao_economica  
                                  END as inscricao
                                , TO_CHAR(licenca.dt_inicio,'DD/MM/YYYY') as dt_inicio                                    
                                , TO_CHAR(licenca.dt_termino,'DD/MM/YYYY') as dt_termino                                  
                                , licenca_situacao.situacao  
                                , lpad( licenca.exercicio, 4, '0')::varchar as exercicio     
                                , CASE 
                                        WHEN li_atividade.cod_licenca IS NOT NULL THEN
                                            li_atividade.nom_atividade     
                                        WHEN li_especial.cod_licenca IS NOT NULL THEN
                                            li_especial.nom_atividade  
                                  END as atividade
                                 , modelo_documento.nome_documento
                                 , licenca.cod_licenca
                        FROM economico.licenca                                                                  
                INNER JOIN ( SELECT cod_licenca
                                             , exercicio
                                             , dt_termino
                                             , CASE WHEN economico.fn_consulta_situacao_licenca(cod_licenca, exercicio) = '' AND dt_termino < now()::date THEN
                                                                'Vencida'::varchar                                                    
                                                        WHEN economico.fn_consulta_situacao_licenca(cod_licenca, exercicio) != '' THEN
                                                                economico.fn_consulta_situacao_licenca(cod_licenca, exercicio)
                                                END AS situacao

                                       FROM economico.licenca
                                    ) as licenca_situacao
                             ON licenca_situacao.cod_licenca = licenca.cod_licenca
                           AND licenca_situacao.exercicio = licenca.exercicio
                                    
                  LEFT JOIN (SELECT licenca_atividade.cod_licenca
                                             , atividade_cadastro_economico.cod_atividade
                                             , licenca_atividade.exercicio
                                             , atividade_cadastro_economico.inscricao_economica
                                             , atividade_cadastro_economico.ocorrencia_atividade
                                             , atividade.nom_atividade  
                                     FROM economico.licenca_atividade
                             INNER JOIN economico.atividade_cadastro_economico       
                                         ON licenca_atividade.inscricao_economica = atividade_cadastro_economico.inscricao_economica
                                       AND licenca_atividade.cod_atividade =  atividade_cadastro_economico.cod_atividade
                                       AND licenca_atividade.ocorrencia_atividade = atividade_cadastro_economico.ocorrencia_atividade
                                       AND atividade_cadastro_economico.principal = true                
                             INNER JOIN economico.atividade
                                         ON atividade.cod_atividade = atividade_cadastro_economico.cod_atividade
                                  ) as li_atividade
                             ON li_atividade.cod_licenca = licenca.cod_licenca
                           AND li_atividade.exercicio = licenca.exercicio
                                                 
                
                   LEFT JOIN (SELECT licenca_especial.cod_licenca
                                             , atividade_cadastro_economico.cod_atividade
                                             , licenca_especial.exercicio
                                             , atividade_cadastro_economico.inscricao_economica
                                             , atividade_cadastro_economico.ocorrencia_atividade
                                             , atividade.nom_atividade  
                                     FROM economico.licenca_especial
                             INNER JOIN economico.atividade_cadastro_economico       
                                         ON licenca_especial.inscricao_economica = atividade_cadastro_economico.inscricao_economica
                                       AND licenca_especial.cod_atividade =  atividade_cadastro_economico.cod_atividade
                                       AND licenca_especial.ocorrencia_atividade = atividade_cadastro_economico.ocorrencia_atividade
                             INNER JOIN economico.atividade
                                         ON atividade.cod_atividade = atividade_cadastro_economico.cod_atividade
                                  ) as li_especial
                             ON li_especial.cod_licenca = licenca.cod_licenca
                           AND li_especial.exercicio = licenca.exercicio
                           
                   LEFT JOIN economico.licenca_documento
                            ON licenca_documento.cod_licenca = licenca.cod_licenca
                          AND licenca_documento.exercicio= licenca.exercicio
                           
                    LEFT JOIN administracao.modelo_documento
                            ON modelo_documento.cod_documento = licenca_documento.cod_documento
                          AND modelo_documento.cod_tipo_documento = licenca_documento.cod_tipo_documento
             

        ";
        if( $this->getDado("stSituacao") != 'Todas'){
            $stFiltro .= "situacao ='".$this->getDado("stSituacao")."' AND ";
        }
        
        if ( $this->getDado("stDataInicial") != '' && $this->getDado("stDataFinal") != '' ) {
            $stFiltro .= " licenca.dt_inicio >= TO_DATE( '".$this->getDado('stDataInicial')."', 'dd/mm/yyyy' )  AND licenca.dt_termino <= TO_DATE( '".$this->getDado('stDataFinal')  ."', 'dd/mm/yyyy' ) AND";
        }
        
        if ( $this->getDado("inInscricaoEconomica") != ''){
             $stFiltro .= " coalesce ( li_atividade.inscricao_economica, li_especial.inscricao_economica) = ".$this->getDado("inInscricaoEconomica")." AND ";
        }
        
        if($this->getDado("stLicenca") != '' && $this->getDado("exercicio") != '' ){
            $stFiltro .= " licenca.cod_licenca = ".$this->getDado("stLicenca")." AND licenca.exercicio= '".$this->getDado("exercicio")."' AND ";
        } else if($this->getDado("stLicenca") != '' && $this->getDado("exercicio") == '' ){
            $stFiltro .= " licenca.cod_licenca = ".$this->getDado("stLicenca")." AND ";
        } else if($this->getDado("stLicenca") == '' && $this->getDado("exercicio") != '' ){
             $stFiltro .= " licenca.exercicio= '".$this->getDado("exercicio")."' AND ";
        }
   
       if ($stFiltro) {
            $stSql.= " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
       }
   
    $stSql .= "  GROUP BY licenca.cod_licenca
                                    , licenca
                                    , inscricao
                                    , licenca.dt_inicio
                                    , licenca.dt_termino
                                    , licenca.exercicio                                                  
                                    , situacao
                                    , atividade
                                    , modelo_documento.nome_documento
                   ORDER BY  licenca.cod_licenca  ";
                               
    return $stSql;

}

}

?>