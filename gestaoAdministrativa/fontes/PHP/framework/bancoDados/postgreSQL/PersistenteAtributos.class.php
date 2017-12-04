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
    * Classe de Persistência direcionada para manipulação de Atributos Dinâmicos
    * Data de Criação   : 05/02/2004

    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package Conectividade
    * @subpackage Persistente

Casos de uso: uc-01.01.00

*/

/**
    * Classe de Persistência direcionada para manipulação de Atributos Dinâmicos
    * @author Desenvolvedor: Diego Barbosa Victoria
*/
class PersistenteAtributos extends Persistente
{
/**
    * @var Object
    * @access Private
*/
var $obPersistenteAtributoModulo;

/**
    * @access Public
    * @param String $valor
*/
function setPersistenteAtributoModulo($valor) { $this->obPersistenteAtributoModulo = $valor; }

/**
    * @access Public
    * @return String
*/
function getPersistenteAtributoModulo() { return $this->obPersistenteAtributoModulo;   }
/**
    * Método Construtor
    * @access Private
*/
function PersistenteAtributos()
{
    parent::Persistente();
    include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoCadastro.class.php" );
    $this->setPersistenteAtributoModulo( new TAdministracaoCadastro );
}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaAtributos.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stFiltro    String de Filtro do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function RecuperaAtributos(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaAtributos().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
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
function RecuperaAtributosSelecionados(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaAtributosSelecionados().$stFiltro.$stOrdem;

    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaAtributosDisponiveis.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stFiltro    String de Filtro do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function RecuperaAtributosDisponiveis(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

//    if( is_object($this->obPersistenteAtributoModulo) )
//        $stSql = $this->montaRecuperaAtributosDisponiveisCadastro().$stFiltro.$stOrdem;
//    else

    $stSql = $this->montaRecuperaAtributosDisponiveis().$stFiltro.$stOrdem;

    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Método que pode ser sobreposto através de uma extensão desta classe.
    * @access Private
    * @return String  Comando SQL
*/
function montaRecuperaAtributosSelecionados()
{
    $stSqlTabela = $this->getTabela()."          AS ACA,     \n";
    $stSqlFiltro  = " ACA.cod_atributo = AD.cod_atributo AND \n";
    $stSqlFiltro .= " ACA.cod_cadastro = AD.cod_cadastro AND \n";
    $stSqlFiltro .= " ACA.cod_modulo   = AD.cod_modulo AND   \n";
    $stSqlFiltro .= " ACA.ativo        = true AND            \n";

    $stSql  = "  SELECT                                                  \n";
    $stSql .= "     AD.cod_cadastro,                                    \n";
    $stSql .= "     AD.cod_atributo,                                    \n";
    $stSql .= "     AD.ativo,                                           \n";
    $stSql .= "     AD.nao_nulo,                                         \n";
    $stSql .= "     AD.nom_atributo,                                     \n";
    $stSql .= "     administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo, AD.cod_cadastro, '') as valor_padrao,          \n";
    $stSql .= "     CASE TA.cod_tipo                                                        \n";
    $stSql .= "       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo, AD.cod_cadastro,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo, AD.cod_cadastro,''))\n";
    $stSql .= "       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo, AD.cod_cadastro,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo, AD.cod_cadastro,''))\n";
    $stSql .= "         ELSE         null                                                   \n";
    $stSql .= "     END AS valor_padrao_desc,                                               \n";
    $stSql .= "     CASE TA.cod_tipo WHEN                                                   \n";
    $stSql .= "         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo, AD.cod_cadastro,'')          \n";
    $stSql .= "         ELSE         null                                                   \n";
    $stSql .= "     END AS valor_desc,                                                      \n";
    $stSql .= "     AD.ajuda,                                            \n";
    $stSql .= "     AD.mascara,                                          \n";
    $stSql .= "     TA.nom_tipo,                                         \n";
    $stSql .= "     TA.cod_tipo                                          \n";
    $stSql .= "  FROM                                                    \n";
    $stSql .= $stSqlTabela;
    $stSql .= "     administracao.atributo_dinamico             AS AD,             \n";
    $stSql .= "     administracao.tipo_atributo                 AS TA              \n";
    $stSql .= "  WHERE                                                   \n";
    $stSql .= $stSqlFiltro;
    $stSql .= "      TA.cod_tipo = AD.cod_tipo                           \n";
    $stSql .= "  AND AD.ativo = true                                    \n";
    $stSql .= "  AND AD.cod_modulo   =".$this->getDado('cod_modulo')."   \n";
    $stSql .= "  AND AD.cod_cadastro=".$this->getDado('cod_cadastro')." \n";

    return $stSql;
}

/**
    * Método que pode ser sobreposto através de uma extensão desta classe.
    * @access Private
    * @return String  Comando SQL
*/
function montaRecuperaAtributosDisponiveis()
{
    $stSql  = "  SELECT                                                      \n";
    $stSql .= "     AD.nao_nulo,                                             \n";
    $stSql .= "     AD.nom_atributo,                                         \n";
    $stSql .= "     AD.cod_atributo,                                         \n";
    $stSql .= "     administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo, AD.cod_cadastro,'') as valor_padrao,          \n";
    $stSql .= "     CASE TA.cod_tipo                                                        \n";
    $stSql .= "       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo, AD.cod_cadastro,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo, AD.cod_cadastro,''))\n";
    $stSql .= "       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo, AD.cod_cadastro,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo, AD.cod_cadastro,''))\n";
    $stSql .= "         ELSE         null                                                   \n";
    $stSql .= "     END AS valor_padrao_desc,                                               \n";
    $stSql .= "     CASE TA.cod_tipo WHEN                                                   \n";
    $stSql .= "         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo, AD.cod_cadastro,'')          \n";
    $stSql .= "         ELSE         null                                                   \n";
    $stSql .= "     END AS valor_desc,                                                      \n";
    $stSql .= "     AD.ajuda,                                                \n";
    $stSql .= "     AD.mascara,                                              \n";
    $stSql .= "     TA.nom_tipo,                                             \n";
    $stSql .= "     TA.cod_tipo                                              \n";
    $stSql .= "  FROM                                                        \n";
    $stSql .= "     administracao.atributo_dinamico             AS AD,                 \n";
    $stSql .= "     administracao.tipo_atributo                 AS TA                  \n";
    $stSql .= "  WHERE   AD.cod_tipo = TA.cod_tipo                           \n";
    $stSql .= "  AND     AD.cod_atributo NOT IN (                            \n";
    $stSql .= "     SELECT cod_atributo FROM                                 \n";
    $stSql .= "     ".$this->getTabela()."                                   \n";
    $stSql .= "     WHERE cod_cadastro =".$this->getDado('cod_cadastro')."   \n";
    $stSql .= "  AND    AD.cod_modulo   = ".$this->getDado('cod_modulo')."   \n";
    //Adicionei essa linha pois o filtro estva sendo setado na RCadastroDinamico
    //e nao estava sendo usado aqui (Marcelo)
    $stSql .= "     ".$this->getDado("stFiltro")."                           \n";

    $stSql .= "     AND   ativo = 't')                                       \n";
    $stSql .= "  AND    AD.cod_modulo   = ".$this->getDado('cod_modulo')."   \n";
    $stSql .= "  AND    AD.cod_cadastro = ".$this->getDado('cod_cadastro')." \n";
    $stSql .= "  AND    AD.ativo = 't' \n";

    return $stSql;
}

/**
    * Método que pode ser sobreposto através de uma extensão desta classe.
    * @access Private
    * @return String  Comando SQL
*/
function montaRecuperaAtributos()
{
    $stSql  = "  SELECT                                                      \n";
    $stSql .= "     AD.nao_nulo,                                             \n";
    $stSql .= "     AD.nom_atributo,                                         \n";
    $stSql .= "     AD.cod_atributo,                                         \n";
    $stSql .= "     administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo, AD.cod_cadastro,'') as valor_padrao,         \n";
    $stSql .= "     CASE TA.cod_tipo                                                        \n";
    $stSql .= "       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo, AD.cod_cadastro,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo, AD.cod_cadastro,''))\n";
    $stSql .= "       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo, AD.cod_cadastro,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo, AD.cod_cadastro,''))\n";
    $stSql .= "         ELSE         null                                                   \n";
    $stSql .= "     END AS valor_padrao_desc,                                               \n";
    $stSql .= "     CASE TA.cod_tipo WHEN                                                   \n";
    $stSql .= "         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo, AD.cod_cadastro,'')          \n";
    $stSql .= "         ELSE         null                                                   \n";
    $stSql .= "     END AS valor_desc,                                                      \n";
    $stSql .= "     AD.ajuda,                                                \n";
    $stSql .= "     AD.mascara,                                              \n";
    $stSql .= "     AC.cod_cadastro,                                         \n";
    $stSql .= "     TA.nom_tipo,                                             \n";
    $stSql .= "     TA.cod_tipo                                              \n";
    $stSql .= "  FROM                                                        \n";
    $stSql .= "     administracao.atributo_dinamico   AS AD,                 \n";
    $stSql .= "     ".$this->getTabela()."            AS AC,                 \n";
    $stSql .= "     administracao.tipo_atributo       AS TA                  \n";
    $stSql .= "  WHERE   AD.cod_tipo = TA.cod_tipo                           \n";
    $stSql .= "  AND     AD.cod_atributo = AC.cod_atributo                   \n";
    $stSql .= "  AND     AD.cod_modulo   = AC.cod_modulo                     \n";
    $stSql .= "  AND     AD.cod_cadastro = AC.cod_cadastro                   \n";
    $stSql .= "  AND     AD.ativo                                            \n";
    $stSql .= "  AND     AC.cod_cadastro = ".$this->getDado('cod_cadastro')."\n";
    $stSql .= "  AND     AD.cod_modulo    = ".$this->getDado('cod_modulo')." \n";

    return $stSql;
}

}
