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
    * Classe de mapeamento da tabela STN.VINCULO_FUNDEB
    * Data de Criação: 01/06/2011

    * @author Analista:
    * @author Desenvolvedor:

    * $Id:$

    * Casos de uso:

*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once CLA_PERSISTENTE;

class TSTNVinculoFundeb extends Persistente
{

    /**
        * Método Construtor
    */
    public function TSTNVinculoFundeb()
    {

        parent::Persistente();

        $this->setTabela('stn.vinculo_fundeb');

        $this->setCampoCod('');
        $this->setComplementoChave( 'cod_plano, cod_entidade, exercicio' );

                $this->AddCampo( 'cod_plano'   , 'integer', true,   '', true, true );
                $this->AddCampo( 'cod_entidade', 'integer', true,   '', true, true );
        $this->AddCampo( 'exercicio'   , 'char'   , true, '04', true, true );

    }

    public function montaRecuperaRelacionamento()
    {
        $stSql = " SELECT 	vinculo_fundeb.exercicio
                        ,	vinculo_fundeb.cod_entidade
                        ,	vinculo_fundeb.cod_plano
                    FROM 	stn.vinculo_fundeb
                    WHERE true ";
        if ($this->getDado("exercicio")) {
            $stSql .= " AND vinculo_fundeb.exercicio = '".$this->getDado("exercicio")."'";
        }

        if ($this->getDado("cod_entidade")) {
            $stSql .= " AND vinculo_fundeb.cod_entidade = '".$this->getDado("cod_entidade")."'";
        }

        if ($this->getDado("cod_plano")) {
            $stSql .= " AND vinculo_fundeb.cod_plano = '".$this->getDado("cod_plano")."'";
        }

        return $stSql;
    }

    public function montaRecuperaVinculoConta()
    {
        $stSql = " SELECT 	vinculo_fundeb.exercicio
                        ,   vinculo_fundeb.cod_entidade
                        ,   vinculo_fundeb.cod_plano
                        ,   plano_conta.nom_conta
                                                ,   plano_conta.cod_estrutural
                    FROM 	stn.vinculo_fundeb
                    INNER JOIN contabilidade.plano_analitica
                            ON  plano_analitica.exercicio = vinculo_fundeb.exercicio
                            AND plano_analitica.cod_plano = vinculo_fundeb.cod_plano
                                        INNER JOIN contabilidade.plano_conta
                                                        ON  plano_conta.exercicio = plano_analitica.exercicio
                            AND plano_conta.cod_conta = plano_analitica.cod_conta
                    WHERE true ";
        if ($this->getDado("exercicio")) {
            $stSql .= " AND vinculo_fundeb.exercicio = '".$this->getDado("exercicio")."'";
        }

        if ($this->getDado("cod_entidade")) {
            $stSql .= " AND vinculo_fundeb.cod_entidade = '".$this->getDado("cod_entidade")."'";
        }

        if ($this->getDado("cod_plano")) {
            $stSql .= " AND vinculo_fundeb.cod_plano = '".$this->getDado("cod_plano")."'";
        }

        return $stSql;
    }

    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaVinculoConta.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaVinculoConta(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem)) $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaVinculoConta().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
}

?>
