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
    * Classe de Mapeamento para tabela organograma_orgao
    * Data de Criação: 25/07/2005

    * @author Analista: Cassiano
    * @author Desenvolvedor: Cassiano

    Casos de uso: uc-01.05.01, uc-01.05.02, uc-01.05.03, uc-04.05.40

    $Id: TOrganogramaOrgao.class.php 65314 2016-05-12 14:51:44Z evandro $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ORGANOGRAMA_ORGAO
  * Data de Criação: 16/08/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Diego Barbosa Victoria

  * @package URBEM
  * @subpackage Mapeamento
*/
class TOrganogramaOrgao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TOrganogramaOrgao()
{
    parent::Persistente();
    $this->setTabela('organograma.orgao');

    $this->setCampoCod('cod_orgao');
    $this->setComplementoChave('');

    $this->AddCampo('cod_orgao'      ,'integer',true,'',true,false);
    $this->AddCampo('cod_calendar'   ,'integer',true,'',false,true);
    $this->AddCampo('cod_norma'      ,'integer',true,'',false,true);
    $this->AddCampo('num_cgm_pf'     ,'integer',true,'',false,true);
    $this->AddCampo('criacao'        ,'date',true,'',false,false);
    $this->AddCampo('inativacao'     ,'date',false,'',false,false);
    $this->AddCampo('sigla_orgao'    ,'varchar',false,'10',false,false);
}

function recuperaOrgaoReduzido(&$rsRecordSet,$stFiltro="",$stOrdem="",$boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaOrgaoReduzido().$stFiltro.$stOrdem;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    $this->setDebug( $stSql );

    return $obErro;
}

function montaRecuperaOrgaoReduzido()
{
    $stSql  = "SELECT orgao.*                                                                          \n";
    $stSql .= "     , ultima_data.data                                                                 \n";
    $stSql .= "     , ovw.orgao_reduzido as reduzido                                                   \n";
    # Recupera a descrição do órgão, baseado na data informada.
    $stSql .= "     , recuperadescricaoorgao(orgao.cod_orgao, ".($this->getDado('vigencia') ? "'".$this->getDado('vigencia')."'" : "now()::date").") as descricao \n";
    $stSql .= "     , ovw.orgao as estruturado                                                         \n";
    $stSql .= "  FROM organograma.orgao                                                                \n";
    $stSql .= "     , organograma.organograma                                                          \n";
    $stSql .= "     , organograma.orgao_nivel                                                          \n";
    $stSql .= "     , organograma.nivel                                                                \n";
    $stSql .= "     , organograma.vw_orgao_nivel as ovw                                                \n";
    $stSql .= "     , (SELECT MAX(cod_organograma) as cod_organograma                                  \n";
    $stSql .= "             , MAX(implantacao) AS data                                                 \n";
    $stSql .= "          FROM organograma.organograma oo                                               \n";
    $stSql .= "         WHERE implantacao <= '".$this->getDado('data_atual')."'
                        AND ativo IS TRUE ) as ultima_data        \n";
    $stSql .= " WHERE organograma.cod_organograma = nivel.cod_organograma                              \n";
    $stSql .= "   AND nivel.cod_organograma       = orgao_nivel.cod_organograma                        \n";
    $stSql .= "   AND nivel.cod_nivel             = orgao_nivel.cod_nivel                              \n";
    $stSql .= "   AND orgao_nivel.cod_orgao       = orgao.cod_orgao                                    \n";
    $stSql .= "   AND orgao.cod_orgao             = ovw.cod_orgao                                      \n";
    $stSql .= "   AND orgao_nivel.cod_organograma = ovw.cod_organograma                                \n";
    $stSql .= "   AND orgao_nivel.cod_organograma = ultima_data.cod_organograma                        \n";
    $stSql .= "   AND nivel.cod_nivel             = ovw.nivel                                          \n";

    return $stSql;

}

function recuperaUltimaCriacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaUltimaCriacao();
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    $this->setDebug( $stSql );

    return $obErro;
}

function montaRecuperaUltimaCriacao()
{
    $stSql  = " SELECT orgao.*                                                                         \n";
    # Recupera a descrição do órgão, baseado na data informada.
    $stSql .= "     , recuperadescricaoorgao(orgao.cod_orgao, ".($this->getDado('vigencia') ? "'".$this->getDado('vigencia')."'" : "now()::date").") as descricao \n";
    $stSql .= "      , organograma.cod_organograma                                                     \n";
    $stSql .= "      , ultima_data.data                                                                \n";
    $stSql .= "      , ovw.orgao_reduzido as reduzido                                                  \n";
    $stSql .= "      , ovw.orgao                                                                       \n";
    $stSql .= "   FROM organograma.orgao                                                               \n";
    $stSql .= "      , organograma.organograma                                                         \n";
    $stSql .= "      , organograma.orgao_nivel                                                         \n";
    $stSql .= "      , organograma.nivel                                                               \n";
    $stSql .= "      , organograma.vw_orgao_nivel as ovw                                               \n";
    $stSql .= "      , (SELECT MAX(cod_organograma) as cod_organograma                                 \n";
    $stSql .= "              , MAX(implantacao) AS data                                                \n";
    $stSql .= "           FROM organograma.organograma oo                                              \n";
    $stSql .= "          WHERE implantacao <= '".$this->getDado('data_atual')."') as ultima_data       \n";
    $stSql .= " WHERE organograma.cod_organograma = nivel.cod_organograma                              \n";
    $stSql .= "   AND nivel.cod_organograma       = orgao_nivel.cod_organograma                        \n";
    $stSql .= "   AND nivel.cod_nivel             = orgao_nivel.cod_nivel                              \n";
    $stSql .= "   AND orgao_nivel.cod_orgao       = orgao.cod_orgao                                    \n";
    $stSql .= "   AND orgao.cod_orgao             = ovw.cod_orgao                                      \n";
    $stSql .= "   AND orgao_nivel.cod_organograma = ovw.cod_organograma                                \n";
    $stSql .= "   AND orgao_nivel.cod_organograma = ultima_data.cod_organograma                        \n";
    $stSql .= "   AND nivel.cod_nivel             = ovw.nivel                                           \n";
    if ( $this->getDado( 'cod_orgao')) {
        $stSql .= " AND orgao.cod_orgao = '". $this->getDado('cod_orgao') ."'                           \n";
    }
    $stSql .= "ORDER BY orgao.cod_orgao                                                                \n";

    return $stSql;

}

function montaRecuperaRelacionamento()
{
    $stSql  = " SELECT                                               \n";
    $stSql .= "     oo.cod_orgao,                                    \n";
    # Recupera a descrição do órgão, baseado na data informada.
    $stSql .= "     recuperadescricaoorgao(oo.cod_orgao, ".($this->getDado('vigencia') ? "'".$this->getDado('vigencia')."'" : "now()::date").") as descricao , \n";
    $stSql .= "     to_char( oo.criacao, 'dd/mm/yyyy' ) as criacao,  \n";
    $stSql .= "     cgm.nom_cgm,                                     \n";
    $stSql .= "     cgm.e_mail                                       \n";
    $stSql .= " FROM                                                 \n";
    $stSql .= "     organograma.orgao_nivel oon,                     \n";
    $stSql .= "     organograma.orgao  oo,                           \n";
    $stSql .= "     sw_cgm  cgm                                      \n";
    $stSql .= " where                                                \n";
    $stSql .= "     oo.cod_orgao = oon.cod_orgao and                 \n";
    $stSql .= "     oo.num_cgm_pf = cgm.numcgm                       \n";
    if ($this->getDado('cod_organograma')) {
        $stSql .= "  and oon.cod_organograma = ".$this->getDado('cod_organograma')."\n";
    }
    if ($this->getDado('cod_orgao')) {
        $stSql .= "  and oo.cod_orgao = ".$this->getDado('cod_orgao')."            \n";
    }
    if ( $this->getDado('inativacao') ) {
        $stSql .= "     and oo.inativacao is null                                                                \n";
    }
    $stSql .= " group by                                             \n";
    $stSql .= "     oo.cod_orgao,                                    \n";
    $stSql .= "     descricao,                                    \n";
    $stSql .= "     criacao,                                         \n";
    $stSql .= "     cgm.nom_cgm,                                     \n";
    $stSql .= "     cgm.e_mail                                       \n";

    return $stSql;
}

function recuperaOrgaoSuperior(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if ($stOrdem) {
        $stOrdem = ' order by ' . $stOrdem ;
    }

    $stSql = $this->montaRecuperaOrgaoSuperior().$stFiltro. $stOrdem ;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;

}
function recuperaOrgaosAtivos(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if ($stOrdem) {
        $stOrdem = ' order by ' . $stOrdem ;
    }

    $stSql = $this->montaRecuperaOrgaosAtivos().$stFiltro. $stOrdem ;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;

}

function montaRecuperaOrgaoSuperior()
{
    $stSQL  = "SELECT                                                                                                 \n ";
    $stSQL .= "    oo.cod_orgao ,                                                                                     \n ";
    $stSQL .= "    oo.cod_calendar ,                                                                                  \n ";
    $stSQL .= "    oo.cod_norma ,                                                                                     \n ";
    $stSQL .= "    oo.num_cgm_pf ,                                                                                    \n ";
    # Recupera a descrição do órgão, baseado na data informada.
    $stSQL .= "    recuperadescricaoorgao(oo.cod_orgao, ".($this->getDado('vigencia') ? "'".$this->getDado('vigencia')."'" : "now()::date").") as descricao, \n";
    $stSQL .= "    TO_CHAR(oo.criacao,'dd/mm/yyyy') AS criacao ,                                                      \n ";
    $stSQL .= "    TO_CHAR(oo.inativacao,'dd/mm/yyyy') AS inativacao                                                  \n ";
    $stSQL .= "FROM                                                                                                   \n ";
    $stSQL .= "    organograma.orgao oo                                                                               \n ";
    $stSQL .= "inner join organograma.orgao_nivel on ( organograma.orgao_nivel.cod_orgao = oo.cod_orgao )             \n ";

    $stSQL .= "WHERE                                                                                                  \n ";
    if ($this->getDado('cod_organograma')) {
        $stSQL .= " cod_organograma = " . $this->getDado('cod_organograma'). "                                       \n";
    }
    if ($this->getDado('cod_orgao')) {
        $stSQL .= " AND oo.cod_orgao in ( " . $this->getDado('cod_orgao'). ")                                            \n";
    }

    if ($this->getDado('cod_nivel ')) {
        $stSQL .= " AND cod_nivel < " . $this->getDado('cod_nivel').  "                                               \n";
    }

    $stSQL .= "   group by oo.cod_orgao,                                                                              \n";
    $stSQL .= "            oo.cod_calendar ,                                                                          \n";
    $stSQL .= "            oo.cod_norma ,                                                                             \n";
    $stSQL .= "            oo.num_cgm_pf ,                                                                            \n";
    $stSQL .= "            criacao ,                                                                                  \n";
    $stSQL .= "            inativacao                                                                                 \n";

    return $stSQL;

}

function montaRecuperaOrgaosAtivos()
{
    $stSQL  = "SELECT                                                                                                 \n ";
    $stSQL .= "    cod_organograma,                                                                                   \n ";
    $stSQL .= "    oo.cod_orgao ,                                                                                     \n ";
    $stSQL .= "    oo.cod_calendar ,                                                                                  \n ";
    $stSQL .= "    oo.cod_norma ,                                                                                     \n ";
    $stSQL .= "    oo.num_cgm_pf ,                                                                                    \n ";
    $stSQL .= "    CASE WHEN oo.sigla_orgao IS NOT NULL
                        THEN oo.sigla_orgao || ' - ' || recuperadescricaoorgao(oo.cod_orgao, ".($this->getDado('vigencia') ? "'".$this->getDado('vigencia')."'" : "now()::date").")
                        ELSE recuperadescricaoorgao(oo.cod_orgao, ".($this->getDado('vigencia') ? "'".$this->getDado('vigencia')."'" : "now()::date").")
                   END AS descricao,                                                                                  \n ";
    $stSQL .= "    TO_CHAR(oo.criacao,'dd/mm/yyyy') AS criacao ,                                                      \n ";
    $stSQL .= "    TO_CHAR(oo.inativacao,'dd/mm/yyyy') AS inativacao                                                  \n ";
    $stSQL .= "FROM                                                                                                   \n ";
    $stSQL .= "    organograma.orgao oo                                                                               \n ";
    $stSQL .= "inner join organograma.orgao_nivel on ( organograma.orgao_nivel.cod_orgao = oo.cod_orgao )             \n ";
    $stSQL .= "WHERE    cod_organograma is not null                                                                   \n ";
    if ($this->getDado('cod_organograma')) {
        $stSQL .= "and cod_organograma = " . $this->getDado('cod_organograma'). "                                     \n";
    }
    if ($this->getDado('cod_orgao')) {
        $stSQL .= " AND cod_orgao < " . $this->getDado('cod_orgao'). "                                                \n";
    }
    if ($this->getDado('inativacao')) {
        $stSQL .= " AND inativacao is null                                                                        \n";
    }
    $stSQL .= "   group by  cod_organograma,                                                                          \n";
    $stSQL .= "             oo.cod_orgao,                                                                             \n";
    $stSQL .= "             oo.cod_calendar ,                                                                         \n";
    $stSQL .= "             oo.cod_norma ,                                                                            \n";
    $stSQL .= "             oo.num_cgm_pf ,                                                                           \n";
    $stSQL .= "             oo.sigla_orgao ,                                                                          \n";
    $stSQL .= "             criacao ,                                                                                 \n";
    $stSQL .= "             inativacao                                                                                \n";

    return $stSQL;
}

function recuperaOrgaoInferior(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if ($stOrdem) {
        $stOrdem = ' order by ' . $stOrdem ;
    }

    $stSql = $this->montaRecuperaOrgaoInferior().$stFiltro. $stOrdem ;
    $this->setDebug( $stSql );

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;

}

function montaRecuperaOrgaoInferior()
{
    $stSQL  = "SELECT                                                                                                 \n ";
    $stSQL .= "    oo.cod_orgao ,                                                                                     \n ";
    $stSQL .= "    oo.cod_calendar ,                                                                                  \n ";
    $stSQL .= "    oo.cod_norma ,                                                                                     \n ";
    $stSQL .= "    oo.num_cgm_pf ,                                                                                    \n ";
    $stSQL .= "    recuperadescricaoorgao(oo.cod_orgao, ".($this->getDado('vigencia') ? "'".$this->getDado('vigencia')."'" : "now()::date").") as descricao, \n ";
    $stSQL .= "    TO_CHAR(oo.criacao,'dd/mm/yyyy') AS criacao ,                                                      \n ";
    $stSQL .= "    TO_CHAR(oo.inativacao,'dd/mm/yyyy') AS inativacao                                                  \n ";
    $stSQL .= "FROM                                                                                                   \n ";
    $stSQL .= "    organograma.orgao oo                                                                               \n ";
    $stSQL .= "inner join organograma.orgao_nivel on ( organograma.orgao_nivel.cod_orgao = oo.cod_orgao )             \n ";
    $stSQL .= "WHERE cod_organograma is not null                                                                      \n ";
    if ($this->getDado('cod_organograma')) {
        $stSQL .= "and  cod_organograma = " . $this->getDado('cod_organograma'). "                                    \n";
    }
    if ($this->getDado('cod_orgao')) {
        $stSQL .= " AND oo.cod_orgao in ( " . $this->getDado('cod_orgao'). ")                                         \n";
    }

    if ($this->getDado('cod_nivel ')) {
        $stSQL .= " AND cod_nivel > " . $this->getDado('cod_nivel').  "                                               \n";
    }
    $stSQL .= "   group by oo.cod_orgao,                                                                              \n";
    $stSQL .= "            oo.cod_calendar ,                                                                          \n";
    $stSQL .= "            oo.cod_norma ,                                                                             \n";
    $stSQL .= "            oo.num_cgm_pf ,                                                                            \n";
    $stSQL .= "            criacao ,                                                                                  \n";
    $stSQL .= "            inativacao                                                                                 \n";

    return $stSQL;
}

function listarOrgaoCodigoComposto(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if ($stOrdem) {
        $stOrdem = ' order by ' . $stOrdem ;
    } else {
         $stOrdem = ' order by  o.cod_orgao,orgao ';
    }
    $stSql = $this->montaListarCodigoComposto().$stFiltro. $stOrdem ;
    $this->setDebug( $stSql );

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;

}

function montaListarCodigoComposto()
{
    $stSql  = " SELECT                     \n";
    $stSql .= "     o.cod_orgao,           \n";
    $stSql .= "     o.num_cgm_pf,          \n";
    $stSql .= "     o.cod_calendar,        \n";
    $stSql .= "     o.cod_norma,           \n";
    $stSql .= "     o.sigla_orgao,         \n";
    $stSql .= "     recuperadescricaoorgao(o.cod_orgao, ".($this->getDado('vigencia') ? "'".$this->getDado('vigencia')."'" : "now()::date").") as descricao,   \n";
    $stSql .= "     o.criacao,             \n";
    $stSql .= "     o.inativacao,          \n";
    $stSql .= "     o.sigla_orgao,         \n";
    $stSql .= "     orn.cod_organograma,   \n";
    $stSql .= "     organograma.fn_consulta_orgao(orn.cod_organograma, o.cod_orgao) AS orgao, \n";
    $stSql .= "     publico.fn_mascarareduzida(organograma.fn_consulta_orgao(orn.cod_organograma, o.cod_orgao)) AS orgao_reduzido, \n";
    $stSql .= "     publico.fn_nivel(organograma.fn_consulta_orgao(orn.cod_organograma, o.cod_orgao)) AS nivel,  \n";
    $stSql .= "    case when to_char(o.inativacao,'dd/mm/yyyy') is null then 'Ativo' else 'Inativo' end as situacao   \n ";
    $stSql .= " FROM                                                                                            \n";
    $stSql .= "     organograma.orgao o,                                                                        \n";
    $stSql .= "     organograma.orgao_nivel orn                                                                 \n";
    $stSql .= " WHERE                                                                                           \n";
    $stSql .= "     o.cod_orgao = orn.cod_orgao                                                                 \n";
    if ( $this->getDado('cod_organograma') ) {
        $stSql .= "     and orn.cod_organograma = ".$this->getDado('cod_organograma')."                         \n";
    }
    if ( $this->getDado('inativacao') ) {
        $stSql .= "     and o.inativacao is null                                                                \n";
    }
    if ( strlen($this->getDado('cod_orgao')) > 0 ) {
        $stSql .= "     and o.cod_orgao = ".$this->getDado('cod_orgao')."                                       \n";
    }
    if ( $this->getDado('descricao') ) {
        $stSql .= "     and lower(recuperadescricaoorgao(o.cod_orgao, now()::date)) LIKE lower('%".$this->getDado('descricao')."%') \n";
    }
    $stSql .= " GROUP BY                                                                                        \n";
    $stSql .= "     o.cod_orgao,           \n";
    $stSql .= "     o.num_cgm_pf,          \n";
    $stSql .= "     o.cod_calendar,        \n";
    $stSql .= "     o.cod_norma,           \n";
    $stSql .= "     o.criacao,             \n";
    $stSql .= "     o.inativacao,          \n";
    $stSql .= "     o.sigla_orgao,         \n";
    $stSql .= "     orn.cod_organograma,   \n";
    $stSql .= "     orgao,                 \n";
    $stSql .= "     orgao_reduzido,        \n";
    $stSql .= "     nivel                  \n";

    return $stSql;

}

function recuperaOrgaos(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro = $this->executaRecupera("montaRecuperaOrgaos",$rsRecordSet, $stFiltro, $stOrdem, $boTransacao);

    return $obErro;
}

function montaRecuperaOrgaos()
{
    $stSql  = "    SELECT orgao_nivel.cod_estrutural                                                                \n";
    $stSql .= "         , recuperaDescricaoOrgao(orgao.cod_orgao,'".$this->getDado("vigencia")."') as descricao     \n";
    $stSql .= "         , orgao.cod_orgao                                                                           \n";
    $stSql .= "      FROM organograma.orgao                                                                         \n";
    $stSql .= "INNER JOIN (SELECT orgao_nivel.*                                                                     \n";
    $stSql .= "                 , organograma.fn_consulta_orgao(orgao_nivel.cod_organograma, orgao_nivel.cod_orgao) AS cod_estrutural\n";
    $stSql .= "              FROM organograma.orgao_nivel) AS orgao_nivel                                           \n";
    $stSql .= "        ON orgao_nivel.cod_orgao = orgao.cod_orgao                                                   \n";
    $stSql .= "       AND orgao_nivel.cod_nivel = publico.fn_nivel(cod_estrutural)                                  \n";
    $stSql .= "     WHERE (orgao.inativacao > '".$this->getDado("vigencia")."' OR orgao.inativacao IS NULL)         \n";
    if (($this->getDado('nivelVigente') != "") && ($this->getDado('nivelVigente') != null)) {
        $stSql .= "       AND orgao_nivel.cod_organograma = (  SELECT organograma.cod_organograma                       \n";
        $stSql .= "                                              FROM organograma.organograma                           \n";
        $stSql .= "                                             WHERE organograma.implantacao <= '".$this->getDado("vigencia")."' \n";
        $stSql .= "                                          ORDER BY organograma.implantacao DESC LIMIT 1)             \n";
    }

    return $stSql;
}

function recuperaOrgaoSuperiorNivel(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro = $this->executaRecupera("montaRecuperaOrgaoSuperiorNivel",$rsRecordSet, $stFiltro, $stOrdem, $boTransacao);

    return $obErro;
}

function montaRecuperaOrgaoSuperiorNivel()
{
    $stSQL  = "    SELECT *                                                                     \n ";
    $stSQL .= "         , recuperadescricaoorgao(cod_orgao,now()::date) as descricao    \n ";
    $stSQL .= "      from organograma.vw_orgao_nivel                                            \n ";
    $stSQL .= "     WHERE 1 = 1                                                         \n ";

    if ($this->getDado('cod_organograma')) {
        $stSQL .= " AND cod_organograma = " . $this->getDado('cod_organograma'). "              \n";
    }

    if ($this->getDado('cod_nivel')) {
        $stSQL .= " AND nivel = " . $this->getDado('cod_nivel').  "                             \n";
    }

    return $stSQL;
}

function recuperaDadosUltimoOrgao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDadosUltimoOrgao();
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    $this->setDebug( $stSql );

    return $obErro;
}

function montaRecuperaDadosUltimoOrgao()
{
    $stSql  = " SELECT  \n";
    # Recupera a descrição do órgão, baseado na data informada.
    $stSql .= "       recuperadescricaoorgao(orgao.cod_orgao, ".($this->getDado('vigencia') ? "'".$this->getDado('vigencia')."'" : "now()::date").") as descricao \n";
    $stSql .= "     , organograma.cod_organograma                                                     \n";
    $stSql .= "     , ovw.orgao_reduzido as reduzido                                                  \n";
    $stSql .= "     , ovw.orgao                                                                       \n";
    $stSql .= "   FROM organograma.orgao                                                               \n";
    $stSql .= "      , organograma.organograma                                                         \n";
    $stSql .= "      , organograma.orgao_nivel                                                         \n";
    $stSql .= "      , organograma.nivel                                                               \n";
    $stSql .= "      , organograma.vw_orgao_nivel as ovw                                               \n";
    $stSql .= " WHERE organograma.cod_organograma = nivel.cod_organograma                              \n";
    $stSql .= "   AND nivel.cod_organograma       = orgao_nivel.cod_organograma                        \n";
    $stSql .= "   AND nivel.cod_nivel             = orgao_nivel.cod_nivel                              \n";
    $stSql .= "   AND orgao_nivel.cod_orgao       = orgao.cod_orgao                                    \n";
    $stSql .= "   AND orgao.cod_orgao             = ovw.cod_orgao                                      \n";
    $stSql .= "   AND orgao_nivel.cod_organograma = ovw.cod_organograma                                \n";
    $stSql .= "   AND nivel.cod_nivel             = ovw.nivel                                           \n";
    if ( $this->getDado( 'cod_orgao')) {
        $stSql .= " AND orgao.cod_orgao = '". $this->getDado('cod_orgao') ."'                           \n";
    }
    $stSql .= "ORDER BY orgao.cod_orgao                                                                \n";

    return $stSql;

}

function recuperaOrgaosServidores(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $stSql  = "\n          SELECT orgao.cod_orgao";
    $stSql .= "\n               , organograma.fn_consulta_orgao(orgao_nivel.cod_organograma, orgao.cod_orgao) AS estrutural";
    $stSql .= "\n               , trim(orgao_descricao.descricao) AS descricao";
    $stSql .= "\n               , de_para_orgao_unidade.num_orgao";
    $stSql .= "\n               , de_para_orgao_unidade.num_unidade";
    $stSql .= "\n            FROM organograma.orgao";
    $stSql .= "\n       LEFT JOIN pessoal.de_para_orgao_unidade";
    $stSql .= "\n              ON de_para_orgao_unidade.cod_orgao = orgao.cod_orgao";
    $stSql .= "\n      INNER JOIN (SELECT orgao_descricao.cod_orgao ";
    $stSql .= "\n                   , MAX(orgao_descricao.timestamp) as orgao_descricao_timestamp ";
    $stSql .= "\n                 FROM organograma.orgao_descricao ";
    $stSql .= "\n            GROUP BY orgao_descricao.cod_orgao ";
    $stSql .= "\n             )  as od ";
    $stSql .= "\n          ON od.cod_orgao = orgao.cod_orgao ";
    $stSql .= "\n  INNER JOIN organograma.orgao_descricao ";
    $stSql .= "\n          ON orgao_descricao.cod_orgao = od.cod_orgao    ";
    $stSql .= "\n         AND orgao_descricao.timestamp =od.orgao_descricao_timestamp  ";
    $stSql .= "\n      INNER JOIN organograma.orgao_nivel";
    $stSql .= "\n              ON orgao_nivel.cod_orgao = orgao.cod_orgao";
    $stSql .= "\n      INNER JOIN (";
    $stSql .= "\n                      SELECT contrato_servidor_orgao.cod_orgao ";
    $stSql .= "\n                        FROM pessoal.contrato_servidor_orgao";
    $stSql .= "\n                    GROUP BY contrato_servidor_orgao.cod_orgao";
    $stSql .= "\n                   UNION ALL ";
    $stSql .= "\n                      SELECT contrato_pensionista_orgao.cod_orgao";
    $stSql .= "\n                        FROM pessoal.contrato_pensionista_orgao";
    $stSql .= "\n                    GROUP BY contrato_pensionista_orgao.cod_orgao";
    $stSql .= "\n                    ORDER BY cod_orgao";
    $stSql .= "\n                 ) AS orgao_servidor";
    $stSql .= "\n              ON orgao_servidor.cod_orgao = orgao.cod_orgao";
    $stSql .= "\n                ".$stFiltro;
    $stSql .= "\n        GROUP BY orgao.cod_orgao";
    $stSql .= "\n               , organograma.fn_consulta_orgao(orgao_nivel.cod_organograma, orgao.cod_orgao)";
    $stSql .= "\n               , orgao_descricao.descricao";
    $stSql .= "\n               , de_para_orgao_unidade.num_orgao";
    $stSql .= "\n               , de_para_orgao_unidade.num_unidade";
    if ($stOrdem != '') {
        $stSql .= "\n        ".$stOrdem;
    } else {
        $stSql .= "\n        ORDER BY orgao.cod_orgao";
    }

    $stFiltro = '';
    $stOrdem = '';
    
    return $this->executaRecuperaSql($stSql, $rsRecordSet, $stFiltro, $stOrdem, $boTransacao);
}

function recuperaOrgaosInventario(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro = $this->executaRecupera("montaRecuperaOrgaosInventario",$rsRecordSet, $stFiltro, $stOrdem, $boTransacao);

    return $obErro;
}

function montaRecuperaOrgaosInventario()
{
    $stSql  = " SELECT  DISTINCT 
                        orgao_nivel.cod_estrutural                                                                
                        , recuperaDescricaoOrgao(orgao.cod_orgao,'".$this->getDado("vigencia")."') as descricao     
                        , orgao.cod_orgao                                                                           
                FROM organograma.orgao                                                                         
                
                INNER JOIN (SELECT  orgao_nivel.*                                                                     
                                    , organograma.fn_consulta_orgao(orgao_nivel.cod_organograma, orgao_nivel.cod_orgao) AS cod_estrutural
                            FROM organograma.orgao_nivel
                ) AS orgao_nivel                                           
                    ON orgao_nivel.cod_orgao = orgao.cod_orgao                                                   
                    AND orgao_nivel.cod_nivel = publico.fn_nivel(cod_estrutural)                                  
                
                INNER JOIN (SELECT  MAX(TIMESTAMP)
                                    ,cod_orgao
                                    ,cod_bem                               
                            FROM patrimonio.historico_bem
                            GROUP BY cod_orgao, cod_bem
                ) as historico_bem
                    ON historico_bem.cod_orgao = orgao.cod_orgao                                  

                LEFT JOIN patrimonio.inventario_historico_bem
                    ON  inventario_historico_bem.cod_bem = historico_bem.cod_bem

                WHERE (orgao.inativacao > '".$this->getDado("vigencia")."' OR orgao.inativacao IS NULL)         
                AND  EXISTS
                 (
                    SELECT  1
                      FROM  organograma.organograma
                     WHERE  organograma.cod_organograma = orgao_nivel.cod_organograma
                       AND  organograma.ativo = true
                 )
            ";

    return $stSql;
}

function recuperaLotacaoOrgao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaLotacaoOrgao();
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    $this->setDebug( $stSql );

    return $obErro;
}

function montaRecuperaLotacaoOrgao()
{
    $stSql  = "  SELECT orgao.cod_orgao
                      , organograma.fn_consulta_orgao(orgao_nivel.cod_organograma, orgao.cod_orgao) AS estrutural
                      , trim(orgao_descricao.descricao) AS descricao
                      , de_para_lotacao_orgao.num_orgao
                   FROM organograma.orgao
              LEFT JOIN pessoal".$this->getDado('stEntidade').".de_para_lotacao_orgao
                     ON de_para_lotacao_orgao.cod_orgao = orgao.cod_orgao
             INNER JOIN (SELECT orgao_descricao.cod_orgao 
                              , MAX(orgao_descricao.timestamp) as orgao_descricao_timestamp 
                           FROM organograma.orgao_descricao 
                       GROUP BY orgao_descricao.cod_orgao 
                       )  as od 
                     ON od.cod_orgao = orgao.cod_orgao 
             INNER JOIN organograma.orgao_descricao 
                     ON orgao_descricao.cod_orgao = od.cod_orgao    
                    AND orgao_descricao.timestamp = od.orgao_descricao_timestamp  
             INNER JOIN organograma.orgao_nivel
                     ON orgao_nivel.cod_orgao = orgao.cod_orgao
             INNER JOIN ( SELECT contrato_servidor_orgao.cod_orgao 
                            FROM pessoal".$this->getDado('stEntidade') .".contrato_servidor_orgao
                        GROUP BY contrato_servidor_orgao.cod_orgao
                       UNION ALL 
                          SELECT contrato_pensionista_orgao.cod_orgao
                            FROM pessoal".$this->getDado('stEntidade') .".contrato_pensionista_orgao
                        GROUP BY contrato_pensionista_orgao.cod_orgao
                        ORDER BY cod_orgao
                      ) AS orgao_servidor
                     ON orgao_servidor.cod_orgao = orgao.cod_orgao
               GROUP BY orgao.cod_orgao
                      , organograma.fn_consulta_orgao(orgao_nivel.cod_organograma, orgao.cod_orgao)
                      , orgao_descricao.descricao
                      , de_para_lotacao_orgao.num_orgao
               ORDER BY orgao.cod_orgao            
            ";

    return $stSql;
}



}
