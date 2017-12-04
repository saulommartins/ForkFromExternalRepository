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
* Classe de Mapeamento para tabela organograma_orgao_nivel
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3476 $
$Name$
$Author: pablo $
$Date: 2005-12-06 13:51:37 -0200 (Ter, 06 Dez 2005) $

Casos de uso: uc-01.05.01, uc-01.05.02, uc-01.05.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ORGANOGRAMA_ORGAO_NIVEL
  * Data de Criação: 16/08/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Diego Barbosa Victoria

  * @package URBEM
  * @subpackage Mapeamento
*/
class TOrganogramaOrgaoNivel extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TOrganogramaOrgaoNivel()
{
    parent::Persistente();
    $this->setTabela('organograma.orgao_nivel');

    $this->setCampoCod('cod_orgao');
    $this->setComplementoChave('cod_nivel,cod_organograma');

    $this->AddCampo('cod_orgao'      ,'integer',true,'',true,true);
    $this->AddCampo('cod_nivel'      ,'integer',true,'',true,true);
    $this->AddCampo('cod_organograma','integer',true,'',true,true);
    $this->AddCampo('valor'          ,'integer',true,'',false,false);
}

function recuperaOrgaoDescricao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSQL = $this->montaRecuperaOrgaoDescricao().$stFiltro.$stOrdem;
    $this->setDebug( $stSQL );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSQL,  $boTransacao );

    return $obErro;
}
/**
    * Monta consulta para recuperar organogramas sem orgao
    * @access Private
    * @return String $stSQL
*/
function montaRecuperaOrgaoDescricao()
{
    $stSQL  = " SELECT * FROM (                                                                      \n";
    $stSQL .= " SELECT orgao.cod_orgao                                                               \n";
    # Recupera a ultima descriçao do orgao.
    $stSQL .= "      , recuperaDescricaoOrgao(orgao.cod_orgao, now()::date) as descricao   \n";
#    $stSQL .= "      , orgao_descricao.descricao                                                     \n";
    $stSQL .= "      , MAX(orgao_descricao.timestamp)                                                \n";
    $stSQL .= "      , orgao_nivel.cod_organograma                                                   \n";
    $stSQL .= "      , organograma.fn_consulta_orgao(orgao_nivel.cod_organograma, orgao.cod_orgao) AS orgao \n ";
    $stSQL .= "      , publico.fn_mascarareduzida(organograma.fn_consulta_orgao( orgao_nivel.cod_organograma \n ";
    $stSQL .= "                                                                , orgao.cod_orgao)) AS orgao_reduzido \n ";
    $stSQL .= "      , publico.fn_nivel(organograma.fn_consulta_orgao(orgao_nivel.cod_organograma, orgao.cod_orgao)) AS nivel \n ";
    $stSQL .= "      , orgao_nivel.cod_nivel                        \n ";
    $stSQL .= "      , orgao_nivel.valor                            \n ";
    $stSQL .= "   FROM organograma.orgao                            \n ";
    $stSQL .= "      , organograma.orgao_nivel                      \n ";
    $stSQL .= "      , organograma.orgao_descricao                  \n ";
    $stSQL .= "  WHERE orgao.cod_orgao = orgao_nivel.cod_orgao      \n ";
    $stSQL .= "    AND orgao_descricao.cod_orgao = orgao.cod_orgao  \n ";
    $stSQL .= " GROUP BY orgao.cod_orgao                            \n ";
#   $stSQL .= "        , orgao_descricao.descricao                  \n ";
    $stSQL .= "        , orgao_nivel.cod_organograma                \n ";
    $stSQL .= "        , orgao_nivel.cod_nivel                      \n ";
    $stSQL .= "        , orgao_nivel.valor                          \n ";
    $stSQL .= " ) as tabela                                         \n ";

    return $stSQL;
}

function recuperaOrgaoDescricaoComponente(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSQL = $this->montaRecuperaOrgaoDescricaoComponente().$stFiltro.$stOrdem;
    $this->setDebug( $stSQL );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSQL,  $boTransacao );

    return $obErro;
}
/**
    * Monta consulta para recuperar organogramas sem orgao
    * @access Private
    * @return String $stSQL
*/
function montaRecuperaOrgaoDescricaoComponente()
{
    $stSQL  = " SELECT * FROM (                                                                      \n";
    $stSQL .= " SELECT orgao.cod_orgao                                                               \n";
    $stSQL .= "      , orgao_descricao.descricao                                                     \n";
    $stSQL .= "      , MAX(orgao_descricao.timestamp)                                                \n";
    $stSQL .= "      , orgao_nivel.cod_organograma                                                   \n";
    $stSQL .= "      , publico.fn_nivel(organograma.fn_consulta_orgao(orgao_nivel.cod_organograma, orgao.cod_orgao)) AS nivel \n ";
    $stSQL .= "      , organograma.fn_consulta_orgao(orgao_nivel.cod_organograma, orgao.cod_orgao) AS orgao \n";
    $stSQL .= "      , orgao_nivel.cod_nivel                        \n ";
    $stSQL .= "      , orgao_nivel.valor                            \n ";
    $stSQL .= "   FROM organograma.orgao                            \n ";
    $stSQL .= " INNER JOIN  organograma.orgao_nivel                      
                    ON orgao.cod_orgao = orgao_nivel.cod_orgao
        
                INNER JOIN (select orgao_descricao.*
                            from organograma.orgao_descricao
                            INNER JOIN ( SELECT max.cod_orgao                         
                                                ,MAX(max.timestamp) as timestamp
                                        from organograma.orgao_descricao as max
                                        group by cod_orgao
                            )as max
                                ON max.cod_orgao = orgao_descricao.cod_orgao
                                AND max.timestamp = orgao_descricao.timestamp                     
        
                        ) AS orgao_descricao     
                        ON orgao.cod_orgao = orgao_descricao.cod_orgao      \n ";       
    $stSQL .= " GROUP BY orgao.cod_orgao                            \n ";
    $stSQL .= "        , orgao_descricao.descricao                  \n ";
    $stSQL .= "        , orgao_nivel.cod_organograma                \n ";
    $stSQL .= "        , orgao_nivel.cod_nivel                      \n ";
    $stSQL .= "        , orgao_nivel.valor                          \n ";
    $stSQL .= " ) as tabela                                         \n ";

    return $stSQL;
}

}
