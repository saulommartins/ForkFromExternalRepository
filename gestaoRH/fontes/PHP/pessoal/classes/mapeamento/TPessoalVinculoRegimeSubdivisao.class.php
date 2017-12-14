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

/*
 * Classe de mapeamento da tabela tcepe.vinculo_regime_subdivisao
 * Data de Criação: 29/09/2014
 * @author Desenvolvedor Diogo Zarpelon <diogo.zarpelon@cnm.org.br>
 * @package URBEM
 * @subpackage
 $Id:$
 */

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once CLA_PERSISTENTE;

class TPessoalVinculoRegimeSubdivisao extends Persistente
{
    /**
     * Método Construtor da classe de mapeamento
     *
     * @return void
    */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela  ('pessoal.vinculo_regime_subdivisao');

        $this->setCampoCod('exercicio');
        $this->setComplementoChave('cod_sub_divisao');

        $this->AddCampo('exercicio'        , 'varchar', true, '4' , true  , true);
        $this->AddCampo('cod_sub_divisao'  , 'integer', true, ''  , true  , true);
        $this->AddCampo('cod_tipo_regime'  , 'integer', true, ''  , false , true);
        $this->AddCampo('cod_tipo_vinculo' , 'integer', true, ''  , false , true);
    }

    public function recuperaVinculoRegimeSubdivisao(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaVinculoRegimeSubdivisao().$stFiltro.$stOrdem;
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    public function montaRecuperaVinculoRegimeSubdivisao()
    {
        $stSql  = "
          SELECT sub_divisao.cod_sub_divisao
               , sub_divisao.cod_regime
               , sub_divisao.descricao
               , vinculo_regime_subdivisao.cod_tipo_regime
               , vinculo_regime_subdivisao.cod_tipo_vinculo

            FROM pessoal".Sessao::getEntidade().".sub_divisao                             

      INNER JOIN pessoal".Sessao::getEntidade().".regime                                  
              ON regime.cod_regime = sub_divisao.cod_regime                               

       LEFT JOIN pessoal".Sessao::getEntidade().".vinculo_regime_subdivisao               
              ON vinculo_regime_subdivisao.cod_sub_divisao = sub_divisao.cod_sub_divisao
             AND vinculo_regime_subdivisao.exercicio = '".Sessao::getExercicio()."'   

           WHERE 1=1 ";

        return $stSql;
    }

}
