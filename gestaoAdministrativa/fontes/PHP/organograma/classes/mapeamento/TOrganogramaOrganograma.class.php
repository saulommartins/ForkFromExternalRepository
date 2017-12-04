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
  * Classe de Mapeamento para tabela organograma_organograma
  * Data de Criação: 25/07/2005

  * @author Analista: Cassiano
  * @author Desenvolvedor: Cassiano

  Casos de uso: uc-01.05.01, uc-01.05.02, uc-01.05.03

  $Id: TOrganogramaOrganograma.class.php 59612 2014-09-02 12:00:51Z gelson $

  */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

/**
  * Efetua conexão com a tabela  ORGANOGRAMA_ORGANOGRAMA
  * Data de Criação: 16/08/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Diego Barbosa Victoria

  * @package URBEM
  * @subpackage Mapeamento
  */
class TOrganogramaOrganograma extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TOrganogramaOrganograma()
    {
        parent::Persistente();
        $this->setTabela('organograma.organograma');

        $this->setCampoCod('cod_organograma');
        $this->setComplementoChave('');

        $this->AddCampo('cod_organograma'       ,'integer' ,true  ,'' ,true  ,false);
        $this->AddCampo('cod_norma'             ,'integer' ,false ,'' ,false ,true );
        $this->AddCampo('implantacao'           ,'date'    ,false ,'' ,false ,false);
        $this->AddCampo('ativo'                 ,'boolean' ,false ,'' ,false ,false);
        $this->AddCampo('permissao_hierarquica' ,'boolean' ,false ,'' ,false ,false);
    }

    /**
        * Seleciona os organogramas sem orgao cadastrado
        * @access Public
        * @param  Object $rsRecordSet Retorna o RecordSet preenchido
        * @param  Object $obTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function recuperaOrganogramas(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSQL = $this->montaRecuperaOrganogramas().$stFiltro.$stOrdem;
        $this->setDebug( $stSQL );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSQL,  $boTransacao );

        return $obErro;
    }
    /**
        * Monta consulta para recuperar organogramas sem orgao
        * @access Private
        * @return String $stSQL
    */
    public function montaRecuperaOrganogramas()
    {
        $stSQL  = " SELECT                                                   \n";
        $stSQL .= "       cod_organograma                                    \n";
        $stSQL .= "     , cod_norma                                          \n";
        $stSQL .= "     , implantacao as dt_implantacao                      \n";
        $stSQL .= "     , TO_CHAR(implantacao,'dd/mm/yyyy') as implantacao   \n";
        $stSQL .= "     , ativo                                              \n";
        $stSQL .= "     , CASE WHEN ativo = true THEN 'Sim' ELSE 'Não' END as msg_ativo \n";
        $stSQL .= "     , CASE WHEN permissao_hierarquica = true THEN 'Sim' ELSE 'Não' END as permissao_hierarquica \n";
        $stSQL .= "                                                          \n";
        $stSQL .= " FROM  organograma.organograma                            \n";

        return $stSQL;
    }

    /**
        * Seleciona os organogramas sem orgao cadastrado somente os ativos
        * @access Public
        * @param  Object $rsRecordSet Retorna o RecordSet preenchido
        * @param  Object $obTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function recuperaOrganogramasAtivo(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if ($stFiltro == '') {
            $stFiltro = ' WHERE ';
        }
        $stFiltro .= ' ativo = true';

        $stSQL = $this->montaRecuperaOrganogramas().$stFiltro.$stOrdem;
        $this->setDebug( $stSQL );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSQL,  $boTransacao );

        return $obErro;
    }

    public function recuperaProcessamentoOrganograma(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSQL = $this->montaRecuperaProcessamentoOrganograma().$stFiltro.$stOrdem;
        $this->setDebug( $stSQL );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSQL,  $boTransacao );

        return $obErro;
    }

    public function montaRecuperaProcessamentoOrganograma()
    {
        $stSql  = "        SELECT  DISTINCT organograma.cod_organograma                           \n";
        $stSql .= "             ,  CASE WHEN organograma.cod_organograma IS NOT NULL THEN         \n";
        $stSql .= "                    'true'                                                     \n";
        $stSql .= "                END as organograma_processado                                  \n";
        $stSql .= "                                                                               \n";
        $stSql .= "          FROM  organograma.de_para_orgao_historico                            \n";
        $stSql .= "                                                                               \n";
        $stSql .= "    INNER JOIN  organograma.orgao_nivel                                        \n";
        $stSql .= "            ON  orgao_nivel.cod_orgao = de_para_orgao_historico.cod_orgao_new  \n";
        $stSql .= "            OR  orgao_nivel.cod_orgao = de_para_orgao_historico.cod_orgao      \n";
        $stSql .= "                                                                               \n";
        $stSql .= "    INNER JOIN  organograma.organograma                                        \n";
        $stSql .= "            ON  organograma.cod_organograma = orgao_nivel.cod_organograma      \n";
        $stSql .= "                                                                               \n";
        $stSql .= "         WHERE  1=1                                                            \n";

        if ($this->getDado('cod_organograma')) {
            $stSql .= " AND  organograma.cod_organograma = ".$this->getDado('cod_organograma');
        }

        return $stSql;
    }

}
?>
