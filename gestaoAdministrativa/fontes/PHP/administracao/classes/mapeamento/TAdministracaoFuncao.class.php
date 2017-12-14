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
* Classe de mapeamento para administracao.funcao
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 28438 $
$Name$
$Author: diogo.zarpelon $
$Date: 2008-03-10 10:58:27 -0300 (Seg, 10 Mar 2008) $

Casos de uso: uc-01.03.95
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
/**
  * Efetua conexão com a tabela  ADMINISTRACAO.FUNCAO
  * Data de Criação: 09/08/2005

  * @author Analista: Cassiano de Vasconcellos Ferreira
  * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

  * @package URBEM
  * @subpackage Mapeamento
*/
class TAdministracaoFuncao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TAdministracaoFuncao()
{
    parent::Persistente();
    $this->setTabela('administracao.funcao');

    $this->setCampoCod('cod_funcao');
    $this->setComplementoChave('cod_modulo,cod_biblioteca');

    $this->AddCampo('cod_modulo','integer',true,'',true,true);
    $this->AddCampo('cod_biblioteca','integer',true,'',true,true);
    $this->AddCampo('cod_funcao','integer',true,'',true,false);
    $this->AddCampo('cod_tipo_retorno','integer',true,'',false,true);
    $this->AddCampo('nom_funcao','varchar',true,'255',false,false);

}

function montaRecuperaRelacionamento()
{
    $stSql  = "  SELECT F.*,T.nom_tipo                  \n";
    $stSql .= "  FROM  administracao.funcao as F,             \n";
    $stSql .= "        administracao.tipo_primitivo as T      \n";
    $stSql .= "  WHERE  F.cod_tipo_retorno = T.cod_tipo \n";

    return $stSql;
}

/**
    * Listas as funções internas do sistema
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaFuncaoInterna(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaFuncaoInterna().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaFuncaoInterna()
{
    $stSql  = " SELECT                                       \n";
    $stSql .= "     f.cod_modulo,                            \n";
    $stSql .= "     f.cod_biblioteca,                        \n";
    $stSql .= "     f.cod_funcao,                            \n";
    $stSql .= "     f.cod_tipo_retorno,                      \n";
    $stSql .= "     f.nom_funcao                             \n";
    $stSql .= " FROM                                         \n";
    $stSql .= "     administracao.funcao AS f                \n";
    $stSql .= " LEFT JOIN                                    \n";
    $stSql .= "     administracao.funcao_externa AS fe       \n";
    $stSql .= " ON                                           \n";
    $stSql .= "     f.cod_modulo     = fe.cod_modulo AND     \n";
    $stSql .= "     f.cod_biblioteca = fe.cod_biblioteca AND \n";
    $stSql .= "     f.cod_funcao     = fe.cod_funcao         \n";
    $stSql .= " WHERE                                        \n";
    $stSql .= "     fe.cod_modulo     IS NULL AND            \n";
    $stSql .= "     fe.cod_biblioteca IS NULL AND            \n";
    $stSql .= "     fe.cod_funcao     IS NULL                \n";

    return $stSql;
}

function recuperaFuncaoCadastrada(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaFuncaoCadastrada().$stFiltro;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaFuncaoCadastrada()
{
    $stSql  = " SELECT                                       \n";
    $stSql .= " 	   proname								 \n";
    $stSql .= "  	 , pronamespace   						 \n";
    $stSql .= "  	 , proowner       						 \n";
    $stSql .= "  	 , prolang        						 \n";
    $stSql .= "  	 , proisagg       						 \n";
    $stSql .= "  	 , prosecdef      						 \n";
    $stSql .= "  	 , proisstrict    						 \n";
    $stSql .= "  	 , proretset      						 \n";
    $stSql .= "  	 , provolatile    						 \n";
    $stSql .= "  	 , pronargs       						 \n";
    $stSql .= "  	 , prorettype     						 \n";
    $stSql .= "  	 , proargtypes    						 \n";
    $stSql .= "  	 , proallargtypes  						 \n";
    $stSql .= "  	 , proargmodes     						 \n";
    $stSql .= "  	 , proargnames     						 \n";
    $stSql .= "  	 , prosrc          						 \n";
    $stSql .= "	     , probin          						 \n";
    $stSql .= "  	 , proacl          						 \n";
    $stSql .= "   FROM										 \n";
    $stSql .= "		   pg_proc								 \n";

    return $stSql;
}
/**
    * Lista as tabelas que utilizam a FK com referência na tabela
    * administracao.funcao
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaTabelasRelacionamentoFuncao(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaTabelasRelacionamentoFuncao();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaTabelasRelacionamentoFuncao()
{
    $stSql  = " SELECT 												 	\n";
    $stSql .= "		tabela_origem.nspname  as schema_origem,		 	\n";
    $stSql .= "		tabela_origem.relname  as tabela_origem,		 	\n";
    $stSql .= "	 	pg_constraint.conname  as fk_origem,			 	\n";
    $stSql .= " 	tabela_alvo.nspname    as schema_alvo,			 	\n";
    $stSql .= "		tabela_alvo.relname    as tabela_alvo			 	\n";
    $stSql .= "	FROM 												 	\n";
    $stSql .= "		pg_catalog.pg_constraint,							\n";
    $stSql .= "     ( 												 	\n";
    $stSql .= "			SELECT 										 	\n";
    $stSql .= "				pg_class.relname, 						 	\n";
    $stSql .= "				pg_class.oid, 							 	\n";
    $stSql .= "				pg_namespace.nspname					 	\n";
    $stSql .= " 		FROM 										 	\n";
    $stSql .= "				pg_class,								 	\n";
    $stSql .= "				pg_namespace							 	\n";
    $stSql .= "			WHERE 										 	\n";
    $stSql .= "				pg_class.relnamespace = pg_namespace.oid 	\n";
    $stSql .= "	  	)  as tabela_origem,							 	\n";
    $stSql .= "     ( 												 	\n";
    $stSql .= "			SELECT 										 	\n";
    $stSql .= "				pg_class.relname, 						 	\n";
    $stSql .= "				pg_class.oid, 							 	\n";
    $stSql .= "				pg_namespace.nspname					 	\n";
    $stSql .= "		 	FROM 										 	\n";
    $stSql .= "				pg_class,								 	\n";
    $stSql .= "			    pg_namespace							 	\n";
    $stSql .= "			WHERE 										 	\n";
    $stSql .= "				pg_class.relnamespace = pg_namespace.oid 	\n";
    $stSql .= "	  	)  as tabela_alvo								 	\n";
    $stSql .= " WHERE 													\n";
    $stSql .= "		pg_constraint.contype   = 'f'						\n";
    $stSql .= "  	AND tabela_origem.oid   = pg_constraint.conrelid	\n";
    $stSql .= "  	AND tabela_alvo.oid     = pg_constraint.confrelid	\n";
    $stSql .= "  	AND tabela_alvo.nspname = 'administracao'			\n";
    $stSql .= "  	AND tabela_alvo.relname In ('funcao')				\n";
    $stSql .= "	ORDER BY 1, 2;											\n";

    return $stSql;
}
/**
    * Lista as tabelas que utilizam a FK com referência na tabela
    * administracao.funcao
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaExistenciaRegistrosFuncao(&$rsRecordSet, $boTransacao = "", $schemaTabela, $arrDados)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaExistenciaRegistrosFuncao($schemaTabela, $arrDados);
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaExistenciaRegistrosFuncao($schemaTabela, $arrDados)
{
    $stSql  = " SELECT				 									\n";
    $stSql .= "		cod_funcao,											\n";
    $stSql .= "		cod_modulo,											\n";
    $stSql .= "	 	cod_biblioteca										\n";
    $stSql .= "	FROM 													\n";
    $stSql .= "		".$schemaTabela."									\n";
    $stSql .= " WHERE 													\n";
    $stSql .= "		cod_funcao=".$arrDados['cod_funcao']."				\n";
    $stSql .= "  	AND cod_modulo=".$arrDados['cod_modulo']."			\n";
    $stSql .= "  	AND cod_biblioteca=".$arrDados['cod_biblioteca']."	\n";
    $stSql .= "	LIMIT 1;												\n";

    return $stSql;
}
function recuperaMascaraFuncao(&$stMascaraFuncao = '')
{
  $obErro      = new Erro;
  $obConexao   = new Conexao;
  $obRecordSet = new RecordSet;
  $stMasc      = '';

  $stSql  = 'SELECT MAX(cod_modulo) as modulo FROM administracao.funcao ';
  $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao);
  $stModulo = $rsRecordSet->getCampo('modulo');

  for ( $x=0; $x<strlen($stModulo);$x++) {
       $stMasc .= '9';
  }
  $stMasc .= '.';

  $stSql  = 'SELECT MAX(cod_biblioteca) as modulo FROM administracao.funcao ';
  $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao);
  $stModulo = $rsRecordSet->getCampo('modulo');

  for ( $x=0; $x<strlen($stModulo);$x++) {
       $stMasc .= '9';
  }
  $stMasc .= '.';

  $stSql  = 'SELECT MAX(cod_funcao) as modulo FROM administracao.funcao ';
  $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao);
  $stModulo = $rsRecordSet->getCampo('modulo');

  for ( $x=0; $x<strlen($stModulo);$x++) {
       $stMasc .= '9';
  }

  return $stMasc;
}

}
