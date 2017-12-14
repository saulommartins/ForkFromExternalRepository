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
    * Classe de mapeamento da tabela PPA.PPA_RECEITA_RECURSO
    *
    *
    * Data de Criação: 23/09/2008
    *
    *
    * @author Analista     : Bruno Ferreira
    * @author Desenvolvedor: Marcio Medeiros
    * @package URBEM
    * @subpackage Mapeamento
    * $Id: TPPAReceitaRecurso.class.php 39527 2009-04-07 19:49:36Z pedro.medeiros $
    * Casos de uso: uc-02.09.05
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TPPAReceitaRecurso extends Persistente
{

    /**
     * Método Construtor
     *
     * @ignore Atualizado para o ticket 14131
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTabela('ppa.ppa_receita_recurso');
        $this->setCampoCod('cod_recurso');
        $this->setComplementoChave('cod_ppa, exercicio, cod_conta, cod_entidade, cod_receita_dados, exercicio_recurso, cod_recurso');
        // campo, tipo, not_null, data_length, pk, fk
        $this->AddCampo('cod_receita',       'integer', true, '',  true, true);
        $this->AddCampo('cod_ppa',           'integer', true, '',  true, true);
        $this->AddCampo('exercicio',         'char',    true, '4', true, true);
        $this->AddCampo('cod_conta',         'integer', true, '',  true, true);
        $this->AddCampo('cod_entidade',      'integer', true, '',  true, true);
        $this->AddCampo('cod_receita_dados', 'integer', true, '',  true, true);
        $this->AddCampo('exercicio_recurso', 'char',    true, '4', true, true);
        $this->AddCampo('cod_recurso',       'integer', true, '',  true, true);
    }

    /**
     * Recupera a lista de Recursos da Receita
     *
     * @param  mixed     $rsRecordSet
     * @param  string    $stFiltro
     * @param  string    $stOrder
     * @param  bool      $boTransacao
     * @return RecordSet
     */
    public function recuperaReceitaRecurso(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaReceitaRecurso", $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
    }

    /**
     * Monta string SQL para o método "recuperaReceitaRecurso"
     *
     * @return string
     */
    public function montaRecuperaReceitaRecurso()
    {

        $stSql  = " SELECT DISTINCT ON (prr.cod_recurso)                      \n";
        $stSql .= "        prr.cod_ppa                                        \n";
        $stSql .= ",       prr.cod_receita                                    \n";
        $stSql .= ",       prr.exercicio                                      \n";
        $stSql .= ",       prr.cod_recurso                                    \n";
        $stSql .= ",       prr.cod_conta                                      \n";
        $stSql .= ",       prr.cod_entidade                                   \n";
        $stSql .= ",       prr.cod_receita_dados                              \n";
        $stSql .= ",       prr.exercicio_recurso                              \n";
        $stSql .= ",      CASE ppa.destinacao_recurso                         \n";
        $stSql .= "       WHEN true THEN ordes.nom_recurso                    \n";
        $stSql .= "       ELSE ord.nom_recurso                                \n";
        $stSql .= "        END as nom_recurso                                 \n";
        $stSql .= "       FROM ppa.ppa_receita_recurso prr                    \n";
        $stSql .= " INNER JOIN ppa.ppa as ppa                                 \n";
        $stSql .= "         ON ppa.cod_ppa = prr.cod_ppa                      \n";
        $stSql .= "  LEFT JOIN (SELECT ord.exercicio as exercicio             \n";
        $stSql .= "                  , ore.cod_recurso as cod_recurso         \n";
        $stSql .= "                  , ore.nom_recurso as nom_recurso         \n";
        $stSql .= "               FROM orcamento.recurso_destinacao as ord    \n";
        $stSql .= "         INNER JOIN orcamento.recurso as ore               \n";
        $stSql .= "                 ON ord.cod_recurso = ore.cod_recurso      \n";
        $stSql .= "        AND ord.exercicio = ore.exercicio ) as ordes       \n";
        $stSql .= "         ON ordes.exercicio = prr.exercicio_recurso        \n";
        $stSql .= "        AND ordes.cod_recurso = prr.cod_recurso            \n";
        $stSql .= "  LEFT JOIN (SELECT ord.cod_recurso as cod_recurso         \n";
        $stSql .= "                  , ord.nom_recurso as nom_recurso         \n";
        $stSql .= "                  , ord.exercicio as exercicio             \n";
        $stSql .= "               FROM orcamento.recurso_direto as ord        \n";
        $stSql .= "         INNER JOIN orcamento.recurso as ore               \n";
        $stSql .= "                 ON ord.cod_recurso = ore.cod_recurso      \n";
        $stSql .= "                AND ord.exercicio = ore.exercicio) as ord  \n";
        $stSql .= "         ON ord.exercicio = prr.exercicio_recurso          \n";
        $stSql .= "        AND ord.cod_recurso = prr.cod_recurso              \n";

        return $stSql;
    }

    /**
     *
     * @param  RecordSet $rsRecordSet
     * @param  string    $stCondicao
     * @param  string    $stOrdem
     * @param  bool      $boTransacao
     * @return RecordSet
     */
    public function recuperaValoresRecurso(&$rsRecordSet, $stCondicao = '' , $stOrdem = '' , $boTransacao = '')
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();
        $stSQL = $this->montaRecuperaValoresRecurso($stCondicao, $stOrdem);

        return $obConexao->executaSQL($rsRecordSet, $stSQL, $boTransacao);
    }

    public function montaRecuperaValoresRecurso($stFiltro, $stOrdem)
    {
        $stSQL  = "SELECT recurso.cod_recurso                                           \n";
        $stSQL .= "     , recurso.exercicio                                             \n";
        $stSQL .= "     , recurso.cod_conta                                             \n";
        $stSQL .= "     , recurso.cod_ppa                                               \n";
        $stSQL .= "     , recurso.cod_receita                                           \n";
        $stSQL .= "     , recurso.cod_receita_dados                                     \n";
        $stSQL .= "     , recurso.exercicio_recurso                                     \n";
        $stSQL .= "     , COALESCE(ano1.valor, 0.00) AS ano1                            \n";
        $stSQL .= "     , COALESCE(ano2.valor, 0.00) AS ano2                            \n";
        $stSQL .= "     , COALESCE(ano3.valor, 0.00) AS ano3                            \n";
        $stSQL .= "     , COALESCE(ano4.valor, 0.00) AS ano4                            \n";
        $stSQL .= "     , COALESCE(total.valor, 0.00) AS total                          \n";
        $stSQL .= "FROM ppa.ppa_receita_recurso as recurso                              \n";
        $stSQL .= "     LEFT JOIN ppa.ppa_receita_recurso_valor as ano1                 \n";
        $stSQL .= "          ON ano1.ano = '1'                                          \n";
        $stSQL .= "             AND recurso.cod_recurso       = ano1.cod_recurso        \n";
        $stSQL .= "             AND recurso.exercicio         = ano1.exercicio          \n";
        $stSQL .= "             AND recurso.cod_conta         = ano1.cod_conta          \n";
        $stSQL .= "             AND recurso.cod_ppa           = ano1.cod_ppa            \n";
        $stSQL .= "             AND recurso.cod_receita       = ano1.cod_receita        \n";
        $stSQL .= "             AND recurso.cod_receita_dados = ano1.cod_receita_dados  \n";
        $stSQL .= "             AND recurso.exercicio_recurso = ano1.exercicio_recurso  \n";
        $stSQL .= "     LEFT JOIN ppa.ppa_receita_recurso_valor as ano2                 \n";
        $stSQL .= "          ON ano2.ano = '2'                                          \n";
        $stSQL .= "             AND recurso.cod_recurso       = ano2.cod_recurso        \n";
        $stSQL .= "             AND recurso.exercicio         = ano2.exercicio          \n";
        $stSQL .= "             AND recurso.cod_conta         = ano2.cod_conta          \n";
        $stSQL .= "             AND recurso.cod_ppa           = ano2.cod_ppa            \n";
        $stSQL .= "             AND recurso.cod_receita       = ano2.cod_receita        \n";
        $stSQL .= "             AND recurso.cod_receita_dados = ano2.cod_receita_dados  \n";
        $stSQL .= "             AND recurso.exercicio_recurso = ano2.exercicio_recurso  \n";
        $stSQL .= "     LEFT JOIN ppa.ppa_receita_recurso_valor as ano3                 \n";
        $stSQL .= "          ON ano3.ano = '3'                                          \n";
        $stSQL .= "             AND recurso.cod_recurso       = ano3.cod_recurso        \n";
        $stSQL .= "             AND recurso.exercicio         = ano3.exercicio          \n";
        $stSQL .= "             AND recurso.cod_conta         = ano3.cod_conta          \n";
        $stSQL .= "             AND recurso.cod_ppa           = ano3.cod_ppa            \n";
        $stSQL .= "             AND recurso.cod_receita       = ano3.cod_receita        \n";
        $stSQL .= "             AND recurso.cod_receita_dados = ano3.cod_receita_dados  \n";
        $stSQL .= "             AND recurso.exercicio_recurso = ano3.exercicio_recurso  \n";
        $stSQL .= "     LEFT JOIN ppa.ppa_receita_recurso_valor as ano4                 \n";
        $stSQL .= "          ON ano4.ano = '4'                                          \n";
        $stSQL .= "             AND recurso.cod_recurso       = ano4.cod_recurso        \n";
        $stSQL .= "             AND recurso.exercicio         = ano4.exercicio          \n";
        $stSQL .= "             AND recurso.cod_conta         = ano4.cod_conta          \n";
        $stSQL .= "             AND recurso.cod_ppa           = ano4.cod_ppa            \n";
        $stSQL .= "             AND recurso.cod_receita       = ano4.cod_receita        \n";
        $stSQL .= "             AND recurso.cod_receita_dados = ano4.cod_receita_dados  \n";
        $stSQL .= "             AND recurso.exercicio_recurso = ano4.exercicio_recurso  \n";
        $stSQL .= "     LEFT JOIN ppa.ppa_receita_recurso_valor as total                \n";
        $stSQL .= "          ON total.ano = '0'                                         \n";
        $stSQL .= "             AND recurso.cod_recurso       = total.cod_recurso       \n";
        $stSQL .= "             AND recurso.exercicio         = total.exercicio         \n";
        $stSQL .= "             AND recurso.cod_conta         = total.cod_conta         \n";
        $stSQL .= "             AND recurso.cod_ppa           = total.cod_ppa           \n";
        $stSQL .= "             AND recurso.cod_receita       = total.cod_receita       \n";
        $stSQL .= "             AND recurso.cod_receita_dados = total.cod_receita_dados \n";
        $stSQL .= "             AND recurso.exercicio_recurso = total.exercicio_recurso \n";
        $stSQL .= $stFiltro . $stOrdem;

        return $stSQL;
    }

} // end of class

?>
