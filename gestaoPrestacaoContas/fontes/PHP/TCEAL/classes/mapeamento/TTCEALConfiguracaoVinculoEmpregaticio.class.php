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
    * Pacote de configuração do TCEAL
    * Data de Criação   : 10/10/2013

    * @author Analista: Carlos Adriano
    * @author Desenvolvedor: Carlos Adriano
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEALConfiguracaoVinculoEmpregaticio extends Persistente
{
    /**
     * Método Construtor
     * @access Private
     */
    public function TTCEALConfiguracaoVinculoEmpregaticio()
    {
        parent::Persistente();
        $this->setTabela('tceal.de_para_tipo_cargo');

        $this->setCampoCod('cod_sub_divisao');
        $this->setComplementoChave('cod_tipo_cargo_tce');

        $this->AddCampo('cod_entidade'      , 'integer', true, '' , true, true);
        $this->AddCampo('exercicio'         , 'char'   , true, '4', true, true);
        $this->AddCampo('cod_sub_divisao'   , 'integer', true, '' , true, true);
        $this->AddCampo('cod_tipo_cargo_tce', 'integer', true, '' , true, true);
    }

    public function recuperaDeParaTipoCargo(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaDeParaTipoCargo().$stFiltro.$stOrdem;
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    public function montaRecuperaDeParaTipoCargo()
    {
        $stSql  = "    SELECT sub_divisao.cod_sub_divisao                                       \n";
        $stSql .= "         , sub_divisao.cod_regime                                            \n";
        $stSql .= "         , sub_divisao.descricao                                             \n";
        $stSql .= "         , de_para_tipo_cargo.cod_tipo_cargo_tce                             \n";
        $stSql .= "         , regime.descricao AS descricao_regime                              \n";
        $stSql .= "      FROM pessoal".Sessao::getEntidade().".sub_divisao                      \n";
        $stSql .= "      JOIN pessoal".Sessao::getEntidade().".regime                           \n";
        $stSql .= "        ON regime.cod_regime = sub_divisao.cod_regime                        \n";
        $stSql .= " LEFT JOIN tceal.de_para_tipo_cargo                                          \n";
        $stSql .= "        ON de_para_tipo_cargo.cod_sub_divisao = sub_divisao.cod_sub_divisao  \n";
        $stSql .= "       AND de_para_tipo_cargo.cod_entidade     = ".Sessao::read('vinculo_empregaticio_cod_entidade')." \n";
        $stSql .= "       AND de_para_tipo_cargo.exercicio       = '".Sessao::getExercicio()."' \n";

        return $stSql;
    }

    public function excluirRegistros()
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaExcluirRegistros();
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    public function montaExcluirRegistros()
    {
        $stSql  = "    DELETE FROM tceal.de_para_tipo_cargo                                              \n";
        $stSql .= "          WHERE de_para_tipo_cargo.cod_entidade  = ".$this->getDado('cod_entidade')." \n";
        $stSql .= "            AND de_para_tipo_cargo.exercicio     = '".$this->getDado('exercicio')."'  \n";

        return $stSql;
    }
}
