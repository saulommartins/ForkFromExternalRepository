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
  * Classe de mapeamento da tabela PESSOAL.REGIME
  * Data de Criação: 07/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Vandre Miguel Ramos

  * @package URBEM
  * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    Caso de uso: uc-04.04.05

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  PESSOAL.REGIME
  * Data de Criação: 07/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Vandre Miguel Ramos

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalRegime extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalRegime()
{
    parent::Persistente();
    $this->setTabela('pessoal.regime');

    $this->setCampoCod('cod_regime');
    $this->setComplementoChave('');

    $this->AddCampo('cod_regime','integer',true,'',true,false);
    $this->AddCampo('descricao','varchar',true,'80',false,false);

}

/**
    * Metodo que verifica se não existe subdivisoes em cargo_sub_divisao e especialidade_sub_divisao
*/
function validaExclusao($stFiltro = "", $boTransacao = "")
{
    $obErro = new erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaValidaExclusaoEspecialidade().$stFiltro;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( $rsRecordSet->getNumLinhas() > 0 ) {
            $obErro->setDescricao('Este regime está sendo utilizado por uma especialidade, por esse motivo não pode ser excluído!');
        } else {
            $stSql  = $this->montaValidaExclusaoCargo().$stFiltro;
            $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                if ( $rsRecordSet->getNumLinhas() > 0 ) {
                    $obErro->setDescricao('Este regime está sendo utilizado por um cargo, por esse motivo não pode ser excluído!');
                }
            }
        }
    }

    return $obErro;
}

function montaValidaExclusaoCargo()
{
    $stSQL .="   SELECT PR.cod_regime                                    \n";
    $stSQL .="     FROM pessoal.regime PR                                \n";
    $stSQL .="     JOIN pessoal.sub_divisao PS                           \n";
    $stSQL .="       ON PS.cod_regime = PR.cod_regime                    \n";
    $stSQL .="     JOIN pessoal.cargo_sub_divisao PCS                    \n";
    $stSQL .="       ON PS.cod_sub_divisao = PCS.cod_sub_divisao         \n";
    $stSQL .="    WHERE PR.cod_regime = ".$this->getDado('cod_regime')." \n";
    $stSQL .="    LIMIT 1                                                \n";

    return $stSQL;
}

function montaValidaExclusaoEspecialidade()
{
    $stSQL .="   SELECT PR.cod_regime                                    \n";
    $stSQL .="     FROM pessoal.regime PR                                \n";
    $stSQL .="     JOIN pessoal.sub_divisao PS                           \n";
    $stSQL .="       ON PS.cod_regime = PR.cod_regime                    \n";
    $stSQL .="     JOIN pessoal.especialidade_sub_divisao PES            \n";
    $stSQL .="       ON PES.cod_sub_divisao = PS.cod_sub_divisao         \n";
    $stSQL .="    WHERE PR.cod_regime = ".$this->getDado('cod_regime')." \n";
    $stSQL .="    LIMIT 1                                                \n";

    return $stSQL;
}

}
