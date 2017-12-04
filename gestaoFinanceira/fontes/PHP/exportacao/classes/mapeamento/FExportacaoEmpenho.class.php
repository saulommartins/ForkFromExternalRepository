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
    * Classe de mapeamento da tabela FN_EXPORTACAO_EMPENHO
    * Data de Criação: 11/04/2005

    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.08.08, uc-02.08.14
*/

/*
$Log$
Revision 1.15  2006/07/05 20:45:59  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FExportacaoEmpenho extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FExportacaoEmpenho()
{
    parent::Persistente();
    $this->setTabela('tcerj.fn_exportacao_empenho');
    $this->AddCampo('num_unidade'           ,'integer'                    ,false,'',false,false);
    $this->AddCampo('exercicio_empenho'     ,'character(4)'               ,false,'',false,false);
    $this->AddCampo('cod_entidade'          ,'integer'                    ,false,'',false,false);
    $this->AddCampo('cod_empenho'           ,'integer'                    ,false,'',false,false);
    $this->AddCampo('exercicio'             ,'character(4)'               ,false,'',false,false);
    $this->AddCampo('tipo_pao'              ,'integer'                    ,false,'',false,false);
    $this->AddCampo('num_pao'               ,'integer'                    ,false,'',false,false);
    $this->AddCampo('cod_recurso'           ,'integer'                    ,false,'',false,false);
    $this->AddCampo('cod_estrutural'        ,'integer'                    ,false,'',false,false);
    $this->AddCampo('valor'                 ,'numeric'                    ,false,'',false,false);
    $this->AddCampo('cod_historico'         ,'integer'                    ,false,'',false,false);
    $this->AddCampo('cod_tipo'              ,'integer'                    ,false,'',false,false);
    $this->AddCampo('stData'                ,'text'                       ,false,'',false,false);
    $this->AddCampo('nom_cgm'               ,'character varying(200)'     ,false,'',false,false);
    $this->AddCampo('cpf'                   ,'character varying(11)'      ,false,'',false,false);
    $this->AddCampo('cnpj'                  ,'character varying(14)'      ,false,'',false,false);
    $this->AddCampo('num_orgao'             ,'integer'                    ,false,'',false,false);
    $this->AddCampo('cod_funcao'            ,'integer'                    ,false,'',false,false);
    $this->AddCampo('cod_subfuncao'         ,'integer'                    ,false,'',false,false);
    $this->AddCampo('cod_programa'          ,'integer'                    ,false,'',false,false);
}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaDadosExportacao.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro

*/
function recuperaDadosExportacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaDadosExportacao().$stCondicao.$stOrdem;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosExportacao()
{
    $stSql  = "SELECT                                                                   \n";
    $stSql .= "     num_unidade          ,                                              \n";
    $stSql .= "     exercicio_empenho    ,                                              \n";
//  $stSql .= "     substr(exercicio,3,2)||lpad(cod_entidade,2,0)||lpad(cod_empenho,6,0) as num_empenho,    \n";
    $stSql .= "     lpad(cod_entidade,2)||lpad(cod_empenho,6,0) as num_empenho,         \n";
    $stSql .= "     cod_entidade          ,                                             \n";
    $stSql .= "     cod_empenho          ,                                              \n";
    $stSql .= "     exercicio            ,                                              \n";
    $stSql .= "     tipo_pao             ,                                              \n";
    $stSql .= "     num_pao              ,                                              \n";
    $stSql .= "     cod_recurso          ,                                              \n";
    $stSql .= "     cod_estrutural       ,                                              \n";
    $stSql .= "     replace(valor,'.','') as valor,                                     \n";
    $stSql .= "     replace(replace(descricao,'\r\n',''),'\n','') as descricao,         \n";
//  $stSql .= "     descricao            ,                                              \n";
    $stSql .= "     cod_tipo             ,                                              \n";
    $stSql .= "     stData               ,                                              \n";
    $stSql .= "     nom_cgm              ,                                              \n";
    $stSql .= "     coalesce(cpf,cnpj,'00000000000000') AS cnpj_cpf,                    \n";
    $stSql .= "     CASE WHEN cnpj IS NOT NULL THEN                                     \n";
    $stSql .= "         2                                                               \n";
    $stSql .= "     ELSE                                                                \n";
    $stSql .= "         1                                                               \n";
    $stSql .= "     END AS tipo_credor,                                                 \n";
    $stSql .= "     num_orgao            ,                                              \n";
    $stSql .= "     cod_funcao           ,                                              \n";
    $stSql .= "     cod_subfuncao        ,                                              \n";
    $stSql .= "     cod_programa         ,                                              \n";
    $stSql .= "     sujeito              ,                                              \n";
    $stSql .= "     num_proc_licit                                                      \n";
    $stSql .= "FROM                                                                     \n";
    $stSql .= "        ".$this->getTabela()."('".$this->getDado("stExercicio")."',      \n";
    $stSql .= "                               '".$this->getDado("stCodEntidades")."',   \n";
    $stSql .= "                               '".$this->getDado("dtInicial")."',        \n";
    $stSql .= "                               '".$this->getDado("dtFinal")."')          \n";
    $stSql .= "AS tabela              (   num_unidade          integer,                 \n";
    $stSql .= "                           exercicio_empenho    character(4),            \n";
    $stSql .= "                           cod_entidade         integer,                 \n";
    $stSql .= "                           cod_empenho          integer,                 \n";
    $stSql .= "                           exercicio            character(4),            \n";
    $stSql .= "                           tipo_pao             integer,                 \n";
    $stSql .= "                           num_pao              integer,                 \n";
    $stSql .= "                           cod_recurso          integer,                 \n";
    $stSql .= "                           cod_estrutural       integer,                 \n";
    $stSql .= "                           valor                numeric,                 \n";
    $stSql .= "                           descricao            character varying(160),  \n";
    $stSql .= "                           cod_tipo             integer,                 \n";
    $stSql .= "                           stData               text,                    \n";
    $stSql .= "                           nom_cgm              character varying(200),  \n";
    $stSql .= "                           cpf                  character varying(11),   \n";
    $stSql .= "                           cnpj                 character varying(14),   \n";
    $stSql .= "                           num_orgao            integer,                 \n";
    $stSql .= "                           cod_funcao           integer,                 \n";
    $stSql .= "                           cod_subfuncao        integer,                 \n";
    $stSql .= "                           cod_programa         integer,                 \n";
    $stSql .= "                           sujeito              text,                    \n";
    $stSql .= "                           num_proc_licit       text                     \n";
    $stSql .= "                           )                                             \n";

    return $stSql;
}

function montaRecuperaDadosExportacaoAjustes()
{
    $stSql  = "SELECT                                                                               \n";
    $stSql .= "    e.exercicio                 as exercicio,                                        \n";
    $stSql .= "    e.cod_empenho               as cod_empenho,                                      \n";
    $stSql .= "    e.cod_pre_empenho           as cod_pre_empenho,                                  \n";
    $stSql .= "    e.cod_entidade              as entidade,                                         \n";
    $stSql .= "    modalidade.valor_padrao     as modalidade,                                       \n";
    $stSql .= "    licitacao.valor             as nro_proc_licitatorio,                             \n";
    $stSql .= "    licitacao.timestamp,                                                             \n";
    $stSql .= "    atributo.cod_atributo,                                                           \n";
    $stSql .= "    cgm.nom_cgm                                                                      \n";
    $stSql .= "FROM                                                                                 \n";
    $stSql .= "    empenho.empenho                     as e                                         \n";
    $stSql .= "    INNER JOIN                                                                       \n";
    $stSql .= "    (                                                                                \n";
    $stSql .= "        SELECT                                                                       \n";
    $stSql .= "            pe.exercicio, pe.cod_pre_empenho, avp.valor_padrao                       \n";
    $stSql .= "        FROM                                                                         \n";
    $stSql .= "            empenho.pre_empenho                 as pe,                               \n";
    $stSql .= "            empenho.atributo_empenho_valor      as aev,                              \n";
    $stSql .= "            administracao.atributo_dinamico     as ad,                               \n";
    $stSql .= "            administracao.atributo_valor_padrao as avp                               \n";
    $stSql .= "                                                                                     \n";
    $stSql .= "        WHERE                                                                        \n";
    $stSql .= "           -- ligaçao entre pre_empenho e atributo_empenho_valor                     \n";
    $stSql .= "            pe.exercicio        = aev.exercicio and                                  \n";
    $stSql .= "            pe.cod_pre_empenho  = aev.cod_pre_empenho and                            \n";
    $stSql .= "            aev.cod_cadastro    = 1 and                                              \n";
    $stSql .= "            aev.cod_modulo      = 10 and                                             \n";
    $stSql .= "                                                                                     \n";
    $stSql .= "            -- ligação entre atributo_empenho_valor e atributo_dinamico              \n";
    $stSql .= "            aev.cod_cadastro    = ad.cod_cadastro and                                \n";
    $stSql .= "            aev.cod_modulo      = ad.cod_modulo and                                  \n";
    $stSql .= "            aev.cod_atributo    = ad.cod_atributo and                                \n";
    $stSql .= "            ad.nom_atributo     ilike '%Modalidade%' and                             \n";
    $stSql .= "                                                                                     \n";
    $stSql .= "            -- ligação entre atributo_dinamico e atributo_valor_padrao               \n";
    $stSql .= "            ad.cod_modulo               = avp.cod_modulo and                         \n";
    $stSql .= "            ad.cod_cadastro             = avp.cod_cadastro and                       \n";
    $stSql .= "            ad.cod_atributo             = avp.cod_atributo and                       \n";
    $stSql .= "            cast(avp.cod_valor as text) = aev.valor                                  \n";
    $stSql .= "                                                                                     \n";
    $stSql .= "        ) as modalidade on (                                                         \n";
    $stSql .= "            modalidade.exercicio        = e.exercicio and                            \n";
    $stSql .= "            modalidade.cod_pre_empenho  = e.cod_pre_empenho )                        \n";
    $stSql .= "  LEFT JOIN (                                                                        \n";
    $stSql .= "            select                                                                   \n";
    $stSql .= "                epe.cod_pre_empenho, epe.exercicio, aev.valor, aev.timestamp, aev.cod_atributo \n";
    $stSql .= "            from                                                                     \n";
    $stSql .= "                empenho.pre_empenho as epe,                                          \n";
    $stSql .= "                empenho.atributo_empenho_valor as aev,                               \n";
    $stSql .= "                administracao.atributo_dinamico as ad                                \n";
    $stSql .= "            where                                                                    \n";
    $stSql .= "                -- ligação entre pre_empenho e atributo_empenho_valor                \n";
    $stSql .= "                epe.cod_pre_empenho = aev.cod_pre_empenho and                        \n";
    $stSql .= "                epe.exercicio       = aev.exercicio and                              \n";
    $stSql .= "                aev.cod_cadastro    = 1 and                                          \n";
    $stSql .= "                aev.cod_modulo      = 10 and                                         \n";
    $stSql .= "                                                                                     \n";
    $stSql .= "                -- ligação entre atributo_empenho_valor e atributo_dinamico          \n";
    $stSql .= "                aev.cod_modulo      = ad.cod_modulo and                              \n";
    $stSql .= "                aev.cod_cadastro    = ad.cod_cadastro and                            \n";
    $stSql .= "                aev.cod_atributo    = ad.cod_atributo and                            \n";
    $stSql .= "                ad.nom_atributo     ilike '%Nro do Processo Licitatório%'            \n";
    $stSql .= "                                                                                     \n";
    $stSql .= "            ) as licitacao on (                                                      \n";
    $stSql .= "                licitacao.cod_pre_empenho = e.cod_pre_empenho and                    \n";
    $stSql .= "                licitacao.exercicio       = e.exercicio ),                           \n";
    $stSql .= "     orcamento.entidade        as oe,                                                \n";
    $stSql .= "     sw_cgm                    as cgm,                                               \n";
    $stSql .= "   ( select                                                                          \n";
    $stSql .= "                      cod_atributo from administracao.atributo_dinamico              \n";
    $stSql .= "              where                                                                  \n";
    $stSql .= "                      nom_atributo ilike '%Nro do Processo Licitatório%' and         \n";
    $stSql .= "                      cod_modulo = 10 and                                            \n";
    $stSql .= "                      cod_cadastro = 1 ) as atributo                                 \n";
    $stSql .= "                                                                                     \n";
    $stSql .= "WHERE                                                                                \n";
    $stSql .= "   oe.cod_entidade = e.cod_entidade AND                                              \n";
    $stSql .= "   oe.exercicio    = e.exercicio AND                                                 \n";
    $stSql .= "   oe.numcgm       = cgm.numcgm AND                                                  \n";
    $stSql .= "   e.exercicio = '".$this->getDado("stExercicio")."' AND                             \n";
    $stSql .= "   e.dt_empenho BETWEEN to_date('".$this->getDado("dtInicial")."','dd/mm/yyyy' ) AND \n";
    $stSql .= "   to_date('".$this->getDado("dtFinal")."','dd/mm/yyyy') AND                         \n";
    $stSql .= "   e.cod_entidade IN (".$this->getDado("stCodEntidades").")                          \n";

    return $stSql;
}

function recuperaDadosExportacaoAjustes(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaDadosExportacaoAjustes().$stCondicao.$stOrdem;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

}
