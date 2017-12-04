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

  * Página de
  * Data de criação : 25/10/2005

  * @author Analista:
  * @author Programador: Fernando Zank Correa Evangelista

  $Id: TPatrimonioBem.class.php 65343 2016-05-13 17:02:26Z arthur $

  Caso de uso: uc-03.01.09
  Caso de uso: uc-03.01.21

  **/

set_time_limit(0);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TPatrimonioBem extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TPatrimonioBem()
    {
        parent::Persistente();
        $this->setTabela('patrimonio.bem');
        $this->setCampoCod('cod_bem');
        $this->setComplementoChave('');
        $this->AddCampo('cod_bem','integer',true,'',true,false);
        $this->AddCampo('cod_natureza','integer',true,'',false,true);
        $this->AddCampo('cod_grupo','integer',true,'',false,true);
        $this->AddCampo('cod_especie','integer',true,'',false,true);
        $this->AddCampo('numcgm','integer',true,'',false,true);
        $this->AddCampo('descricao','varchar',true,'60',false,false);
        $this->AddCampo('detalhamento','text',true,'',false,false);
        $this->AddCampo('dt_aquisicao','date',true,'',false,false);
        $this->AddCampo('dt_incorporacao','date',true,'',false,false);
        $this->AddCampo('dt_depreciacao','date',true,'',false,false);
        $this->AddCampo('dt_garantia','date',true,'',false,false);
        $this->AddCampo('identificacao','boolean',true,'',false,false);
        $this->AddCampo('num_placa','varchar',true,'20',false,false);
        $this->AddCampo('vida_util','integer',false,'',false,false);
        $this->AddCampo('vl_bem','numeric',true,'14.2',false,false);
        $this->AddCampo('vl_depreciacao','numeric',false,'14.2',false,false);
        $this->AddCampo('depreciavel','boolean',false,'',false,false);
        $this->AddCampo('depreciacao_acelerada','boolean',false,'',false,false);
        $this->AddCampo('quota_depreciacao_anual','numeric',false,'5.2',false,false);
        $this->AddCampo('quota_depreciacao_anual_acelerada','numeric',false,'5.2',false,false);
    }

    public function recuperaMax(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaMax().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function recuperaBem(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaBem().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function recuperaDescricaoBem(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaDescricaoBem().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function recuperaFichaPatrimonialResumida(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaFichaPatrimonialResumida().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function recuperaFichaPatrimonialResumidaHistorico(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaFichaPatrimonialResumidaHistorico().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function recuperaFichaPatrimonialCompletaHistorico(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaFichaPatrimonialCompletaHistorico().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function recuperaFichaPatrimonialCompleta(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaFichaPatrimonialCompleta( $stOrdem ).$stFiltro;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function recuperaListaPatrimonialCompleta(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaListaPatrimonialCompleta( $stOrdem ).$stFiltro;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function recuperaRelatorioBaixa(&$rsRecordSet, $stFiltro = "",  $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaRelatorioBaixa().$stFiltro;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaBem()
    {
        $stSql  = "select                               \n";
        $stSql .= "    *                                \n";
        $stSql .= "from                                 \n";
        $stSql .= "    patrimonio.bem as b           \n";

        return $stSql;
    }

    public function montaRecuperaDescricaoBem()
    {
        $stSql  = "select                                           \n";
        $stSql .= "distinct(lower(trim(descricao))) as descricao    \n";
        $stSql .= "from                                             \n";
        $stSql .= "patrimonio.bem as b                              \n";

        return $stSql;
    }

    public function montaRecuperaFichaPatrimonialResumidaHistorico()
    {
        $stSql  = "SELECT                                                                                    \n";
        $stSql .= "    b.cod_bem,                                                                            \n";
        $stSql .= "    sw_cgm.nom_cgm,                                                                       \n";
        $stSql .= "    b.num_placa,                                                                          \n";
        $stSql .= "    e.nom_especie,                                                                        \n";
        $stSql .= "    n.nom_natureza,                                                                       \n";
        $stSql .= "    g.nom_grupo,                                                                          \n";
        $stSql .= "    b.descricao as detalhamento,                                                          \n";
        $stSql .= "    bc.nota_fiscal,                                                                       \n";
        $stSql .= "    lo.descricao as nom_local,                                                                         \n";
        $stSql .= "    b.dt_aquisicao,                                                                          \n";
        $stSql .= "    b.dt_incorporacao,                                                                       \n";
        $stSql .= "    n.cod_natureza ||'.'||g.cod_grupo||'.'||e.cod_especie as classificacao,               \n";
        $stSql .= "    hb.timestamp                                                                          \n";
        $stSql .= "FROM                                                                                      \n";
        $stSql .= "     patrimonio.natureza as n,                                                            \n";
        $stSql .= "     patrimonio.grupo    as g,                                                            \n";
        $stSql .= "     patrimonio.especie  as e,                                                            \n";
        $stSql .= "     sw_cgm,                                                                              \n";
        $stSql .= "     patrimonio.bem      as b                                                            \n";
        $stSql .="LEFT OUTER JOIN patrimonio.bem_baixado as bb ON                                            \n";
        $stSql .="    bb.cod_bem = b.cod_bem                                                                 \n";
        $stSql .= "     LEFT OUTER JOIN                                                                      \n";
        $stSql .= "           patrimonio.bem_comprado as bc on                                               \n";
        $stSql .= "           bc.cod_bem = b.cod_bem,                                                         \n";
        $stSql .= "     patrimonio.historico_bem as hb,                                                      \n";
        $stSql .= "     organograma.local as lo                                                            \n";
        $stSql .= "WHERE                                                                                     \n";
        $stSql .= "     bb.cod_bem is NULL                                                                   \n";
        $stSql .= "AND  b.cod_especie  = e.cod_especie                                                       \n";
        $stSql .= "AND  b.cod_grupo    = e.cod_grupo                                                         \n";
        $stSql .= "AND  b.cod_natureza = e.cod_natureza                                                      \n";
        $stSql .= "AND e.cod_grupo    =  g.cod_grupo                                                         \n";
        $stSql .= "AND e.cod_natureza = g.cod_natureza                                                       \n";
        $stSql .= "AND g.cod_natureza = n.cod_natureza                                                       \n";
        $stSql .= "AND b.numcgm = sw_cgm.numcgm                                                              \n";
        if ($this->getDado("inCodNatureza") > 0 ) {
            $stSql .= "    AND b.cod_natureza= ".$this->getDado("inCodNatureza")."                                                      \n";
        }
        if ($this->getDado("inCodGrupo") > 0 ) {
            $stSql .= "    AND b.cod_grupo=    ".$this->getDado("inCodGrupo")."                             \n";
        }
        if ($this->getDado("inCodEspecie") > 0) {
            $stSql .= "    AND b.cod_especie=  ".$this->getDado("inCodEspecie")."                           \n";
        }
        if ($this->getDado("inCodOrgao")>0) {
            $stSql .= "     AND hb.cod_orgao = ".$this->getDado("inCodOrgao")."                             \n";
        }
        if ($this->getDado("inCodLocal")>0) {
            $stSql .= "     AND lo.cod_local = ".$this->getDado("inCodLocal")."                             \n";
        }
        if ($this->getDado("inCodFornecedor")>0) {
            $stSql .= "     AND b.numcgm = ".$this->getDado("inCodFornecedor")."                             \n";
        }
        if ($this->getDado("stDataFinal")>0) {
            $stSql .= "     AND b.dt_aquisicao BETWEEN to_date('".$this->getDado('stDataInicial')."','dd/mm/yyyy')  \n";
            $stSql .= "     AND to_date ('".$this->getDado('stDataFinal')."','dd/mm/yyyy')                          \n";
        }
        if ($this->getDado("stDataFinalIncorporacao")>0) {
            $stSql .= "     AND b.dt_incorporacao BETWEEN to_date('".$this->getDado('stDataInicialIncorporacao')."','dd/mm/yyyy')  \n";
            $stSql .= "     AND to_date ('".$this->getDado('stDataFinalIncorporacao')."','dd/mm/yyyy')                          \n";
        }
        $stSql .= "    AND b.cod_bem between ".$this->getDado("inCodBemInicial")." and  ".$this->getDado("inCodBemFinal")."         \n";
        $stSql .= "    AND hb.cod_bem = b.cod_bem                                                            \n";
        $stSql .= "    AND hb.cod_local = lo.cod_local                                                       \n";
        $stSql .= "    AND hb.cod_orgao = hb.cod_orgao                                                       \n";
        $stSql .= "ORDER BY                                                                                  \n";
        $stSql .= "    b.cod_bem,hb.timestamp desc                                                           \n";

        return $stSql;
    }

    public function montaRecuperaFichaPatrimonialResumida()
    {
        $stSql  = "SELECT                                                                                    \n";
        $stSql .= "    b.cod_bem,                                                                            \n";
        $stSql .= "    sw_cgm.nom_cgm,                                                                       \n";
        $stSql .= "    b.num_placa,                                                                          \n";
        $stSql .= "    e.nom_especie,                                                                        \n";
        $stSql .= "    n.nom_natureza,                                                                       \n";
        $stSql .= "    g.nom_grupo,                                                                          \n";
        $stSql .= "    b.descricao as detalhamento,                                                          \n";
        $stSql .= "    lo.descricao as nom_local,                                                                         \n";
        $stSql .= "    bc.nota_fiscal,                                                                       \n";
        $stSql .= "    b.dt_aquisicao,                                                                          \n";
        $stSql .= "    b.dt_incorporacao,                                                                       \n";
        $stSql .= "    n.cod_natureza ||'.'||g.cod_grupo||'.'||e.cod_especie as classificacao,               \n";
        $stSql .= "    hb.timestamp                                                                          \n";
        $stSql .= "FROM                                                                                      \n";
        $stSql .= "     patrimonio.natureza as n,                                                            \n";
        $stSql .= "     patrimonio.grupo    as g,                                                            \n";
        $stSql .= "     patrimonio.especie  as e,                                                            \n";
        $stSql .= "     sw_cgm,                                                                              \n";
        $stSql .= "     patrimonio.bem      as b                                                             \n";
        $stSql .="LEFT OUTER JOIN patrimonio.bem_baixado as bb ON                                               \n";
        $stSql .="    bb.cod_bem = b.cod_bem                                                                   \n";
        $stSql .= "     LEFT OUTER JOIN                                                                      \n";
        $stSql .= "           patrimonio.bem_comprado as bc on                                               \n";
        $stSql .= "           bc.cod_bem = b.cod_bem                                                         \n";
        $stSql .= "            LEFT OUTER JOIN (                                                             \n";
        $stSql .= "                SELECT  timestamp                                                         \n";
        $stSql .= "                        ,cod_bem                                                          \n";
        $stSql .= "                        ,cod_local                                                        \n";
        $stSql .= "                        ,cod_orgao                                                        \n";
        $stSql .= "                  FROM  patrimonio.historico_bem as hb                                    \n";
        $stSql .= "                 WHERE  timestamp||cod_bem IN  (                                          \n";
        $stSql .= "                                                SELECT  max(timestamp)||cod_bem           \n";
        $stSql .= "                                                  FROM    patrimonio.historico_bem as hbi \n";
        $stSql .= "                                            WHERE   cod_bem between ".$this->getDado("inCodBemInicial")." and  ".$this->getDado("inCodBemFinal")."\n";
        $stSql .= "                                                 GROUP BY cod_bem                         \n";
        $stSql .= "                                               )                                          \n";
        $stSql .= "            ) as hb ON ( hb.cod_bem = b.cod_bem )                                         \n";
        $stSql .= "LEFT OUTER JOIN organograma.local as lo ON                                                   \n";
        $stSql .= "        hb.cod_local = lo.cod_local                                                       \n";
        $stSql .= "    AND lo.cod_local is not NULL                                                          \n";
        $stSql .= "WHERE                                                                                     \n";
        $stSql .= "     bb.cod_bem is NULL                                                                   \n";
        $stSql .= "AND  b.cod_especie  = e.cod_especie                                                       \n";
        $stSql .= "AND  b.cod_grupo    = e.cod_grupo                                                         \n";
        $stSql .= "AND  b.cod_natureza = e.cod_natureza                                                      \n";
        $stSql .= "AND e.cod_grupo    =  g.cod_grupo                                                         \n";
        $stSql .= "AND e.cod_natureza = g.cod_natureza                                                       \n";
        $stSql .= "AND g.cod_natureza = n.cod_natureza                                                       \n";
        $stSql .= "AND b.numcgm = sw_cgm.numcgm                                                              \n";

        if ($this->getDado("inCodNatureza") > 0 ) {
            $stSql .= "    AND b.cod_natureza= ".$this->getDado("inCodNatureza")."                                                      \n";
        }
        if ($this->getDado("inCodGrupo") > 0 ) {
            $stSql .= "    AND b.cod_grupo=    ".$this->getDado("inCodGrupo")."                                                         \n";
        }
        if ($this->getDado("inCodEspecie") > 0) {
            $stSql .= "    AND b.cod_especie=  ".$this->getDado("inCodEspecie")."                                                       \n";
        }
        if ($this->getDado("inCodOrgao")>0) {
            $stSql .= "     AND hb.cod_orgao = ".$this->getDado("inCodOrgao")."                             \n";
        }
        if ($this->getDado("inCodLocal")>0) {
            $stSql .= "     AND lo.cod_local = ".$this->getDado("inCodLocal")."                             \n";
        }
        if ($this->getDado("inCodFornecedor")>0) {
            $stSql .= "     AND b.numcgm = ".$this->getDado("inCodFornecedor")."                             \n";
        }
        if ($this->getDado("stDataFinal")>0) {
            $stSql .= "     AND b.dt_aquisicao BETWEEN to_date('".$this->getDado('stDataInicial')."','dd/mm/yyyy')  \n";
            $stSql .= "     AND to_date ('".$this->getDado('stDataFinal')."','dd/mm/yyyy')                          \n";
        }
        if ($this->getDado("stDataFinalIncorporacao")>0) {
            $stSql .= "     AND b.dt_incorporacao BETWEEN to_date('".$this->getDado('stDataInicialIncorporacao')."','dd/mm/yyyy')  \n";
            $stSql .= "     AND to_date ('".$this->getDado('stDataFinalIncorporacao')."','dd/mm/yyyy')                          \n";
        }
        $stSql .= "    AND b.cod_bem between ".$this->getDado("inCodBemInicial")." and  ".$this->getDado("inCodBemFinal")."         \n";
        $stSql .= "ORDER BY                                                                                                         \n";
        $stSql .= "    b.cod_bem,hb.timestamp                                                                                        \n";

        return $stSql;
    }

    public function montaRecuperaFichaPatrimonialCompletaHistorico()
    {
        $stSql  ="SELECT                                                                                       \n";
        $stSql .="    h.cod_bem,                                                                               \n";
        $stSql .="    b.num_placa,                                                                             \n";
        $stSql .="    n.nom_natureza,                                                                          \n";
        $stSql .="    g.nom_grupo,                                                                             \n";
        $stSql .="    e.nom_especie,                                                                           \n";
        $stSql .="    a.nom_atributo,                                                                          \n";
        $stSql .="    bae.valor,                                                                               \n";
        $stSql .="    b.descricao as detalhamento,                                                             \n";
        $stSql .="    h.descricao as desc_situacao,                                                            \n";
        $stSql .="    c.nom_cgm,                                                                               \n";
        $stSql .="    a.cod_atributo,                                                                          \n";
        $stSql .="    bc.nota_fiscal,                                                                          \n";
        $stSql .="    n.cod_natureza || '.' || g.cod_grupo || '.' || e.cod_especie as classificacao,           \n";
        $stSql .="    bc.cod_empenho || '/' || bc.exercicio as num_empenho,                                    \n";
        $stSql .="    b.dt_aquisicao,                                                                          \n";
        $stSql .="    b.dt_incorporacao,                                                                       \n";
        $stSql .="    b.vl_bem,                                                                                \n";
        $stSql .="    b.dt_garantia,                                                                           \n";
        $stSql .="    b.identificacao,                                                                         \n";
        $stSql .="    lo.descricao as nom_local,                                                                            \n";
        $stSql .="    org_descricao.descricao as nom_orgao,                                                                           \n";
        $stSql .="    sit.nom_situacao,                                                                        \n";
        $stSql .="    h.timestamp                                                                              \n";
        $stSql .="FROM                                                                                         \n";
        $stSql .="    patrimonio.bem as b                                                                      \n";
        $stSql .="JOIN sw_cgm as c ON                                                                          \n";
        $stSql .="    c.numcgm = b.numcgm                                                                      \n";
        $stSql .="JOIN patrimonio.especie as e ON                                                              \n";
        $stSql .="    e.cod_natureza = b.cod_natureza AND                                                      \n";
        $stSql .="    e.cod_grupo    = b.cod_grupo    AND                                                      \n";
        $stSql .="    e.cod_especie  = b.cod_especie                                                           \n";
        $stSql .="      INNER JOIN patrimonio.grupo as g ON                                                    \n";
        $stSql .="      g.cod_natureza = e.cod_natureza AND                                                    \n";
        $stSql .="      g.cod_grupo    = e.cod_grupo                                                           \n";
        $stSql .="        INNER JOIN patrimonio.natureza as n ON                                               \n";
        $stSql .="        n.cod_natureza = g.cod_natureza                                                      \n";
        $stSql .="    LEFT OUTER JOIN patrimonio.especie_atributo as ea ON                                     \n";
        $stSql .="         e.cod_natureza = ea.cod_natureza and                                                \n";
        $stSql .="         e.cod_grupo    = ea.cod_grupo    and                                                \n";
        $stSql .="         e.cod_especie  = ea.cod_especie                                                     \n";
        $stSql .="         LEFT OUTER JOIN administracao.atributo_dinamico as a ON                              \n";
        $stSql .="              a.cod_atributo = ea.cod_atributo                                               \n";
        $stSql .="         LEFT OUTER JOIN patrimonio.bem_atributo_especie as bae ON                           \n";
        $stSql .="              ea.cod_natureza = bae.cod_natureza and                                         \n";
        $stSql .="              ea.cod_grupo    = bae.cod_grupo    and                                         \n";
        $stSql .="              ea.cod_especie  = bae.cod_especie  and                                         \n";
        $stSql .="              b.cod_bem       = bae.cod_bem                                                  \n";
        $stSql .="LEFT OUTER JOIN patrimonio.historico_bem as h ON                                             \n";
        $stSql .="     h.cod_bem = b.cod_bem                                                                   \n";
        $stSql .="     JOIN organograma.local as lo ON                                                           \n";
        $stSql .="          lo.cod_local        = h.cod_local                                                   \n";
        $stSql .="     JOIN organograma.orgao as org ON                                                         \n";
        $stSql .="          org.cod_orgao        = h.cod_orgao               \n";
        $stSql .="     JOIN organograma.orgao_descricao as org_descricao ON  \n";
        $stSql .="          org.cod_orgao        = org_descricao.cod_orgao               \n";
        $stSql .="    JOIN patrimonio.situacao_bem as sit ON                                                    \n";
        $stSql .="         sit.cod_situacao    = h.cod_situacao                                                 \n";
        $stSql .="                                                                                              \n";
        $stSql .="LEFT OUTER JOIN patrimonio.bem_comprado as bc ON                                              \n";
        $stSql .="    bc.cod_bem = b.cod_bem                                                                    \n";
        $stSql .="LEFT OUTER JOIN patrimonio.bem_baixado as bb ON                                               \n";
        $stSql .="    bb.cod_bem = b.cod_bem                                                                   \n";
        $stSql .="WHERE                                                                                         \n";
        $stSql .="     bb.cod_bem IS NULL                                                                       \n";
        if ($this->getDado("inCodNatureza") > 0 ) {
                $stSql .= "    AND b.cod_natureza= ".$this->getDado("inCodNatureza")."                                                      \n";
        }
        if ($this->getDado("inCodGrupo") > 0) {
            $stSql .= " AND b.cod_grupo=".$this->getDado("inCodGrupo")."                               \n";
        }
        if ($this->getDado("inCodEspecie") > 0) {
            $stSql .= "AND b.cod_especie=".$this->getDado("inCodEspecie")."                            \n";
        }
        if ($this->getDado("inCodOrgao")>0) {
            $stSql .= "     AND org.cod_orgao = ".$this->getDado("inCodOrgao")."                             \n";
        }

        if ($this->getDado("inCodLocal")>0) {
            $stSql .= "     AND lo.cod_local = ".$this->getDado("inCodLocal")."                             \n";
        }
        if ($this->getDado("inCodFornecedor")>0) {
            $stSql .= "     AND b.numcgm = ".$this->getDado("inCodFornecedor")."                             \n";
        }
        if ($this->getDado("stDataFinal")>0) {
            $stSql .= "     AND b.dt_aquisicao BETWEEN to_date('".$this->getDado('stDataInicial')."','dd/mm/yyyy')  \n";
            $stSql .= "     AND to_date ('".$this->getDado('stDataFinal')."','dd/mm/yyyy')                          \n";
        }
        if ($this->getDado("stDataFinalIncorporacao")>0) {
            $stSql .= "     AND b.dt_incorporacao BETWEEN to_date('".$this->getDado('stDataInicialIncorporacao')."','dd/mm/yyyy')  \n";
            $stSql .= "     AND to_date ('".$this->getDado('stDataFinalIncorporacao')."','dd/mm/yyyy')                          \n";
        }
        $stSql .= "    AND b.cod_bem between ".$this->getDado("inCodBemInicial")." and  ".$this->getDado("inCodBemFinal")." \n";
        $stSql .="GROUP BY                                                                                      \n";
        $stSql .="     h.cod_bem,                                                                               \n";
        $stSql .="     h.timestamp,                                                                             \n";
        $stSql .="     b.num_placa,                                                                             \n";
        $stSql .="     n.nom_natureza,                                                                          \n";
        $stSql .="     g.nom_grupo,                                                                             \n";
        $stSql .="     e.nom_especie,                                                                           \n";
        $stSql .="     a.nom_atributo,                                                                          \n";
        $stSql .="     bc.nota_fiscal,                                                                          \n";
        $stSql .="     bae.valor,                                                                               \n";
        $stSql .="     b.descricao,                                                                             \n";
        $stSql .="     h.descricao,                                                                             \n";
        $stSql .="     c.nom_cgm,                                                                               \n";
        $stSql .="     a.cod_atributo,                                                                          \n";
        $stSql .="     n.cod_natureza || '.' || g.cod_grupo || '.' || e.cod_especie,                            \n";
        $stSql .="     bc.cod_empenho || '/' || bc.exercicio,                                                   \n";
        $stSql .="     b.dt_aquisicao,                                                                          \n";
        $stSql .="     b.dt_incorporacao,                                                                       \n";
        $stSql .="     b.vl_bem,                                                                                \n";
        $stSql .="     b.dt_garantia,                                                                           \n";
        $stSql .="     b.identificacao,                                                                         \n";
        $stSql .="     lo.descricao,                                                                            \n";
        $stSql .="     org_descricao.descricao,                                                                           \n";
        $stSql .="     sit.nom_situacao                                                                         \n";
        $stSql .="ORDER BY                                                                                      \n";
        $stSql .="     h.cod_bem,                                                                               \n";
        $stSql .="     h.timestamp desc,                                                                        \n";
        $stSql .="     a.nom_atributo                                                                           \n";

        return $stSql;
    }

    public function montaRecuperaFichaPatrimonialCompleta($stOrdem = null)
    {
        $stSql  = "SELECT                                                                             \n";
        $stSql .="     hb.cod_bem,                                                                    \n";
        $stSql .="     b.num_placa,                                                                   \n";
        $stSql .="     n.nom_natureza,                                                                \n";
        $stSql .="     g.nom_grupo,                                                                   \n";
        $stSql .="     e.nom_especie,                                                                 \n";
        $stSql .="     a.nom_atributo,                                                                \n";
        $stSql .="     bae.valor,                                                            \n";
        $stSql .="     initcap(b.descricao) as detalhamento,                                                   \n";
        $stSql .="     hb.descricao as desc_situacao,                                                 \n";
        $stSql .="     initcap(c.nom_cgm) as nom_cgm,                                                                     \n";
        $stSql .="     a.cod_atributo,                                                                \n";
        $stSql .="     n.cod_natureza || '.' || g.cod_grupo || '.' || e.cod_especie as classificacao, \n";
        $stSql .="     bc.cod_empenho || '/' || bc.exercicio as num_empenho,                          \n";
        $stSql .="     to_char(b.dt_aquisicao,'dd/mm/YYYY') as dt_aquisicao,                          \n";
        $stSql .="     to_char(b.dt_incorporacao,'dd/mm/YYYY') as dt_incorporacao,                    \n";
        $stSql .="     b.vl_bem,                                                                      \n";
        $stSql .="     b.dt_garantia,                                                                 \n";
        $stSql .="     b.identificacao,                                                               \n";
        $stSql .="     bc.nota_fiscal,                                                                \n";
        $stSql .="     lo.descricao as nom_local,                                                                  \n";
        $stSql .="     org_descricao.descricao as nom_orgao,                                                                 \n";
        //$stSql .="     un.nom_unidade,                                                                \n";
        //$stSql .="     de.nom_departamento,                                                           \n";
        //$stSql .="     se.nom_setor,                                                                  \n";
        $stSql .="     sit.nom_situacao,                                                              \n";
        $stSql .="     hb.timestamp                                                                   \n";
        $stSql .= "FROM                                                                               \n";
        $stSql .= "    patrimonio.bem as b                                                            \n";
        $stSql .= "JOIN sw_cgm as c ON                                                                \n";
        $stSql .= "     c.numcgm = b.numcgm                                                           \n";
        $stSql .= "JOIN patrimonio.especie as e ON                                                    \n";
        $stSql .= "     e.cod_natureza = b.cod_natureza AND                                           \n";
        $stSql .= "     e.cod_grupo    = b.cod_grupo    AND                                           \n";
        $stSql .= "     e.cod_especie  = b.cod_especie                                                \n";
        $stSql .= "       INNER JOIN patrimonio.grupo as g ON                                         \n";
        $stSql .= "       g.cod_natureza = e.cod_natureza AND                                         \n";
        $stSql .= "       g.cod_grupo    = e.cod_grupo                                                \n";
        $stSql .= "         INNER JOIN patrimonio.natureza as n ON                                    \n";
        $stSql .= "         n.cod_natureza = g.cod_natureza                                           \n";
        $stSql .= "     LEFT OUTER JOIN patrimonio.especie_atributo as ea ON                          \n";
        $stSql .= "          e.cod_natureza = ea.cod_natureza and                                     \n";
        $stSql .= "          e.cod_grupo    = ea.cod_grupo    and                                     \n";
        $stSql .= "          e.cod_especie  = ea.cod_especie                                          \n";
        $stSql .= "          LEFT OUTER JOIN administracao.atributo_dinamico as a ON                  \n";
        $stSql .= "               a.cod_atributo = ea.cod_atributo   and                              \n";
        $stSql .= "         a.cod_modulo = ea.cod_modulo and                                          \n";
        $stSql .= "         a.cod_cadastro = ea.cod_cadastro                                          \n";
        $stSql .= "          LEFT OUTER JOIN patrimonio.bem_atributo_especie as bae ON                \n";
        $stSql .= "               ea.cod_natureza = bae.cod_natureza and                              \n";
        $stSql .= "               ea.cod_grupo    = bae.cod_grupo    and                              \n";
        $stSql .= "               ea.cod_especie  = bae.cod_especie  and                              \n";
        $stSql .= "               b.cod_bem       = bae.cod_bem                                       \n";
        $stSql .= "                                                                                   \n";
        $stSql .= "                                                                                   \n";

        $stSql .= "LEFT OUTER JOIN (SELECT h.* from patrimonio.historico_bem as h, patrimonio.historico_bem as uhb where h.cod_bem = uhb.cod_bem group by h.cod_bem, h.cod_situacao, h.cod_local,  \n";

        $stSql .= "h.cod_orgao, h.timestamp, h.descricao having h.timestamp = max(uhb.timestamp)) as hb ON             \n";
        $stSql .= "     hb.cod_bem = b.cod_bem                                                        \n";
        $stSql .= "     JOIN organograma.local as lo ON                                             \n";
        //$stSql .= "          lo.ano_exercicio    = hb.ano_exercicio    AND                            \n";
        //$stSql .= "          lo.cod_orgao        = hb.cod_orgao        AND                            \n";
        //$stSql .= "          lo.cod_unidade      = hb.cod_unidade      AND                            \n";
        //$stSql .= "          lo.cod_departamento = hb.cod_departamento AND                            \n";
        //$stSql .= "          lo.cod_setor        = hb.cod_setor        AND                            \n";
        $stSql .= "          lo.cod_local        = hb.cod_local                                       \n";
        $stSql .= "     JOIN organograma.orgao as org ON                                            \n";
        $stSql .= "          org.cod_orgao        = hb.cod_orgao                                      \n";
        $stSql .= "     JOIN organograma.orgao_descricao as org_descricao ON                                            \n";
        $stSql .= "          org.cod_orgao        = org_descricao.cod_orgao                                      \n";
        $stSql .= "     JOIN patrimonio.situacao_bem as sit ON                                        \n";
        $stSql .= "          sit.cod_situacao    = hb.cod_situacao                                    \n";
        $stSql .= "                                                                                   \n";
        $stSql .= "LEFT OUTER JOIN patrimonio.bem_comprado as bc ON                                   \n";
        $stSql .= "     bc.cod_bem = b.cod_bem                                                        \n";
        $stSql .= "LEFT OUTER JOIN patrimonio.bem_baixado as bb ON                                    \n";
        $stSql .= "     bb.cod_bem = b.cod_bem                                                        \n";
        $stSql .= "WHERE                                                                               \n";
        $stSql .= "     bb.cod_bem is NULL                                                             \n";
        $stSql .= "     AND    b.cod_bem between ".$this->getDado("inCodBemInicial")." and  ".$this->getDado("inCodBemFinal")." \n";
        if ($this->getDado("inCodNatureza") > 0 ) {
            $stSql .= "    AND b.cod_natureza= ".$this->getDado("inCodNatureza")."                                                      \n";
        }
        if ($this->getDado("inCodGrupo") > 0) {
            $stSql .= " AND b.cod_grupo=".$this->getDado("inCodGrupo")."                               \n";
        }
        if ($this->getDado("inCodEspecie") > 0) {
            $stSql .= "AND b.cod_especie=".$this->getDado("inCodEspecie")."                            \n";
        }
        if ($this->getDado("inCodOrgao")>0) {
            $stSql .= "     AND hb.cod_orgao = ".$this->getDado("inCodOrgao")."                             \n";
        }

        if ($this->getDado("inCodLocal")>0) {
            $stSql .= "     AND lo.cod_local = ".$this->getDado("inCodLocal")."                             \n";
        }
        if ($this->getDado("inCodFornecedor")>0) {
            $stSql .= "     AND b.numcgm = ".$this->getDado("inCodFornecedor")."                             \n";
        }
        if ($this->getDado("stDataFinal")>0) {
            $stSql .= "     AND b.dt_aquisicao BETWEEN to_date('".$this->getDado('stDataInicial')."','dd/mm/yyyy')  \n";
            $stSql .= "     AND to_date ('".$this->getDado('stDataFinal')."','dd/mm/yyyy')                          \n";
        }
        if ($this->getDado("stDataFinalIncorporacao")>0) {
            $stSql .= "     AND b.dt_incorporacao BETWEEN to_date('".$this->getDado('stDataInicialIncorporacao')."','dd/mm/yyyy')  \n";
            $stSql .= "     AND to_date ('".$this->getDado('stDataFinalIncorporacao')."','dd/mm/yyyy')                          \n";
        }
        if ( $this->getDado("stNumPlacaInicial") != "" ) {
            $stNumPlacaFinal = $this->getDado("stNumPlacaFinal") ? $this->getDado("stNumPlacaFinal") : $this->getDado("stNumPlacaInicial");
            $stSql .= "     AND b.num_placa between ".$this->getDado("stNumPlacaInicial")." and ".$stNumPlacaFinal." \n";
        }

        $stSql .= "GROUP BY                                                                            \n";
        $stSql .= "     hb.cod_bem,                                                                    \n";
        $stSql .= "     hb.timestamp,                                                                  \n";
        $stSql .= "     b.num_placa,                                                                   \n";
        $stSql .= "     n.nom_natureza,                                                                \n";
        $stSql .= "     g.nom_grupo,                                                                   \n";
        $stSql .= "     e.nom_especie,                                                                 \n";
        $stSql .= "     a.nom_atributo,                                                                \n";
        $stSql .= "     bae.valor,                                                            \n";
        $stSql .= "     b.descricao,                                                                   \n";
        $stSql .= "     hb.descricao,                                                                  \n";
        $stSql .= "     c.nom_cgm,                                                                     \n";
        $stSql .= "     a.cod_atributo,                                                                \n";
        $stSql .= "     n.cod_natureza || '.' || g.cod_grupo || '.' || e.cod_especie,                  \n";
        $stSql .= "     bc.cod_empenho || '/' || bc.exercicio,                                         \n";
        $stSql .= "     b.dt_aquisicao,                                                                \n";
        $stSql .= "     b.dt_incorporacao,                                                             \n";
        $stSql .= "     b.vl_bem,                                                                      \n";
        $stSql .= "     b.dt_garantia,                                                                 \n";
        $stSql .= "     b.identificacao,                                                               \n";
        $stSql .= "     bc.nota_fiscal,                                                                \n";
        $stSql .= "     lo.descricao,                                                                  \n";
        $stSql .= "     org_descricao.descricao,                                                      \n";
        $stSql .= "     sit.nom_situacao                                                               \n";
        $stSql .= "ORDER BY                                                                            \n";
        if ($stOrdem) {
            $stSql .= $stOrdem ;
        } else {
            $stSql .= "     hb.cod_bem,                                                                    \n";
            $stSql .= "     hb.timestamp desc,                                                             \n";
            $stSql .= "     a.nom_atributo                                                                 \n";
        }

        return $stSql;
    }

    public function montaRecuperaListaPatrimonialCompleta($stOrdem = null)
    {
        $stSql  = "SELECT                                                                             \n";
        $stSql .="     hb.cod_bem,                                                                    \n";
        $stSql .="     b.num_placa,                                                                   \n";
        $stSql .="     initcap(b.descricao) as detalhamento,                                                   \n";
        $stSql .="     hb.descricao as desc_situacao,                                                 \n";
        $stSql .="     initcap(c.nom_cgm) as nom_cgm,                                                                     \n";
        $stSql .="     bc.cod_empenho || '/' || bc.exercicio as num_empenho,                          \n";
        $stSql .="     to_char(b.dt_aquisicao,'dd/mm/YYYY') as dt_aquisicao,                          \n";
        $stSql .="     to_char(b.dt_incorporacao,'dd/mm/YYYY') as dt_incorporacao,                          \n";
        $stSql .="     b.vl_bem,                                                                      \n";
        $stSql .="     b.dt_garantia,                                                                 \n";
        $stSql .="     b.identificacao,                                                               \n";
        $stSql .="     bc.nota_fiscal,                                                                \n";
        $stSql .="     lo.descricao as nom_local,                                                                  \n";
        $stSql .="     hb.timestamp                                                                   \n";
        $stSql .= "FROM                                                                               \n";
        $stSql .= "    patrimonio.bem as b                                                            \n";
        $stSql .= "JOIN sw_cgm as c ON                                                                \n";
        $stSql .= "     c.numcgm = b.numcgm                                                           \n";
        $stSql .= "                                                                                   \n";
        $stSql .= "LEFT OUTER JOIN (SELECT h.* from patrimonio.historico_bem as h, patrimonio.historico_bem as uhb where h.cod_bem = uhb.cod_bem group by h.cod_bem, h.cod_situacao, h.cod_local, \n";
        $stSql .= "h.cod_orgao, h.timestamp, h.descricao having h.timestamp = max(uhb.timestamp)) as hb ON \n";
        $stSql .= "     hb.cod_bem = b.cod_bem                                                        \n";
        $stSql .= "     JOIN organograma.local as lo ON                                             \n";
        $stSql .= "          lo.cod_local        = hb.cod_local                                       \n";

        $stSql .= "     JOIN organograma.orgao as org ON         \n";
        $stSql .= "          org.cod_orgao        = hb.cod_orgao   \n";
        $stSql .= "                                                \n";
        $stSql .= "LEFT OUTER JOIN patrimonio.bem_comprado as bc ON                                   \n";
        $stSql .= "     bc.cod_bem = b.cod_bem                                                        \n";
        $stSql .= "LEFT OUTER JOIN patrimonio.bem_baixado as bb ON                                    \n";
        $stSql .= "     bb.cod_bem = b.cod_bem                                                        \n";
        $stSql .= "WHERE                                                                               \n";
        $stSql .= "     bb.cod_bem is NULL                                                             \n";
        $stSql .= "     AND    b.cod_bem between ".$this->getDado("inCodBemInicial")." and  ".$this->getDado("inCodBemFinal")." \n";
        if ($this->getDado("inCodNatureza") > 0 ) {
            $stSql .= "    AND b.cod_natureza= ".$this->getDado("inCodNatureza")."                                                      \n";
        }
        if ($this->getDado("inCodGrupo") > 0) {
            $stSql .= " AND b.cod_grupo=".$this->getDado("inCodGrupo")."                               \n";
        }
        if ($this->getDado("inCodEspecie") > 0) {
            $stSql .= "AND b.cod_especie=".$this->getDado("inCodEspecie")."                            \n";
        }
        if ($this->getDado("inCodOrgao")>0) {
            $stSql .= "     AND hb.cod_orgao = ".$this->getDado("inCodOrgao")."                             \n";
        }

        if ($this->getDado("inCodLocal")>0) {
            $stSql .= "     AND lo.cod_local = ".$this->getDado("inCodLocal")."                             \n";
        }
        if ($this->getDado("inCodFornecedor")>0) {
            $stSql .= "     AND b.numcgm = ".$this->getDado("inCodFornecedor")."                             \n";
        }
        if ($this->getDado("stDataFinal")>0) {
            $stSql .= "     AND b.dt_aquisicao BETWEEN to_date('".$this->getDado('stDataInicial')."','dd/mm/yyyy')  \n";
            $stSql .= "     AND to_date ('".$this->getDado('stDataFinal')."','dd/mm/yyyy')                          \n";
        }
        if ( $this->getDado("stNumPlacaInicial") != "" ) {
            $stNumPlacaFinal = $this->getDado("stNumPlacaFinal") ? $this->getDado("stNumPlacaFinal") : $this->getDado("stNumPlacaInicial");
            $stSql .= "     AND b.num_placa between ".$this->getDado("stNumPlacaInicial")." and ".$stNumPlacaFinal." \n";
        }

        $stSql .= "GROUP BY                                                                            \n";
        $stSql .= "     hb.cod_bem,                                                                    \n";
        $stSql .= "     hb.timestamp,                                                                  \n";
        $stSql .= "     b.num_placa,                                                                   \n";
        $stSql .= "     b.descricao,                                                                   \n";
        $stSql .= "     hb.descricao,                                                                  \n";
        $stSql .= "     c.nom_cgm,                                                                     \n";
        $stSql .= "     bc.cod_empenho || '/' || bc.exercicio,                                         \n";
        $stSql .= "     b.dt_aquisicao,                                                                \n";
        $stSql .= "     b.dt_incorporacao,                                                             \n";
        $stSql .= "     b.vl_bem,                                                                      \n";
        $stSql .= "     b.dt_garantia,                                                                 \n";
        $stSql .= "     b.identificacao,                                                               \n";
        $stSql .= "     bc.nota_fiscal,                                                                \n";
        $stSql .= "     lo.descricao                                                                   \n";
        $stSql .= "ORDER BY                                                                            \n";
        if ($stOrdem) {
            $stSql .= $stOrdem ;
        } else {
            $stSql .= "     hb.cod_bem,                                                                    \n";
            $stSql .= "     hb.timestamp desc,                                                             \n";
        }

        return $stSql;
    }

    public function montaRecuperaRelatorioBaixa()
    {
        if ( $this->getDado("inCodOrdem") == 1 ) {
             $stOrder = "ORDER BY B.cod_bem";
         } elseif ($this->getDado("inCodOrdem") == 2) {
             $stOrder = " ORDER BY UPPER(descricao)";
         } elseif ($this->getDado("inCodOrdem") == 3) {
             $stOrder = "ORDER BY B.dt_aquisicao";
         }

         if ($this->getDado("inCodAtributo") > 0 ) {

            $filtroAtributo = "inner JOIN     patrimonio.bem_atributo_especie as BAE ON
                                   BAE.cod_atributo = ".$this->getDado("inCodAtributo")."
                                   AND BAE.cod_bem = B.cod_bem
                                   AND BAE.cod_especie = B.cod_especie
                                   AND BAE.cod_grupo = B.cod_grupo
                                   AND BAE.cod_natureza = B.cod_natureza
                             inner join   patrimonio.especie_atributo as EA on
                                       BAE.cod_atributo = EA.cod_atributo
                                   AND BAE.cod_especie = EA.cod_especie
                                   AND BAE.cod_grupo = EA.cod_grupo
                                   AND BAE.cod_natureza = EA.cod_natureza
                             LEFT JOIN   administracao.atributo_dinamico as AP  on
                                   EA.cod_atributo = AP.cod_atributo";

            $filtroSelect = "BAE.valor as atributo,
                            upper(AP.nom_atributo) as nom_atributo,";
            $filtroGroup  = "atributo,
                            nom_atributo,";

        } else {
            $filtroAtributo = "";
            $filtroSelect = "";
            $filtroTabela = "";
            $filtroGroup = "";
        }
        $stSql  ="SELECT                                           \n";
        $stSql .="     B.cod_bem as codigo,                        \n";
        $stSql .="     B.descricao,                                \n";
        $stSql .="     ".$filtroSelect."                           \n";
        $stSql .="     B.dt_aquisicao as aquisicao,                \n";
        $stSql .="     B.vl_bem as valor,                          \n";
        $stSql .="     BB.dt_baixa as baixa                        \n";
        $stSql .=" FROM                                            \n";
        $stSql .="     patrimonio.bem as B                         \n";
        $stSql .="inner join  patrimonio.bem_baixado as BB on           \n";
        $stSql .="         BB.cod_bem = B.cod_bem                  \n";
        $stSql .="     AND BB.dt_baixa BETWEEN TO_DATE('".$this->getDado("stDataInicial")."','dd/mm/yyyy')  \n";
        $stSql .="     AND TO_DATE('".$this->getDado("stDataFinal")."','dd/mm/yyyy')                        \n";
        $stSql .="     ".$filtroAtributo."                         \n";
        $stSql .=" ".$stOrder."                                    \n";

        return $stSql ;
    }

    public function montaRecuperaMax()
    {
        $stSql  ="SELECT                \n";
        $stSql .="    max(cod_bem)      \n";
        $stSql .="FROM                  \n";
        $stSql .="  patrimonio.bem  \n";

        return $stSql;
    }

    public function recuperaRelacionamento(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaRelacionamento",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaRelacionamento()
    {
        $stSql = "
            SELECT bem.cod_bem
                 , bem.cod_natureza
                 , bem.cod_grupo
                 , bem.cod_especie
                 , bem.numcgm AS num_fornecedor
                 , fornecedor.nom_cgm as nom_fornecedor
                 , CASE WHEN (SELECT count(cod_bem) FROM patrimonio.inventario_historico_bem WHERE inventario_historico_bem.cod_bem = bem.cod_bem) > 0
                              THEN true
                              ELSE false
                       END AS inventario
                 , bem.descricao
                 , bem.descricao AS descricao_padrao
                 , bem.detalhamento
                 , TO_CHAR(bem.dt_aquisicao,'dd/mm/yyyy') AS dt_aquisicao
                 , TO_CHAR(bem.dt_incorporacao,'dd/mm/yyyy') AS dt_incorporacao
                 , depreciacao.dt_depreciacao
                 , TO_CHAR(bem.dt_garantia,'dd/mm/yyyy') AS dt_garantia
                 , bem.vl_bem
                 , bem.vl_depreciacao
                 , bem.identificacao
                 , bem.num_placa
                 , bem.vida_util
                 , bem_comprado.exercicio
                 , bem_comprado.cod_entidade
                 , bem_comprado.cod_empenho
                 , bem_comprado.nota_fiscal
                 , bem_comprado.num_orgao
                 , bem_comprado.num_unidade
                 , TO_CHAR(bem_comprado.data_nota_fiscal,'dd/mm/yyyy') AS data_nota_fiscal
                 , bem_comprado.caminho_nf
                 , bem_responsavel.numcgm as num_responsavel
                 , bem_responsavel.nom_cgm as nom_responsavel
                 , bem.depreciavel
                 , bem.depreciacao_acelerada
                 , bem.quota_depreciacao_anual
                 , bem.quota_depreciacao_anual_acelerada
                 , TO_CHAR(bem_responsavel.dt_inicio,'dd/mm/yyyy') AS dt_inicio
                 , bem_marca.cod_marca
                 , bem_marca.descricao as nome_marca
                 , situacao_bem.cod_situacao
                 , situacao_bem.nom_situacao
                 , historico_bem.cod_local
                 , historico_bem.cod_orgao
                 , historico_bem.timestamp::date as data_historico_bem
                 , to_char(now()::date,'YYYY') as ano_exercicio
                 , historico_bem.descricao AS historico_descricao
                 , apolice.cod_apolice
                 , apolice.numcgm as num_seguradora
                 , bem_comprado_tipo_documento_fiscal.cod_tipo_documento_fiscal
                 , natureza.nom_natureza
                 , tipo_natureza.codigo
                 , tipo_natureza.descricao AS descricao_natureza
                 
              FROM patrimonio.bem
              
         LEFT JOIN patrimonio.bem_comprado
                ON bem_comprado.cod_bem = bem.cod_bem
         
         LEFT JOIN tceal.bem_comprado_tipo_documento_fiscal
                ON bem_comprado_tipo_documento_fiscal.cod_bem = bem_comprado.cod_bem
         
          LEFT JOIN ( SELECT historico_bem.cod_bem
                          , historico_bem.cod_local
                          , historico_bem.cod_situacao
                          , historico_bem.cod_orgao
                          , historico_bem.descricao
                          , historico_bem.timestamp
                       FROM patrimonio.historico_bem
                INNER JOIN (  SELECT  cod_bem
                                   ,  MAX(timestamp) AS timestamp
                                FROM  patrimonio.historico_bem
                            GROUP BY  cod_bem
                            ) AS historico_bem_max
                         ON historico_bem.cod_bem = historico_bem_max.cod_bem
                        AND historico_bem.timestamp   = historico_bem_max.timestamp
                    )   AS historico_bem
                ON  historico_bem.cod_bem = bem.cod_bem
         
         LEFT JOIN ( SELECT apolice_bem.cod_bem
                          , apolice_bem.cod_apolice
                          , apolice_bem.timestamp
                       FROM patrimonio.apolice_bem
                 INNER JOIN ( SELECT cod_bem
                                   , MAX(timestamp) AS timestamp
                                FROM patrimonio.apolice_bem
                            GROUP BY cod_bem
                            ) AS apolice_bem_max
                         ON apolice_bem_max.cod_bem = apolice_bem.cod_bem
                        AND apolice_bem_max.timestamp = apolice_bem.timestamp
                   ) AS apolice_bem
                ON apolice_bem.cod_bem = bem.cod_bem
        
         LEFT JOIN patrimonio.apolice
                ON apolice.cod_apolice = apolice_bem.cod_apolice
       
        LEFT JOIN patrimonio.situacao_bem
                ON situacao_bem.cod_situacao = historico_bem.cod_situacao
    
    LEFT JOIN ( SELECT bem_responsavel.cod_bem
                          , bem_responsavel.numcgm
                          , bem_responsavel.dt_inicio
                          , sw_cgm.nom_cgm
                       FROM patrimonio.bem_responsavel
                 INNER JOIN sw_cgm
                         ON sw_cgm.numcgm = bem_responsavel.numcgm

                 INNER JOIN ( SELECT cod_bem
                                   , MAX(dt_inicio) AS dt_inicio
                                   , MAX(timestamp) AS timestamp
                                FROM patrimonio.bem_responsavel
                            GROUP BY cod_bem
                            ) AS bem_responsavel_max
                         ON bem_responsavel_max.cod_bem = bem_responsavel.cod_bem
                        AND bem_responsavel_max.timestamp = bem_responsavel.timestamp

                   ) AS bem_responsavel
                ON bem_responsavel.cod_bem = bem.cod_bem
         
         LEFT JOIN sw_cgm AS fornecedor
                ON fornecedor.numcgm = bem.numcgm
         
         LEFT JOIN ( SELECT bem_marca.cod_bem
                          , bem_marca.cod_marca
                          , marca.descricao
                       FROM patrimonio.bem_marca
                 INNER JOIN almoxarifado.marca
                         ON bem_marca.cod_marca = marca.cod_marca
                   ) AS bem_marca
                ON bem.cod_bem = bem_marca.cod_bem
         
         LEFT JOIN ( SELECT depreciacao.cod_bem
                          , TO_CHAR(depreciacao.dt_depreciacao, 'DD/MM/YYYY') AS dt_depreciacao
                       FROM patrimonio.depreciacao

		          LEFT JOIN patrimonio.depreciacao_anulada
		  	             ON depreciacao.cod_bem         = depreciacao_anulada.cod_bem
		                AND depreciacao.cod_depreciacao = depreciacao_anulada.cod_depreciacao
		                AND depreciacao.timestamp       = depreciacao_anulada.timestamp
		  	        
		              WHERE depreciacao_anulada.cod_depreciacao IS NULL
		                AND depreciacao.timestamp = ( SELECT max(depreciacao_interna.timestamp)
                                                        FROM patrimonio.depreciacao AS depreciacao_interna
                                                       WHERE depreciacao_interna.cod_bem = depreciacao.cod_bem
                                                         AND SUBSTRING(depreciacao_interna.competencia, 1,4) = '".Sessao::getExercicio()."' )
	            ) AS depreciacao 
	           ON depreciacao.cod_bem = bem.cod_bem
        
       INNER JOIN patrimonio.especie
	           ON especie.cod_especie  = bem.cod_especie
	          AND especie.cod_grupo    = bem.cod_grupo
	          AND especie.cod_natureza = bem.cod_natureza

       INNER JOIN patrimonio.grupo
	           ON grupo.cod_grupo    = especie.cod_grupo
	          AND grupo.cod_natureza = especie.cod_natureza
       
       INNER JOIN patrimonio.natureza
               ON natureza.cod_natureza = grupo.cod_natureza
       
       INNER JOIN patrimonio.tipo_natureza
	           ON tipo_natureza.codigo = natureza.cod_tipo
               
            WHERE ";
        if ( $this->getDado('cod_bem') ) {
            $stSql.= " bem.cod_bem = ".$this->getDado('cod_bem')."   AND ";
        }

        return substr($stSql,0,-6);
    }

    public function recuperaRelacionamentoInventario(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaRelacionamentoInventario",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaRelacionamentoInventario()
    {
        $stSql = "SELECT bem.cod_bem
                       , bem.cod_natureza
                       , bem.cod_grupo
                       , bem.cod_especie
                       , bem.numcgm AS num_fornecedor
                       , fornecedor.nom_cgm as nom_fornecedor
                       , CASE WHEN (SELECT count(cod_bem) FROM patrimonio.inventario_historico_bem WHERE inventario_historico_bem.cod_bem = bem.cod_bem) > 0
                              THEN bem.descricao||' - ESTÁ EM INVENTÁRIO'
                              ELSE bem.descricao
                       END AS descricao
                       , bem.detalhamento
                       , TO_CHAR(bem.dt_aquisicao,'dd/mm/yyyy') AS dt_aquisicao
                       , TO_CHAR(bem.dt_incorporacao,'dd/mm/yyyy') AS dt_incorporacao
                       , TO_CHAR(bem.dt_depreciacao,'dd/mm/yyyy') AS dt_depreciacao
                       , TO_CHAR(bem.dt_garantia,'dd/mm/yyyy') AS dt_garantia
                       , bem.vl_bem
                       , bem.vl_depreciacao
                       , bem.identificacao
                       , bem.num_placa
                       , bem_comprado.exercicio
                       , bem_comprado.cod_entidade
                       , bem_comprado.cod_empenho
                       , bem_comprado.nota_fiscal
                       , bem_responsavel.numcgm as num_responsavel
                       , bem_responsavel.nom_cgm as nom_responsavel
                       , TO_CHAR(bem_responsavel.dt_inicio,'dd/mm/yyyy') AS dt_inicio
                       , bem_marca.cod_marca
                       , bem_marca.descricao as nome_marca
                       , situacao_bem.cod_situacao
                       , situacao_bem.nom_situacao
                       , historico_bem.cod_local
                       , historico_bem.cod_orgao
                       , historico_bem.timestamp::date as data_historico_bem
                       , to_char(now()::date,'YYYY') as ano_exercicio
                       , historico_bem.descricao AS historico_descricao
                       , apolice.cod_apolice
                       , apolice.numcgm as num_seguradora
                    FROM patrimonio.bem
               LEFT JOIN patrimonio.bem_comprado
                      ON bem_comprado.cod_bem = bem.cod_bem
               LEFT JOIN ( SELECT historico_bem.cod_bem
                                , historico_bem.cod_local
                                , historico_bem.cod_situacao
                                , historico_bem.cod_orgao
                                , historico_bem.descricao
                                , historico_bem.timestamp
                            FROM patrimonio.historico_bem
                      INNER JOIN (  SELECT  cod_bem
                                          , MAX(timestamp) AS timestamp
                                        FROM  patrimonio.historico_bem
                                    GROUP BY  cod_bem
                                ) AS historico_bem_max
                              ON historico_bem.cod_bem = historico_bem_max.cod_bem
                             AND historico_bem.timestamp   = historico_bem_max.timestamp
                        )   AS historico_bem
                      ON  historico_bem.cod_bem = bem.cod_bem
               LEFT JOIN ( SELECT apolice_bem.cod_bem
                                , apolice_bem.cod_apolice
                                , apolice_bem.timestamp
                            FROM patrimonio.apolice_bem
                      INNER JOIN ( SELECT cod_bem
                                        , MAX(timestamp) AS timestamp
                                    FROM patrimonio.apolice_bem
                                GROUP BY cod_bem
                                ) AS apolice_bem_max
                              ON apolice_bem_max.cod_bem = apolice_bem.cod_bem
                             AND apolice_bem_max.timestamp = apolice_bem.timestamp
                        ) AS apolice_bem
                      ON apolice_bem.cod_bem = bem.cod_bem
               LEFT JOIN patrimonio.apolice
                      ON apolice.cod_apolice = apolice_bem.cod_apolice
               LEFT JOIN patrimonio.situacao_bem
                      ON situacao_bem.cod_situacao = historico_bem.cod_situacao
               LEFT JOIN ( SELECT bem_responsavel.cod_bem
                                , bem_responsavel.numcgm
                                , bem_responsavel.dt_inicio
                                , sw_cgm.nom_cgm
                            FROM patrimonio.bem_responsavel
                      INNER JOIN sw_cgm
                              ON sw_cgm.numcgm = bem_responsavel.numcgm
                      INNER JOIN ( SELECT cod_bem
                                        , MAX(dt_inicio) AS dt_inicio
                                        , MAX(timestamp) AS timestamp
                                      FROM patrimonio.bem_responsavel
                                  GROUP BY cod_bem
                                  ) AS bem_responsavel_max
                              ON bem_responsavel_max.cod_bem = bem_responsavel.cod_bem
                             AND bem_responsavel_max.timestamp = bem_responsavel.timestamp
                        ) AS bem_responsavel
                      ON bem_responsavel.cod_bem = bem.cod_bem
               LEFT JOIN sw_cgm AS fornecedor
                      ON fornecedor.numcgm = bem.numcgm
               LEFT JOIN ( SELECT bem_marca.cod_bem
                                , bem_marca.cod_marca
                                , marca.descricao
                            FROM patrimonio.bem_marca
                      INNER JOIN almoxarifado.marca
                              ON bem_marca.cod_marca = marca.cod_marca
                        ) AS bem_marca
                      ON bem.cod_bem = bem_marca.cod_bem
                   WHERE ";
        if ( $this->getDado('cod_bem') ) {
            $stSql.= " bem.cod_bem = ".$this->getDado('cod_bem')."   AND ";
        }

        return substr($stSql,0,-6);
    }

    public function recuperaRelacionamentoTransferencia(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
         return $this->executaRecupera("montaRecuperaRelacionamentoTransferencia",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaRelacionamentoTransferencia()
    {
        $stSql.= "
            SELECT bem.cod_bem
                 , bem.num_placa
                 , bem.descricao
                 , historico_bem.cod_local
                 , historico_bem.cod_situacao
                 , historico_bem.cod_orgao
                 , to_char(now()::date,'YYYY') as ano_exercicio
                 , historico_bem.descricao as historico_descricao
                 , CASE WHEN( bem_baixado.cod_bem IS NULL )
                        THEN 'Não'
                        ELSE 'Sim'
                   END AS baixado
              FROM patrimonio.bem
         LEFT JOIN ( SELECT historico_bem.cod_bem
                          , historico_bem.cod_situacao
                          , historico_bem.cod_local
                          , historico_bem.cod_orgao
                          , historico_bem.descricao
                          , historico_bem.timestamp
                       FROM patrimonio.historico_bem
                INNER JOIN (  SELECT  cod_bem
                                   ,  MAX(timestamp) AS timestamp
                                FROM  patrimonio.historico_bem
                            GROUP BY  cod_bem
                            ) AS historico_bem_max
                         ON historico_bem.cod_bem = historico_bem_max.cod_bem
                        AND historico_bem.timestamp   = historico_bem_max.timestamp
                   )   AS historico_bem
                ON historico_bem.cod_bem = bem.cod_bem
         LEFT JOIN patrimonio.bem_baixado
                ON bem_baixado.cod_bem = bem.cod_bem

         LEFT JOIN ( SELECT bem_responsavel.cod_bem
                          , bem_responsavel.numcgm
                          , bem_responsavel.dt_inicio AS dt_inicio
                          , sw_cgm.nom_cgm
                       FROM patrimonio.bem_responsavel
                 INNER JOIN ( SELECT cod_bem
                                   , MAX(timestamp) AS timestamp
                                FROM patrimonio.bem_responsavel
                            GROUP BY cod_bem
                            ) AS bem_responsavel_max
                         ON bem_responsavel_max.cod_bem = bem_responsavel.cod_bem
                        AND bem_responsavel_max.timestamp = bem_responsavel.timestamp
                 INNER JOIN sw_cgm
                         ON sw_cgm.numcgm = bem_responsavel.numcgm
                   ) AS bem_responsavel
                ON bem_responsavel.cod_bem = bem.cod_bem

        ";

        return $stSql;
    }

    public function recuperaRelacionamentoAnalitico(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
         return $this->executaRecupera("montaRecuperaRelacionamentoAnalitico",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaRelacionamentoAnalitico()
    {
        $stSql = "
            SELECT bem.cod_bem
                 , bem.cod_natureza
                 , natureza.nom_natureza
                 , bem.cod_grupo
                 , grupo.nom_grupo
                 , bem.cod_especie
                 , especie.nom_especie
                 , bem.numcgm AS num_fornecedor
                 , fornecedor.nom_cgm as nom_fornecedor
                 , bem.descricao
                 , bem.detalhamento
                 , TO_CHAR(bem.dt_aquisicao,'dd/mm/yyyy') AS dt_aquisicao
                 , TO_CHAR(bem.dt_incorporacao,'dd/mm/yyyy') AS dt_incorporacao
                 , TO_CHAR(bem.dt_depreciacao,'dd/mm/yyyy') AS dt_depreciacao
                 , TO_CHAR(bem.dt_garantia,'dd/mm/yyyy') AS dt_garantia
                 , bem.vl_bem
                 , bem.vl_depreciacao
                 , bem.identificacao
                 , bem.num_placa
                 , bem.vida_util
                 , bem_comprado.exercicio
                 , bem_comprado.cod_entidade
                 , entidade_cgm.nom_cgm AS nom_entidade
                 , bem_comprado.num_orgao AS num_orgao_a
                 , orgao.nom_orgao AS nom_orgao_a
                 , bem_comprado.num_unidade AS num_unidade_a
                 , unidade.nom_unidade AS nom_unidade_a
                 , bem_comprado.cod_empenho
                 , bem_comprado.nota_fiscal
                 , bem_comprado.caminho_nf
                 , bem_responsavel.numcgm as num_responsavel
                 , bem_responsavel.nom_cgm as nom_responsavel
                 , TO_CHAR(bem_responsavel.dt_inicio,'dd/mm/yyyy') AS dt_inicio
                 , bem_marca.cod_marca
                 , bem_marca.descricao as nome_marca
                 , situacao_bem.cod_situacao
                 , situacao_bem.nom_situacao
                 , historico_bem.cod_local
                 , historico_bem.nom_local
                 , historico_bem.cod_orgao
                 , historico_bem.nom_orgao
                 , historico_bem.descricao AS historico_descricao
                 , apolice.num_apolice
                 , TO_CHAR(apolice.dt_vencimento,'dd/mm/yyyy') AS vencimento_apolice
                 , apolice.numcgm as num_seguradora
                 , seguradora.nom_cgm AS nom_seguradora
                 , TO_CHAR(bem_baixado.dt_baixa,'dd/mm/yyyy') AS dt_baixa
                 , bem_baixado.motivo
                 , (
                     SELECT orgao FROM organograma.vw_orgao_nivel WHERE cod_orgao = historico_bem.cod_orgao ORDER BY nivel DESC LIMIT 1
                   ) as orgao_resumido
                 , orgao.num_orgao AS orgao_num_orgao
                 , orgao.nom_orgao AS orgao_nom_orgao
                 , unidade.num_unidade AS unidade_num_unidade
                 , unidade.nom_unidade AS unidade_nom_unidade
              FROM patrimonio.bem
        INNER JOIN patrimonio.natureza
                ON natureza.cod_natureza = bem.cod_natureza
        INNER JOIN patrimonio.grupo
                ON grupo.cod_grupo = bem.cod_grupo
               AND grupo.cod_natureza = bem.cod_natureza
        INNER JOIN patrimonio.especie
                ON especie.cod_especie = bem.cod_especie
               AND especie.cod_grupo = bem.cod_grupo
               AND especie.cod_natureza = bem.cod_natureza
         LEFT JOIN patrimonio.bem_comprado
                ON bem_comprado.cod_bem = bem.cod_bem
         LEFT JOIN orcamento.unidade
                ON unidade.exercicio   = bem_comprado.exercicio
               AND unidade.num_orgao   = bem_comprado.num_orgao
               AND unidade.num_unidade = bem_comprado.num_unidade
         LEFT JOIN orcamento.orgao
                ON orgao.exercicio = unidade.exercicio
               AND orgao.num_orgao = unidade.num_orgao
         LEFT JOIN orcamento.entidade
                ON entidade.cod_entidade = bem_comprado.cod_entidade
               AND entidade.exercicio = bem_comprado.exercicio
         LEFT JOIN sw_cgm as entidade_cgm
                ON entidade_cgm.numcgm = entidade.numcgm
          LEFT JOIN ( SELECT historico_bem.cod_bem
                           , historico_bem.cod_local
                           , local.descricao as nom_local
                           , historico_bem.cod_situacao
                           , historico_bem.cod_orgao
                           , orgao_descricao.descricao as nom_orgao
                           , historico_bem.descricao
                           , historico_bem.timestamp
                        FROM patrimonio.historico_bem
                  INNER JOIN (  SELECT  cod_bem
                                     ,  MAX(timestamp) AS timestamp
                                  FROM  patrimonio.historico_bem
                              GROUP BY  cod_bem
                             ) AS historico_bem_max
                          ON historico_bem.cod_bem = historico_bem_max.cod_bem
                         AND historico_bem.timestamp   = historico_bem_max.timestamp
                  INNER JOIN organograma.orgao
                          ON orgao.cod_orgao = historico_bem.cod_orgao
                  INNER JOIN organograma.orgao_descricao
                          ON orgao.cod_orgao = orgao_descricao.cod_orgao

         INNER JOIN ( SELECT cod_orgao,
                     MAX(timestamp) AS timestamp
                 FROM organograma.orgao_descricao
                 GROUP BY cod_orgao
               ) as max_orgao_descricao
                          ON max_orgao_descricao.cod_orgao = orgao_descricao.cod_orgao
                          AND max_orgao_descricao.timestamp = orgao_descricao.timestamp

                  INNER JOIN organograma.local
                          ON local.cod_local = historico_bem.cod_local
                    )   AS historico_bem
                ON  historico_bem.cod_bem = bem.cod_bem
         LEFT JOIN ( SELECT apolice_bem.cod_bem
                          , apolice_bem.cod_apolice
                          , apolice_bem.timestamp
                       FROM patrimonio.apolice_bem
                 INNER JOIN ( SELECT cod_bem
                                   , MAX(timestamp) AS timestamp
                                FROM patrimonio.apolice_bem
                            GROUP BY cod_bem
                            ) AS apolice_bem_max
                         ON apolice_bem_max.cod_bem = apolice_bem.cod_bem
                        AND apolice_bem_max.timestamp = apolice_bem.timestamp
                   ) AS apolice_bem
                ON apolice_bem.cod_bem = bem.cod_bem
         LEFT JOIN patrimonio.apolice
                ON apolice.cod_apolice = apolice_bem.cod_apolice
         LEFT JOIN sw_cgm AS seguradora
                ON seguradora.numcgm = apolice.numcgm
         LEFT JOIN patrimonio.situacao_bem
                ON situacao_bem.cod_situacao = historico_bem.cod_situacao
         LEFT JOIN ( SELECT bem_responsavel.cod_bem
                          , bem_responsavel.numcgm
                          , bem_responsavel.dt_inicio AS dt_inicio
                          , sw_cgm.nom_cgm
                       FROM patrimonio.bem_responsavel
                 INNER JOIN ( SELECT cod_bem
                                   , MAX(timestamp) AS timestamp
                                FROM patrimonio.bem_responsavel
                            GROUP BY cod_bem
                            ) AS bem_responsavel_max
                         ON bem_responsavel_max.cod_bem = bem_responsavel.cod_bem
                        AND bem_responsavel_max.timestamp = bem_responsavel.timestamp
                 INNER JOIN sw_cgm
                         ON sw_cgm.numcgm = bem_responsavel.numcgm
                   ) AS bem_responsavel
                ON bem_responsavel.cod_bem = bem.cod_bem
         LEFT JOIN ( SELECT bem_marca.cod_bem
                          , bem_marca.cod_marca
                          , marca.descricao
                       FROM patrimonio.bem_marca
                 INNER JOIN almoxarifado.marca
                         ON bem_marca.cod_marca = marca.cod_marca
                   ) AS bem_marca
                ON bem.cod_bem = bem_marca.cod_bem
         LEFT JOIN sw_cgm AS fornecedor
                ON fornecedor.numcgm = bem.numcgm
         LEFT JOIN patrimonio.bem_baixado
                ON bem_baixado.cod_bem = bem.cod_bem
             WHERE ";
        if ( $this->getDado('cod_bem') ) {
            $stSql.= " bem.cod_bem = ".$this->getDado('cod_bem')."   AND ";
        }
        
        return substr($stSql,0,-6);
    }

    public function recuperaMaxNumPlacaAlfanumerico(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
         return $this->executaRecupera("montaRecuperaMaxNumPlacaAlfanumerico",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaMaxNumPlacaAlfanumerico()
    {
        $stSql.= " SELECT  num_placa
                     FROM  patrimonio.bem
                    WHERE  1=1
                      AND  num_placa ~ '[a-zA-Z]'
                 ORDER BY  num_placa DESC
                    LIMIT  1";

        return $stSql;
    }

    public function recuperaMaxNumPlacaNumerico(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
         return $this->executaRecupera("montaRecuperaMaxNumPlacaNumerico",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaMaxNumPlacaNumerico()
    {
        $stSql .= "SELECT  MAX(num_placa::INTEGER) AS num_placa
                     FROM  patrimonio.bem
                    WHERE  1=1
                      AND  num_placa ~  E'^[0-9]+$'
                      AND  TRIM(num_placa) != ''
                      AND  num_placa IS NOT NULL ";

        return $stSql;
    }

    public function recuperaBemResponsavel(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
         return $this->executaRecupera("montaRecuperaBemResponsavel",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaBemResponsavel()
    {
        $stSql.= "
            SELECT cod_bem
              FROM patrimonio.bem_responsavel ";

        return $stSql;
    }

    public function recuperaBemExistente(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaBemExistente().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    public function montaRecuperaBemExistente()
    {
        $stSql  = "       SELECT  COUNT(bem.cod_bem) as total               \n";
        $stSql .= "         FROM  patrimonio.bem                            \n";
        $stSql .= "   INNER JOIN  patrimonio.historico_bem                  \n";
        $stSql .= "           ON  historico_bem.cod_bem = bem.cod_bem       \n";
        $stSql .= "        WHERE  1=1                                       \n";

        if ($this->getDado('cod_bem')) {
            $stSql.= "  AND  historico_bem.cod_bem = ".$this->getDado('cod_bem');
        }

        if ($this->getDado('cod_orgao')) {
            $stSql.= "  AND  historico_bem.cod_orgao = ".$this->getDado('cod_orgao');
        }

        if ($this->getDado('cod_local')) {
            $stSql.= "  AND  historico_bem.cod_local = ".$this->getDado('cod_local');
        }

        return $stSql;
    }

    public function recuperaSaldoBem(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaSaldoBem().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    public function montaRecuperaSaldoBem()
    {
        if ($this->getDado('cod_bem')) {
            $stSql  = " SELECT cod_bem
                             , vl_acumulado
                             , vl_atualizado
                             , vl_bem
                          FROM patrimonio.fn_depreciacao_acumulada(".$this->getDado('cod_bem').")
                            AS ( cod_bem INTEGER
                               , vl_acumulado NUMERIC(14,2)
                               , vl_atualizado NUMERIC(14,2)
                               , vl_bem NUMERIC(14,2)
                               , min_competencia VARCHAR
                               , max_competencia VARCHAR
                               );";
        }

        return $stSql;
    }

    public function recuperaOrgaoBem(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaOrgaoBem().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    public function montaRecuperaOrgaoBem()
    {
        $stSql  = ' SELECT * FROM patrimonio.historico_bem WHERE cod_bem = '.$this->getDado('cod_bem').' ORDER BY timestamp DESC LIMIT 1';

        return $stSql;
    }
    
    public function recuperaOrganogramaBem(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaOrganogramaBem().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    public function montaRecuperaOrganogramaBem()
    {
        $stSql  = ' SELECT * FROM organograma.orgao_nivel WHERE cod_orgao = '.$this->getDado('cod_orgao').' LIMIT 1';

        return $stSql;
    }
    
    public function recuperaCountOrgaoNivelBem(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaCountOrgaoNivelBem().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    public function montaRecuperaCountOrgaoNivelBem()
    {
        $stSql  = ' SELECT count(*) AS count FROM organograma.orgao_nivel WHERE cod_orgao = '.$this->getDado('cod_orgao');

        return $stSql;
    }
    
    public function recuperaVwOrgaoNivelBem(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaVwOrgaoNivelBem().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    public function montaRecuperaVwOrgaoNivelBem()
    {
        $stSql  = ' SELECT * FROM organograma.vw_orgao_nivel';

        return $stSql;
    }
    
    public function recuperaOrgaoDescricaoBem(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaOrgaoDescricaoBem().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    public function montaRecuperaOrgaoDescricaoBem()
    {
        $stSql  = ' SELECT * FROM organograma.orgao_descricao WHERE cod_orgao = '.$this->getDado('cod_orgao');

        return $stSql;
    }
    
    public function recuperaValorDepreciacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaValorDepreciacao().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    public function montaRecuperaValorDepreciacao()
    {
        $stSql  = " SELECT retorno.cod_bem
                         , retorno.vl_acumulado
                         , retorno.vl_atualizado
                         , retorno.vl_bem
                         , TO_CHAR(depreciacao.dt_depreciacao, 'DD/MM/YYYY') AS dt_depreciacao
                      
                      FROM patrimonio.fn_depreciacao_acumulada(".$this->getDado('cod_bem').")
                        AS retorno ( cod_bem INTEGER
                                    , vl_acumulado NUMERIC(14,2)
                                    , vl_atualizado NUMERIC(14,2)
                                    , vl_bem NUMERIC(14,2)
                                    , min_competencia VARCHAR
                                    , max_competencia VARCHAR
                                   ) 

                  INNER JOIN patrimonio.depreciacao
                          ON retorno.cod_bem = depreciacao.cod_bem

                   LEFT JOIN patrimonio.depreciacao_anulada
                          ON depreciacao.cod_bem         = depreciacao_anulada.cod_bem
                         AND depreciacao.cod_depreciacao = depreciacao_anulada.cod_depreciacao
                         AND depreciacao.timestamp       = depreciacao_anulada.timestamp
             
                       WHERE depreciacao_anulada.cod_depreciacao IS NULL
                         AND depreciacao.timestamp = ( SELECT max(depreciacao_interna.timestamp)
                                                         FROM patrimonio.depreciacao AS depreciacao_interna
                                                        WHERE depreciacao_interna.cod_bem = depreciacao.cod_bem
                                                          AND SUBSTRING(depreciacao_interna.competencia, 1,4) = '".Sessao::getExercicio()."' )";

        return $stSql;
    }
    
    public function recuperaContaContabil(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $stGrupo = "",$boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaContaContabil().$stFiltro.$stOrdem.$stGrupo;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    public function montaRecuperaContaContabil()
    {
        $stSql  = " SELECT grupo_plano_analitica.cod_plano
                         , grupo_plano_analitica.cod_plano_doacao
                         , grupo_plano_analitica.cod_plano_perda_involuntaria
                         , grupo_plano_analitica.cod_plano_transferencia
                         , grupo_plano_analitica.cod_plano_alienacao_ganho
                         , grupo_plano_analitica.cod_plano_alienacao_perda
                         , natureza.cod_tipo
                         , natureza.cod_natureza                 
                         , natureza.nom_natureza
                         , grupo.cod_grupo
                         , grupo.nom_grupo
           
                     FROM patrimonio.bem
           
               INNER JOIN patrimonio.especie
                       ON especie.cod_natureza = bem.cod_natureza
                      AND especie.cod_grupo    = bem.cod_grupo
                      AND especie.cod_especie  = bem.cod_especie
           
               INNER JOIN patrimonio.grupo
                       ON grupo.cod_natureza = especie.cod_natureza
                      AND grupo.cod_grupo    = especie.cod_grupo
           
               INNER JOIN patrimonio.natureza
                       ON natureza.cod_natureza = grupo.cod_natureza
           
                LEFT JOIN patrimonio.grupo_plano_analitica
                       ON grupo_plano_analitica.cod_grupo    = grupo.cod_grupo
                      AND grupo_plano_analitica.cod_natureza = grupo.cod_natureza
                      AND grupo_plano_analitica.exercicio    = '".$this->getDado('exercicio')."'
                ";
        return $stSql;
    }
    
}
?>