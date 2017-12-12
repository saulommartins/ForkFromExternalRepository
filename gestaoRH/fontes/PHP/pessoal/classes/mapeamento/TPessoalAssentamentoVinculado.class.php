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
    * Classe de mapeamento da tabela PESSOAL.ASSENTAMENTO_VINCULADO
    * Data de Criação: 04/08/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    * Casos de uso: uc-04.04.13
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  PESSOAL.ASSENTAMENTO_VINCULADO
  * Data de Criação: 04/08/2005

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalAssentamentoVinculado extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalAssentamentoVinculado()
{
    parent::Persistente();
    $this->setTabela('pessoal.assentamento_vinculado');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_condicao,timestamp,cod_assentamento,cod_assentamento_assentamento');

    $this->AddCampo('cod_condicao','integer',true,'',true,true);
    $this->AddCampo('timestamp','timestamp',false,'',true,true);
    $this->AddCampo('cod_assentamento','integer',true,'',true,true);
    $this->AddCampo('cod_assentamento_assentamento','integer',true,'',true,true);
    $this->AddCampo('condicao','char',false,'1',true,false);
    $this->AddCampo('dias_incidencia','integer,',false,'',true,false);
    $this->AddCampo('dias_protelar_averbar','integer,',false,'',false,false);
}

function recuperaAssentamentoVinculado(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaAssentamentoVinculado().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaAssentamentoVinculado()
{
   $stSql .= " select   pav.*                                                                                                     \n";
   $stSql .= "        , pavf.cod_funcao                                                                                           \n";
   $stSql .= "        , pavf.cod_biblioteca                                                                                       \n";
   $stSql .= "        , pavf.cod_modulo                                                                                           \n";
   $stSql .= "        , pcla.cod_classificacao                                                                                    \n";
   $stSql .= "        , pcla.descricao                                                                                            \n";
   $stSql .= "        , trim(paa.sigla) as sigla                                                                                  \n";
   $stSql .= "                                                                                                                    \n";
   $stSql .= "  from        pessoal.condicao_assentamento as pca                                                                  \n";
   $stSql .= "  inner join  pessoal.assentamento_vinculado as pav                                                                 \n";
   $stSql .= "            on ( pav.cod_condicao     = pca.cod_condicao                                                            \n";
   $stSql .= "             and pav.timestamp        = pca.timestamp                                                               \n";
   $stSql .= "             and pav.cod_assentamento = pca.cod_assentamento )                                                      \n";
   $stSql .= "  inner join  pessoal.assentamento_assentamento as paa                                                              \n";
   $stSql .= "            on ( paa.cod_assentamento = pav.cod_assentamento_assentamento )                                         \n";
   $stSql .= "  inner join  pessoal.classificacao_assentamento as pcla                                                            \n";
   $stSql .= "            on ( pcla.cod_classificacao = paa.cod_classificacao )                                                   \n";
   //$stSql .= "  inner join ( select max(pca_.timestamp)  as max_timestamp from pessoal.condicao_assentamento as pca_ ) as max   \n";
   //$stSql .= "            on ( pca.timestamp = max.max_timestamp )                                                              \n";
   $stSql .= "  left  join pessoal.assentamento_vinculado_funcao as pavf                                        \n";
   $stSql .= "            on ( pavf.cod_condicao                   = pav.cod_condicao                           \n";
   $stSql .= "             and pavf.timestamp                      = pav.timestamp                              \n";
   $stSql .= "             and pavf.cod_assentamento               = pav.cod_assentamento                       \n";
   $stSql .= "             and pavf.cod_assentamento_assentamento  = pav.cod_assentamento_assentamento )        \n";

   return $stSql;
}

function recuperaAssentamento(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaAssentamento().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaAssentamento()
{
    $stSQL .="SELECT                                                                                      \n";
    $stSQL .="             av.*                                                                           \n";
    $stSQL .="           , af.cod_funcao                                                                  \n";
    $stSQL .="           , af.cod_biblioteca                                                              \n";
    $stSQL .="           , af.cod_modulo                                                                  \n";
    $stSQL .="           , ca.cod_classificacao                                                           \n";
    $stSQL .="           , ca.descricao                                                                   \n";
    $stSQL .="           , trim(paa.sigla) as sigla                                                       \n";
    $stSQL .="       FROM                                                                                 \n";
    $stSQL .="              pessoal.assentamento_vinculado as av                                          \n";
    $stSQL .="LEFT  JOIN                                                                                  \n";
    $stSQL .="               pessoal.assentamento_vinculado_funcao as af                                  \n";
    $stSQL .="        ON (                                                                                \n";
    $stSQL .="                     af.timestamp                     = av.timestamp                        \n";
    $stSQL .="             AND     af.cod_condicao                  = av.cod_condicao                     \n";
    $stSQL .="             AND     af.cod_assentamento_assentamento = av.cod_assentamento_assentamento    \n";
    $stSQL .="             AND     af.cod_assentamento              = av.cod_assentamento                 \n";
    $stSQL .="             AND     af.dias_incidencia               = av.dias_incidencia                  \n";
    $stSQL .="             AND     af.dias_protelar_averbar         = av.dias_protelar_averbar            \n";
    $stSQL .="             AND     af.condicao                      = av.condicao        )                \n";
    $stSQL .="      JOIN                                                                                  \n";
    $stSQL .="               pessoal.assentamento_assentamento as paa                                     \n";
    $stSQL .="        ON     paa.cod_assentamento = av.cod_assentamento                                   \n";
    $stSQL .="      JOIN                                                                                  \n";
    $stSQL .="               pessoal.classificacao_assentamento as ca                                     \n";
    $stSQL .="        ON     ca.cod_classificacao = paa.cod_classificacao                                 \n";
    $stSQL .="                                                                                            \n";
    $stSQL .="      JOIN    (SELECT                                                                       \n";
    $stSQL .="                      cod_assentamento,                                                     \n";
    $stSQL .="                      max(timestamp) as timestamp,                                          \n";
    $stSQL .="                      cod_condicao   as cod_condicao                                        \n";
    $stSQL .="                FROM pessoal.condicao_assentamento                                          \n";
    $stSQL .="            GROUP BY cod_assentamento,cod_condicao ) as mc                                  \n";
    $stSQL .="       ON   mc.cod_assentamento  = paa.cod_assentamento                                     \n";
    $stSQL .="       AND  av.cod_assentamento  = mc.cod_assentamento                                      \n";
    $stSQL .="       AND  av.cod_condicao      = mc.cod_condicao                                          \n";
    $stSQL .="       AND  av.timestamp         = mc.timestamp                                             \n";

    return $stSQL;
}

}
