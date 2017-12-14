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
    * Pacote de configuração do TCETO - Mapeamento Unidade Orçamentária
    * Data de Criação   : 05/11/2014

    * @author Analista: Silvia Martins Silva
    * @author Desenvolvedor: Michel Teixeira
    * $Id: TExportacaoTCETOUniOrcam.class.php 60654 2014-11-06 13:18:49Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TExportacaoTCETOUniOrcam extends Persistente
{
    function TExportacaoTCETOUniOrcam()
    {
        parent::Persistente();
        $this->setTabela('tceto.uniorcam');
        $this->setComplementoChave('exercicio,num_unidade,num_orgao');
    
        $this->AddCampo('exercicio'     ,'varchar',true,'4' ,true ,true );
        $this->AddCampo('num_unidade'   ,'integer',true,''  ,true ,true );
        $this->AddCampo('num_orgao'     ,'integer',true,''  ,true ,true );
        $this->AddCampo('numcgm'        ,'integer',true,''  ,false,true );
        $this->AddCampo('identificador' ,'integer',true,''  ,false,false);
    }

    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaDadosExportacao.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaDadosUniOrcam(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->MontaRecuperaDadosUniOrcam().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function MontaRecuperaDadosUniOrcam()
    {
        $stSql  = "SELECT                                                           \n";
        $stSql .= "     oo.nom_orgao,                                               \n";
        $stSql .= "     oo.num_orgao,                                               \n";
        $stSql .= "     ou.nom_unidade,                                             \n";
        $stSql .= "     ou.num_unidade,                                             \n";
        $stSql .= "     tu.identificador,                                           \n";
        $stSql .= "     tu.numcgm,                                                  \n";
        $stSql .= "     sw_cgm.nom_cgm                                              \n";
        $stSql .= "FROM                                                             \n";
        $stSql .= "     orcamento.orgao     as oo,                                  \n";
        $stSql .= "     orcamento.unidade   as ou                                   \n";
        $stSql .= "LEFT JOIN tceto.uniorcam as tu                                   \n";
        $stSql .= "       ON ou.exercicio   = tu.exercicio                          \n";
        $stSql .= "      AND ou.num_unidade = tu.num_unidade                        \n";
        $stSql .= "      AND ou.num_orgao   = tu.num_orgao                          \n";
        $stSql .= "LEFT JOIN sw_cgm                                                 \n";
        $stSql .= "       ON sw_cgm.numcgm  =  tu.numcgm                            \n";
        $stSql .= "    WHERE oo.num_orgao   = ou.num_orgao                          \n";
        $stSql .= "      AND oo.exercicio   = ou.exercicio                          \n";
        $stSql .= "      AND oo.exercicio   = '".$this->getDado("exercicio")."'     \n";

        return $stSql;
    }

    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaDadosExportacao.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaDadosUniOrcamConversao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->MontaRecuperaDadosUniOrcamConversao().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function MontaRecuperaDadosUniOrcamConversao()
    {
        $stSql  = "";
        $stSql .= "SELECT DISTINCT                                                  \n";
        $stSql .= "     ee.exercicio,                                               \n";
        $stSql .= "     ee.num_orgao,                                               \n";
        $stSql .= "     ee.num_unidade,                                             \n";
        $stSql .= "     tu.identificador,                                           \n";
        $stSql .= "     tu.numcgm,                                                  \n";
        $stSql .= "     sw_cgm.nom_cgm                                              \n";
        $stSql .= "FROM                                                             \n";
        $stSql .= "     empenho.restos_pre_empenho as ee                            \n";
        $stSql .= "LEFT JOIN                                                        \n";
        $stSql .= "     tceto.uniorcam      as tu                                   \n";
        $stSql .= "ON                                                               \n";
        $stSql .= "         ee.exercicio        = tu.exercicio                      \n";
        $stSql .= "     and ee.num_unidade      = tu.num_unidade                    \n";
        $stSql .= "     and ee.num_orgao        = tu.num_orgao                      \n";
        $stSql .= "LEFT JOIN sw_cgm                                                 \n";
        $stSql .= "ON                                                               \n";
        $stSql .= "         sw_cgm.numcgm       =  tu.numcgm                        \n";
        
        $stSql .= "UNION                                                            \n";
        $stSql .= "SELECT                                                           \n";
        $stSql .= "     '2004' as exercicio,                                        \n";
        $stSql .= "     oo.num_orgao,                                               \n";
        $stSql .= "     ou.num_unidade,                                             \n";
        $stSql .= "     tu.identificador,                                           \n";
        $stSql .= "     tu.numcgm,                                                  \n";
        $stSql .= "     sw_cgm.nom_cgm                                              \n";
        $stSql .= "FROM                                                             \n";
        $stSql .= "     orcamento.orgao     as oo,                                  \n";
        $stSql .= "     orcamento.unidade   as ou                                   \n";
        $stSql .= "LEFT JOIN                                                        \n";
        $stSql .= "     tceto.uniorcam      as tu                                   \n";
        $stSql .= "ON                                                               \n";
        $stSql .= "         '2004'              = tu.exercicio                      \n";
        $stSql .= "     and ou.num_unidade      = tu.num_unidade                    \n";
        $stSql .= "     and ou.num_orgao        = tu.num_orgao                      \n";
        $stSql .= "LEFT JOIN sw_cgm                                                 \n";
        $stSql .= "ON                                                               \n";
        $stSql .= "         sw_cgm.numcgm       =  tu.numcgm                        \n";
        $stSql .= "WHERE                                                            \n";
        $stSql .= "         oo.num_orgao = ou.num_orgao                             \n";
        $stSql .= "     and oo.exercicio = ou.exercicio                             \n";
        $stSql .= "     and oo.exercicio        = '2005'                            \n";

        $stSql .= "UNION                                                            \n";
        $stSql .= "SELECT                                                           \n";
        $stSql .= "     oo.exercicio,                                               \n";
        $stSql .= "     oo.num_orgao,                                               \n";
        $stSql .= "     ou.num_unidade,                                             \n";
        $stSql .= "     tu.identificador,                                           \n";
        $stSql .= "     tu.numcgm,                                                  \n";
        $stSql .= "     sw_cgm.nom_cgm                                              \n";
        $stSql .= "FROM                                                             \n";
        $stSql .= "     orcamento.orgao     as oo,                                  \n";
        $stSql .= "     orcamento.unidade   as ou                                   \n";
        $stSql .= "LEFT JOIN                                                        \n";
        $stSql .= "     tceto.uniorcam      as tu                                   \n";
        $stSql .= "ON                                                               \n";
        $stSql .= "         ou.exercicio        = tu.exercicio                      \n";
        $stSql .= "     and ou.num_unidade      = tu.num_unidade                    \n";
        $stSql .= "     and ou.num_orgao        = tu.num_orgao                      \n";
        $stSql .= "LEFT JOIN sw_cgm                                                 \n";
        $stSql .= "ON                                                               \n";
        $stSql .= "         sw_cgm.numcgm       =  tu.numcgm                        \n";
        $stSql .= "WHERE                                                            \n";
        $stSql .= "         oo.num_orgao = ou.num_orgao                             \n";
        $stSql .= "     and oo.exercicio = ou.exercicio                             \n";
        $stSql .= "     and oo.exercicio       < '".$this->getDado("exercicio")."'  \n";

        return $stSql;
    }

}
?>
