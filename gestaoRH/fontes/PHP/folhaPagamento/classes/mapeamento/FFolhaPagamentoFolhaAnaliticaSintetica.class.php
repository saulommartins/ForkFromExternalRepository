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
    * Classe de mapeamento da tabela FFOLHAPAGAMENTOCONTRACHEQUE
    * Data de Criação: 24/01/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: tiago $
    $Date: 2007-07-12 18:38:13 -0300 (Qui, 12 Jul 2007) $

    * Casos de uso: uc-04.05.50
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Data de Criação: 24/01/2007

  * @author Analista: Dagiane
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class FFolhaPagamentoFolhaAnaliticaResumida extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FFolhaPagamentoFolhaAnaliticaResumida()
{
    parent::Persistente();
    $this->setTabela('folhaAnaliticaResumida');
}

function folhaAnaliticaResumida(&$rsRecordSet,$boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $stSql  = $this->montaFolhaAnaliticaResumida().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaFolhaAnaliticaResumida()
{
    $stSql .= "SELECT * FROM ".$this->getTabela()."(".$this->getDado("cod_configuracao").",".$this->getDado("cod_periodo_movimentacao").",".$this->getDado("cod_complementar").",'".$this->getDado("filtro")."','".$this->getDado("ordenacao")."',".$this->getDado("cod_atributo").",'".$this->getDado("valor")."','".Sessao::getEntidade()."','".Sessao::getExercicio()."') as retorno  \n";

    return $stSql;
}

function folhaSintetica(&$rsRecordSet,$boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $stSql  = $this->montaFolhaSintetica().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaFolhaSintetica()
{
    $stSql .= "SELECT * FROM folhaSintetica(".$this->getDado("cod_configuracao").",".$this->getDado("cod_periodo_movimentacao").",".$this->getDado("cod_complementar").",'".$this->getDado("filtro")."','".$this->getDado("ordenacao")."',".$this->getDado("cod_atributo").",'".$this->getDado("valor")."','".Sessao::getEntidade()."','".Sessao::getExercicio()."') as retorno  \n";

    return $stSql;
}

function folhaAnalitica(&$rsRecordSet,$boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $stSql  = $this->montaFolhaAnalitica().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaFolhaAnalitica()
{
    $stSql .= "SELECT * FROM folhaAnalitica(".$this->getDado("cod_configuracao").",".$this->getDado("cod_periodo_movimentacao").",".$this->getDado("cod_complementar").",'".$this->getDado("filtro")."','".$this->getDado("ordenacao")."',".$this->getDado("cod_atributo").",'".$this->getDado("valor")."','".Sessao::getEntidade()."','".Sessao::getExercicio()."') as retorno  \n";

    return $stSql;
}

function eventosCalculadosFolhaAnaliticaResumida(&$rsRecordSet,$boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $stSql  = $this->montaEventosCalculadosFolhaAnaliticaResumida().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaEventosCalculadosFolhaAnaliticaResumida()
{
    $stSql .= "SELECT * FROM eventosCalculadosFolhaAnaliticaResumida(".$this->getDado("cod_configuracao").",".$this->getDado("cod_contrato").",".$this->getDado("cod_periodo_movimentacao").",".$this->getDado("cod_complementar").",'".$this->getDado("ordenacao")."','".Sessao::getEntidade()."') as retorno  \n";

    return $stSql;
}

function eventosCalculadosFolhaAnalitica(&$rsRecordSet,$boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $stSql  = $this->montaEventosCalculadosFolhaAnalitica().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaEventosCalculadosFolhaAnalitica()
{
    $stSql .= "SELECT * FROM eventosCalculadosFolhaAnalitica(".$this->getDado("cod_configuracao").",".$this->getDado("cod_contrato").",".$this->getDado("cod_periodo_movimentacao").",".$this->getDado("cod_complementar").",'".$this->getDado("ordenacao")."','".$this->getDado("naturezaE")."','".$this->getDado("naturezaD")."','".Sessao::getEntidade()."') as retorno  \n";

    return $stSql;
}

function eventosCalculadosComplementarFolhaAnalitica(&$rsRecordSet,$boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $stSql  = $this->montaEventosCalculadosComplementarFolhaAnalitica().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaEventosCalculadosComplementarFolhaAnalitica()
{
    $stSql .= "SELECT * FROM eventosCalculadosComplementarFolhaAnalitica(".$this->getDado("cod_contrato").",".$this->getDado("cod_periodo_movimentacao").",".$this->getDado("cod_complementar").",'".Sessao::getEntidade()."') as retorno  \n";

    return $stSql;
}

function situacaoServidorFolhaAnaliticaResumida(&$rsRecordSet,$boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $stSql  = $this->montaSituacaoServidorFolhaAnaliticaResumida().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaSituacaoServidorFolhaAnaliticaResumida()
{
    ;

    $stSql  = "SELECT  																																  		                                                      \n";
    $stSql .= "     CASE                                                                                                                                                                                          \n";
    $stSql .= "     WHEN adido_ou_cedido.situacao IS NOT NULL  AND ( ( aposentado.situacao IS NOT NULL AND adido_ou_cedido.dt_final > aposentado.dt_concessao ) OR aposentado.situacao IS NULL ) THEN             \n";
    $stSql .= "         adido_ou_cedido.situacao                                                                                                                                                                  \n";
    $stSql .= "     WHEN aposentado.situacao IS NOT NULL AND ( ( adido_ou_cedido.situacao IS NOT NULL AND  aposentado.dt_concessao > adido_ou_cedido.dt_final ) OR adido_ou_cedido.situacao IS NULL ) THEN        \n";
    $stSql .= "         aposentado.situacao                                                                                                                                                                       \n";
    $stSql .= "     WHEN aposentado.situacao IS NULL AND adido_ou_cedido.situacao IS NULL AND contrato.cod_contrato IS NOT NULL THEN                                                                              \n";
    $stSql .= "         'Ativo'                                                                                                                                                                                   \n";
    $stSql .= "     END AS situacao                                                                                                                                                                               \n";
    $stSql .= "FROM 																																	   		 	 \n";
    $stSql .= "( 																																		       		 \n";
    $stSql .= " 	SELECT AC.cod_contrato, max(AC.timestamp) AS timestamp, 																				   		 \n";
    $stSql .= "            dt_final,                                                                                                                                 \n";
    $stSql .= " 		CASE 																																   		 \n";
    $stSql .= " 			WHEN tipo_cedencia = 'a' THEN 'Adido' 																							   		 \n";
    $stSql .= "  			WHEN tipo_cedencia = 'c' THEN 'Cedido' 																							   		 \n";
    $stSql .= "  		ELSE 																																   		 \n";
    $stSql .= "				'' 																																   		 \n";
    $stSql .= "			END AS situacao   																													   		 \n";
    $stSql .= "  	FROM pessoal.adido_cedido AC 																											   		  \n";
    $stSql .= "     INNER JOIN                                    \n";
    $stSql .= "  	( SELECT cod_contrato, max(timestamp) AS timestamp FROM pessoal.adido_cedido WHERE cod_contrato = ".$this->getDado("cod_contrato")." GROUP BY cod_contrato ) AS max_adido_cedido \n";
    $stSql .= "		ON 																																			    \n";
    $stSql .= "			AC.cod_contrato = max_adido_cedido.cod_contrato 																						 \n";
    $stSql .= "			AND AC.timestamp = max_adido_cedido.timestamp  																								 \n";
    $stSql .= "         AND AC.cod_contrato = ".$this->getDado("cod_contrato")."
                                                                      \n";
    $stSql .= "         AND NOT EXISTS( SELECT cod_contrato, max(timestamp_cedido_adido) AS timestamp_cedido_adido FROM pessoal.adido_cedido_excluido ACE  WHERE ACE.cod_contrato = max_adido_cedido.cod_contrato AND ACE.timestamp_cedido_adido = max_adido_cedido.timestamp GROUP BY cod_contrato )\n";
    $stSql .= "			GROUP BY AC.cod_contrato, situacao, dt_final   																								 \n";
    $stSql .= ") AS adido_ou_cedido 																																 \n";
    $stSql .= "FULL JOIN																																	         \n";
    $stSql .= "( 																																					 \n";
    $stSql .= "  SELECT A.cod_contrato, 																															 \n";
    $stSql .= "	 	max(A.timestamp) AS timestamp, 																													 \n";
    $stSql .= "     dt_concessao,                                                                                                                                    \n";
    $stSql .= "  	CASE WHEN A.cod_contrato IS NULL  																												 \n";
    $stSql .= "  	THEN 																																			 \n";
    $stSql .= "  		'' 																																			 \n";
    $stSql .= "  	ELSE  																																			 \n";
    $stSql .= "  		'Aposentado' 																																 \n";
    $stSql .= "  	END AS situacao 																																 \n";
    $stSql .= "  FROM pessoal.aposentadoria A 																														 \n";
    $stSql .= "  INNER JOIN                                          \n";
    $stSql .= "  	( SELECT cod_contrato, max(timestamp) AS timestamp FROM pessoal.aposentadoria WHERE cod_contrato = ".$this->getDado("cod_contrato")." GROUP BY cod_contrato ) AS max_aposentadoria  \n";
    $stSql .= "  ON  																																			 	\n";
    $stSql .= "	    A.cod_contrato = max_aposentadoria.cod_contrato 																												\n";
    $stSql .= "	 	AND max_aposentadoria.timestamp = A.timestamp 																									\n";
    $stSql .= "	 	AND A.cod_contrato = ".$this->getDado("cod_contrato")." 																															\n";
    $stSql .= "	 	AND NOT EXISTS ( SELECT AE.cod_contrato, max(timestamp_aposentadoria) AS timestamp_aposentadoria FROM pessoal.aposentadoria_excluida AE WHERE AE.cod_contrato = max_aposentadoria.cod_contrato AND AE.timestamp_aposentadoria = max_aposentadoria.timestamp AND AE.cod_contrato = ".$this->getDado("cod_contrato")." GROUP BY cod_contrato ) 	\n";
    $stSql .= "	 	GROUP BY A.cod_contrato, situacao, dt_concessao																												\n";
    $stSql .= " ) AS aposentado ON aposentado.cod_contrato = adido_ou_cedido.cod_contrato																			\n";

    $stSql .= " RIGHT JOIN  																																		\n";
    $stSql .= " (  																																					\n";
    $stSql .= " 	SELECT cod_contrato FROM  																														\n";
    $stSql .= "			pessoal.contrato_servidor WHERE cod_contrato = ".$this->getDado("cod_contrato")."  															\n";
    $stSql .= "			GROUP BY cod_contrato 																														\n";
    $stSql .= " ) AS contrato ON contrato.cod_contrato = ".$this->getDado("cod_contrato")."
                                        \n";

    return $stSql;
}

}
?>
