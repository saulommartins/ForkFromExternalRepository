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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criação: 18/07/2013

  * @author Analista:
  * @author Desenvolvedor:

*/

class TTCERNSubdivisaoDescricaoSIAI extends Persistente
{
/**
* Método Construtor
* * @access Private
*/

    public function TTCERNSubdivisaoDescricaoSIAI()
    {
        parent::Persistente();
        $this->setTabela('tcern.sub_divisao_descricao_siai');
        $this->setCampoCod('cod_sub_divisao');
        $this->setComplementoChave('exercicio, cod_entidade, cod_siai');

        //AddCampo($stNome,$stTipo,$boRequerido='', $nrTamanho='',$boPrimaryKey='',$stForeignKey='',$stCampoForeignKey='',$stConteudo=null){
        $this->AddCampo('exercicio',        'varchar', true,  '4', false, true );
        $this->AddCampo('cod_entidade',     'integer', true,  '',  false, true );
        $this->AddCampo('cod_sub_divisao',  'integer', true,  '',  true,  true );
        $this->AddCampo('cod_siai',         'integer', true,  '',  false, true );
    }

    public function recuperaEntidade(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
    {
        return $this->executaRecupera("montaRecuperaEntidade", $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
    }

    public function montaRecuperaEntidade()
    {
        $stSql = "
        SELECT *
          FROM ".$this->getTabela()."
         WHERE exercicio = '" . $this->getDado('exercicio') . "'
           AND cod_entidade = " . $this->getDado('cod_entidade') . "
        ";

        return $stSql;
    }

    public function montaRecuperaRelacionamento()
    {
        $stSql  = "    SELECT sub_divisao_descricao_siai.cod_siai                                       \n";
        $stSql .= "         , sub_divisao.cod_sub_divisao                                               \n";
        $stSql .= "      FROM tcern.sub_divisao_descricao_siai                                          \n";
        $stSql .= "INNER JOIN pessoal".Sessao::getEntidade().".sub_divisao                              \n";
        $stSql .= "        ON sub_divisao_descricao_siai.cod_sub_divisao = sub_divisao.cod_sub_divisao  \n";
        $stSql .= "       AND sub_divisao_descricao_siai.cod_entidade = ".Sessao::getCodEntidade($boTransacao)."    \n";
        $stSql .= "       AND sub_divisao_descricao_siai.exercicio = '".Sessao::getExercicio()."'       \n";

        return $stSql;
    }
}
