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
    * Classe de mapeamento para a tabela MONETARIO.CONTA_COPRRENTE
    * Data de Criação: 31/10/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lizandro Kirst da Silva

    * @package URBEM
    * @subpackage Regra

    * $Id: TMONContaCorrente.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.05.03
*/

/*
$Log$
Revision 1.8  2006/09/15 14:46:11  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TMONContaCorrente extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TMONContaCorrente()
{
    parent::Persistente();
    $this->setTabela('monetario.conta_corrente');

    $this->setCampoCod('cod_conta_corrente');
    $this->setComplementoChave('cod_banco,cod_agencia');

    $this->AddCampo('cod_banco','integer',true,'',true,true);
    $this->AddCampo('cod_agencia','integer',true,'',true,true);
    $this->AddCampo('cod_conta_corrente','integer',true,'',true,false);
    $this->AddCampo('num_conta_corrente','varchar',true,'',false,false);
    $this->AddCampo('data_criacao','date',true,'',false,false);
    $this->AddCampo('cod_tipo','integer',true,'',false,true);
    
    $this->setDado('exercicio', Sessao::getExercicio());
}

function montaRecuperaRelacionamento()
{
 $stSql  = "SELECT                                                           \n";
 $stSql .= "    CCor.cod_banco,                                              \n";
 $stSql .= "    CCor.cod_agencia,                                            \n";
 $stSql .= "    CCor.cod_conta_corrente,                                     \n";
 $stSql .= "    CCor.num_conta_corrente,                                     \n";
 $stSql .= "    TCon.cod_tipo,                                               \n";
 $stSql .= "    TCon.descricao,                                              \n";
 $stSql .= "    TO_CHAR(CCor.data_criacao , 'DD/MM/YYYY') as data_criacao,   \n";
 $stSql .= "    Ag.num_agencia,                                              \n";
 $stSql .= "    Ag.nom_agencia,                                              \n";
 $stSql .= "    Ban.num_banco,                                               \n";
 $stSql .= "    Ban.nom_banco                                                \n";
 $stSql .= "FROM                                                             \n";
 $stSql .= "    monetario.conta_corrente AS CCor                             \n";

 $stSql .= "INNER JOIN                                                       \n";
 $stSql .= "    monetario.banco AS Ban                                       \n";
 $stSql .= "ON                                                               \n";
 $stSql .= "    Ban.cod_banco = Ccor.cod_banco                               \n";

 $stSql .= "INNER JOIN                                                       \n";
 $stSql .= "    monetario.tipo_conta AS TCon                                 \n";
 $stSql .= "ON                                                               \n";
 $stSql .= "    TCon.cod_tipo = Ccor.cod_tipo                                \n";

 $stSql .= "LEFT JOIN                                                        \n";
 $stSql .= "    monetario.agencia AS Ag                                      \n";
 $stSql .= "ON                                                               \n";
 $stSql .= "    CCor.cod_agencia = Ag.cod_agencia                            \n";
 $stSql .= "    AND                                                          \n";
 $stSql .= "    Ban.cod_banco = Ag.cod_banco                                 \n";

 return $stSql;

}

function recuperaExisteContaCorrente(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaExisteContaCorrente( ).$stCondicao.$stOrdem;
    //echo '<br>SQL: '.$stSql; exit;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaExisteContaCorrente()
{
    $stSql .= "    SELECT                           \n";
    $stSql .= "        *                            \n";
    $stSql .= "    FROM                             \n";
    $stSql .= "        monetario.conta_corrente     \n";

    return $stSql;
}


function recuperaContaCorrenteConciliacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaContaCorrenteConciliacao().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaContaCorrenteConciliacao()
{    
    $stSql = "SELECT                                                                                               \n";
    $stSql .= "    CCor.cod_banco,                                                                                 \n";                                           
    $stSql .= "    CCor.cod_agencia,                                                                               \n";                                          
    $stSql .= "    CCor.cod_conta_corrente,                                                                        \n";                                   
    $stSql .= "    CCor.num_conta_corrente,                                                                        \n";                                    
    $stSql .= "    TCon.cod_tipo,                                                                                  \n";                                         
    $stSql .= "    TCon.descricao,                                                                                 \n";                                           
    $stSql .= "    TO_CHAR(CCor.data_criacao , 'DD/MM/YYYY') as data_criacao,                                      \n"; 
    $stSql .= "    Ag.num_agencia,                                                                                 \n";                                          
    $stSql .= "    Ag.nom_agencia,                                                                                 \n";                                              
    $stSql .= "    Ban.num_banco,                                                                                  \n";                                             
    $stSql .= "    Ban.nom_banco,                                                                                  \n";
    $stSql .= "    plano_banco.cod_entidade,                                                                       \n";
    $stSql .= "    plano_banco.exercicio,                                                                          \n";
    $stSql .= "    sw_cgm.nom_cgm AS nom_entidade                                                                  \n"; 
    $stSql .= "                                                                                                    \n";                         
    $stSql .= "FROM                                                                                                \n";                            
    $stSql .= "     monetario.conta_corrente AS CCor                                                               \n";                          
    $stSql .= "INNER JOIN                                                                                          \n";                                             
    $stSql .= "    monetario.banco AS Ban                                                                          \n";
    $stSql .= "ON                                                                                                  \n";                                            
    $stSql .= "    Ban.cod_banco = Ccor.cod_banco                                                                  \n";
    if($this->getDado('inNumBanco'))
        $stSql .= "AND Ban.num_banco = '".$this->getDado('inNumBanco')."'                                          \n";
    $stSql .= "INNER JOIN                                                                                          \n";                                        
    $stSql .= "    monetario.tipo_conta AS TCon                                                                    \n";                             
    $stSql .= "ON                                                                                                  \n";                                     
    $stSql .= "    TCon.cod_tipo = Ccor.cod_tipo                                                                   \n";                            
    $stSql .= "LEFT JOIN                                                                                           \n";                                                    
    $stSql .= "    monetario.agencia AS Ag                                                                         \n";                                 
    $stSql .= "ON                                                                                                  \n";                                                     
    $stSql .= "    CCor.cod_agencia = Ag.cod_agencia                                                               \n";                           
    $stSql .= "AND                                                                                                 \n";                                    
    $stSql .= "    Ban.cod_banco = Ag.cod_banco                                                                    \n";
    if($this->getDado('inNumAgencia'))
        $stSql .= "AND Ag.num_agencia = '".$this->getDado('inNumAgencia')."'                                       \n";
    $stSql .= "JOIN (  SELECT  plano_banco.exercicio, plano_banco.cod_banco, plano_banco.cod_agencia,              \n";
    $stSql .= "                plano_banco.cod_conta_corrente, plano_banco.cod_entidade                            \n";
    $stSql .= "        FROM contabilidade.plano_banco                                                              \n";
    $stSql .= "        JOIN contabilidade.plano_analitica                                                          \n";
    $stSql .= "          ON plano_analitica.cod_plano = plano_banco.cod_plano                                      \n";
    $stSql .= "         AND plano_analitica.exercicio = plano_banco.exercicio                                      \n";
    $stSql .= "        JOIN contabilidade.plano_conta                                                              \n";
    $stSql .= "          ON plano_conta.cod_conta = plano_analitica.cod_conta                                      \n";
    $stSql .= "         AND plano_conta.exercicio = plano_analitica.exercicio                                      \n";
    $stSql .= "       WHERE plano_conta.cod_estrutural NOT LIKE '1.1.1.1.3%'                                       \n";
    $stSql .= "         AND plano_banco.exercicio = '".$this->getDado('exercicio')."'                              \n";
    if($this->getDado('entidades'))
        $stSql .= "         AND plano_banco.cod_entidade IN (".$this->getDado('entidades').")                      \n";
    $stSql .= "    ) AS plano_banco                                                                                \n";
    $stSql .= "ON plano_banco.cod_banco = CCor.cod_banco                                                           \n";
    $stSql .= "AND plano_banco.cod_agencia = CCor.cod_agencia                                                      \n";
    $stSql .= "AND plano_banco.cod_conta_corrente = CCor.cod_conta_corrente                                        \n";
    if($this->getDado('num_conta_corrente'))
        $stSql .= "AND CCor.num_conta_corrente = '".$this->getDado('num_conta_corrente')."'                        \n";
    $stSql .= "                                                                                                    \n";
    $stSql .= "JOIN orcamento.entidade                                                                             \n";
    $stSql .= "ON entidade.exercicio=plano_banco.exercicio                                                         \n";
    $stSql .= "AND entidade.cod_entidade=plano_banco.cod_entidade                                                  \n";
    $stSql .= "JOIN sw_cgm                                                                                         \n";
    $stSql .= "ON sw_cgm.numcgm=entidade.numcgm                                                                    \n";
    $stSql .= "                                                                                                    \n";
    $stSql .= "GROUP BY CCor.cod_banco,                                                                            \n";                                             
    $stSql .= "         CCor.cod_agencia,                                                                          \n";                                           
    $stSql .= "         CCor.cod_conta_corrente,                                                                   \n";                                   
    $stSql .= "         CCor.num_conta_corrente,                                                                   \n";                                   
    $stSql .= "         TCon.cod_tipo,                                                                             \n";                                          
    $stSql .= "         TCon.descricao,                                                                            \n";                                           
    $stSql .= "         CCor.data_criacao,                                                                         \n"; 
    $stSql .= "         Ag.num_agencia,                                                                            \n";                                          
    $stSql .= "         Ag.nom_agencia,                                                                            \n";                                           
    $stSql .= "         Ban.num_banco,                                                                             \n";                                           
    $stSql .= "         Ban.nom_banco,                                                                             \n";
    $stSql .= "         ban.cod_banco,                                                                             \n";
    $stSql .= "         Ag.cod_agencia,                                                                            \n";
    $stSql .= "         plano_banco.cod_entidade,                                                                  \n";
    $stSql .= "         plano_banco.exercicio,                                                                     \n";
    $stSql .= "         sw_cgm.nom_cgm                                                                             \n";
    
    return $stSql;
}

function recuperaContaCorrentePlanoBanco(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaContaCorrentePlanoBanco().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaContaCorrentePlanoBanco()
{    
    $stSql = "SELECT                                                                                               \n";
    $stSql .= "    CCor.cod_banco,                                                                                 \n";                                           
    $stSql .= "    CCor.cod_agencia,                                                                               \n";                                          
    $stSql .= "    CCor.cod_conta_corrente,                                                                        \n";                                   
    $stSql .= "    CCor.num_conta_corrente,                                                                        \n";                                    
    $stSql .= "    TCon.cod_tipo,                                                                                  \n";                                         
    $stSql .= "    TCon.descricao,                                                                                 \n";                                           
    $stSql .= "    TO_CHAR(CCor.data_criacao , 'DD/MM/YYYY') as data_criacao,                                      \n"; 
    $stSql .= "    Ag.num_agencia,                                                                                 \n";                                          
    $stSql .= "    Ag.nom_agencia,                                                                                 \n";                                              
    $stSql .= "    Ban.num_banco,                                                                                  \n";                                             
    $stSql .= "    Ban.nom_banco,                                                                                  \n";
    $stSql .= "    plano_banco.cod_entidade,                                                                       \n";
    $stSql .= "    plano_banco.exercicio,                                                                          \n";
    $stSql .= "    plano_banco.cod_plano,                                                                          \n";
    $stSql .= "    plano_banco.nom_conta                                                                           \n";
    $stSql .= "                                                                                                    \n";                         
    $stSql .= "FROM                                                                                                \n";                            
    $stSql .= "     monetario.conta_corrente AS CCor                                                               \n";                          
    $stSql .= "INNER JOIN                                                                                          \n";                                             
    $stSql .= "    monetario.banco AS Ban                                                                          \n";                                    
    $stSql .= "ON                                                                                                  \n";                                            
    $stSql .= "    Ban.cod_banco = Ccor.cod_banco                                                                  \n";                         
    $stSql .= "INNER JOIN                                                                                          \n";                                        
    $stSql .= "    monetario.tipo_conta AS TCon                                                                    \n";                             
    $stSql .= "ON                                                                                                  \n";                                     
    $stSql .= "    TCon.cod_tipo = Ccor.cod_tipo                                                                   \n";                            
    $stSql .= "LEFT JOIN                                                                                           \n";                                                    
    $stSql .= "    monetario.agencia AS Ag                                                                         \n";                                 
    $stSql .= "ON                                                                                                  \n";                                                     
    $stSql .= "    CCor.cod_agencia = Ag.cod_agencia                                                               \n";                           
    $stSql .= "AND                                                                                                 \n";                                    
    $stSql .= "    Ban.cod_banco = Ag.cod_banco                                                                    \n";                              
    $stSql .= "JOIN (  SELECT  plano_banco.exercicio, plano_banco.cod_banco, plano_banco.cod_agencia,              \n";
    $stSql .= "                plano_banco.cod_conta_corrente, plano_banco.cod_entidade,                           \n";
    $stSql .= "                plano_banco.cod_plano, plano_conta.nom_conta                                        \n";
    $stSql .= "        FROM contabilidade.plano_banco                                                              \n";
    $stSql .= "        JOIN contabilidade.plano_analitica                                                          \n";
    $stSql .= "          ON plano_analitica.cod_plano = plano_banco.cod_plano                                      \n";
    $stSql .= "         AND plano_analitica.exercicio = plano_banco.exercicio                                      \n";
    $stSql .= "        JOIN contabilidade.plano_conta                                                              \n";
    $stSql .= "          ON plano_conta.cod_conta = plano_analitica.cod_conta                                      \n";
    $stSql .= "         AND plano_conta.exercicio = plano_analitica.exercicio                                      \n";
    $stSql .= "       WHERE plano_conta.cod_estrutural NOT LIKE '1.1.1.1.3%'                                       \n";
    $stSql .= "         AND plano_banco.exercicio = '".$this->getDado('exercicio')."'                              \n";
    if($this->getDado('entidades'))
        $stSql .= "         AND plano_banco.cod_entidade IN (".$this->getDado('entidades').")                      \n";
    $stSql .= "    ) AS plano_banco                                                                                \n";
    $stSql .= "ON plano_banco.cod_banco = CCor.cod_banco                                                           \n";
    $stSql .= "AND plano_banco.cod_agencia = CCor.cod_agencia                                                      \n";
    $stSql .= "AND plano_banco.cod_conta_corrente = CCor.cod_conta_corrente                                        \n";
    if($this->getDado('num_conta_corrente'))
        $stSql .= "AND CCor.num_conta_corrente = '".$this->getDado('num_conta_corrente')."'                        \n";
    $stSql .= "                                                                                                    \n";
    $stSql .= "GROUP BY CCor.cod_banco,                                                                            \n";                                             
    $stSql .= "         CCor.cod_agencia,                                                                          \n";                                           
    $stSql .= "         CCor.cod_conta_corrente,                                                                   \n";                                   
    $stSql .= "         CCor.num_conta_corrente,                                                                   \n";                                   
    $stSql .= "         TCon.cod_tipo,                                                                             \n";                                          
    $stSql .= "         TCon.descricao,                                                                            \n";                                           
    $stSql .= "         CCor.data_criacao,                                                                         \n"; 
    $stSql .= "         Ag.num_agencia,                                                                            \n";                                          
    $stSql .= "         Ag.nom_agencia,                                                                            \n";                                           
    $stSql .= "         Ban.num_banco,                                                                             \n";                                           
    $stSql .= "         Ban.nom_banco,                                                                             \n";
    $stSql .= "         ban.cod_banco,                                                                             \n";
    $stSql .= "         Ag.cod_agencia,                                                                            \n";
    $stSql .= "         plano_banco.cod_entidade,                                                                  \n";
    $stSql .= "         plano_banco.exercicio,                                                                     \n";
    $stSql .= "         plano_banco.cod_plano,                                                                     \n";
    $stSql .= "         plano_banco.nom_conta                                                                      \n";
    
    return $stSql;
}

}
