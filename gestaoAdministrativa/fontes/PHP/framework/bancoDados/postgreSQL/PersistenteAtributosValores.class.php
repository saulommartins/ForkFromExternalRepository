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
    * Classe de Persistência direcionada para manipulação dos Valores dos Atributos Dinâmicos
    * Data de Criação   : 21/05/2004

    * @author Desenvolvedor: Diego Barbosa Victoria
    * @author Analista: Jorge B. Ribarr

    * @package Conectividade
    * @subpackage Persistente

Casos de uso: uc-01.01.00

*/

/**
    * Classe de Persistência direcionada para manipulação de Atributos Dinâmicos
    * @author Desenvolvedor: Diego Barbosa Victoria
    * @author Analista: Jorge B. Ribarr
*/
class PersistenteAtributosValores extends Persistente
{
/**
    * @var Object
    * @access Private
*/
var $obPersistenteAtributo;

/**
    * @access Public
    * @param String $valor
*/
function setPersistenteAtributo($valor) { $this->obPersistenteAtributo = $valor; }

/**
    * @access Public
    * @return String
*/
function getPersistenteAtributo() { return $this->obPersistenteAtributo;   }

/**
    * Método Construtor
    * @access Private
*/
function PersistenteAtributosValores()
{
    parent::Persistente();
    include_once (CAM_GA_ADM_MAPEAMENTO.'TAdministracaoAtributoDinamico.class.php');
    include_once ( CAM_GA_ADM_MAPEAMENTO.'TAdministracaoCadastro.class.php');

    $this->setPersistenteAtributo( new TAdministracaoAtributoDinamico );
}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaAtributosSelecionados.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stFiltro    String de Filtro do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function RecuperaAtributosSelecionadosValores(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaAtributosSelecionadosValores().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    if( $this->getPersistenteAtributo() )
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    else
        $obErro->setDescricao("Deve ser setado o atributo PersistenteAtributo");

    return $obErro;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaAtributosHistorico.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stFiltro    String de Filtro do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function RecuperaAtributosSelecionadosValoresHistorico(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaAtributosSelecionadosValoresHistorico().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    if( $this->getPersistenteAtributo() )
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    else
        $obErro->setDescricao("Deve ser setado o atributo PersistenteAtributo");

    return $obErro;
}

function montaRecuperaAtributosSelecionadosValores()
{
    $stSql  = "  SELECT                                                                     \n";
    $stSql .= "     AD.cod_cadastro,                                                        \n";
    $stSql .= "     AD.cod_atributo,                                                        \n";
    $stSql .= "     AD.ativo,                                                               \n";
    $stSql .= "     AD.nao_nulo,                                                            \n";
    $stSql .= "     AD.nom_atributo,                                                        \n";
    $stSql .= "     CASE TA.cod_tipo                                                        \n";
    $stSql .= "         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)\n";
    $stSql .= "         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'')         \n";
    $stSql .= "     END AS valor_padrao,                                                    \n";
    $stSql .= "     CASE TA.cod_tipo                                                        \n";
    $stSql .= "       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''))\n";
    $stSql .= "       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))\n";
    $stSql .= "         ELSE         null                                                   \n";
    $stSql .= "     END AS valor_padrao_desc,                                               \n";
    $stSql .= "     CASE TA.cod_tipo WHEN                                                   \n";
    $stSql .= "         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)\n";
    $stSql .= "         ELSE         null                                                   \n";
    $stSql .= "     END AS valor_desc,                                                      \n";
    $stSql .= "     AD.ajuda,                                                               \n";
    $stSql .= "     AD.mascara,                                                             \n";
    $stSql .= "     TA.cod_tipo,                                                            \n";
    $stSql .= "     TA.nom_tipo,                                                            \n";
    $stSql .= "     VALOR.valor,                                                            \n";
    $stSql .= "     VALOR.timestamp                                                         \n";
    $stSql .= "  FROM                                                                       \n";
    $stSql .= "     administracao.atributo_dinamico          AS AD,                                   \n";
    $stSql .= "     administracao.tipo_atributo              AS TA,                                   \n";
    $stSql .= "     ".$this->obPersistenteAtributo->getTabela()." AS ACA                    \n";
    $stSql .= "     LEFT JOIN                                                               \n";
    $stSql .= "     ".$this->getTabela()."         AS VALOR                                 \n";
    $stSql .= "  ON ( ACA.cod_atributo = VALOR.cod_atributo                                 \n";
    $stSql .= "          AND ACA.cod_cadastro = VALOR.cod_cadastro                          \n";
    $stSql .= "             ".$this->getDado('stCondicao')."                                \n";
    $stSql .= "         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN ( \n";
    $stSql .= "             SELECT                                                          \n";
    $stSql .= "         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)   \n";
    $stSql .= "             FROM                                                            \n";
    $stSql .= "                ".$this->obPersistenteAtributo->getTabela()." AS ACA,        \n";
    $stSql .= "                ".$this->getTabela()."         AS VALOR,                     \n";
    $stSql .= "                administracao.atributo_dinamico          AS AD,                        \n";
    $stSql .= "                administracao.tipo_atributo              AS TA                         \n";
    $stSql .= "             WHERE                                                           \n";
    $stSql .= "                ACA.cod_atributo = AD.cod_atributo                           \n";
    $stSql .= "                AND ACA.cod_cadastro = AD.cod_cadastro                       \n";
    $stSql .= "                AND ACA.cod_modulo   = AD.cod_modulo                         \n";
    $stSql .= "             AND ACA.cod_atributo = VALOR.cod_atributo                       \n";
    //$stSql .= "             AND VALOR.valor not like '% ,%'                                 \n";
    $stSql .= "             AND ACA.cod_cadastro = VALOR.cod_cadastro                       \n";
    $stSql .= "             AND ACA.cod_modulo   = VALOR.cod_modulo                         \n";
    $stSql .= "             ".$this->getDado('stCondicao')."                                \n";
    $stSql .= "             AND AD.cod_tipo = TA.cod_tipo                                   \n";
    $stSql .= "             AND ACA.ativo = true                                            \n";
    $stSql .= "             AND AD.cod_modulo   =".$this->getDado('cod_modulo')."           \n";
    $stSql .= "             AND AD.cod_cadastro=".$this->getDado('cod_cadastro')."         \n";
    $stSql .= "             ".$this->getDado('stFiltroValores')."                           \n";
    $stSql .= "             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo ".$this->getDado('stGroupBy')." \n";
    $stSql .= "                                  )                                          \n";
    $stSql .= "             ".$this->getDado('stFiltroValores')."                           \n";
    $stSql .= "     )                                                                       \n";
    $stSql .= "  WHERE                                                                      \n";
    $stSql .= "      AD.cod_tipo = TA.cod_tipo                                              \n";
    $stSql .= "  AND ACA.ativo = true                                                       \n";
    $stSql .= "  AND     AD.ativo                                                           \n";
    $stSql .= "  AND AD.cod_atributo =  ACA.cod_atributo                                    \n";
    $stSql .= "  AND AD.cod_modulo   = ACA.cod_modulo                                       \n";
    $stSql .= "  AND AD.cod_cadastro = ACA.cod_cadastro                                     \n";
    $stSql .= "  AND ACA.cod_cadastro=".$this->getDado('cod_cadastro')."                    \n";
    $stSql .= "  AND ACA.cod_modulo  =".$this->getDado('cod_modulo')."                      \n";
    $stSql .= " ".$this->getDado('stFiltroAtributos')."                                     \n";
    $stSql .= " ".$this->getDado('stFiltro')."                                              \n";

    return $stSql;
}

function montaRecuperaAtributosSelecionadosValoresHistorico()
{
    $stSql  = "  SELECT                                                                     \n";
    $stSql .= "     ACA.cod_cadastro,                                                       \n";
    $stSql .= "     ACA.cod_atributo,                                                       \n";
    $stSql .= "     ACA.ativo,                                                              \n";
    $stSql .= "     AD.nao_nulo,                                                            \n";
    $stSql .= "     AD.nom_atributo,                                                        \n";
    $stSql .= "     CASE TA.cod_tipo                                                        \n";
    $stSql .= "         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)\n";
    $stSql .= "         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'')         \n";
    $stSql .= "     END AS valor_padrao,                                                    \n";
    $stSql .= "     CASE TA.cod_tipo                                                        \n";
    $stSql .= "       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''))\n";
    $stSql .= "       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))\n";
    $stSql .= "         ELSE         null                                                   \n";
    $stSql .= "     END AS valor_padrao_desc,                                               \n";
    $stSql .= "     CASE TA.cod_tipo WHEN                                                   \n";
    $stSql .= "         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))\n";
    $stSql .= "         ELSE         null                                                   \n";
    $stSql .= "     END AS valor_desc,                                                      \n";
    $stSql .= "     AD.ajuda,                                                               \n";
    $stSql .= "     AD.mascara,                                                             \n";
    $stSql .= "     TA.cod_tipo,                                                            \n";
    $stSql .= "     TA.nom_tipo,                                                            \n";
    $stSql .= "     VALOR.valor,                                                            \n";
    $stSql .= "     VALOR.timestamp                                                         \n";
    $stSql .= "  FROM                                                                       \n";
    $stSql .= "     administracao.atributo_dinamico          AS AD,                                   \n";
    $stSql .= "     administracao.tipo_atributo              AS TA,                                   \n";
    $stSql .= "     ".$this->obPersistenteAtributo->getTabela()." AS ACA                    \n";
    $stSql .= "     LEFT OUTER JOIN                                                         \n";
    $stSql .= "     ".$this->getTabela()."         AS VALOR                                 \n";
    $stSql .= "  ON ( ACA.cod_atributo = VALOR.cod_atributo                                 \n";
    $stSql .= "          AND ACA.cod_cadastro = VALOR.cod_cadastro                          \n";
    $stSql .= "             ".$this->getDado('stCondicao')."                                \n";
    $stSql .= "        AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (                       \n";
    $stSql .= "             SELECT                                                          \n";
    $stSql .= "        (CAST(VALOR.timestamp as varchar))||CAST(VALOR.cod_atributo as varchar)                               \n";
    $stSql .= "             FROM                                                            \n";
    $stSql .= "                ".$this->obPersistenteAtributo->getTabela()." AS ACA,        \n";
    $stSql .= "                ".$this->getTabela()."         AS VALOR,                     \n";
    $stSql .= "                administracao.atributo_dinamico          AS AD,                        \n";
    $stSql .= "                administracao.tipo_atributo              AS TA                         \n";
    $stSql .= "             WHERE                                                           \n";
    $stSql .= "                ACA.cod_atributo = AD.cod_atributo                           \n";
    $stSql .= "             AND ACA.cod_atributo = VALOR.cod_atributo                       \n";
    $stSql .= "             AND ACA.cod_cadastro = VALOR.cod_cadastro                       \n";
    $stSql .= "             ".$this->getDado('stCondicao')."                                \n";
    $stSql .= "             AND AD.cod_tipo = TA.cod_tipo                                   \n";
    $stSql .= "             AND ACA.ativo = true                                            \n";
    $stSql .= "             AND AD.cod_modulo   =".$this->getDado('cod_modulo')."           \n";
    $stSql .= "             AND ACA.cod_cadastro=".$this->getDado('cod_cadastro')."         \n";
    $stSql .= "             ".$this->getDado('stFiltroValores')."                           \n";
    $stSql .= " GROUP BY VALOR.cod_cadastro, VALOR.timestamp ,VALOR.cod_atributo ".$this->getDado('stGroupBy')." \n";
    $stSql .= "                                  )                                          \n";
    $stSql .= "             ".$this->getDado('stFiltroValores')."                           \n";
    $stSql .= "     )                                                                       \n";
    $stSql .= "  WHERE                                                                      \n";
    $stSql .= "     ACA.cod_atributo = AD.cod_atributo                                      \n";
    $stSql .= "  AND AD.cod_tipo = TA.cod_tipo                                              \n";
    $stSql .= "  AND ACA.ativo = true                                                       \n";
    $stSql .= "  AND ACA.cod_modulo = AD.cod_modulo                                         \n";
    $stSql .= "  AND ACA.cod_cadastro = AD.cod_cadastro                                     \n";
    $stSql .= "  AND AD.cod_modulo   =".$this->getDado('cod_modulo')."                      \n";
    $stSql .= "  AND ACA.cod_cadastro=".$this->getDado('cod_cadastro')."                    \n";
    $stSql .= " ".$this->getDado('stFiltroAtributos')."                                     \n";
    $stSql .= " ".$this->getDado('stFiltro')."                                              \n";

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaAtributosSelecionados.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stFiltro    String de Filtro do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function RecuperaAtributosAtivosInativosValores(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaAtributosAtivosInativosValores().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    if( $this->getPersistenteAtributo() )
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    else
        $obErro->setDescricao("Deve ser setado o atributo PersistenteAtributo");

    return $obErro;
}

function montaRecuperaAtributosAtivosInativosValores()
{
    $stSql  = "  SELECT                                                                     \n";
    $stSql .= "     AD.cod_cadastro,                                                        \n";
    $stSql .= "     AD.cod_atributo,                                                        \n";
    $stSql .= "     AD.ativo,                                                               \n";
    $stSql .= "     AD.nao_nulo,                                                            \n";
    $stSql .= "     AD.nom_atributo,                                                        \n";
    $stSql .= "     CASE TA.cod_tipo                                                        \n";
    $stSql .= "         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)\n";
    $stSql .= "         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'')         \n";
    $stSql .= "     END AS valor_padrao,                                                    \n";
    $stSql .= "     CASE TA.cod_tipo                                                        \n";
    $stSql .= "       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''))\n";
    $stSql .= "       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))\n";
    $stSql .= "         ELSE         null                                                   \n";
    $stSql .= "     END AS valor_padrao_desc,                                               \n";
    $stSql .= "     CASE TA.cod_tipo WHEN                                                   \n";
    $stSql .= "         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)\n";
    $stSql .= "         ELSE         null                                                   \n";
    $stSql .= "     END AS valor_desc,                                                      \n";
    $stSql .= "     AD.ajuda,                                                               \n";
    $stSql .= "     AD.mascara,                                                             \n";
    $stSql .= "     TA.cod_tipo,                                                            \n";
    $stSql .= "     TA.nom_tipo,                                                            \n";
    $stSql .= "     VALOR.valor,                                                            \n";
    $stSql .= "     VALOR.timestamp                                                         \n";
    $stSql .= "  FROM                                                                       \n";
    $stSql .= "     administracao.atributo_dinamico          AS AD,                                   \n";
    $stSql .= "     administracao.tipo_atributo              AS TA,                                   \n";
    $stSql .= "     ".$this->obPersistenteAtributo->getTabela()." AS ACA                    \n";
    $stSql .= "     LEFT JOIN                                                               \n";
    $stSql .= "     ".$this->getTabela()."         AS VALOR                                 \n";
    $stSql .= "  ON ( ACA.cod_atributo = VALOR.cod_atributo                                 \n";
    $stSql .= "          AND ACA.cod_cadastro = VALOR.cod_cadastro                          \n";
    $stSql .= "             ".$this->getDado('stCondicao')."                                \n";
    $stSql .= "          AND VALOR.timestamp::varchar||VALOR.cod_atributo::varchar IN (                       \n";
    $stSql .= "             SELECT                                                          \n";
    $stSql .= "                (max(VALOR.timestamp)::varchar)||VALOR.cod_atributo::varchar                     \n";
    $stSql .= "             FROM                                                            \n";
    $stSql .= "                ".$this->obPersistenteAtributo->getTabela()." AS ACA,        \n";
    $stSql .= "                ".$this->getTabela()."         AS VALOR,                     \n";
    $stSql .= "                administracao.atributo_dinamico          AS AD,                        \n";
    $stSql .= "                administracao.tipo_atributo              AS TA                         \n";
    $stSql .= "             WHERE                                                           \n";
    $stSql .= "                ACA.cod_atributo = AD.cod_atributo                           \n";
    $stSql .= "                AND ACA.cod_cadastro = AD.cod_cadastro                       \n";
    $stSql .= "                AND ACA.cod_modulo   = AD.cod_modulo                         \n";
    $stSql .= "             AND ACA.cod_atributo = VALOR.cod_atributo                       \n";
    $stSql .= "             AND ACA.cod_cadastro = VALOR.cod_cadastro                       \n";
    $stSql .= "             AND ACA.cod_modulo   = VALOR.cod_modulo                         \n";
    $stSql .= "             ".$this->getDado('stCondicao')."                                \n";
    $stSql .= "             AND AD.cod_tipo = TA.cod_tipo                                   \n";
    $stSql .= "             AND AD.cod_modulo   =".$this->getDado('cod_modulo')."           \n";
    $stSql .= "             AND AD.cod_cadastro=".$this->getDado('cod_cadastro')."         \n";
    $stSql .= "             ".$this->getDado('stFiltroValores')."                           \n";
    $stSql .= "             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo ".$this->getDado('stGroupBy')." \n";
    $stSql .= "                                  )                                          \n";
    $stSql .= "             ".$this->getDado('stFiltroValores')."                           \n";
    $stSql .= "     )                                                                       \n";
    $stSql .= "  WHERE                                                                      \n";
    $stSql .= "      AD.cod_tipo = TA.cod_tipo                                              \n";
    $stSql .= "  AND AD.cod_atributo =  ACA.cod_atributo                                    \n";
    $stSql .= "  AND AD.cod_modulo   = ACA.cod_modulo                                       \n";
    $stSql .= "  AND AD.cod_cadastro = ACA.cod_cadastro                                     \n";
    $stSql .= "  AND ACA.cod_cadastro=".$this->getDado('cod_cadastro')."                    \n";
    $stSql .= "  AND ACA.cod_modulo  =".$this->getDado('cod_modulo')."                      \n";
    $stSql .= " ".$this->getDado('stFiltroAtributos')."                                     \n";
    $stSql .= " ".$this->getDado('stFiltro')."                                              \n";

    return $stSql;
}

}
