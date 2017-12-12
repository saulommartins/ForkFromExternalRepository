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
    * Pacote de configuração do TCEAL
    * Data de Criação   : 08/10/2013

    * @author Analista: Carlos Adriano
    * @author Desenvolvedor: Carlos Adriano
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TExportacaoTCEALCredor extends Persistente
{
function TExportacaoTCEALCredor()
{
    parent::Persistente();
    $this->setTabela('tceal.credor');
    $this->setComplementoChave('exercicio,numcgm');

    $this->AddCampo('exercicio','varchar',true,'4',true,true);
    $this->AddCampo('numcgm','integer',true,'',true,true);
    $this->AddCampo('tipo','integer',true,'',false,false);
}

function montaRecuperaDadosCredor()
{
    $stSQL = " SELECT                                         \n";
    $stSQL .= " cgm.numcgm,                                             \n";
    $stSQL .= " cgm.nom_cgm,                                            \n";
    $stSQL .= " cr.tipo                                                 \n";
    $stSQL .= " FROM                                                    \n";
    $stSQL .= " empenho.pre_empenho AS pe,                              \n";
    $stSQL .= " empenho.empenho AS em,                                  \n";
    $stSQL .= " sw_cgm AS cgm                                           \n";
    $stSQL .= " LEFT JOIN tceal.credor AS cr ON                         \n";
    $stSQL .= " cr.numcgm = cgm.numcgm                                  \n";
    $stSQL .= " AND cr.exercicio = '".$this->getDado("exercicio")."'    \n";
    $stSQL .= " WHERE                                                   \n";
    $stSQL .= " pe.cgm_beneficiario = cgm.numcgm                        \n";
    $stSQL .= " AND pe.exercicio = em.exercicio                         \n";
    $stSQL .= " AND pe.cod_pre_empenho = em.cod_pre_empenho             \n";
    $stSQL .= " AND pe.exercicio = '".$this->getDado("exercicio")."'    \n";
    $stSQL .= " AND pe.exercicio||em.cod_entidade IN (                  \n";
    $stSQL .= "     SELECT exercicio||cod_entidade                      \n";
    $stSQL .= "     FROM orcamento.usuario_entidade                     \n";
    $stSQL .= "     WHERE exercicio = '".$this->getDado("exercicio")."' \n";
    $stSQL .= "     AND   numcgm = ".$this->getDado("numcgm").")        \n";
    $stSQL .= " GROUP BY cgm.numcgm, cgm.nom_cgm, cr.tipo               \n";
    $stSQL .= " ORDER BY cgm.numcgm, cgm.nom_cgm                        \n";

    return $stSQL;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no
    * montaRecuperaDadosExportacao.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condiï¿½ï¿½o do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenaï¿½ï¿½o do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaDadosCredor(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaDadosCredor().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

 /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaDadosConversao.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
   public function recuperaDadosCredorConversao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
   {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->MontaRecuperaDadosCredorConversao().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

  public function montaRecuperaDadosCredorConversao()
  {
    $stSQL = " SELECT                                          \n";
    $stSQL .= " pe.exercicio,                                           \n";
    $stSQL .= " cgm.numcgm,                                             \n";
    $stSQL .= " cgm.nom_cgm,                                            \n";
    $stSQL .= " cr.tipo                                                 \n";
    $stSQL .= " FROM                                                    \n";
    $stSQL .= " empenho.pre_empenho AS pe,                              \n";
    $stSQL .= " empenho.empenho AS em,                                  \n";
    $stSQL .= " sw_cgm AS cgm                                           \n";
    $stSQL .= " LEFT JOIN tcers.credor AS cr ON                         \n";
    $stSQL .= " cr.numcgm = cgm.numcgm                                  \n";
    $stSQL .= " AND cr.exercicio < '".$this->getDado("exercicio")."'    \n";
    if ($this->getDado("ano")) {
        $stSQL .= " AND cr.exercicio = '".$this->getDado("ano")."'      \n";
    }
    $stSQL .= " WHERE                                                   \n";
    $stSQL .= " pe.cgm_beneficiario = cgm.numcgm                        \n";
    $stSQL .= " AND pe.exercicio = em.exercicio                         \n";
    $stSQL .= " AND pe.cod_pre_empenho = em.cod_pre_empenho             \n";
    $stSQL .= " AND pe.exercicio < '".$this->getDado("exercicio")."'    \n";
    if ($this->getDado("ano")) {
        $stSQL .= " AND pe.exercicio = '".$this->getDado("ano")."'      \n";
    }
    //$stSQL .= " AND pe.exercicio||em.cod_entidade IN (                  \n";
    //$stSQL .= "     SELECT exercicio||cod_entidade                      \n";
    //$stSQL .= "     FROM orcamento.usuario_entidade                     \n";
    //$stSQL .= "     WHERE exercicio < '".$this->getDado("exercicio")."' \n";
    //$stSQL .= "     AND   numcgm = ".$this->getDado("numcgm").")        \n";
    $stSQL .= " GROUP BY pe.exercicio, cgm.numcgm, cgm.nom_cgm, cr.tipo   \n";
    $stSQL .= " ORDER BY pe.exercicio DESC, cgm.numcgm, cgm.nom_cgm ASC \n";
 // echo $stSql;
    return $stSQL;
}

function recuperaExercicios(&$rsRecordSet, $boTransacao = "")
{
     $obErro      = new Erro;
     $obConexao   = new Conexao;
     $rsRecordSet = new RecordSet;

     $stSql = $this->montaRecuperaExercicios();
     $this->setDebug( $stSql );
     $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

     return $obErro;
}

function montaRecuperaExercicios()
{
    $stSql = " SELECT                                                   \n";
    $stSql.= "      exercicio                                           \n";
    $stSql.= " FROM empenho.pre_empenho                                 \n";
    $stSql.= " GROUP BY exercicio                                       \n";
    $stSql.= " ORDER BY exercicio desc                                  \n";
    //echo $stSql;
    return $stSql;
}

}
?>
