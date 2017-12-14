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
    * Classe de mapeamento da tabela ORCAMENTO.UNIDADE
    * Data de Criação: 16/10/2008

    * @author Analista: Heleno Santos
    * @author Desenvolvedor: Aldo Jean Soares Silva

    * @package URBEM
    * @subpackage Mapeamento

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TPPAUnidadeOrcamentaria extends Persistente
{
    /**
    * Método Construtor
    */
    public function TPPAUnidadeOrcamentaria()
    {
        parent::Persistente();
        $this->setTabela('orcamento.unidade');
        $this->setCampoCod('');
        $this->setComplementoChave('exercicio,num_unidade,num_orgao');
        $this->AddCampo('exercicio',   'char',    true, '04', true,  true);
        $this->AddCampo('num_unidade', 'integer', true, '',   true,  false);
        $this->AddCampo('num_orgao',   'integer', true, '',   true,  true);
        $this->AddCampo('exercicio',   'char',    true, '04', false, false);
        $this->AddCampo('cod_orgao',   'integer', true, '',   false, false);
        $this->AddCampo('cod_unidade', 'integer', true, '',   false, false);
    }

    /**
     *
     * @param  RecordSet $rsRecordSet
     * @param  string    $stFiltro
     * @param  string    $stOrder
     * @return object    Erro
     */
    public function recuperaUnidadeOrcamentaria(&$rsRecordSet, $stFiltro, $stOrder)
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaUnidadeOrcamentaria().$stFiltro.$stOrder;
        $boTransacao = "";
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    /**
     *
     * @return strign SQL
     */
    public function montaRecuperaUnidadeOrcamentaria()
    {
        $stSql  = "  select OU.exercicio                                          \n";
        $stSql .= "         , OU.num_unidade                                      \n";
        $stSql .= "         , OU.num_orgao                                        \n";
        $stSql .= "         , OU.exercicio                                        \n";
        $stSql .= "         , OU.cod_orgao                                        \n";
        $stSql .= "         , OU.cod_unidade                                      \n";
        $stSql .= "         , AU.nom_unidade as nom_unidade                       \n";
        $stSql .= "         , AO.nom_orgao as nom_orgao                           \n";
        $stSql .= "           from orcamento.unidade as OU                        \n";
        $stSql .= "           join administracao.unidade as AU                    \n";
        $stSql .= "                on AU.cod_unidade = OU.cod_unidade             \n";
        $stSql .= "                and AU.cod_orgao = OU.cod_orgao                \n";
        $stSql .= "                and AU.ano_exercicio = OU.exercicio            \n";
        $stSql .= "           join orcamento.orgao as OO                          \n";
        $stSql .= "                on OO.exercicio = OU.exercicio                 \n";
        $stSql .= "                and OO.num_orgao = OU.num_orgao                \n";
        $stSql .= "           join administracao.orgao as AO                      \n";
        $stSql .= "                on AO.ano_exercicio = OO.exercicio             \n";
        $stSql .= "                and AO.cod_orgao = OO.cod_orgao                \n";

        return $stSql;
    }

    /**
     *
     * @param  RecordSet $rsRecordSet
     * @param  string    $stCondicao
     * @param  string    $stOrdem
     * @param  bool      $boTransacao
     * @return object    Erro
     */
    public function recuperaTodos(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaTodos().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    /**
     *
     * @return string SQL
     */
    public function montaRecuperaTodos()
    {
        $stSql = " SELECT OU.exercicio
                        , OU.num_unidade
                        , OU.num_orgao
                        , administracao.orgao.nom_orgao || ' - ' || administracao.unidade.nom_unidade as nom_unidade
                     from orcamento.unidade AS OU
                     join administracao.unidade
                       on administracao.unidade.cod_unidade = OU.cod_unidade
                      and administracao.unidade.cod_orgao = OU.cod_orgao
                      and administracao.unidade.ano_exercicio = OU.exercicio
                     join orcamento.orgao
                       on orcamento.orgao.exercicio = OU.exercicio
                      and orcamento.orgao.num_orgao = OU.num_orgao
                     join administracao.orgao
                       on administracao.orgao.ano_exercicio = orcamento.orgao.exercicio
                      and administracao.orgao.cod_orgao = orcamento.orgao.cod_orgao";

        return $stSql;
    }

}
