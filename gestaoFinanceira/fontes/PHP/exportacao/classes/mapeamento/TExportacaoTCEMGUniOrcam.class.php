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
    * Classe de mapeamento da tabela TCEMG.UNIORCAM
    * Data de Criação: 16/01/2014

    * @author Analista: Eduardo Schitz
    * @author Desenvolvedor: Franver Sarmento de Moraes

    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TExportacaoTCEMGUniOrcam.class.php 63535 2015-09-09 17:25:06Z franver $
    * $Name: $
    * $Revision: 63535 $
    * $Author: franver $
    * $Date: 2015-09-09 14:25:06 -0300 (Wed, 09 Sep 2015) $

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TExportacaoTCEMGUniOrcam extends Persistente
{
    public function TExportacaoTCEMGUniOrcam()
    {
        parent::Persistente();
        $this->setTabela('tcemg.uniorcam');
        $this->setComplementoChave('exercicio,num_unidade,num_orgao');

        $this->AddCampo('exercicio','varchar',true,'4',true,true);
        $this->AddCampo('num_unidade','integer',true,'',true,true);
        $this->AddCampo('num_orgao','integer',true,'',true,true);
        $this->AddCampo('identificador','integer',true,'',false,false);
        $this->AddCampo('cgm_ordenador','integer',false,'',false,false);
        $this->AddCampo('num_orgao_atual','integer',false,'',false,false);
        $this->AddCampo('num_unidade_atual','integer',false,'',false,false);
        $this->AddCampo('exercicio_atual','varchar',false,'4',false,false);
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
        $stSql .= "     tu.identificador,                                           \n";
        $stSql .= "     tu.cgm_ordenador AS num_cgm,                                \n";
        $stSql .= "     sw_cgm.nom_cgm AS nom_cgm_responsavel                       \n";
        $stSql .= "FROM                                                             \n";
        $stSql .= "     orcamento.orgao     as oo,                                  \n";
        $stSql .= "     orcamento.unidade   as ou                                   \n";
        $stSql .= "LEFT JOIN                                                        \n";
        $stSql .= "     tcemg.uniorcam      as tu                                   \n";
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
        $stSql .= "     tu.cgm_ordenador,                                           \n";
        $stSql .= "     tu.num_unidade_atual,                                       \n";
        $stSql .= "     tu.num_orgao_atual,                                         \n";
        $stSql .= "     tu.exercicio_atual,                                         \n";
        $stSql .= "     sw_cgm.nom_cgm AS nom_cgm_responsavel                       \n";
        $stSql .= "FROM                                                             \n";
        $stSql .= "     empenho.restos_pre_empenho as ee                            \n";
        $stSql .= "LEFT JOIN                                                        \n";
        $stSql .= "     tcemg.uniorcam      as tu                                   \n";
        $stSql .= "ON                                                               \n";
        $stSql .= "         ee.exercicio        = tu.exercicio                      \n";
        $stSql .= "     and ee.num_unidade      = tu.num_unidade                    \n";
        $stSql .= "     and ee.num_orgao        = tu.num_orgao                      \n";
        $stSql .= "LEFT JOIN sw_cgm                                                 \n";
        $stSql .= "ON sw_cgm.numcgm=tu.cgm_ordenador                                \n";
        $stSql .= "UNION                                                            \n";
        $stSql .= "SELECT                                                           \n";
        $stSql .= "     '2004' as exercicio,                                        \n";
        $stSql .= "     oo.num_orgao,                                               \n";
        $stSql .= "     ou.num_unidade,                                             \n";
        $stSql .= "     tu.identificador,                                           \n";
        $stSql .= "     tu.cgm_ordenador,                                           \n";
        $stSql .= "     tu.num_unidade_atual,                                       \n";
        $stSql .= "     tu.num_orgao_atual,                                         \n";
        $stSql .= "     tu.exercicio_atual,                                         \n";
        $stSql .= "     sw_cgm.nom_cgm AS nom_cgm_responsavel                       \n";
        $stSql .= "FROM                                                             \n";
        $stSql .= "     orcamento.orgao     as oo,                                  \n";
        $stSql .= "     orcamento.unidade   as ou                                   \n";
        $stSql .= "LEFT JOIN                                                        \n";
        $stSql .= "     tcemg.uniorcam      as tu                                   \n";
        $stSql .= "ON                                                               \n";
        $stSql .= "         ou.exercicio        = tu.exercicio                      \n";
        $stSql .= "     and ou.num_unidade      = tu.num_unidade                    \n";
        $stSql .= "     and ou.num_orgao        = tu.num_orgao                      \n";
        $stSql .= "LEFT JOIN sw_cgm                                                 \n";
        $stSql .= "ON sw_cgm.numcgm=tu.cgm_ordenador                                \n";
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
        $stSql .= "     tu.cgm_ordenador,                                           \n";
        $stSql .= "     tu.num_unidade_atual,                                       \n";
        $stSql .= "     tu.num_orgao_atual,                                         \n";
        $stSql .= "     tu.exercicio_atual,                                         \n";
        $stSql .= "     sw_cgm.nom_cgm AS nom_cgm_responsavel                       \n";
        $stSql .= "FROM                                                             \n";
        $stSql .= "     orcamento.orgao     as oo,                                  \n";
        $stSql .= "     orcamento.unidade   as ou                                   \n";
        $stSql .= "LEFT JOIN                                                        \n";
        $stSql .= "     tcemg.uniorcam      as tu                                   \n";
        $stSql .= "LEFT JOIN sw_cgm                                                 \n";
        $stSql .= "ON sw_cgm.numcgm=tu.cgm_ordenador                                \n";
        $stSql .= "ON                                                               \n";
        $stSql .= "         ou.exercicio        = tu.exercicio                      \n";
        $stSql .= "     and ou.num_unidade      = tu.num_unidade                    \n";
        $stSql .= "     and ou.num_orgao        = tu.num_orgao                      \n";
        $stSql .= "WHERE                                                            \n";
        $stSql .= "         oo.num_orgao = ou.num_orgao                             \n";
        $stSql .= "     and oo.exercicio = ou.exercicio                             \n";
        $stSql .= "     and oo.exercicio       < '".$this->getDado("exercicio")."'  \n";

        return $stSql;
    }

    public function recuperaDadosExportacaoUOC(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaDadosExportacaoUOC",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaDadosExportacaoUOC()
    {
        $stSql = " SELECT  lpad(".$this->getDado('cod_orgao')."::VARCHAR,2,'0') AS codorgao
                        , lpad(lpad(uniorcam.num_orgao::VARCHAR, 2, '0')||lpad(uniorcam.num_unidade::VARCHAR, 2, '0'),5,'0') AS codunidadesub
                        , CASE WHEN uniorcam.identificador = 1 OR uniorcam.identificador = 2 OR uniorcam.identificador = 3 OR
                                    uniorcam.identificador = 4 OR uniorcam.identificador = 99
                                THEN lpad(uniorcam.identificador::VARCHAR, 2,'0')
                                ELSE ''
                            END AS idfundo
                        , unidade.nom_unidade AS descunidadesub
                        , 2 AS esubunidade
                        , uniorcam.num_orgao
                        , uniorcam.num_unidade

                     FROM tcemg.uniorcam

                     JOIN orcamento.orgao
                       ON orgao.num_orgao = uniorcam.num_orgao
                      AND orgao.exercicio = uniorcam.exercicio

                     JOIN orcamento.unidade
                       ON unidade.num_unidade = uniorcam.num_unidade
                      AND unidade.num_orgao = uniorcam.num_orgao
                      AND unidade.exercicio = uniorcam.exercicio

                    WHERE uniorcam.exercicio = '".$this->getDado('exercicio')."'

                      AND NOT EXISTS (
                                         SELECT 1
                                           FROM tcemg.arquivo_uoc
                                          WHERE arquivo_uoc.num_orgao   = uniorcam.num_orgao
                                            AND arquivo_uoc.num_unidade = uniorcam.num_unidade
                                            AND arquivo_uoc.exercicio   = '".$this->getDado('exercicio')."'
                                            AND arquivo_uoc.mes < ".$this->getDado('mes')." 
                                        )

        ";

        return $stSql;
    }

    public function recuperaDadosExportacaoIUOC(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaDadosExportacaoIUOC",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaDadosExportacaoIUOC()
    {
        $stSql = " SELECT  lpad(".$this->getDado('cod_orgao')."::VARCHAR,2,'0') AS codorgao
                        , lpad(lpad(uniorcam.num_orgao::VARCHAR, 2, '0')||lpad(uniorcam.num_unidade::VARCHAR, 2, '0'),5,'0') AS codunidadesub
                        , CASE WHEN uniorcam.identificador = 1 OR uniorcam.identificador = 2 OR uniorcam.identificador = 3 OR
                                    uniorcam.identificador = 4 OR uniorcam.identificador = 99
                                THEN lpad(uniorcam.identificador::VARCHAR, 2,'0')
                                ELSE ''
                            END AS idfundo
                        , unidade.nom_unidade AS descunidadesub
                        , 2 AS esubunidade
                        , uniorcam.num_orgao
                        , uniorcam.num_unidade

                     FROM tcemg.uniorcam

                     JOIN orcamento.orgao
                       ON orgao.num_orgao = uniorcam.num_orgao
                      AND orgao.exercicio = uniorcam.exercicio

                     JOIN orcamento.unidade
                       ON unidade.num_unidade = uniorcam.num_unidade
                      AND unidade.num_orgao = uniorcam.num_orgao
                      AND unidade.exercicio = uniorcam.exercicio

                    WHERE uniorcam.exercicio = '".$this->getDado('exercicio')."'

                      AND NOT EXISTS (
                                         SELECT 1
                                           FROM tcemg.arquivo_uoc
                                          WHERE arquivo_uoc.num_orgao   = uniorcam.num_orgao
                                            AND arquivo_uoc.num_unidade = uniorcam.num_unidade
                                            AND arquivo_uoc.exercicio   = '".$this->getDado('exercicio')."'
                                        )
                      AND NOT EXISTS (
                                         SELECT 1
                                           FROM tcemg.arquivo_iuoc
                                          WHERE arquivo_iuoc.num_orgao   = uniorcam.num_orgao
                                            AND arquivo_iuoc.num_unidade = uniorcam.num_unidade
                                            AND arquivo_iuoc.exercicio   = '".$this->getDado('exercicio')."'
                                            AND arquivo_iuoc.mes         < ".$this->getDado('mes')." 
                                        )

        ";

        return $stSql;
    }

    public function recuperaDadosEntidade(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaDadosEntidade",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaDadosEntidade()
    {
        $stSql = "SELECT *
                    FROM administracao.configuracao_entidade
                   WHERE configuracao_entidade.cod_entidade IN (".$this->getDado('entidades').")
                     AND configuracao_entidade.exercicio = '".$this->getDado('exercicio')."'
                     AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'";

        return $stSql;
    }
}
