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
    * Classe de mapeamento da tabela contabilidade.plano_conta_geral
    * Data de Criação: 08/10/2012

    * @author Analista: Tonismar
    * @author Desenvolvedor: Eduardo

    * @package URBEM
    * @subpackage Mapeamento
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TContabilidadePlanoContaGeral extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */

    public function TContabilidadePlanoContaGeral()
    {
        parent::Persistente();
        $this->setTabela('contabilidade.plano_conta_geral');

        $this->setCampoCod('cod_plano');
        $this->setComplementoChave('cod_uf, codigo_estrutural');

        $this->AddCampo('cod_plano'          , 'integer', true, ''   , true , false);
        $this->AddCampo('cod_uf'             , 'integer', true, ''   , true , true);
        $this->AddCampo('versao'             , 'char'   , true, '10' , false, false);
        $this->AddCampo('dt_versao'          , 'date'   , true, ''   , false, false);
    }

    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método
        * montaRecuperaDados.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaUFs(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stOrdem = " ORDER BY nom_uf";
        $stSql = $this->montaRecuperaUFs().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaUFs()
    {
        $stSQL  = "  SELECT CASE WHEN sw_uf.cod_uf = 0 THEN             \n";
        $stSQL .= "             'União'                                 \n";
        $stSQL .= "         ELSE                                        \n";
        $stSQL .= "             sw_uf.nom_uf                            \n";
        $stSQL .= "     END AS nom_uf                                   \n";
        $stSQL .= "       , CASE WHEN sw_uf.cod_uf = 0 THEN             \n";
        $stSQL .= "             'União'                                 \n";
        $stSQL .= "         ELSE                                        \n";
        $stSQL .= "             sw_uf.sigla_uf                          \n";
        $stSQL .= "     END AS sigla_uf                                 \n";
        $stSQL .= "       , sw_uf.cod_uf                                \n";
        $stSQL .= "    FROM contabilidade.plano_conta_geral             \n";
        $stSQL .= "    JOIN sw_uf                                       \n";
        $stSQL .= "      ON sw_uf.cod_uf = plano_conta_geral.cod_uf     \n";
        $stSQL .= "GROUP BY sw_uf.nom_uf                                \n";
        $stSQL .= "       , sw_uf.cod_uf                                \n";
        $stSQL .= "       , sw_uf.sigla_uf                              \n";

        return $stSQL;
    }

    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método
        * montaRecuperaDados.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaVersoes(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stOrdem  = "GROUP BY sw_uf.nom_uf                                \n";
        $stOrdem .= "       , sw_uf.cod_uf                                \n";
        $stOrdem .= "       , sw_uf.sigla_uf                              \n";
        $stOrdem .= "       , plano_conta_geral.cod_plano                 \n";
        $stOrdem .= "       , plano_conta_geral.versao                    \n";
        $stOrdem .= "       , plano_conta_geral.dt_versao                 \n";
        $stOrdem .= " ORDER BY nom_uf";

        $stSql = $this->montaRecuperaVersoes().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaVersoes()
    {
        $stSQL  = "  SELECT sw_uf.nom_uf                                \n";
        $stSQL .= "       , sw_uf.cod_uf                                \n";
        $stSQL .= "       , sw_uf.sigla_uf                              \n";
        $stSQL .= "       , plano_conta_geral.cod_plano                 \n";
        $stSQL .= "       , plano_conta_geral.versao                    \n";
        $stSQL .= "       , plano_conta_geral.dt_versao                 \n";
        $stSQL .= "    FROM contabilidade.plano_conta_geral             \n";
        $stSQL .= "    JOIN sw_uf                                       \n";
        $stSQL .= "      ON sw_uf.cod_uf = plano_conta_geral.cod_uf     \n";

        return $stSQL;
    }
}
?>
