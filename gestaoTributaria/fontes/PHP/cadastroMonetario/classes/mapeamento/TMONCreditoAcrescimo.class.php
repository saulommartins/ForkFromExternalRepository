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
    * Classe de regra de negócio para MONETARIO.CREDITO_ACRESCIMO
    * Data de Criação: 22/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho

    * @package URBEM
    * @subpackage Regra

    * $Id: TMONCreditoAcrescimo.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.05.10
*/

/*
$Log$
Revision 1.10  2007/08/06 18:59:52  cercato
Bug#9792#

Revision 1.9  2006/11/22 15:41:00  cercato
Bug #7578#

Revision 1.8  2006/09/15 14:46:11  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TMONCreditoAcrescimo extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TMONCreditoAcrescimo()
{
   parent::Persistente();
   $this->setTabela('monetario.credito_acrescimo');

   $this->setCampoCod('');
   $this->setComplementoChave('cod_credito,cod_natureza,cod_genero,cod_especie,cod_acrescimo,cod_tipo');

   $this->AddCampo('cod_credito','INTEGER',true,'',true,false);
   $this->AddCampo('cod_natureza','INTEGER',true,'',true,true);
   $this->AddCampo('cod_genero','INTEGER',true,'',true,true);
   $this->AddCampo('cod_especie','INTEGER',true,'',true,true);
   $this->AddCampo('cod_acrescimo','INTEGER',true,'',true, true );
   $this->AddCampo('timestamp','timestamp',false,'',false,true);
   $this->AddCampo('cod_tipo','INTEGER',false,'',true,true);
}

function montaRecuperaRelacionamento()
{
    $stSql  = " SELECT                                                                                                                                               \r\n ";
    $stSql .= "     mc.cod_credito,                                                                                                                              \r\n ";
    $stSql .= "     mc.cod_tipo,                                                                                                                                   \r\n ";
    $stSql .= "     mn.cod_natureza,                                                                                                                           \r\n ";
    $stSql .= "     mn.nom_natureza,                                                                                                                          \r\n ";
    $stSql .= "     mg.cod_genero,                                                                                                                             \r\n ";
    $stSql .= "     mg.nom_genero,                                                                                                                            \r\n ";
    $stSql .= "     me.cod_especie,                                                                                                                            \r\n ";
    $stSql .= "     me.nom_especie,                                                                                                                           \r\n ";
    $stSql .= "     mc.descricao_credito,                                                                                                                    \r\n ";
    $stSql .= "     ag.cod_grupo,                                                                                                                                \r\n ";
    $stSql .= "     ag.ano_exercicio                                                                                                                            \r\n ";
    $stSql .= " FROM                                                                                                                                                  \r\n ";
    $stSql .= "     monetario.credito as mc                                                                                                                \r\n ";
    $stSql .= "     INNER JOIN monetario.especie_credito as me ON   mc.cod_natureza = me.cod_natureza   AND     \r\n ";
    $stSql .= "                                                     mc.cod_genero = me.cod_genero       AND                                    \r\n ";
    $stSql .= "                                                     mc.cod_especie=me.cod_especie                                                  \r\n ";
    $stSql .= "                                                                                                                                                            \r\n ";
    $stSql .= "     INNER JOIN monetario.genero_credito as mg ON    me.cod_natureza = mg.cod_natureza AND       \r\n ";
    $stSql .= "                                                     me.cod_genero = mg.cod_genero                                                  \r\n ";
    $stSql .= "                                                                                                                                                            \r\n ";
    $stSql .= "     INNER JOIN monetario.natureza_credito as mn ON  mg.cod_natureza = mn.cod_natureza               \r\n ";
    $stSql .= "                                                                                                                                                            \r\n ";
    $stSql .= "     LEFT JOIN arrecadacao.credito_grupo as ac ON      ac.cod_credito = mc.cod_credito AND              \r\n ";
    $stSql .= "                                                    ac.cod_especie = mc.cod_especie AND                                           \r\n ";
    $stSql .= "                                                    ac.cod_genero  = mc.cod_genero  AND                                           \r\n ";
    $stSql .= "                                                    ac.cod_natureza= mc.cod_natureza                                                \r\n ";
    $stSql .= "     LEFT JOIN arrecadacao.grupo_credito as ag ON      ag.cod_grupo = ac.cod_grupo     AND             \r\n ";
    $stSql .= "                                                    ag.ano_exercicio = ano_exercicio                                                    \r\n ";

return $stSql;
}

function recuperaAcrescimosDoCredito(&$rsRecordSet, $stFiltro ="", $stOrdem="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaAcrescimosDoCredito().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    //$this->debug();
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaAcrescimosDoCredito()
{
$stSql  =   "   SELECT *                                                                                                                     \n";
$stSql .=   "   FROM                                                                                                                          \n";
$stSql .=   "   monetario.credito_acrescimo as ca                                                                            \n";
$stSql .=   "   INNER JOIN monetario.acrescimo as ac ON ca.cod_acrescimo = ac.cod_acrescimo    \n";

return $stSql;

}

function recuperaAcrescimosDoCreditoGF(&$rsRecordSet, $stFiltro ="", $stOrdem="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaAcrescimosDoCreditoGF().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    //$this->debug();exit;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaAcrescimosDoCreditoGF()
{
    $stSql  =   " SELECT
                    mca.cod_acrescimo,
                    mca.cod_tipo,
                    cpa.cod_plano,
                    orc.cod_receita,
                    CASE WHEN cpa IS NOT NULL THEN
                        true
                    ELSE
                        false
                    END AS tipo_cpa,
                    CASE WHEN orc IS NOT NULL THEN
                        true
                    ELSE
                        false
                    END AS tipo_orc

                FROM
                    monetario.credito_acrescimo AS mca

                LEFT JOIN
                    contabilidade.plano_analitica_credito_acrescimo AS cpa
                ON
                    cpa.cod_credito = mca.cod_credito
                    AND cpa.cod_genero = mca.cod_genero
                    AND cpa.cod_especie = mca.cod_especie
                    AND cpa.cod_natureza = mca.cod_natureza
                    AND cpa.cod_acrescimo = mca.cod_acrescimo
                    AND cpa.cod_tipo = mca.cod_tipo

                LEFT JOIN
                    orcamento.receita_credito_acrescimo AS orc
                ON
                    orc.cod_credito = mca.cod_credito
                    AND orc.cod_genero = mca.cod_genero
                    AND orc.cod_especie = mca.cod_especie
                    AND orc.cod_natureza = mca.cod_natureza
                    AND orc.cod_acrescimo = mca.cod_acrescimo
                    AND orc.cod_tipo = mca.cod_tipo \n";

    return $stSql;
}

} // fecha classe
