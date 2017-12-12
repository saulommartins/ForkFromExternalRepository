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
    * Mapeamento da tabela organograma.de_para_local
    * Data de criação: 08/12/2008

    * @author Analista: Gelson Wolowski
    * @author Programador: Diogo Zarpelon

    * @ignore

    $Id:$

    **/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE );

class TMigraOrganogramaLocal extends Persistente
{
    /**
      * Método Construtor
      * @access Private
      */

    public function TMigraOrganogramaLocal()
    {
        parent::Persistente();
        $this->setTabela('organograma.de_para_local');

        # Declaração da PK.
        $this->setCampoCod('');

        # Declaração do Complemento da PK.
        $this->setComplementoChave('ano_exercicio, cod_orgao, cod_unidade, cod_departamento, cod_setor, cod_local');

        # Mapeamento da tabela.
        $this->AddCampo('ano_exercicio'         ,'varchar' ,true ,'4' ,true  ,false);
        $this->AddCampo('cod_orgao'             ,'integer' ,true ,''  ,true  ,false);
        $this->AddCampo('cod_unidade'           ,'integer' ,true ,''  ,true  ,false);
        $this->AddCampo('cod_departamento'      ,'integer' ,true ,''  ,true  ,false);
        $this->AddCampo('cod_setor'             ,'integer' ,true ,''  ,true  ,false);
        $this->AddCampo('cod_local'             ,'integer' ,true ,''  ,true  ,false);
        $this->AddCampo('cod_local_organograma' ,'integer' ,true ,''  ,false ,false);
    }

    # Monta a estrutura do Organamograma Padrão (antigo).
    public function recuperaOrganogramaLocalPadrao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaOrganogramaLocalPadrao().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaOrganogramaLocalPadrao()
    {
        $stSql  = "         SELECT                                                                  \n";
        $stSql .= "                    orgao.ano_exercicio                                          \n";
        $stSql .= "                ,   orgao.cod_orgao                                              \n";
        $stSql .= "                ,   unidade.cod_unidade                                          \n";
        $stSql .= "                ,   departamento.cod_departamento                                \n";
        $stSql .= "                ,   setor.cod_setor                                              \n";
        $stSql .= "                ,   local.cod_local                                              \n";
        $stSql .= "                ,   local.nom_local                                              \n";
        $stSql .= "                ,   de_para_local.cod_local_organograma                          \n";
        $stSql .= "                                                                                 \n";
        $stSql .= "          FROM  organograma.de_para_local                                        \n";
        $stSql .= "                                                                                 \n";
        $stSql .= "    INNER JOIN  administracao.orgao                                              \n";
        $stSql .= "            ON  orgao.cod_orgao     = de_para_local.cod_orgao                    \n";
        $stSql .= "           AND  orgao.ano_exercicio = de_para_local.ano_exercicio                \n";
        $stSql .= "                                                                                 \n";
        $stSql .= "    INNER JOIN  administracao.unidade                                            \n";
        $stSql .= "            ON  unidade.cod_orgao     = de_para_local.cod_orgao                  \n";
        $stSql .= "           AND  unidade.cod_unidade   = de_para_local.cod_unidade                \n";
        $stSql .= "           AND  unidade.ano_exercicio = de_para_local.ano_exercicio              \n";
        $stSql .= "                                                                                 \n";
        $stSql .= "    INNER JOIN  administracao.departamento                                       \n";
        $stSql .= "            ON  departamento.cod_orgao        = de_para_local.cod_orgao          \n";
        $stSql .= "           AND  departamento.cod_unidade      = de_para_local.cod_unidade        \n";
        $stSql .= "           AND  departamento.cod_departamento = de_para_local.cod_departamento   \n";
        $stSql .= "           AND  departamento.ano_exercicio    = de_para_local.ano_exercicio      \n";
        $stSql .= "                                                                                 \n";
        $stSql .= "    INNER JOIN  administracao.setor                                              \n";
        $stSql .= "            ON  setor.cod_orgao        = de_para_local.cod_orgao                 \n";
        $stSql .= "           AND  setor.cod_unidade      = de_para_local.cod_unidade               \n";
        $stSql .= "           AND  setor.cod_departamento = de_para_local.cod_departamento          \n";
        $stSql .= "           AND  setor.cod_setor        = de_para_local.cod_setor                 \n";
        $stSql .= "           AND  setor.ano_exercicio    = de_para_local.ano_exercicio             \n";
        $stSql .= "                                                                                 \n";
        $stSql .= "    INNER JOIN  administracao.local                                              \n";
        $stSql .= "            ON  local.cod_orgao        = de_para_local.cod_orgao                 \n";
        $stSql .= "           AND  local.cod_unidade      = de_para_local.cod_unidade               \n";
        $stSql .= "           AND  local.cod_departamento = de_para_local.cod_departamento          \n";
        $stSql .= "           AND  local.cod_setor        = de_para_local.cod_setor                 \n";
        $stSql .= "           AND  local.cod_local        = de_para_local.cod_local                 \n";
        $stSql .= "           AND  local.ano_exercicio    = de_para_local.ano_exercicio             \n";
        $stSql .= "                                                                                 \n";
        $stSql .= "      WHERE  1=1                                                                 \n";
        $stSql .= "                                                                                 \n";
        $stSql .= "        AND (                                                                    \n";
        $stSql .= "                 de_para_local.cod_orgao        <> 0                             \n";
        $stSql .= "             OR  de_para_local.cod_unidade      <> 0                             \n";
        $stSql .= "             OR  de_para_local.cod_departamento <> 0                             \n";
        $stSql .= "             OR  de_para_local.cod_setor        <> 0                             \n";
        $stSql .= "             OR  de_para_local.ano_exercicio    <> '0000'                        \n";
        $stSql .= "             OR  de_para_local.cod_local        <> 0                             \n";
        $stSql .= "            )                                                                    \n";
        $stSql .= "                                                                                 \n";
        $stSql .= "      ORDER BY                                                                   \n";
        $stSql .= "                orgao.cod_orgao                                                  \n";
        $stSql .= "             ,  unidade.cod_unidade                                              \n";
        $stSql .= "             ,  departamento.cod_departamento                                    \n";
        $stSql .= "             ,  setor.cod_setor                                                  \n";
        $stSql .= "             ,  local.cod_local                                                  \n";

        return $stSql;
    }

    # Verifica a totalidade da migração do Setor.
    public function recuperaMigraTotalidade(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaMigraTotalidade().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaMigraTotalidade()
    {
        $stSql  = " SELECT                                               \n";
        $stSql .= "         CASE WHEN count(1) = 0                       \n";
        $stSql .= "              THEN 'true'                             \n";
        $stSql .= "              ELSE 'false'                            \n";
        $stSql .= "         END as finalizado                            \n";
        $stSql .= "                                                      \n";
        $stSql .= "   FROM  organograma.de_para_local                    \n";
        $stSql .= "                                                      \n";
        $stSql .= "  WHERE  de_para_local.cod_local_organograma IS NULL  \n";

        return $stSql;
    }

}
