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
    * Mapeamento da tabela organograma.de_para_orgao
    * Data de criação: 13/04/2009

    * @author Analista:    Gelson Wolowski <gelson.goncalves@cnm.org.br>
    * @author Programador: Diogo Zarpelon  <diogo.zarpelon@cnm.org.br>

    * @ignore

    $Id:$

    **/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TConfigurarMigracaoOrganogramaDinamico extends Persistente
{
    /**
      * Método Construtor
      * @access Private
      */

    public function __construct()
    {
        parent::Persistente();

        $this->setTabela('organograma.de_para_orgao');

        # Declaração da PK.
        $this->setCampoCod('cod_orgao');

        # Mapeamento da tabela.
        $this->AddCampo('cod_orgao'       ,'integer' ,true  ,''  ,true  ,false);
        $this->AddCampo('cod_organograma' ,'integer' ,false ,''  ,true  ,false);
        $this->AddCampo('cod_orgao_new'   ,'integer' ,true  ,''  ,false ,false);
    }

    # Recupera todos os organogramas que estão sendo utilizados no sistema disponíveis para a migração.
    public function recuperaOrganogramaUtilizado(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaOrganogramaUtilizado().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaOrganogramaUtilizado()
    {
        $stSql  = "     SELECT  DISTINCT de_para_orgao.cod_organograma                          \n";
        $stSql .= "          ,  TO_CHAR(organograma.implantacao, 'DD/MM/YYYY') as implantacao   \n";
        $stSql .= "                                                                             \n";
        $stSql .= "       FROM  organograma.de_para_orgao                                       \n";
        $stSql .= "                                                                             \n";
        $stSql .= " INNER JOIN  organograma.organograma                                         \n";
        $stSql .= "         ON  organograma.cod_organograma = de_para_orgao.cod_organograma     \n";
        $stSql .= "                                                                             \n";
        $stSql .= "      WHERE  1=1                                                             \n";
        $stSql .= "                                                                             \n";
        $stSql .= "   ORDER BY  de_para_orgao.cod_organograma                                   \n";
        $stSql .= "          ,  TO_CHAR(organograma.implantacao, 'DD/MM/YYYY')                  \n";

        return $stSql;
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
        $stSql  = "     SELECT                                                                  \n";
        $stSql .= "             cod_orgao                                                       \n";
        $stSql .= "          ,  cod_organograma                                                 \n";
        $stSql .= "          ,  (                                                               \n";
        $stSql .= "                 SELECT  descricao                                           \n";
        $stSql .= "                   FROM  organograma.orgao_descricao                         \n";
        $stSql .= "                  WHERE  orgao_descricao.cod_orgao = de_para_orgao.cod_orgao \n";
        $stSql .= "                  LIMIT  1                                                   \n";
        $stSql .= "             ) as descricao                                                  \n";
        $stSql .= "          ,  (                                                               \n";
        $stSql .= "                 SELECT  orgao_reduzido                                      \n";
        $stSql .= "                   FROM  organograma.vw_orgao_nivel                          \n";
        $stSql .= "                  WHERE  vw_orgao_nivel.cod_orgao = de_para_orgao.cod_orgao  \n";
        $stSql .= "                  LIMIT  1                                                   \n";
        $stSql .= "             ) as orgao_reduzido                                             \n";
        $stSql .= "          ,  cod_orgao_new                                                   \n";
        $stSql .= "                                                                             \n";
        $stSql .= "       FROM  organograma.de_para_orgao                                       \n";
        $stSql .= "                                                                             \n";
        $stSql .= "      WHERE  1=1                                                             \n";

        if ($this->getDado('cod_organograma')) {
            $stSql .= "      AND  de_para_orgao.cod_organograma = ".$this->getDado('cod_organograma');
        }

        $stSql .= "                                                                             \n";
        $stSql .= "   ORDER BY  orgao_reduzido                                                  \n";
        $stSql .= "          ,  de_para_orgao.cod_organograma                                   \n";
        $stSql .= "          ,  de_para_orgao.cod_orgao                                         \n";

        return $stSql;
    }

    # Monta a estrutura do Organamograma antigo.
    public function recuperaOrganogramaAntigo(&$rsRecordSet, $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaOrganogramaAntigo().$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaOrganogramaAntigo()
    {
        $stSql  = "      SELECT                                                                                                               ";
        $stSql .= "            orgao.cod_orgao                                                                                                ";
        $stSql .= "            ,de_para_orgao.cod_organograma                                                                                 ";
        $stSql .= "            ,recuperadescricaoorgao(orgao.cod_orgao, now()::date) as descricao                                             ";
        $stSql .= "            ,publico.fn_mascarareduzida(organograma.fn_consulta_orgao(orn.cod_organograma, orgao.cod_orgao)) AS orgao_reduzido ";
        $stSql .= "            ,cod_orgao_new                                                                                                 ";
        $stSql .= "            ,orgao.criacao                                                                                                 ";
        $stSql .= "            ,orn.cod_organograma                                                                                           ";
        $stSql .= "            ,organograma.fn_consulta_orgao(orn.cod_organograma, orgao.cod_orgao) AS orgao                                  ";
        $stSql .= "            ,publico.fn_nivel(organograma.fn_consulta_orgao(orn.cod_organograma, orgao.cod_orgao)) AS nivel                ";
        $stSql .= "       FROM organograma.orgao orgao                                                                                        ";
        $stSql .= " INNER JOIN organograma.orgao_nivel orn                                                                                    ";
        $stSql .= "         ON orgao.cod_orgao = orn.cod_orgao                                                                                ";
        $stSql .= "  LEFT JOIN organograma.de_para_orgao                                                                                      ";
        $stSql .= "         ON de_para_orgao.cod_orgao = orgao.cod_orgao                                                                      ";
        $stSql .= "      WHERE orn.cod_organograma = ".$this->getDado('cod_organograma')."                                                    ";
        $stSql .= "         OR de_para_orgao.cod_organograma = ".$this->getDado('cod_organograma')."                                          ";
        $stSql .= "   GROUP BY                                                                                                                ";
        $stSql .= "            de_para_orgao.cod_orgao                                                                                        ";
        $stSql .= "           ,de_para_orgao.cod_organograma                                                                                  ";
        $stSql .= "           ,orgao.cod_orgao                                                                                                ";
        $stSql .= "           ,orgao.criacao                                                                                                  ";
        $stSql .= "           ,orn.cod_organograma                                                                                            ";
        $stSql .= "           ,cod_orgao_new                                                                                                  ";
        $stSql .= "           ,nivel                                                                                                          ";
        $stSql .= "  ORDER BY  orgao                                                                                                          ";
        $stSql .= "           ,cod_orgao                                                                                                      ";

        return $stSql;
    }

    # Verifica a totalidade da migração do Órgão.
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
        $stSql  = " SELECT                                          \n";
        $stSql .= "         CASE WHEN count(1) = 0                  \n";
        $stSql .= "              THEN 'true'                        \n";
        $stSql .= "              ELSE 'false'                       \n";
        $stSql .= "         END as finalizado                       \n";
        $stSql .= "                                                 \n";
        $stSql .= "   FROM  organograma.de_para_orgao               \n";
        $stSql .= "                                                 \n";
        $stSql .= "  WHERE  de_para_orgao.cod_orgao_new IS NULL     \n";

        return $stSql;
    }

    # Efetiva o processamento da migração (chamada para a PL que executa as rotinas no banco)
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
        $stSql  = " SELECT *                                                        \n";
        $stSql .= "   FROM organograma.fn_migra_orgaos(".Sessao::read('numCgm').")  \n";

        return $stSql;
    }

    # Executa a carga para a tabela organograma.de_para_orgao preencher com os órgãos utilizados no sistema.
    public function recuperaCargaDeParaOrganograma(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaCargaDeParaOrganograma();
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaCargaDeParaOrganograma()
    {
        $stSql  = " SELECT  fn_popula_de_para_orgaos as nro_atualizacoes \n";
        $stSql .= "   FROM  organograma.fn_popula_de_para_orgaos()       \n";

        return $stSql;
    }

    public function limparMigracaoParcial()
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaLimparMigracaoParcial().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaDML($stSql, $boTransacao);

        return $obErro;
    }

    public function montaLimparMigracaoParcial()
    {
        $stSql  = " UPDATE  organograma.de_para_orgao  \n";
        $stSql .= "    SET  cod_orgao_new = null       \n";

        return $stSql;
    }

    # Comentario a seguir.
    public function recuperaVerificadorMigracaoParcial(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaVerificadorMigracaoParcial().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaVerificadorMigracaoParcial()
    {
        $stSql  = "    SELECT                                                       \n";
        $stSql .= "        (                                                        \n";
        $stSql .= "            SELECT                                               \n";
        $stSql .= "                    CASE WHEN count(1) = 0                       \n";
        $stSql .= "                    THEN 'true'                                  \n";
        $stSql .= "                    ELSE 'false'                                 \n";
        $stSql .= "                    END as finalizado                            \n";
        $stSql .= "              FROM  organograma.de_para_orgao                    \n";
        $stSql .= "             WHERE  de_para_orgao.cod_orgao_new IS NULL          \n";
        $stSql .= "        )   as finalizado                                        \n";
        $stSql .= "         ,  orgao_nivel.cod_organograma                          \n";
        $stSql .= "      FROM  organograma.de_para_orgao                            \n";
        $stSql .= "                                                                 \n";
        $stSql .= "INNER JOIN  organograma.orgao_nivel                              \n";
        $stSql .= "        ON  orgao_nivel.cod_orgao = de_para_orgao.cod_orgao_new  \n";
        $stSql .= "                                                                 \n";
        $stSql .= "     WHERE  de_para_orgao.cod_orgao_new IS NOT NULL              \n";
        $stSql .= "                                                                 \n";
        $stSql .= "     LIMIT  1                                                    \n";

        return $stSql;
    }

    # Monta o RecordSet com todos os órgãos do novo Organograma.
    public function recuperaFuturoOrgao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaFuturoOrgao().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaFuturoOrgao()
    {
        $stSql  = "                                                                                                                                       \n";
        $stSql .= "    SELECT                                                                                                                             \n";
        $stSql .= "            orgao.cod_orgao                                                                                                            \n";
        $stSql .= "         ,  recuperaDescricaoOrgao(orgao.cod_orgao, now()::date) as descricao                                                          \n";
        $stSql .= "         ,  orgao_nivel.cod_organograma                                                                                                \n";
        $stSql .= "         ,  organograma.fn_consulta_orgao(orgao_nivel.cod_organograma, orgao.cod_orgao) AS orgao                                       \n";
        $stSql .= "         ,  publico.fn_mascarareduzida(organograma.fn_consulta_orgao(orgao_nivel.cod_organograma, orgao.cod_orgao)) AS orgao_reduzido  \n";
        $stSql .= "         ,  (                                                                                                                          \n";
        $stSql .= "              SELECT  cod_orgao_new                                                                                                    \n";
        $stSql .= "                FROM  organograma.de_para_orgao                                                                                        \n";
        $stSql .= "               WHERE  de_para_orgao.cod_orgao = orgao.cod_orgao                                                                        \n";
        $stSql .= "            ) as cod_orgao_new                                                                                                         \n";
        $stSql .= "                                                                                                                                       \n";
        $stSql .= "      FROM  organograma.orgao                                                                                                          \n";
        $stSql .= "         ,  organograma.orgao_nivel                                                                                                    \n";
        $stSql .= "         ,  organograma.orgao_descricao                                                                                                \n";
        $stSql .= "                                                                                                                                       \n";
        $stSql .= "     WHERE  orgao.cod_orgao = orgao_nivel.cod_orgao                                                                                    \n";

        if ($this->getDado('cod_organograma'))
            $stSql .= "       AND  orgao_nivel.cod_organograma = ".$this->getDado('cod_organograma')."                                                    \n";

        $stSql .= "       AND  orgao_nivel.cod_nivel = 1                                                                                                  \n";
        $stSql .= "       AND  publico.fn_mascarareduzida(organograma.fn_consulta_orgao(orgao_nivel.cod_organograma, orgao.cod_orgao)) <> ''              \n";
        $stSql .= "                                                                                                                                       \n";
        $stSql .= "  GROUP BY  orgao.cod_orgao                                                                                                            \n";
        $stSql .= "         ,  orgao_nivel.cod_organograma                                                                                                \n";
        $stSql .= "                                                                                                                                       \n";
        $stSql .= "  ORDER BY  orgao ASC                                                                                                                  \n";

        return $stSql;
    }

    # Retorna os dados do novo organograma.
    public function recuperaDadosNovoOrganograma(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaDadosNovoOrganograma().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    public function montaRecuperaDadosNovoOrganograma()
    {
        $stSql  = "      SELECT  organograma.cod_organograma                                  \n";
        $stSql .= "           ,  implantacao                                                  \n";
        $stSql .= "           ,  TO_CHAR(implantacao, 'DD/MM/YYYY') as implantacao_formatado  \n";
        $stSql .= "                                                                           \n";
        $stSql .= "        FROM  organograma.de_para_orgao                                    \n";
        $stSql .= "                                                                           \n";
        $stSql .= "  INNER JOIN  organograma.orgao_nivel                                      \n";
        $stSql .= "          ON  orgao_nivel.cod_orgao = de_para_orgao.cod_orgao_new          \n";
        $stSql .= "                                                                           \n";
        $stSql .= "  INNER JOIN  organograma.organograma                                      \n";
        $stSql .= "          ON  organograma.cod_organograma = orgao_nivel.cod_organograma    \n";
        $stSql .= "                                                                           \n";
        $stSql .= "       LIMIT  1                                                            \n";

        return $stSql;
    }

}

?>
