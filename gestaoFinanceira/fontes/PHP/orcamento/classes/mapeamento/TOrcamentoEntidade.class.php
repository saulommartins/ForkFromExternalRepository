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
    * Classe de mapeamento da tabela ORCAMENTO.ENTIDADE
    * Data de Criação: 13/07/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Marcelo B. Paulino

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2008-03-26 16:20:04 -0300 (Qua, 26 Mar 2008) $

    * Casos de uso: uc-02.01.02
*/

/*
$Log$
Revision 1.14  2006/12/01 12:37:43  hboaventura
correção do componente

Revision 1.13  2006/07/05 20:42:02  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ORCAMENTO.ENTIDADE
  * Data de Criação: 13/07/2004

  * @author Analista: Jorge B. Ribarr
  * @author Desenvolvedor: Marcelo B. Paulino

*/
class TOrcamentoEntidade extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TOrcamentoEntidade()
{
    parent::Persistente();
    $this->setTabela('orcamento.entidade');

    $this->setCampoCod('cod_entidade');
    $this->setComplementoChave('exercicio');

    $this->AddCampo('exercicio','char',true,'04',true,false);
    $this->AddCampo('cod_entidade','integer',true,'',true,false);
    $this->AddCampo('numcgm','integer',true,'',false,true);
    $this->AddCampo('cod_responsavel','integer',true,'',false,true);
    $this->AddCampo('cod_resp_tecnico','integer',true,'',false,true);
    $this->AddCampo('cod_profissao','integer',true,'',false,true);

}

/**
    * Mesma função do recuperaTodos, mas efetua chamada a outro método para montar o SQL, o método montaRecuperaRelacionamentoNomes.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaRelacionamentoNomes(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaRelacionamentoNomes().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Mesma função do recuperaTodos, mas efetua chamada a outro método para montar o SQL, o método montaRecuperaUsuariosEntidade.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaUsuariosEntidade(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaUsuariosEntidade().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function recuperaUsuariosEntidadeCnpj(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaUsuariosEntidadeCnpj().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}
function recuperaReceitaDespesaEntidade(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaReceitaDespesaEntidade();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}
function recuperaEntidades(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaEntidades().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}
function recuperaEntidadeGeral(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaEntidadeGeral().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function recuperaEntidadeRestos(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaRelacionamentoRestos().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}


function verificaEntidadeRestos(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaVerificaEntidadeRestos().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamento()
{
    $stSql  = " SELECT                               \n";
    $stSql .= "     E.*,                             \n";
    $stSql .= "     CGM.numcgm,                      \n";
    $stSql .= "     CGM.nom_cgm                      \n";
    $stSql .= " FROM                                 \n";
    $stSql .= "     orcamento.entidade  AS E,    \n";
    $stSql .= "     sw_cgm              AS CGM   \n";
    $stSql .= " WHERE                                \n";
    $stSql .= "     E.numcgm = CGM.numcgm            \n";

    return $stSql;
}

function montaRecuperaRelacionamentoRestos()
{
    $stSql  = "  SELECT  sw_cgm.numcgm                                                      
                       , sw_cgm.nom_cgm                                                    
                       , entidade.*   
             
                   FROM orcamento.entidade
             INNER JOIN sw_cgm                                                              
                     ON sw_cgm.numcgm = entidade.numcgm
             
                  WHERE cod_entidade
                 NOT IN ( SELECT cod_entidade 
                            FROM administracao.configuracao_entidade
                           WHERE configuracao_entidade.exercicio  = '".$this->getDado('exercicio')."'
                             AND configuracao_entidade.cod_modulo = 10
                             AND configuracao_entidade.parametro  = 'virada_GF'
                             AND LOWER(TRIM(configuracao_entidade.valor)) = LOWER('".$this->getDado('valor')."')
                             )
                             
                    AND entidade.exercicio  = '".$this->getDado('exercicio')."' ";

    return $stSql;
}


function montaVerificaEntidadeRestos()
{
    $stSql  = " SELECT sw_cgm.numcgm                                                      
                     , sw_cgm.nom_cgm                                                    
                     , entidade.*   
                  FROM orcamento.entidade
            INNER JOIN sw_cgm                                                              
                    ON sw_cgm.numcgm = entidade.numcgm
            INNER JOIN administracao.configuracao_entidade
                    ON configuracao_entidade.cod_entidade = entidade.cod_entidade
                   AND configuracao_entidade.exercicio    = entidade.exercicio
                        
                   AND configuracao_entidade.parametro    = 'virada_GF'
                   AND configuracao_entidade.cod_modulo   = 10	
                WHERE entidade.exercicio  = '".$this->getDado('exercicio')."'
                  AND LOWER(TRIM(configuracao_entidade.valor)) = '".$this->getDado('valor')."'";

    return $stSql;
}

function montaRecuperaRelacionamentoNomes()
{
    $stSql  = " SELECT                                              
                    ENT.exercicio,                                  
                    ENT.cod_entidade,                               
                    ENT.cod_profissao      AS cod_profissao,        
                    CGM.numcgm             AS numcgm,               
                    CGM.nom_cgm            AS entidade,             
                    RESP.numcgm            AS cod_responsavel,      
                    RESP.nom_cgm           AS responsavel,          
                    RESPTEC.numcgm         AS cod_resp_tecnico,     
                    RESPTEC.nom_cgm        AS resp_tecnico,         
                    sw_cgm_pessoa_juridica.cnpj as cnpj_entidade,
                    el.logotipo                                     
                FROM                                                
                     ".$this->getTabela()."  AS ENT                  
                LEFT OUTER JOIN orcamento.entidade_logotipo AS el
                     ON ENT.cod_entidade     = el.cod_entidade 
                    AND ENT.exercicio        = el.exercicio,            
                
                sw_cgm                 AS CGM
                
                LEFT JOIN sw_cgm_pessoa_juridica
                     ON sw_cgm_pessoa_juridica.numcgm = CGM.numcgm,
                
                sw_cgm                 AS RESP,                 
                sw_cgm                 AS RESPTEC               
                WHERE                                               
                     ENT.numcgm           = CGM.numcgm       AND     
                     ENT.cod_responsavel  = RESP.numcgm      AND     
                     ENT.cod_resp_tecnico = RESPTEC.numcgm   AND     \n";
    
    if(!is_null($this->getDado('cod_entidade'))){
        $stSql .= "     ENT.cod_entidade     IN (".$this->getDado('cod_entidade').") AND \n";
    }
    
    $stSql .= "     ENT.exercicio        = '".$this->getDado('exercicio')."'        \n";

    return $stSql;
}

function montaRecuperaEntidades()
{
    $stSql  = " SELECT                                                                      \n";
    $stSql .= "     e.*,                                                                    \n";
    $stSql .= "     tabela.exercicio_atual,                                                 \n";
    $stSql .= "     c.nom_cgm,                                                              \n";
    $stSql .= "     c.numcgm                                                                \n";
    $stSql .= " FROM                                                                        \n";
    $stSql .= "     (SELECT                                                                 \n";
    $stSql .= "        E.cod_entidade,                                                      \n";
    $stSql .= "        max(E.exercicio) as exercicio,                                       \n";
    $stSql .= "        publico.concatenar_hifen(coalesce(EA.exercicio_atual,'')) as         \n";
    $stSql .= "     exercicio_atual                                                         \n";
    $stSql .= "     FROM                                                                    \n";
    $stSql .= "         orcamento.entidade  as e                                        \n";
    $stSql .= "             LEFT OUTER JOIN (                                               \n";
    $stSql .= "                 select exercicio as exercicio_atual, cod_entidade from      \n";
    $stSql .= "     orcamento.entidade where exercicio='".$this->getDado('exercicio')."'\n";
    $stSql .= "             ) as EA ON (                                                    \n";
    $stSql .= "                 e.cod_entidade  = ea.cod_entidade   AND                     \n";
    $stSql .= "                 e.exercicio     = ea.exercicio_atual                        \n";
    $stSql .= "             )                                                               \n";
    $stSql .= "     GROUP BY                                                                \n";
    $stSql .= "         E.cod_entidade                                                      \n";
    $stSql .= "     ORDER BY                                                                \n";
    $stSql .= "     E.cod_entidade                                                          \n";
    $stSql .= "  )as tabela,                                                                \n";
    $stSql .= "     orcamento.entidade as e,                                            \n";
    $stSql .= "     sw_cgm as c                                                            \n";
    $stSql .= " WHERE                                                                       \n";
    $stSql .= "     tabela.cod_entidade = e.cod_entidade                                    \n";
    $stSql .= " AND tabela.exercicio = e.exercicio                                          \n";
    $stSql .= " AND e.numcgm = c.numcgm                                                     \n";
    $stSql .= " AND tabela.exercicio_atual = '".$this->getDado('exercicio')."'              \n";

    return $stSql;
}

function montaRecuperaUsuariosEntidade()
{
    $stSql  = " SELECT                                   \n";
    $stSql .= "     E.cod_entidade,                      \n";
    $stSql .= "     C.nom_cgm                            \n";
    $stSql .= " FROM                                     \n";
    $stSql .= "     orcamento.entidade      as   E,      \n";
    $stSql .= "     sw_cgm                  as   C       \n";
    $stSql .= " WHERE                                    \n";
    $stSql .= "     E.numcgm = C.numcgm AND              \n";

    return $stSql;
}
function montaRecuperaUsuariosEntidadeCnpj()
{
    $stSql  = " SELECT                                   \n";
    $stSql .= "     E.cod_entidade,                      \n";
    $stSql .= "     C.nom_cgm,                           \n";
    $stSql .= "     PJ.cnpj,                              \n";
    $stSql .= "     PJ.numcgm                              \n";
    $stSql .= " FROM                                     \n";
    $stSql .= "     orcamento.entidade      as   E,      \n";
    $stSql .= "     sw_cgm                  as   C,      \n";
    $stSql .= "     sw_cgm_pessoa_juridica  as   PJ      \n";
    $stSql .= " WHERE                                    \n";
    $stSql .= "     E.numcgm = C.numcgm AND              \n";
    $stSql .= "    PJ.numcgm = C.numcgm AND              \n";

    return $stSql;
}
function montaRecuperaReceitaDespesaEntidade()
{
    $stSql  = " SELECT                                   \n";
    $stSql .= "     d.cod_entidade                       \n";
    $stSql .= " FROM                                     \n";
    $stSql .= "     orcamento.entidade  as e,            \n";
    $stSql .= "     orcamento.despesa   as d             \n";
    $stSql .= " WHERE                                    \n";
    $stSql .= "     e.cod_entidade = d.cod_entidade AND  \n";
    $stSql .= "     e.exercicio    = d.exercicio    AND  \n";
    $stSql .= "     e.cod_entidade = ".$this->getDado('cod_entidade')." AND \n";
    $stSql .= "     e.exercicio    = '".$this->getDado('exercicio')."'\n";
    $stSql .= " GROUP BY                                 \n";
    $stSql .= "         d.cod_entidade                   \n";
    $stSql .= " UNION                                    \n";
    $stSql .= " SELECT                                   \n";
    $stSql .= "     r.cod_entidade                       \n";
    $stSql .= " FROM                                     \n";
    $stSql .= "     orcamento.entidade  as e,            \n";
    $stSql .= "     orcamento.receita   as r             \n";
    $stSql .= " WHERE                                    \n";
    $stSql .= "     e.cod_entidade = r.cod_entidade AND  \n";
    $stSql .= "     e.exercicio    = r.exercicio    AND  \n";
    $stSql .= "     e.cod_entidade = ".$this->getDado('cod_entidade')." AND \n";
    $stSql .= "     e.exercicio    = '".$this->getDado('exercicio')."'\n";
    $stSql .= " GROUP BY                                 \n";
    $stSql .= "    r.cod_entidade                        \n";

    return $stSql;
}
function montaRecuperaEntidadeGeral()
{
    $stSql  = " SELECT                                   \n";
    $stSql .= "     C.numcgm,                            \n";
    $stSql .= "     C.nom_cgm,                           \n";
    $stSql .= "     E.cod_entidade                       \n";
    $stSql .= " FROM                                     \n";
    $stSql .= "     orcamento.entidade      as   E,      \n";
    $stSql .= "     sw_cgm                  as   C       \n";
    $stSql .= " WHERE                                    \n";
    $stSql .= "     E.numcgm = C.numcgm                  \n";
    if ($this->getDado('exercicio')) {
       $stSql .= " AND E.exercicio = '".$this->getDado('exercicio')."'\n";
    }
    $stSql .= "GROUP BY                                  \n";
    $stSql .= "     C.numcgm,                            \n";
    $stSql .= "     C.nom_cgm,                           \n";
    $stSql .= "     E.cod_entidade                       \n";
    $stSql .= "ORDER BY                                  \n";
    $stSql .= "     C.nom_cgm                            \n";

    return $stSql;
}

/**
 * Função exclusiva para uso no MANAD (Manual Normativo de Arquivos Digitais)
 *
 * Bloco 0 - ABERTURA, IDENTIFICAÇÃO E REFERÊNCIAS
 * Registro do Tipo 0000
 *
 * @access Public
 * @param  Object  $rsRecordSet Objeto RecordSet
 * @param  String  $stEntidades String Entidades
 * @param  Boolean $boTransacao
 * @return Object  Objeto Erro
 */
function recuperaDadosMANAD(&$rsRecordSet, $stEntidades = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stWhere = " WHERE entidade.exercicio = '".$this->getDado('exercicio')."' ";
    if (trim($stEntidades)) {
        $stWhere .= " AND entidade.cod_entidade IN (".$stEntidades.") ";
    }

    $stSql = $this->montaRecuperaDadosMANAD().$stWhere;
    $this->setDebug($stSql);

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
 * Query para uso no MANAD (Manual Normativo de Arquivos Digitais)
 *
 * Bloco 0 - ABERTURA, IDENTIFICAÇÃO E REFERÊNCIAS
 * Registro do Tipo 0000
 *
 * @access Public
 */
function montaRecuperaDadosMANAD()
{
    $stSql .= "     SELECT '0000' as reg                                                  \n";
    $stSql .= "            , cgm.nom_cgm as nome                                          \n";
    $stSql .= "            , cgm_pj.cnpj as cnpj                                          \n";
    $stSql .= "            , '' as cpf                                                    \n";
    $stSql .= "            , '' as cei                                                    \n";
    $stSql .= "            , '' as nit                                                    \n";
    $stSql .= "            , uf.sigla_uf as uf                                            \n";
    $stSql .= "            , cgm_pj.insc_estadual as ie                                   \n";
    $stSql .= "            , config_cod_mun.valor as cod_mun                              \n";
    $stSql .= "            , '' as im                                                     \n";
    $stSql .= "            , '' as suframa                                                \n";
    $stSql .= "            , '0' as ind_centr                                             \n";
    $stSql .= "            , '".$this->getDado('stDtInicial')."' as dt_ini                \n";
    $stSql .= "            , '".$this->getDado('stDtFinal')."' as dt_fin                  \n";
    $stSql .= "            , '003' as cod_ver                                             \n";
    $stSql .= "            , config_cod_fin.valor as cod_fin                              \n";
    $stSql .= "            , '2' as ind_ed                                                \n";
    $stSql .= "       FROM orcamento.entidade AS entidade                                 \n";
    $stSql .= " INNER JOIN sw_cgm AS cgm ON cgm.numcgm = entidade.numcgm                  \n";
    $stSql .= "  LEFT JOIN sw_cgm_pessoa_juridica AS cgm_pj ON cgm_pj.numcgm = cgm.numcgm \n";
    $stSql .= "  LEFT JOIN sw_uf as uf ON uf.cod_uf = cgm.cod_uf                          \n";
    $stSql .= " INNER JOIN administracao.configuracao as config_cod_mun                   \n";
    $stSql .= "         ON config_cod_mun.cod_modulo = 59                                 \n";
    $stSql .= "        AND config_cod_mun.exercicio = entidade.exercicio                  \n";
    $stSql .= "        AND config_cod_mun.parametro = 'manad_cod_mun'                     \n";
    $stSql .= " INNER JOIN administracao.configuracao as config_cod_fin                   \n";
    $stSql .= "         ON config_cod_fin.cod_modulo = 59                                 \n";
    $stSql .= "        AND config_cod_fin.exercicio = entidade.exercicio                  \n";
    $stSql .= "        AND config_cod_fin.parametro = 'manad_cod_fin'                     \n";

    return $stSql;
}

}
