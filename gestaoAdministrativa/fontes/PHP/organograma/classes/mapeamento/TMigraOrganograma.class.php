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
    * Mapeamento da tabela organograma.de_para_setor
    * Data de criação: 05/12/2008

    * @author Analista: Gelson Wolowski
    * @author Programador: Diogo Zarpelon

    * @ignore

    $Id:$

    **/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TMigraOrganograma extends Persistente
{
    public $inCodNivelMinimo;

    public function getNivelMinimo() { return $this->inCodNivelMinimo; }

    public function setNivelMinimo($value) { $this->inCodNivelMinimo = $value; }

    /**
      * Método Construtor
      * @access Private
      */

    public function __construct()
    {
        parent::Persistente();

        $this->setTabela('organograma.de_para_setor');

        # Declaração da PK.
        $this->setCampoCod('');

        # Declaração do Complemento da PK.
        $this->setComplementoChave('ano_exercicio, cod_orgao, cod_unidade, cod_departamento, cod_setor');

        # Mapeamento da tabela.
        $this->AddCampo('ano_exercicio'         ,'varchar' ,true ,'4' ,true  ,false);
        $this->AddCampo('cod_orgao'             ,'integer' ,true ,''  ,true  ,false);
        $this->AddCampo('cod_unidade'           ,'integer' ,true ,''  ,true  ,false);
        $this->AddCampo('cod_departamento'      ,'integer' ,true ,''  ,true  ,false);
        $this->AddCampo('cod_setor'             ,'integer' ,true ,''  ,true  ,false);
        $this->AddCampo('cod_orgao_organograma' ,'integer' ,true ,''  ,false ,false);
    }

    # Monta a estrutura do Organamograma Padrão (antigo).
    public function recuperaOrganogramaPadrao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaOrganogramaPadrao().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaOrganogramaPadrao()
    {
        $stSql  = "      SELECT                                                                  \n";
        $stSql .= "             orgao.ano_exercicio                                              \n";
        $stSql .= "          ,  orgao.cod_orgao                                                  \n";
        $stSql .= "          ,  unidade.cod_unidade                                              \n";
        $stSql .= "          ,  departamento.cod_departamento                                    \n";
        $stSql .= "          ,  setor.cod_setor                                                  \n";
        $stSql .= "          ,  orgao.nom_orgao                                                  \n";
        $stSql .= "          ,  unidade.nom_unidade                                              \n";
        $stSql .= "          ,  departamento.nom_departamento                                    \n";
        $stSql .= "          ,  setor.nom_setor                                                  \n";
        $stSql .= "          ,  de_para_setor.cod_orgao_organograma                              \n";
        $stSql .= "                                                                              \n";
        $stSql .= "       FROM  organograma.de_para_setor                                        \n";
        $stSql .= "                                                                              \n";
        $stSql .= " INNER JOIN  administracao.orgao                                              \n";
        $stSql .= "         ON  orgao.cod_orgao     = de_para_setor.cod_orgao                    \n";
        $stSql .= "        AND  orgao.ano_exercicio = de_para_setor.ano_exercicio                \n";
        $stSql .= "                                                                              \n";
        $stSql .= " INNER JOIN  administracao.unidade                                            \n";
        $stSql .= "         ON  unidade.cod_orgao     = de_para_setor.cod_orgao                  \n";
        $stSql .= "        AND  unidade.cod_unidade   = de_para_setor.cod_unidade                \n";
        $stSql .= "        AND  unidade.ano_exercicio = de_para_setor.ano_exercicio              \n";
        $stSql .= "                                                                              \n";
        $stSql .= " INNER JOIN  administracao.departamento                                       \n";
        $stSql .= "         ON  departamento.cod_orgao        = de_para_setor.cod_orgao          \n";
        $stSql .= "        AND  departamento.cod_unidade      = de_para_setor.cod_unidade        \n";
        $stSql .= "        AND  departamento.cod_departamento = de_para_setor.cod_departamento   \n";
        $stSql .= "        AND  departamento.ano_exercicio    = de_para_setor.ano_exercicio      \n";
        $stSql .= "                                                                              \n";
        $stSql .= " INNER JOIN  administracao.setor                                              \n";
        $stSql .= "         ON  setor.cod_orgao        = de_para_setor.cod_orgao                 \n";
        $stSql .= "        AND  setor.cod_unidade      = de_para_setor.cod_unidade               \n";
        $stSql .= "        AND  setor.cod_departamento = de_para_setor.cod_departamento          \n";
        $stSql .= "        AND  setor.cod_setor        = de_para_setor.cod_setor                 \n";
        $stSql .= "        AND  setor.ano_exercicio    = de_para_setor.ano_exercicio             \n";
        $stSql .= "                                                                              \n";
        $stSql .= "      WHERE  1=1                                                              \n";
        $stSql .= "                                                                              \n";
        $stSql .= "        AND (                                                                 \n";
        $stSql .= "                 de_para_setor.cod_orgao        <> 0                          \n";
        $stSql .= "             OR  de_para_setor.cod_unidade      <> 0                          \n";
        $stSql .= "             OR  de_para_setor.cod_departamento <> 0                          \n";
        $stSql .= "             OR  de_para_setor.cod_setor        <> 0                          \n";
        $stSql .= "             OR  de_para_setor.ano_exercicio    <> '0000'                     \n";
        $stSql .= "            )                                                                 \n";
        $stSql .= "                                                                              \n";
        $stSql .= "   ORDER BY                                                                   \n";
        $stSql .= "             orgao.cod_orgao                                                  \n";
        $stSql .= "          ,  unidade.cod_unidade                                              \n";
        $stSql .= "          ,  departamento.cod_departamento                                    \n";
        $stSql .= "          ,  setor.cod_setor                                                  \n";

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
        $stSql .= "   FROM  organograma.de_para_setor                    \n";
        $stSql .= "                                                      \n";
        $stSql .= "  WHERE  de_para_setor.cod_orgao_organograma IS NULL  \n";

        return $stSql;
    }

    public function recuperaMsgMigra(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaMsgMigra();
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaMsgMigra()
    {
        $stSql  = " SELECT *                         \n";
        $stSql .= "   FROM fn_migra_organograma()    \n";

        return $stSql;
    }

    # Limpa os órgãos inválidos que por acaso possam ter sido setados, antes da regra implementada da GF.
    public function atualizaSetoresInvalidos()
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaAtualizaSetoresInvalidos().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaDML($stSql, $boTransacao);

        return $obErro;
    }

    public function montaAtualizaSetoresInvalidos()
    {
        $stSql  = " UPDATE  organograma.de_para_setor                                                   \n";
        $stSql .= "    SET  cod_orgao_organograma = null                                                \n";
        $stSql .= "  WHERE  (                                                                           \n";
        $stSql .= "             SELECT  true                                                            \n";
        $stSql .= "               FROM  organograma.vw_orgao_nivel                                      \n";
        $stSql .= "              WHERE  vw_orgao_nivel.cod_orgao = de_para_setor.cod_orgao_organograma  \n";
        $stSql .= "                AND  vw_orgao_nivel.nivel < ".$this->getNivelMinimo()."              \n";
        $stSql .= "              LIMIT  1                                                               \n";
        $stSql .= "         )                                                                           \n";
        # Condição para não limpar o órgão 'Não Informado' criado pela PL que migra o Organograma.
        $stSql .= "    AND  ano_exercicio    <> '0000'                                                  \n";
        $stSql .= "    AND  cod_orgao        <> 0                                                       \n";
        $stSql .= "    AND  cod_unidade      <> 0                                                       \n";
        $stSql .= "    AND  cod_departamento <> 0                                                       \n";
        $stSql .= "    AND  cod_setor        <> 0                                                       \n";

        return $stSql;
    }

}
