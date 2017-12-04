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
    * Classe de mapeamento da tabela TCEPB.UNIORCAM
    * Data de Criação: 16/01/2014

    * @author Analista: Eduardo Schitz
    * @author Desenvolvedor: Franver Sarmento de Moraes

    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TExportacaoTCEPBUniOrcam.class.php 59612 2014-09-02 12:00:51Z gelson $
    * $Name: $
    * $Revision: 58265 $
    * $Author: franver $
    * $Date: 2014-05-20 14:21:14 -0300 (Ter, 20 Mai 2014) $

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TExportacaoTCEPBUniOrcam extends Persistente
{
    public function TExportacaoTCEPBUniOrcam()
    {
        parent::Persistente();
        $this->setTabela('tcepb.uniorcam');
        $this->setComplementoChave('exercicio,num_unidade,num_orgao');

        $this->AddCampo('exercicio','varchar',true,'4',true,true);
        $this->AddCampo('num_unidade','integer',true,'',true,true);
        $this->AddCampo('num_orgao','integer',true,'',true,true);
        $this->AddCampo('cgm_ordenador','integer',false,'',false,false);
        $this->AddCampo('natureza_juridica','integer',false,'',false,false);
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
        $stSql  = "";
        $stSql .= "SELECT                                                           \n";
        $stSql .= "     oo.nom_orgao,                                               \n";
        $stSql .= "     oo.num_orgao,                                               \n";
        $stSql .= "     ou.nom_unidade,                                             \n";
        $stSql .= "     ou.num_unidade,                                             \n";
        $stSql .= "     tu.cgm_ordenador AS num_cgm,                                           \n";
        $stSql .= "     tu.natureza_juridica,                                       \n";
        $stSql .= "     sw_cgm.nom_cgm AS nom_cgm_responsavel                                                \n";
        $stSql .= "FROM                                                             \n";
        $stSql .= "     orcamento.orgao     as oo,                              \n";
        $stSql .= "     orcamento.unidade   as ou                               \n";
        $stSql .= "LEFT JOIN                                                        \n";
        $stSql .= "     tcepb.uniorcam      as tu                               \n";
        $stSql .= "ON                                                               \n";
        $stSql .= "         ou.exercicio        = tu.exercicio                      \n";
        $stSql .= "     and ou.num_unidade      = tu.num_unidade                    \n";
        $stSql .= "     and ou.num_orgao        = tu.num_orgao                      \n";
        $stSql .= "LEFT JOIN sw_cgm                                                 \n";
        $stSql .= "ON sw_cgm.numcgm=tu.cgm_ordenador                                \n";
        $stSql .= "WHERE                                                            \n";
        $stSql .= "         oo.num_orgao = ou.num_orgao                             \n";
        $stSql .= "     and oo.exercicio = ou.exercicio                             \n";
        $stSql .= "     and oo.exercicio        = '".$this->getDado("exercicio")."' \n";
        return $stSql;
    }
    
    public function recuperaOrdenadoresDespesa(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaOrdenadoresDespesa().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaOrdenadoresDespesa()
    {
        $stSql = "SELECT sw_cgm.nom_cgm AS nome_ordenador
                       , sw_cgm_pessoa_fisica.cpf AS cpf_ordenador
                    FROM tcepb.uniorcam
                    JOIN sw_cgm
                      ON sw_cgm.numcgm = uniorcam.cgm_ordenador
               LEFT JOIN sw_cgm_pessoa_fisica
                      ON sw_cgm_pessoa_fisica.numcgm = uniorcam.cgm_ordenador
                   WHERE uniorcam.exercicio = '".$this->getDado('exercicio')."'
                GROUP BY 1,2                                                ";
        return $stSql;
    }
}
