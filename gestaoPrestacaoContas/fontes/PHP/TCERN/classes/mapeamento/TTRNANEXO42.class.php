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
    * Data de Criação: 14/02/2012

    * @author Desenvolvedor: Jean Felipe da Silva

    * @package URBEM
    * @subpackage Mapeamento
/
/*
$Log$
Revision 1.1  2007/07/11 04:46:53  diego
Primeira versão.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criação: 14/02/2012

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTRNANEXO42 extends Persistente
{
function TTRNANEXO42()
{
    $this->setEstrutura( array() );
    $this->setEstruturaAuxiliar( array() );
    $this->setDado('exercicio',Sessao::getExercicio());
}

function recuperaOrgao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaOrgao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaOrgao()
{
    $stSql = "SELECT  configuracao_entidade.valor AS cod_orgao
            , sw_cgm.nom_cgm AS nom_orgao
            , '' AS registro
        FROM administracao.configuracao_entidade
        JOIN orcamento.entidade
          ON entidade.exercicio = configuracao_entidade.exercicio
         AND entidade.cod_entidade = configuracao_entidade.cod_entidade
        JOIN sw_cgm
          ON sw_cgm.numcgm = entidade.numcgm

        WHERE configuracao_entidade.exercicio = '".$this->getDado('exercicio')."'
          AND configuracao_entidade.cod_entidade = ( SELECT valor
                                                       FROM administracao.configuracao
                                                      WHERE parametro = 'cod_entidade_prefeitura'
                                                        AND exercicio = '".$this->getDado('exercicio')."' )
          AND configuracao_entidade.cod_modulo = 49
          AND configuracao_entidade.parametro = 'cod_orgao_tce'";

    return $stSql;
}

function recuperaPrefeitura(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaPrefeitura",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT '1' AS tipo_registro
            , unidade_gestora.cod_institucional AS institucional
            , sw_cgm.nom_cgm AS unidade_gestora
                , sw_cgm_pessoa_juridica.cnpj AS cnpj
            , unidade_gestora.personalidade AS personalidade
            , unidade_gestora.administracao AS administracao
            , natureza_juridica.cod_natureza AS natureza
            , CASE WHEN unidade_gestora.situacao = TRUE THEN 1
                             ELSE 2
                          END AS situacao
            , norma.nom_norma AS norma
            , to_char(norma.dt_publicacao,'dd/mm/yyyy') AS dt_norma
            , sw_cgm.logradouro AS logradouro
            , sw_cgm.bairro AS bairro
            , sw_cgm.numero AS numero
            , sw_cgm.complemento AS complemento
            , sw_cgm.cep AS cep
            , sw_municipio.nom_municipio AS cidade
            , sw_uf.sigla_uf AS uf

        FROM tcern.unidade_gestora
        JOIN tcern.natureza_juridica
          ON natureza_juridica.cod_natureza = unidade_gestora.natureza
        JOIN normas.norma
          ON unidade_gestora.cod_norma = norma.cod_norma
        JOIN sw_cgm
          ON sw_cgm.numcgm = unidade_gestora.cgm_unidade
        JOIN sw_cgm_pessoa_juridica
          ON sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm
        JOIN sw_municipio
          ON sw_municipio.cod_municipio = sw_cgm.cod_municipio
         AND sw_municipio.cod_uf = sw_cgm.cod_uf
        JOIN sw_uf
          ON sw_uf.cod_uf = sw_municipio.cod_uf

        WHERE unidade_gestora.exercicio = ".$this->getDado('exercicio')."

        GROUP BY institucional
            , unidade_gestora
            , cnpj
            , logradouro
            , bairro
            , numero
            , complemento
            , cep
            , cidade
            , uf
            , personalidade
            , administracao
            , natureza_juridica.cod_natureza
            , situacao
            , norma
            , dt_norma";

    return $stSql;
}

function recuperaRespGestora(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaRespGestora",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaRespGestora()
{
    $stSql = "SELECT '2' AS tipo_registro
             , sw_cgm_pessoa_fisica.cpf AS cpf
             , sw_cgm.nom_cgm AS nome
             , unidade_gestora_responsavel.cargo AS cargo
             , funcao_gestor.cod_funcao AS funcao
             , to_char(unidade_gestora_responsavel.dt_inicio,'dd/mm/yyyy') AS dt_ini
             , to_char(unidade_gestora_responsavel.dt_fim,'dd/mm/yyyy') AS dt_fim
             , sw_cgm.logradouro AS logradouro
             , sw_cgm.bairro AS bairro
             , sw_cgm.numero AS numero
             , sw_cgm.complemento AS complemento
             , sw_cep.cep AS cep
             , sw_municipio.nom_municipio AS cidade
             , sw_uf.sigla_uf AS uf
             , unidade_gestora.cod_institucional AS cod_ug

        FROM tcern.unidade_gestora_responsavel
        JOIN tcern.unidade_gestora
          ON unidade_gestora.id = unidade_gestora_responsavel.id_unidade
        JOIN tcern.funcao_gestor
          ON funcao_gestor.cod_funcao = unidade_gestora_responsavel.cod_funcao
        JOIN sw_cgm
          ON sw_cgm.numcgm = unidade_gestora_responsavel.cgm_responsavel
        JOIN sw_cgm_pessoa_fisica
          ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
        JOIN sw_municipio
          ON sw_municipio.cod_municipio = sw_cgm.cod_municipio
         AND sw_municipio.cod_uf = sw_cgm.cod_uf
        JOIN sw_uf
          ON sw_uf.cod_uf = sw_municipio.cod_uf
        JOIN sw_cep
          ON sw_cep.cep = sw_cgm.cep

        WHERE unidade_gestora.exercicio = '".$this->getDado('exercicio')."'
          AND unidade_gestora_responsavel.dt_inicio >= '".$this->getDado('dtIni')."'
          AND unidade_gestora_responsavel.dt_fim <= '".$this->getDado('dtFim')."'";

    return $stSql;
}

function recuperaOrcamentaria(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaOrcamentaria",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaOrcamentaria()
{
    $stSql = "SELECT '3' AS tipo_registro
            , unidade_orcamentaria.cod_institucional AS institucional
            , unidade.nom_unidade AS unidade_orcamentaria
                , sw_cgm_pessoa_juridica.cnpj AS cnpj
            , CASE WHEN unidade_orcamentaria.situacao = TRUE THEN 1
                             ELSE 2
                          END AS situacao
            , norma.nom_norma AS norma
            , to_char(norma.dt_publicacao,'dd/mm/yyyy') AS dt_norma
            , sw_cgm.logradouro AS logradouro
            , sw_cgm.bairro AS bairro
            , sw_cgm.numero AS numero
            , sw_cgm.complemento AS complemento
            , sw_cgm.cep AS cep
            , sw_municipio.nom_municipio AS cidade
            , sw_uf.sigla_uf AS uf
            , unidade_gestora.cod_institucional AS cod_ug

        FROM tcern.unidade_orcamentaria
        JOIN tcern.unidade_gestora
          ON unidade_gestora.id = unidade_orcamentaria.id_unidade_gestora
        JOIN orcamento.unidade
          ON unidade_orcamentaria.exercicio = unidade.exercicio
         AND unidade_orcamentaria.num_orgao = unidade.num_orgao
         AND unidade_orcamentaria.num_unidade = unidade.num_unidade
        JOIN normas.norma
          ON norma.cod_norma = unidade_orcamentaria.cod_norma
        JOIN sw_cgm
          ON sw_cgm.numcgm = unidade_orcamentaria.cgm_unidade_orcamentaria
        JOIN sw_cgm_pessoa_juridica
          ON sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm
        JOIN sw_municipio
          ON sw_municipio.cod_municipio = sw_cgm.cod_municipio
         AND sw_municipio.cod_uf = sw_cgm.cod_uf
        JOIN sw_uf
          ON sw_uf.cod_uf = sw_municipio.cod_uf

        WHERE unidade_gestora.exercicio = '".$this->getDado('exercicio')."'";

    return $stSql;
}

function recuperaRespOrcamentaria(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaRespOrcamentaria",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaRespOrcamentaria()
{
    $stSql = "SELECT '4' AS tipo_registro
             , sw_cgm_pessoa_fisica.cpf AS cpf
             , sw_cgm.nom_cgm AS nome
             , unidade_orcamentaria_responsavel.cargo AS cargo
             , funcao_gestor.cod_funcao AS funcao
             , to_char(unidade_orcamentaria_responsavel.dt_inicio,'dd/mm/yyyy') AS dt_ini
             , to_char(unidade_orcamentaria_responsavel.dt_fim,'dd/mm/yyyy') AS dt_fim
             , sw_cgm.logradouro AS logradouro
             , sw_cgm.bairro AS bairro
             , sw_cgm.numero AS numero
             , sw_cgm.complemento AS complemento
             , sw_cep.cep AS cep
             , sw_municipio.nom_municipio AS cidade
             , sw_uf.sigla_uf AS uf
             , unidade_orcamentaria.cod_institucional AS cod_uo

        FROM tcern.unidade_orcamentaria_responsavel
        JOIN tcern.unidade_orcamentaria
          ON unidade_orcamentaria.id = unidade_orcamentaria_responsavel.id_unidade
        JOIN tcern.funcao_gestor
          ON funcao_gestor.cod_funcao = unidade_orcamentaria_responsavel.cod_funcao
        JOIN tcern.unidade_gestora
          ON unidade_gestora.id = unidade_orcamentaria.id_unidade_gestora
        JOIN sw_cgm
          ON sw_cgm.numcgm = unidade_orcamentaria_responsavel.cgm_responsavel
        JOIN sw_cgm_pessoa_fisica
          ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
        JOIN sw_municipio
          ON sw_municipio.cod_municipio = sw_cgm.cod_municipio
         AND sw_municipio.cod_uf = sw_cgm.cod_uf
        JOIN sw_uf
          ON sw_uf.cod_uf = sw_municipio.cod_uf
        JOIN sw_cep
          ON sw_cep.cep = sw_cgm.cep

        WHERE unidade_gestora.exercicio = '".$this->getDado('exercicio')."'
          AND unidade_orcamentaria_responsavel.dt_inicio >= '".$this->getDado('dtIni')."'
          AND unidade_orcamentaria_responsavel.dt_fim <= '".$this->getDado('dtFim')."'";

    return $stSql;
}

}
